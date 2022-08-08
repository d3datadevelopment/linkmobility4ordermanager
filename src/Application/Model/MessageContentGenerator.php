<?php

namespace D3\Linkmobility4Ordermanager\Application\Model;

use D3\Linkmobility4Ordermanager\Application\Model\Exceptions\emptyMesageException;
use D3\ModCfg\Application\Model\d3str;
use D3\Ordermanager\Application\Model\d3ordermanager as Manager;
use D3\Ordermanager\Application\Model\d3ordermanager_renderererrorhandler;
use D3\Ordermanager\Modules\Application\Model\d3_oxemail_ordermanager;
use D3\Ordermanager\Modules\Application\Model\d3_oxorder_ordermanager as Item;
use D3\OxidServiceBridges\Internal\Framework\Module\Path\ModulePathResolverBridgeInterface;
use Exception;
use InvalidArgumentException;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Exception\ArticleException;
use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Controller\BaseController;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use stdClass;

class MessageContentGenerator
{
    public const TEMPLATE_FROM_ADMIN = 'fromAdmin';
    public const TEMPLATE_FROM_FRONTEND = 'fromFrontend';
    public const TEMPLATE_FROM_MODULE = 'fromModule';

    private $templateFrom = self::TEMPLATE_FROM_ADMIN;
    private $tplModuleId;
    /** @var Manager */
    private $manager;
    /** @var Item */
    private $item;

    /**
     * @param Manager $manager
     * @param Item $item
     * @throws ArticleException
     * @throws ArticleInputException
     */
    public function __construct(Manager $manager, Item $item)
    {
        $this->setManager($manager);
        $this->prepareItem($item);
        $this->setItem($item);
    }

    /**
     * @param $tplName
     * @return string
     * @throws emptyMesageException
     * @throws Exception
     */
    public function generateFromTpl($tplName): string
    {
        [$iCurrentTplLang, $iCurrentBaseLang, $iCurrentCurrency] = $this->preGenerate();

        $mail = $this->getMailer();
        $engine = $this->getTemplateEngine($mail);
        $content = $engine->render($tplName);

        return $this->postGenerate($content, $iCurrentTplLang, $iCurrentBaseLang, $iCurrentCurrency);
    }

    /**
     * @param string $cmsIdent
     * @return string
     * @throws emptyMesageException
     * @throws Exception
     */
    public function generateFromCms(string $cmsIdent): string
    {
        [$iCurrentTplLang, $iCurrentBaseLang, $iCurrentCurrency] = $this->preGenerate();

        $mail = $this->getMailer();
        $engine = $this->getTemplateEngine($mail);

        $oUtilsView = Registry::getUtilsView();
        $oContent = oxNew(Content::class);
        $oContent->loadInLang(
            $this->getItem()->getFieldData('oxlang'),
            $cmsIdent
        );

        $content    = $oUtilsView->getRenderedContent(
            $oContent->getFieldData('oxcontent'),
            $engine->getGlobals(),
            $oContent->getId() . 'oxcontent'
        );

        return $this->postGenerate($content, $iCurrentTplLang, $iCurrentBaseLang, $iCurrentCurrency);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function preGenerate(): array
    {
        Registry::getConfig()->setAdminMode($this->templateFrom === self::TEMPLATE_FROM_ADMIN);
        $iOrderLangId = $this->getItem()->getFieldData('oxlang');
        $oLang = Registry::getLang();
        /** @var int $iCurrentTplLang */
        $iCurrentTplLang = $oLang->getTplLanguage();
        /** @var int $iCurrentBaseLang */
        $iCurrentBaseLang = $oLang->getBaseLanguage();
        $oLang->setTplLanguage($iOrderLangId);
        $oLang->setBaseLanguage($iOrderLangId);

        /** @var int $iCurrentCurrency */
        $iCurrentCurrency = Registry::getConfig()->getShopCurrency();
        /** @var stdClass $oOrderCurr */
        $oOrderCurr = $this->getItem()->getOrderCurrency();
        $iOrderCurr = $oOrderCurr->id;
        Registry::getConfig()->setActShopCurrency($iOrderCurr);

        set_error_handler(
            [d3GetModCfgDIC()->get(d3ordermanager_renderererrorhandler::class), 'd3HandleTemplateEngineErrors']
        );

        return array($iCurrentTplLang, $iCurrentBaseLang, $iCurrentCurrency);
    }

    /**
     * @param string $content
     * @param int $iCurrentTplLang
     * @param int $iCurrentBaseLang
     * @param int $iCurrentCurrency
     * @return string
     */
    public function postGenerate(string $content, int $iCurrentTplLang, int $iCurrentBaseLang, int $iCurrentCurrency): string
    {
        restore_error_handler();

        $oLang = Registry::getLang();
        $oLang->setTplLanguage($iCurrentTplLang);
        $oLang->setBaseLanguage($iCurrentBaseLang);
        Registry::getConfig()->setActShopCurrency($iCurrentCurrency);

        Registry::getConfig()->setAdminMode(true);

        if (false === (bool) strlen($content)) {
            throw oxNew(emptyMesageException::class, 'message content is empty', $this->getManager()->getFieldData('oxtitle'));
        }

        return $content;
    }

    /**
     * @param Item $item
     * @return void
     * @throws ArticleException
     * @throws ArticleInputException
     */
    protected function prepareItem(Item $item)
    {
        /** @var Basket $oBasket */
        $oBasket = $item->d3getOrderBasket4OrderManager($this->getManager());

        $oPayment = oxNew(Payment::class);
        $oPayment->loadInLang($item->getFieldData('oxlang'), $oBasket->getPaymentId());

        $item->d3setBasket4OrderManager($oBasket);
        $item->d3setPayment4OrderManager($oPayment);
    }

    /**
     * @param string $from
     * @param string|null $moduleId
     * @return void
     */
    public function setTemplateFrom(string $from, string $moduleId = null)
    {
        switch ($from) {
            case self::TEMPLATE_FROM_ADMIN:
                $this->templateFrom = self::TEMPLATE_FROM_ADMIN;
                return;
            case self::TEMPLATE_FROM_FRONTEND:
                $this->templateFrom = self::TEMPLATE_FROM_FRONTEND;
                return;
            case self::TEMPLATE_FROM_MODULE:
                if (is_null($moduleId)) throw oxNew(InvalidArgumentException::class, 'missing module');
                $this->templateFrom = self::TEMPLATE_FROM_MODULE;
                $this->tplModuleId = $moduleId;
                return;
        }

        throw oxNew(InvalidArgumentException::class, 'unknown template source');
    }

    /**
     * @param Email $mail
     * @return TemplateEngineInterface
     */
    protected function getTemplateEngine(Email $mail): TemplateEngineInterface
    {
        /** @var TemplateRendererInterface $renderer */
        $renderer = ContainerFactory::getInstance()->getContainer()
            ->get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
        $engine = $renderer->getTemplateEngine();

        // ToDo: check in TWIG and change to a generic solution (e.g. path names in template name)
        // Smarty only
        if (method_exists($engine, '__set')) {
            $engine->__set('template_dir', $this->getTemplateDir4OrderManager());
        }

        foreach ($mail->getViewData() as $id => $value) {
            $engine->addGlobal($id, $value);
        }

        return $engine;
    }

    /**
     * @return string
     */
    public function getTemplateDir4OrderManager(): string
    {
        if ($this->templateFrom === self::TEMPLATE_FROM_MODULE) {
            /** @var ModulePathResolverBridgeInterface $pathResolverBridge */
            $pathResolverBridge = ContainerFactory::getInstance()->getContainer()->get(ModulePathResolverBridgeInterface::class);
            $sModulePath = $pathResolverBridge->getFullModulePathFromConfiguration(
                $this->tplModuleId,
                Registry::getConfig()->getShopId()
            );
            $sPath = (oxNew(d3str::class))->untrailingslashit($sModulePath);
        } else {
            $blAdmin = $this->templateFrom === self::TEMPLATE_FROM_ADMIN;
            $sPath   = Registry::getConfig()->getTemplateDir($blAdmin);
        }
        return $sPath;
    }

    /**
     * @return d3_oxemail_ordermanager
     */
    public function getMailer(): d3_oxemail_ordermanager
    {
        $mail = oxNew(Email::class);

        $this->setViewData($mail);

        return $mail;
    }

    /**
     * @param Email $mail
     * @return void
     */
    public function setViewData(Email $mail)
    {
        $oShop = $this->getShop();

        $mail->setViewData("oShop", $oShop);
        $mail->setViewData("oViewConf", (oxNew(BaseController::class))->getViewConfig());
        $mail->setViewData("oOrder", $this->getItem());
        $mail->setViewData("oUser", $this->getItem()->getOrderUser());
        $mail->setViewData("shopTemplateDir", Registry::getConfig()->getTemplateDir());
        $mail->setViewData("charset", Registry::getLang()->translateString("charset"));

        $mail->setViewData("shop", $oShop);
        $mail->setViewData("order", $this->getItem());
        $mail->setViewData("user", $this->getItem()->getOrderUser());
        $mail->setViewData("payment", $this->getItem()->d3getPayment4OrderManager());
        $mail->setViewData("oDelSet", $this->getItem()->getDelSet());
        $mail->setViewData("currency", $this->getItem()->getOrderCurrency());
        $mail->setViewData("basket", $this->getItem()->d3getBasket4OrderManager());
        $mail->setViewData("oEmailView", $mail);
    }

    /**
     * @return Shop
     */
    public function getShop(): Shop
    {
        return Registry::getConfig()->getActiveShop();
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    /**
     * @return Manager
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * @param Manager $manager
     */
    public function setManager(Manager $manager): void
    {
        $this->manager = $manager;
    }
}
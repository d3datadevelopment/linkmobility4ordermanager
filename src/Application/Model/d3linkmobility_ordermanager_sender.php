<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * https://www.d3data.de
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author    D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link      https://www.oxidmodule.com
 */

declare(strict_types=1);

namespace D3\Linkmobility4Ordermanager\Application\Model;

use D3\Linkmobility4Ordermanager\Application\Model\Exceptions\emptyMesageException;
use D3\Linkmobility4OXID\Application\Model\Exceptions\noRecipientFoundException;
use D3\Linkmobility4OXID\Application\Model\MessageTypes\Sms;
use D3\Linkmobility4OXID\Application\Model\OrderRecipients;
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\ValueObject\Recipient;
use D3\ModCfg\Application\Model\d3str;
use D3\ModCfg\Application\Model\Exception\d3ParameterNotFoundException;
use D3\Ordermanager\Application\Model\d3ordermanager;
use D3\Ordermanager\Application\Model\d3ordermanager as Manager;
use D3\Ordermanager\Application\Model\d3ordermanager_renderererrorhandler;
use D3\OxidServiceBridges\Internal\Framework\Module\Path\ModulePathResolverBridgeInterface;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Exception\ArticleException;
use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Controller\BaseController;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class d3linkmobility_ordermanager_sender
{
    /** @var d3ordermanager */
    protected $manager;

    /** @var Order */
    protected $item;

    /**
     * @param Manager $manager
     * @param Order $item
     * @return void
     * @throws ArticleException
     * @throws ArticleInputException
     * @throws noRecipientFoundException
     * @throws d3ParameterNotFoundException
     * @throws emptyMesageException
     */
    public function sendOrderManagerSms(d3ordermanager $manager, Order $item)
    {
        $this->setManager($manager);
        $this->setItem($item);

        $sms = oxNew(Sms::class, $this->getMessageBody());
        $sms->sendCustomRecipientMessage($this->getRecipients());
    }

    /**
     * @return string
     * @throws ArticleException
     * @throws ArticleInputException
     * @throws d3ParameterNotFoundException
     * @throws emptyMesageException
     */
    protected function getMessageBody(): string
    {
        $oManager = $this->getManager();

        $viewData = [];

        $blTplFromAdmin = $oManager->getValue('sLinkMobilityMessageFromTheme') == 'admin';

        $oConfig = Registry::getConfig();
        $oConfig->setAdminMode($blTplFromAdmin);

        /** @var TemplateRendererInterface $renderer */
        $renderer = ContainerFactory::getInstance()->getContainer()
             ->get(TemplateRendererBridgeInterface::class)
             ->getTemplateRenderer();
        $templateEngine = $renderer->getTemplateEngine();

        /** @var Basket $oBasket */
        $oBasket = $oManager->getCurrentItem()->d3getOrderBasket4OrderManager($oManager);

        $oPayment = oxNew(Payment::class);
        $oPayment->loadInLang($oManager->getCurrentItem()->getFieldData('oxlang'), $oBasket->getPaymentId());

        $oManager->getCurrentItem()->d3setBasket4OrderManager($oBasket);
        $oManager->getCurrentItem()->d3setPayment4OrderManager($oPayment);

        $oShop = Registry::getConfig()->getActiveShop();

        $viewData["oShop"] = $oShop;
        $viewData["oViewConf"] = (oxNew(BaseController::class))->getViewConfig();
        $viewData["oOrder"] = $oManager->getCurrentItem();
        $viewData["oUser"] = $oManager->getCurrentItem()->getOrderUser();
        $viewData["shopTemplateDir"] = $oConfig->getTemplateDir(false);
        $viewData["charset"] = Registry::getLang()->translateString("charset");

        $viewData["shop"] = $oShop;
        $viewData["order"] = $oManager->getCurrentItem();
        $viewData["user"] = $oManager->getCurrentItem()->getOrderUser();
        $viewData["payment"] = $oPayment;
        $viewData["oDelSet"] = $oManager->getCurrentItem()->getDelSet();
        $viewData["currency"] = $oManager->getCurrentItem()->getOrderCurrency();
        $viewData["basket"] = $oBasket;
        $viewData["oEmailView"] = oxNew(Email::class);

        // ToDo: check in TWIG and change to a generic solution (e.g. path names in template name)
        // Smarty only
        if (method_exists($templateEngine, '__set')) {
            $templateEngine->__set('template_dir', $this->getTemplateDir4OrderManager($oManager));
        }

        foreach ($viewData as $id => $value) {
            $templateEngine->addGlobal($id, $value);
        }

        $content = $this->_d3GenerateOrderManagerMessageContent($templateEngine);
        $oConfig->setAdminMode(true);

        return $content;
    }

    /**
     * @param TemplateEngineInterface $templateEngine
     * @return string
     * @throws d3ParameterNotFoundException
     * @throws emptyMesageException
     */
    protected function _d3GenerateOrderManagerMessageContent(TemplateEngineInterface $templateEngine): string
    {
        $iOrderLangId = $this->getManager()->getCurrentItem()->getFieldData('oxlang');
        $oLang        = Registry::getLang();
        /** @var int $iCurrentTplLang */
        $iCurrentTplLang = $oLang->getTplLanguage();
        /** @var int $iCurrentBaseLang */
        $iCurrentBaseLang = $oLang->getBaseLanguage();
        $oLang->setTplLanguage($iOrderLangId);
        $oLang->setBaseLanguage($iOrderLangId);
        $content = '';

        /** @var int $iCurrentCurrency */
        $iCurrentCurrency = Registry::getConfig()->getShopCurrency();
        /** @var \stdClass $oOrderCurr */
        $oOrderCurr = $this->getManager()->getCurrentItem()->getOrderCurrency();
        $iOrderCurr = $oOrderCurr->id;
        Registry::getConfig()->setActShopCurrency($iOrderCurr);

        set_error_handler(
            [d3GetModCfgDIC()->get(d3ordermanager_renderererrorhandler::class), 'd3HandleTemplateEngineErrors']
        );

        if ($this->getManager()->getValue('sLinkMobilityMessageFromSource') == 'cms') {
            $oUtilsView = Registry::getUtilsView();
            $oContent = oxNew(Content::class);
            $oContent->loadInLang($iOrderLangId, $this->getManager()->getValue('sLinkMobilityMessageFromContentname'));

            $content    = $oUtilsView->getRenderedContent(
                $oContent->getFieldData('oxcontent'),
                $templateEngine->getGlobals(),
                $oContent->getId() . 'oxcontent'
            );
        } elseif ($this->getManager()->getValue('sLinkMobilityMessageFromSource') == 'template') {
            $content    = $templateEngine->render($this->getManager()->getValue('sLinkMobilityMessageFromTemplatename'));
        }

        if (false === (bool) strlen($content)) {
            throw oxNew(emptyMesageException::class, 'message content is empty', $this->getManager()->getFieldData('oxtitle'));
        }

        restore_error_handler();

        $oLang->setTplLanguage($iCurrentTplLang);
        $oLang->setBaseLanguage($iCurrentBaseLang);
        Registry::getConfig()->setActShopCurrency($iCurrentCurrency);

        return $content;
    }

    /**
     * @param Manager $oManager
     * @return string
     */
    public function getTemplateDir4OrderManager(Manager $oManager): string
    {
        if ($oManager->getValue('sLinkMobilityMessageFromTheme') == 'module') {
            $sModuleId = $oManager->getValue('sLinkMobilityMessageFromModulePath');
            /** @var ModulePathResolverBridgeInterface $pathResolverBridge */
            $pathResolverBridge = ContainerFactory::getInstance()->getContainer()->get(ModulePathResolverBridgeInterface::class);
            $sModulePath = $pathResolverBridge->getFullModulePathFromConfiguration(
                $sModuleId,
                Registry::getConfig()->getShopId()
            );
            $sPath = (oxNew(d3str::class))->untrailingslashit($sModulePath);
        } else {
            $blAdmin = $oManager->getValue('sLinkMobilityMessageFromTheme') == 'admin';
            $sPath   = Registry::getConfig()->getTemplateDir($blAdmin);
        }
        return $sPath;
    }

    /**
     * @return array
     * @throws StandardException
     * @throws d3ParameterNotFoundException
     */
    protected function getRecipients(): array
    {
        $recipients = [];
        if ((bool) $this->getManager()->getValue('blLinkMobilityMessageToCustomer')) {
            try {
                $recipients[] = (oxNew(OrderRecipients::class, $this->getItem()))->getSmsRecipient();
            } catch (noRecipientFoundException $e) {
                /** @var string $note */
                $note = Registry::getLang()->translateString('D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE_NORECIPIENT', null, true);
                $this->getManager()->getRemarkHandler()->addNote($note);
            }
        }
        if ((bool) $this->getManager()->getValue('blLinkMobilityMessageToCustom') &&
            strlen(trim($this->getManager()->getValue('sLinkMobilityMessageToCustomAddress')))
        ) {
            foreach ($this->extractCustomAddresses() as $phoneNumber => $countryId) {
                try {
                    $recipients[] = oxNew(Recipient::class, $phoneNumber, $countryId);
                } catch (RecipientException $e) {
                    Registry::getLogger()->info($e->getMessage(), [$phoneNumber, $countryId]);
                    /** @var string $format */
                    $format = Registry::getLang()->translateString(
                        'D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE_RECIPIENTERROR',
                        null,
                        true
                    );
                    $this->getManager()->getRemarkHandler()->addNote(sprintf($format, $phoneNumber, $countryId));
                }
            }
        }

        return $recipients;
    }

    /**
     * @return array
     */
    protected function extractCustomAddresses(): array
    {
        $addresses = [];
        $customAddresses = trim($this->getManager()->getValue('sLinkMobilityMessageToCustomAddress'));

        if (strlen($customAddresses)) {
            foreach (explode(';', $customAddresses) as $addressGroups) {
                [$phoneNumber, $countryId] = explode('@', trim($addressGroups));
                $addresses[trim($phoneNumber)] = trim($countryId);
            }
        }

        return $addresses;
    }

    /**
     * @return Order
     */
    public function getItem(): Order
    {
        return $this->item;
    }

    /**
     * @param Order $item
     */
    public function setItem(Order $item): void
    {
        $this->item = $item;
    }

    /**
     * @param d3ordermanager $manager
     */
    public function setManager(d3ordermanager $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * @return d3ordermanager
     */
    public function getManager(): d3ordermanager
    {
        return $this->manager;
    }
}

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

use D3\Linkmobility4OXID\Application\Model\MessageTypes\Sms;
use D3\Linkmobility4OXID\Application\Model\OrderRecipients;
use D3\LinkmobilityClient\ValueObject\Recipient;
use D3\Ordermanager\Application\Model\d3ordermanager;
use D3\Ordermanager\Application\Model\d3ordermanager as Manager;
use D3\OxidServiceBridges\Internal\Framework\Module\Path\ModulePathResolverBridgeInterface;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class d3linkmobility_ordermanager_sender
{
    /** @var d3ordermanager */
    protected $manager;

    /** @var Order */
    protected $item;

    public function sendOrderManagerSms(d3ordermanager $manager, Order $item)
    {
        $this->setManager($manager);
        $this->setItem($item);

        $sms = oxNew(Sms::class, $this->getMessageBody());
        $sms->sendCustomRecipientMessage($this->getRecipients());
    }

    /**
     * @return string
     */
    protected function getMessageBody(): string
    {
        $oManager = $this->getManager();

        $aContent = array();
        $viewData = [];

        $blTplFromAdmin = $oManager->getValue('sLinkMobilityMessageFromTheme') == 'admin';

        $oConfig = Registry::getConfig();
        $oConfig->setAdminMode($blTplFromAdmin);

        /** @var TemplateRendererInterface $renderer */
        $renderer = ContainerFactory::getInstance()->getContainer()
             ->get(TemplateRendererBridgeInterface::class)
             ->getTemplateRenderer();
        $templateEngine = $renderer->getTemplateEngine();

        /** @var $oBasket Basket */
        $oBasket = $oManager->getCurrentItem()->d3getOrderBasket4OrderManager($oManager);

        $oPayment = oxNew(Payment::class);
        $oPayment->loadInLang($oManager->getCurrentItem()->getFieldData('oxlang'), $oBasket->getPaymentId());

        $oManager->getCurrentItem()->d3setBasket4OrderManager($oBasket);
        $oManager->getCurrentItem()->d3setPayment4OrderManager($oPayment);

        $oShop = Registry::getConfig()->getActiveShop();

        $viewData["oShop"] = $oShop;
        $viewData["oViewConf"] = $this->getViewConfig();
        $viewData["oOrder"] = $oManager->getCurrentItem();
        $viewData["oUser"] = $oManager->getCurrentItem()->getOrderUser();
        $viewData["shopTemplateDir"] = $this->d3GetOrderManagerConfigObject()->getTemplateDir(false);
        $viewData["charset"] = $this->d3GetOrderManagerLanguageObject()->translateString("charset");

        $viewData["shop"] = $oShop;
        $viewData["order"] = $oManager->getCurrentItem();
        $viewData["user"] = $oManager->getCurrentItem()->getOrderUser();
        $viewData["payment"] = $oPayment;
        $viewData["oDelSet"] = $oManager->getCurrentItem()->getDelSet();
        $viewData["currency"] = $oManager->getCurrentItem()->getOrderCurrency();
        $viewData["basket"] = $oBasket;

        // ToDo: check in TWIG and change to a generic solution (e.g. path names in template name)
        // Smarty only
        if (method_exists($templateEngine, '__set')) {
            $templateEngine->__set( 'template_dir', $this->getTemplateDir4OrderManager( $oManager ) );
        }

        foreach ($viewData as $id => $value) {
            $templateEngine->addGlobal($id, $value);
        }

        if (false == $this->d3GetOrderManagerSet()->getLicenseConfigData('blUseMailSendOnly', 0)) {
            $templateEngine = $this->d3SendMailHook($templateEngine);
        }

        $aContent = $this->_d3GenerateOrderManagerMailContent($aContent, $templateEngine);
        $oConfig->setAdminMode(true);

        return $aContent;
    }

    /**
     * @param Manager $oManager
     *
     * @return string
     * @throws Exception
     */
    public function getTemplateDir4OrderManager( Manager $oManager ): string
    {
        if ($oManager->getValue('sSendMailFromTheme') == 'module') {
            $sModuleId = $oManager->getValue('sSendMailFromModulePath');
            /** @var ModulePathResolverBridgeInterface $pathResolverBridge */
            $pathResolverBridge = $this->d3getOrderManagerDIContainer()->get(ModulePathResolverBridgeInterface::class);
            $sModulePath = $pathResolverBridge->getFullModulePathFromConfiguration(
                $sModuleId,
                Registry::getConfig()->getShopId()
            );
            $sPath = $this->getD3OrderManagerStrObject()->untrailingslashit($sModulePath);
        } else {
            $blAdmin = $oManager->getValue('sSendMailFromTheme') == 'admin';
            $sPath   = $this->d3GetOrderManagerConfigObject()->getTemplateDir($blAdmin);
        }
        return $sPath;
    }

    /**
     * @return array
     * @throws \D3\Linkmobility4OXID\Application\Model\Exceptions\noRecipientFoundException
     * @throws \D3\ModCfg\Application\Model\Exception\d3ParameterNotFoundException
     */
    protected function getRecipients(): array
    {
        $aEditedValues = $this->getManager()->getEditedValues();

        $recipients = [];
        if ($aEditedValues && $aEditedValues['blLinkMobilityMessageToCustomer']) {
            $recipients[] = oxNew(OrderRecipients::class, $this->getItem())->getSmsRecipient();
        }
        if ($aEditedValues  && $aEditedValues['blLinkMobilityMessageToCustom']) {
            $recipients[] = oxNew(Recipient::class, 'number', 'DE');
        }

        return $recipients;
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
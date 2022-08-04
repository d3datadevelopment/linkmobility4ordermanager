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
use OxidEsales\Eshop\Application\Model\Order;

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
        return '';
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
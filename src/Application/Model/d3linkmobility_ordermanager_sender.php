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

use D3\Linkmobility4OXID\Application\Model\Exceptions\noRecipientFoundException;
use D3\Linkmobility4OXID\Application\Model\MessageTypes\Sms;
use D3\Linkmobility4OXID\Application\Model\OrderRecipients;
use D3\LinkmobilityClient\Exceptions\RecipientException;
use D3\LinkmobilityClient\ValueObject\Recipient;
use D3\ModCfg\Application\Model\Exception\d3ParameterNotFoundException;
use D3\Ordermanager\Application\Model\d3ordermanager;
use D3\Ordermanager\Application\Model\d3ordermanager as Manager;
use D3\Ordermanager\Application\Model\Exceptions\emptyMesageException;
use D3\Ordermanager\Application\Model\MessageContentGenerator;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Exception\ArticleException;
use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;

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
     * @throws StandardException
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
     * @throws d3ParameterNotFoundException
     * @throws emptyMesageException
     */
    protected function getMessageBody(): string
    {
        $generator = oxNew(MessageContentGenerator::class, $this->getManager(), $this->getManager()->getCurrentItem());

        if ($this->getManager()->getValue('sLinkMobilityMessageFromSource') == 'cms') {
            return $generator->generateFromCms($this->getManager()->getValue('sLinkMobilityMessageFromContentname'));
        } elseif ($this->getManager()->getValue('sLinkMobilityMessageFromSource') == 'template') {
            $fromTheme = $this->getManager()->getValue('sLinkMobilityMessageFromTheme');
            $generator->setTemplateFrom(
                $fromTheme === 'admin' ? MessageContentGenerator::TEMPLATE_FROM_ADMIN :
                    ($fromTheme === 'module' ? MessageContentGenerator::TEMPLATE_FROM_MODULE :
                        MessageContentGenerator::TEMPLATE_FROM_FRONTEND),
                $this->getManager()->getValue('sLinkMobilityMessageFromModulePath')
            );
            return $generator->generateFromTpl($this->getManager()->getValue('sLinkMobilityMessageFromTemplatename'));
        }

        throw oxNew(emptyMesageException::class);
    }

    /**
     * @return array
     * @throws StandardException
     * @throws d3ParameterNotFoundException
     */
    protected function getRecipients(): array
    {
        $recipients = [];
        if ($this->getManager()->getValue('blLinkMobilityMessageToCustomer')) {
            try {
                $recipients[] = (oxNew(OrderRecipients::class, $this->getItem()))->getSmsRecipient();
            } catch (noRecipientFoundException $e) {
                /** @var string $note */
                $note = Registry::getLang()->translateString('D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE_NORECIPIENT', null, true);
                $this->getManager()->getRemarkHandler()->addNote($note);
            }
        }
        if ($this->getManager()->getValue('blLinkMobilityMessageToCustom') &&
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

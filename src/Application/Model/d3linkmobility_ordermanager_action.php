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

use D3\Ordermanager\Application\Model\Actions\d3ordermanager_action_abstract;
use D3\Ordermanager\Application\Model\d3ordermanager_conf;
use Doctrine\DBAL\DBALException;
use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;

class d3linkmobility_ordermanager_action extends d3ordermanager_action_abstract
{
    public $sTplName = 'd3linkmobility_ordermanager_action.tpl';
    CONST ACTIVE_SWITCH = 'blActionLinkmobility_status';
    public $sTitleIdent = 'D3_LINKMOBILITY_ORDERMANAGER_ACTION';

    CONST SOURCE_TEMPLATE = 'template';
    CONST SOURCE_CMS = 'cms';

    /**
     * @return array
     */
    public final function isAllowedInEditions(): array
    {
        return [
            d3ordermanager_conf::SERIAL_BIT_FREE_EDITION,
            d3ordermanager_conf::SERIAL_BIT_STANDARD_EDITION,
            d3ordermanager_conf::SERIAL_BIT_PREMIUM_EDITION
        ];
    }

    /**
     * @return string
     */
    public function getUnvalidConfigurationMessageIdent() : string
    {
        if ($this->hasRequiredValuesNoSource(false)) {
            return 'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ERR_NOVALIDSOURCE';
        } elseif ($this->hasRequiredValuesTplSource(false)) {
            return 'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ERR_NOVALIDTPL';
        } else {
            return 'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ERR_NOVALIDCMS';
        }
    }

    /**
     * @throws DBALException
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws StandardException
     * @throws d3ShopCompatibilityAdapterException
     * @throws d3_cfg_mod_exception
     */
    public function startAction()
    {
        if (false == $this->isExecutable()) {
            return;
        }

        $this->throwUnvalidConfigurationException();

        startProfile(__METHOD__);

        /** @var Language $oLang */
        $oLang = d3GetModCfgDIC()->get('d3ox.ordermanager.'.Language::class);

        $this->getManager()->getRemarkHandler()->addNote(
            sprintf(
                $oLang->translateString('D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE', null, true),
                $this->getRecipientDescription()
            )
        );

        $this->startExecution();

        stopProfile(__METHOD__);
    }

    /**
     * @return bool
     */
    public function getRecipientDescription(): string
    {
        $aEditedValues = $this->getManager()->getEditedValues();

        $aMailDesc = [];
        $aEditedValues ?
            ($aEditedValues['blSendMailToCustomer'] ? $aMailDesc[] = 'Customer' : '') :
            ($this->getManager()->getValue('blSendMailToCustomer') ? $aMailDesc[] = 'Customer' : '');
        $aEditedValues ?
            ($aEditedValues['blSendMailToOwner'] ? $aMailDesc[] = 'Owner' : '') :
            ($this->getManager()->getValue('blSendMailToOwner') ? $aMailDesc[] = 'Owner' : '');
        $aEditedValues ?
            ($aEditedValues['blSendMailToCustom'] ? $aMailDesc[] = 'Custom: ' . $aEditedValues['sSendMailToCustomAddress'] : '') :
            ($this->getManager()->getValue('blSendMailToCustom') ?
                $aMailDesc[] = 'Custom: ' . $this->getManager()->getValue('sSendMailToCustomAddress') :
                ''
            );
    }

        return implode(', ', $aMailDesc);
    }

    /**
     * @param bool $blExpected
     *
     * @return bool
     */
    protected function hasRequiredValuesTplSource(bool $blExpected): bool
    {
        $source = (string) $this->getManager()->getValue( 'sLinkMobilityMessageFromSource' );

        if (trim($source) !== self::SOURCE_TEMPLATE) {
            return false;
        }

        $template = (string) $this->getManager()->getValue( 'sLinkMobilityMessageFromTemplatename' );
        $theme = (string) $this->getManager()->getValue( 'sLinkMobilityMessageFromTheme' );

        if ($blExpected === true) {
            return (bool) strlen( trim( $template ) ) === true &&
                (bool) strlen( trim( $theme ) ) === true;
        } else {
            return (bool) strlen( trim( $template ) ) === false ||
                (bool) strlen( trim( $theme ) ) === false;
        }
    }

    /**
     * @param bool $blExpected
     *
     * @return bool
     */
    protected function hasRequiredValuesCmsSource(bool $blExpected): bool
    {
        $source = (string) $this->getManager()->getValue( 'sLinkMobilityMessageFromSource' );

        if (trim($source) !== self::SOURCE_CMS) {
            return false;
        }

        /** @var Content $content */
        $content = oxNew(Content::class);
        $contentname = (string) $this->getManager()->getValue( 'sLinkMobilityMessageFromContentname' );

        if ($blExpected === true) {
            return (bool) strlen( trim( $contentname ) ) === true &&
                $content->exists(trim( $contentname )) === true;
        } else {
            return (bool) strlen( trim( $contentname ) ) === false ||
                $content->exists(trim( $contentname )) === false;
        }
    }

    /**
     * @return bool
     * @throws DBALException
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function canExecuteMethod(): bool
    {
        return $this->getManager()->getExecMode();
    }

    /**
     * @return string
     */
    public function getActiveSwitchParameter() : string
    {
        return self::ACTIVE_SWITCH;
    }
}
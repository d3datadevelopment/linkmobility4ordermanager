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

// @codeCoverageIgnoreStart

$sLangName = 'Deutsch';

$aLang = [
    'charset'                                            => 'UTF-8',

    'D3_LINKMOBILITY_ORDERMANAGER_ACTION'                => 'SMS senden (via LINK Mobility)',

    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ToUseLMSettings'    => '',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ToUseLMSettings_DESC' => '',

    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROM1'              => '',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMSUBJECT'        => 'Betreff-Template',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMCMS_SUBJECT'    => 'Der CMS-Titel ist gleichzeitig der Mail-Betreff.',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE'       => 'aus Templatedatei',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE_SOURCE'=> 'Template-Datei',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_ADMIN'      => 'aus <b>Admin</b>-Templateverzeichnis',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_FRONTEND'   => 'aus <b>Frontent</b>-Templateverzeichnis',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_MODULE'     => 'aus <b>Modul</b>-Verzeichnis',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_DESC'       => 'Neben den Templates werden auch die Sprachbausteine je nach Einstellung immer aus dem Admin- bzw. Frontend-Bereich übersetzt. Übertragen Sie daher ggf. die Einträge manuell. Laden Sie ein Template aus einem Modulverzeichnis, werden generell die Frontend-Sprachbausteine verwendet.',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE_DESC'  => 'Geben Sie den vollständigen Templatenamen (inkl. Ordner ab tpl-Ordner und Dateiendung) an',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMCMS'            => 'aus Kundeninformation',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMCMS_SOURCE'     => 'CMS-Eintrag',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROM2'              => 'an',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOCUSTOMER'         => 'Kunde',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOCUSTOMER_DESC'    => 'Der Nachrichtenempfänger wird aus den Kundendaten an den Bestellungen ermittelt. Welche Daten der Bestellung hierfür verwendet werden, setzen Sie in den Einstellungen des LINK Mobility Moduls unter "Erweiterungen -> Module -> Einstell.".',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOOWNER'            => 'Shopbetreiber',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOMAIL'             => 'folgende Mobilfunknummer(n)',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOMAIL_DESC'        => '<p>Geben Sie in dem Eingabefeld eine oder mehrere gültige Mobilfunknummern und deren Herkunftsland in folgendem Format an und aktivieren Sie die Option mit dem Häkchenfeld:</p><code>017112345678@DE; 015212345678@AT</code>',

    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ERR_NOVALIDSOURCE'  => 'keine gültige Inhaltsquelle gesetzt',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ERR_NOVALIDTPL'     => 'keine gültigen Templatedaten gesetzt',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ERR_NOVALIDCMS'     => 'kein gültiger CMS-Eintrag gesetzt',
    'D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ERR_UNDEFINED'      => 'unbekannter Fehler',

    'D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE'                         => 'sende SMS via LinkMobility an %s',
    'D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE_EMPTYMESSAGE'            => 'SMS wurde wegen leerer Nachricht nicht gesendet',
    'D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE_NORECIPIENT'             => 'SMS wurde wegen fehlender Empfänger nicht gesendet',
    'D3_ORDERMANAGER_JOBDESC_SENDLMMESSAGE_RECIPIENTERROR'          => 'SMS Empfänger %1$s (%2$s) konnte nicht verwendet werden',
];

// @codeCoverageIgnoreEnd

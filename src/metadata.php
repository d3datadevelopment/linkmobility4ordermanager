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

use D3\Linkmobility4Ordermanager\Modules\Ordermanager\Application\Model\d3ordermanager_conf_linkmobility;
use D3\Ordermanager\Application\Model\d3ordermanager_conf;

$sMetadataVersion = '2.1';
$sModuleId        = 'd3linkmobility4ordermanager';
$sD3Logo          = '<img src="https://logos.oxidmodule.com/d3logo.svg" alt="(D3)" style="height:1em;width:1em"> ';

/**
 * Module information
 */
$aModule = [
    'id'           => $sModuleId,
    'title'        => $sD3Logo . ' Auftragsmanager-Erweiterung: LINK Mobility Mobile Messaging',
    'description'  => [
        'de'    =>  'Anbindung der LINK Mobility API (Nachrichtenversand per SMS) an den D3 Auftragsmanager',
        'en'    =>  '',
    ],
    'version'      => '1.0.0.0',
    'author'       => 'D&sup3; Data Development (Inh.: Thomas Dartsch)',
    'email'        => 'support@shopmodule.com',
    'url'          => 'https://www.oxidmodule.com/',
    'extend'       => [
        d3ordermanager_conf::class => d3ordermanager_conf_linkmobility::class,
    ],
    'controllers'  => [],
    'templates'    => [
        'd3linkmobility_ordermanager_action.tpl'    => 'd3/linkmobility4ordermanager/Application/views/admin/tpl/d3linkmobility_ordermanager_action.tpl',
    ],
    'events'       => [],
    'blocks'       => [],
    'settings'     => [],
];

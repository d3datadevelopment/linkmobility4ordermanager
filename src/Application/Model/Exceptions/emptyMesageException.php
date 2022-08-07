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

namespace D3\Linkmobility4Ordermanager\Application\Model\Exceptions;

use OxidEsales\Eshop\Core\Exception\StandardException;

class emptyMesageException extends StandardException
{
    public function __construct($sMessage = "not set", $iCode = 0, \Exception $previous = null)
    {
        $sMessage = 'empty message content in task '.$sMessage;
        parent::__construct($sMessage, $iCode, $previous);
    }
}

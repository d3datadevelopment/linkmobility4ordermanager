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

namespace D3\Linkmobility4Ordermanager\Modules\Ordermanager\Application\Model;

use D3\Linkmobility4Ordermanager\Application\Model\Actions\d3linkmobility_ordermanager_action;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;

class d3ordermanager_conf_linkmobility extends d3ordermanager_conf_linkmobility_parent
{
    /**
     * @param array $actionIdList
     *
     * @return array
     */
    public function addModuleDependendActions(array $actionIdList): array
    {
        $actionIdList = parent::addModuleDependendActions($actionIdList);

        if ($this->hasLinkMobilityPlugin()) {
            $actionIdList = $this->addModuleDependendItem(
                $actionIdList,
                'D3_ORDERMANAGER_ACTION_INFO',
                'linkmobility',
                d3linkmobility_ordermanager_action::class
            );
        }

        return $actionIdList;
    }

    /**
     * @return bool
     */
    protected function hasLinkMobilityPlugin(): bool
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $moduleStateService = $container->get(ModuleStateServiceInterface::class);

        return $moduleStateService->isActive('d3linkmobility', Registry::getConfig()->getShopId());
    }
}
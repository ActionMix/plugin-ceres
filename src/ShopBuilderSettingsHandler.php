<?php

namespace Ceres;

use Plenty\Modules\ShopBuilder\Helper\MappableSettingsHandler;

class ShopBuilderSettingsHandler extends MappableSettingsHandler
{
    protected $mappings = [
        'checkoutCategory'      => 'IO.routing.category_checkout',
        'checkoutEnableRoute'   => 'IO.routing.enabled_routes'
    ];

    protected $casts = [
        'category_checkout'     => 'int'
    ];

    public function readEnabledRoutesCheckout($pluginConfigValue)
    {
        $enabledRoutes = explode(", ", $pluginConfigValue);
        return in_array("checkout", $enabledRoutes) || $pluginConfigValue === "all";
    }

    public function writeEnabledRoutesCheckout($enableCheckoutRoute, $currentValue)
    {
        return $this->setEnabledRoute("checkout", $enableCheckoutRoute, $currentValue);
    }

    private function setEnabledRoute($key, $value, $currentValue)
    {
        $enabledRoutes = explode(", ", $currentValue );

        if ( $enabledRoutes === "all" )
        {
            $enabledRoutes = [];
        }

        if( $value && !in_array($key, $enabledRoutes) )
        {
            $enabledRoutes[] = $key;
        }
        else if ( !$value && in_array($key, $enabledRoutes) )
        {
            array_splice(
                $enabledRoutes,
                array_search($key, $enabledRoutes),
                1
            );
        }

        return implode(", ", $enabledRoutes);
    }
}
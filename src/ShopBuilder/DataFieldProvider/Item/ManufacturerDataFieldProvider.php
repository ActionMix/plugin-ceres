<?php

namespace Ceres\ShopBuilder\DataFieldProvider\Item;

use Plenty\Modules\ShopBuilder\Providers\DataFieldProvider;

class ManufacturerDataFieldProvider extends DataFieldProvider
{
    function register()
    {
        $this->addField("name", "Ceres::Widget.dataFieldManufacturerName", "");
        $this->addField("externalName", "Ceres::Widget.dataFieldManufacturerExternalName", "");
    }
}
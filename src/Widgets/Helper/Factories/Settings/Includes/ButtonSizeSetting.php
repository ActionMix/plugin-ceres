<?php


namespace Ceres\Widgets\Helper\Factories\Settings\Includes;


use Ceres\Widgets\Helper\Factories\Settings\SelectSettingFactory;
use Ceres\Widgets\Helper\Factories\Settings\ValueListFactory;

class ButtonSizeSetting extends SelectSettingFactory
{
    public function __construct()
    {
        parent::__construct();

        $this->withName("Widget.widgetButtonSizeLabel")
            ->withTooltip("Widget.widgetButtonSizeTooltip")
            ->withDefaultValue("")
            ->withListBoxValues(
                ValueListFactory::make()
                    ->addEntry("btn-sm", "Widget.widgetButtonSizeSm")
                    ->addEntry("", "Widget.widgetButtonSizeNormal")
                    ->addEntry("btn-lg", "Widget.widgetButtonSizeLg")
                    ->toArray()
            );
    }

}

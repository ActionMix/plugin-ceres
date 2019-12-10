<?php

namespace Ceres\Widgets\Common;

use Ceres\Widgets\Helper\BaseWidget;
use Ceres\Widgets\Helper\Factories\Settings\ValueListFactory;
use Ceres\Widgets\Helper\Factories\WidgetSettingsFactory;
use Ceres\Widgets\Helper\WidgetCategories;
use Ceres\Widgets\Helper\Factories\WidgetDataFactory;
use Ceres\Widgets\Helper\WidgetTypes;

class BackgroundWidget extends BaseWidget
{
    protected $template = "Ceres::Widgets.Common.BackgroundWidget";

    public function getData()
    {
        return WidgetDataFactory::make("Ceres::BackgroundWidget")
            ->withLabel("Widget.backgroundLabel")
            ->withPreviewImageUrl("/images/widgets/background.svg")
            ->withType(WidgetTypes::STATIC)
            ->withCategory(WidgetCategories::IMAGE)
            ->withPosition(700)
            ->toArray();
    }

    public function getSettings()
    {
        /** @var WidgetSettingsFactory $settings */
        $settings = pluginApp(WidgetSettingsFactory::class);

        $settings->createCustomClass();

        $settings->createSlider("opacity")
            ->withDefaultValue(100)
            ->withName("Widget.backgroundOpacityLabel")
            ->withOption("inputInterval", 1)
            ->withOption("inputMax", 100);

        $settings->createCheckbox("fullWidth")
            ->withDefaultValue(true)
            ->withName("Widget.backgroundFullWidthLabel")
            ->withTooltip("Widget.backgroundFullWidthTooltip");
            
        $settings->createCheckbox("backgroundFixed")
            ->withDefaultValue(false)
            ->withName("Widget.backgroundFixedLabel")
            ->withTooltip("Widget.backgroundFixedTooltip");

        $settings->createCheckbox("backgroundRepeat")
            ->withCondition("backgroundSize !== 'bg-cover'")
            ->withDefaultValue(false)
            ->withName("Widget.backgroundRepeatLabel")
            ->withTooltip("Widget.backgroundRepeatTooltip");

        $settings->createSelect("backgroundSize")
            ->withDefaultValue("cover")
            ->withName("Widget.backgroundSizeLabel")
            ->withTooltip("Widget.backgroundSizeTooltip")
            ->withListBoxValues(
                ValueListFactory::make()
                    ->addEntry("bg-cover", "Widget.backgroundSizeCover")
                    ->addEntry("bg-contain", "Widget.backgroundSizeContain")
                    ->addEntry("bg-auto", "Widget.backgroundSizeAuto")
                    ->toArray()
        );

        $settings->createHeight();

        $settings->createSpacing(true, true);

        return $settings->toArray();
    }

    protected function getTemplateData($widgetSettings, $isPreview)
    {
        $stylingClasses = "";

        if ( array_key_exists("backgroundFixed", $widgetSettings) && $widgetSettings["backgroundFixed"]["mobile"] == false )
        {
            $stylingClasses .= "bg-scroll ";
        }

        if ( array_key_exists("backgroundRepeat", $widgetSettings) && $widgetSettings["backgroundRepeat"]["mobile"] == true )
        {
            $stylingClasses .= "bg-repeat ";
        }

        if ( array_key_exists("backgroundSize", $widgetSettings) && $widgetSettings["backgroundSize"]["mobile"] )
        {
            $stylingClasses .= $widgetSettings["backgroundSize"]["mobile"];
        }

        return [
            'stylingClasses'  => $stylingClasses
        ];
    }
}

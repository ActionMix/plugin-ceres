<?php

namespace Ceres\Widgets\Presets\Legal;

use Ceres\Widgets\Helper\Factories\PresetWidgetFactory;
use Ceres\Widgets\Helper\PresetHelper;
use Plenty\Modules\ShopBuilder\Contracts\ContentPreset;

class DefaultCancellationFormPreset implements ContentPreset
{
    /** @var PresetHelper $preset */
    private $preset;

    public function getWidgets()
    {
        $this->preset = pluginApp(PresetHelper::class);

        $this->createHeadline();
        $this->createLegalTextsWidget();
        $this->createSeparatorWidget();
        $this->createPrintButtonWidget();

        return $this->preset->toArray();
    }

    private function createHeadline()
    {
        $text = '';
        $text .= '{% autoescape false %}';
        $text .= '<h1 class="print-header">{{ trans("Ceres::Template.cancellationForm", {"hyphen": "&shy;"}) }}</h1>';
        $text .= '{% endautoescape %}';

        $this->preset->createWidget('Ceres::CodeWidget')
                     ->withSetting("text", $text)
                     ->withSetting("appearance", "none")
                     ->withSetting("customPadding", true)
                     ->withSetting("padding.top.value", 3)
                     ->withSetting("padding.top.unit", null)
                     ->withSetting("padding.bottom.value", 0)
                     ->withSetting("padding.bottom.unit", null)
                     ->withSetting("customMargin", true)
                     ->withSetting("margin.bottom.value", 0)
                     ->withSetting("margin.bottom.unit", null);
    
        $this->preset->createWidget("Ceres::SeparatorWidget")
                    ->withSetting("customMargin", true)
                     ->withSetting("margin.top.value", 5)
                     ->withSetting("margin.top.unit", null)
                     ->withSetting("margin.bottom.value", 5)
                     ->withSetting("margin.bottom.unit", null);
    }

    private function createLegalTextsWidget()
    {
        $this->preset->createWidget("Ceres::LegalTextsWidget")
            ->withSetting("type", "cancellationForm")
            ->withSetting("spacing.customMargin", true)
            ->withSetting("spacing.margin.bottom.value", 0)
            ->withSetting("spacing.margin.bottom.unit", null);
    }

    private function createSeparatorWidget()
    {
        $this->preset->createWidget("Ceres::SeparatorWidget")
            ->withSetting("customMargin", true)
            ->withSetting("margin.top.value", 5)
            ->withSetting("margin.top.unit", null)
            ->withSetting("margin.bottom.value", 5)
            ->withSetting("margin.bottom.unit", null);
    }

    private function createPrintButtonWidget()
    {
        $this->preset->createWidget("Ceres::PrintButtonWidget")
            ->withSetting("customClass", "float-right")
            ->withSetting("buttonSize", "md");
    }
}

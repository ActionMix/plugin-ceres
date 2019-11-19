<?php

namespace Ceres\Widgets\Form;

use Ceres\Widgets\Helper\BaseWidget;
use Ceres\Widgets\Helper\Factories\WidgetSettingsFactory;
use Ceres\Widgets\Helper\WidgetCategories;
use Ceres\Widgets\Helper\WidgetDataFactory;
use Ceres\Widgets\Helper\WidgetTypes;

class TextAreaWidget extends BaseWidget
{
    protected $template = "Ceres::Widgets.Form.TextAreaWidget";

    public function getData()
    {
        return WidgetDataFactory::make("Ceres::SelectionWidget")
            ->withLabel("Widget.selectionLabel")
            ->withPreviewImageUrl("/images/widgets/input-select.svg")
            ->withType(WidgetTypes::FORM)
            ->withCategory(WidgetCategories::FORM)
            ->withPosition(500)
            ->toArray();
    }

    public function getSettings()
    {
        /** @var WidgetSettingsFactory $settingsFactory */
        $settingsFactory = pluginApp(WidgetSettingsFactory::class);

        $settingsFactory->createCustomClass();

        $settingsFactory->createText("key")
            ->withDefaultValue("")
            ->withName("Widget.mailFormFieldKeyLabel")
            ->withTooltip("Widget.mailFormFieldKeyTooltip");

        $settingsFactory->createText("label")
            ->withDefaultValue("")
            ->withName("Widget.mailFormFieldLabelLabel")
            ->withTooltip("Widget.mailFormFieldLabelTooltip");

        $settingsFactory->createNumber("rows")
            ->withDefaultValue(15)
            ->withName("Widget.textAreaRowsLabel")
            ->withTooltip("Widget.textAreaRowsTooltip");

        $settingsFactory->createCheckbox("fixedHeight")
            ->withDefaultValue(false)
            ->withName("Widget.mailFormFieldIsRequiredLabel")
            ->withTooltip("Widget.mailFormFieldIsRequiredTooltip");

        $settingsFactory->createSpacing(false, true);

        return $settingsFactory->toArray();
    }
}

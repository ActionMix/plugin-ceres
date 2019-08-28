<?php

namespace Ceres\Widgets\Presets;

use Ceres\Config\CeresConfig;
use Ceres\Widgets\Helper\Factories\PresetWidgetFactory;
use Ceres\Widgets\Helper\PresetHelper;
use Plenty\Modules\ShopBuilder\Contracts\ContentPreset;

class DefaultOrderConfirmationPreset implements ContentPreset
{
    /** @var PresetHelper */
    private $preset;
    
    /** @var CeresConfig */
    private $ceresConfig;
    
    /** @var PresetWidgetFactory */
    private $twoColumnWidget;
    
    /** @var PresetWidgetFactory */
    private $threeColumnWidget;
    
    /** @var PresetWidgetFactory */
    private $fourColumnWidget;
    
    public function getWidgets()
    {
        $this->preset = pluginApp(PresetHelper::class);
        $this->ceresConfig = pluginApp(CeresConfig::class);
        
        $this->createHeadline();
        
        $this->createTwoColumnWidget();
        
        $this->createOrderDataWidget();
        $this->createThreeColumnWidget();

        // $this->createTrackingLinkWidget();
        $this->createOrderDocumentsWidget();
        // $this->createRetourLinkWidget();

        $this->createPurchasedItemsWidget();
        $this->createOrderTotalsWidget();

        $this->createSeparatorWidget();

        $this->createFourColumnWidget();
        // $this->createHomeLinkWidget();
        // $this->createMyAccountLinkWidget();
        
        return $this->preset->toArray();
    }
    
    private function createHeadline()
    {
        $this->preset->createWidget("Ceres::InlineTextWidget")
                     ->withSetting("text", "<h1 class=\"h2\">{{ trans(\"Ceres::Template.orderConfirmationThanks\") }}</h1>")
                     ->withSetting("appearance", "none")
                     ->withSetting("spacing.customPadding", true)
                     ->withSetting("spacing.padding.top.value", 0)
                     ->withSetting("spacing.padding.top.unit", null)
                     ->withSetting("spacing.padding.bottom.value", 0)
                     ->withSetting("spacing.padding.bottom.unit", null)
                     ->withSetting("spacing.padding.left.value", 0)
                     ->withSetting("spacing.padding.left.unit", null)
                     ->withSetting("spacing.customMargin", true)
                     ->withSetting("spacing.margin.bottom.value", 0)
                     ->withSetting("spacing.margin.bottom.unit", null);

        $this->preset->createWidget("Ceres::InlineTextWidget")
                     ->withSetting("text", "<p>{{ trans(\"Ceres::Template.orderConfirmationWillBeProcessed\") }}</p>")
                     ->withSetting("appearance", "none")
                     ->withSetting("spacing.customMargin", true)
                     ->withSetting("spacing.margin.bottom.value", 5)
                     ->withSetting("spacing.margin.bottom.unit", null)
                     ->withSetting("spacing.customPadding", true)
                     ->withSetting("spacing.padding.top.value", 0)
                     ->withSetting("spacing.padding.top.unit", null)
                     ->withSetting("spacing.padding.bottom.value", 0)
                     ->withSetting("spacing.padding.bottom.unit", null)
                     ->withSetting("spacing.padding.left.value", 0)
                     ->withSetting("spacing.padding.left.unit", null)
                     ->withSetting("spacing.padding.right.value", 0)
                     ->withSetting("spacing.padding.right.unit", null);
    }
    
    private function createOrderDataWidget()
    {
        $this->twoColumnWidget->createChild("first", "Ceres::OrderDataWidget")
                              ->withSetting("addressFields", ["title", "contactPerson", "name1", "name2", "name3", "name4", "address1", "address2", "address3", "address4", "postalCode", "town", "country"]);
    }

    private function createTrackingLinkWidget()
    {
        $this->threeColumnWidget->createChild("first", "Ceres::LinkWidget");
    }

    private function createOrderDocumentsWidget()
    {
        $this->threeColumnWidget->createChild("second", "Ceres::OrderDocumentsWidget")
                                ->withSetting("customClass","")
                                ->withSetting("spacing.customMargin", true)
                                ->withSetting("spacing.margin.top.value", 3)
                                ->withSetting("spacing.margin.top.unit", null);
    }

    private function createRetourLinkWidget()
    {
        $this->threeColumnWidget->createChild("third", "Ceres::LinkWidget");
    }

    private function createPurchasedItemsWidget()
    {
        $this->twoColumnWidget->createChild("second", "Ceres::PurchasedItemsWidget")
                              ->withSetting("customClass","")
                              ->withSetting("spacing.customMargin", true)
                              ->withSetting("spacing.margin.bottom.value", 30)
                              ->withSetting("spacing.margin.bottom.unit", "px");
    }

    private function createOrderTotalsWidget()
    {
        $this->twoColumnWidget->createChild("second", "Ceres::OrderTotalsWidget")
                              ->withSetting("visibleFields", ["orderValueNet", "orderValueGross", "rebate", "shippingCostsNet", "shippingCostsGross", "totalSumNet", "promotionCoupon", "vats", "totalSumGross", "salesCoupon", "openAmount"]);
    }

    private function createSeparatorWidget()
    {
        $this->preset->createWidget("Ceres::SeparatorWidget")
                     ->withSetting("customClass","");
    }

    private function createHomeLinkWidget()
    {
        $this->fourColumnWidget->createChild("second", "Ceres::LinkWidget");
    }

    private function createMyAccountLinkWidget()
    {
        $this->fourColumnWidget->createChild("third", "Ceres::LinkWidget");
    }

    private function createTwoColumnWidget()
    {
        $this->twoColumnWidget = $this->preset->createWidget("Ceres::TwoColumnWidget")
                                              ->withSetting("layout", "oneToOne");
    }
    
    private function createThreeColumnWidget()
    {
        $this->threeColumnWidget = $this->twoColumnWidget->createChild("first", "Ceres::ThreeColumnWidget")
                                                         ->withSetting("layout", "oneToOneToOne");
    }

    private function createFourColumnWidget()
    {
        $this->fourColumnWidget = $this->preset->createWidget("Ceres::FourColumnWidget");
    }
}
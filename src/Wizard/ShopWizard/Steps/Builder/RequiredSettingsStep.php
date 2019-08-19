<?php
/**
 * Created by PhpStorm.
 * User: Victor Albulescu
 * Date: 30/05/2019
 * Time: 12:56
 */

namespace Ceres\Wizard\ShopWizard\Steps\Builder;

use Ceres\Wizard\ShopWizard\Services\DefaultSettingsService;


class RequiredSettingsStep extends Step
{
    /**
     * @return array
     */
    public function generateStep(): array
    {

        $shopWizardService = pluginApp(DefaultSettingsService::class);

        $hasShippingMethod = $shopWizardService->hasShippingMethods();
        $hasShippingProfile = $shopWizardService->hasShippingProfiles();
        $hasPaymentMethod = $shopWizardService->hasPaymentMethods();
        $hasShippingCountry = $shopWizardService->hasShippingCountries();
        $hasLocation = $shopWizardService->hasLocations();


        return [
            "title" => "Wizard.reqSettings",
            "description" => "Wizard.reqSettingsDescription",
            "condition" => !$this->hasRequiredSettings(),
            "validationClass" => "Ceres\Wizard\ShopWizard\Validators\RequiredSettingsDataValidator",
            "sections" => [
                $this->generateSection("shippingMethod", $hasShippingMethod, "/system/assistants/overview/integration/DHLShipping_settingsCombination"),
                $this->generateSection("shippingProfile", $hasShippingProfile, "/system/assistants/overview/integration/DHLShipping_settingsCombination"),
                $this->generateSection("paymentMethod", $hasPaymentMethod, "/system/assistants/overview/payment/paypal-configuration-assistant"),
                $this->generateSection("deliveryCountry", $hasShippingCountry, "/system/assistants/overview/integration/fulfillment-shipping-profile-wizard"),
                $this->generateSection("location", $hasLocation, "/system/assistants/overview/basic-setup/accounting-location-assistant")

            ]
        ];
    }

    /**
     * @param $name
     * @param $condition
     * @param $url
     *
     * @return array
     */
    private function generateSection($name, $condition, $url): array
    {
        return [
            "title" => "Wizard." . $name,
            "description" => "Wizard." . $name . "Description",
            "condition" => !$condition,
            "form" => [
                $name . "Assistant" => [
                    "type" => "button",

                    "options" => [
                        "name" => "Wizard.". $name ."Assistant",
                        "link" => [
                            "fromAction" => false,
                            "newWindow" => false,
                            "url" => $url
                        ]
                    ]
                ]
            ]
        ];
    }
}
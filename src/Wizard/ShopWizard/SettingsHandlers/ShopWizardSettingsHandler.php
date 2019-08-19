<?php
/**
 * Created by PhpStorm.
 * User: Victor Albulescu
 * Date: 03/06/2019
 * Time: 14:01
 */

namespace Ceres\Wizard\ShopWizard\SettingsHandlers;

use Ceres\Wizard\ShopWizard\Helpers\LanguagesHelper;
use Ceres\Wizard\ShopWizard\Models\ShopWizardPreviewConfiguration;
use Ceres\Wizard\ShopWizard\Repositories\ShopWizardConfigRepository;
use Ceres\Wizard\ShopWizard\Services\MappingService;
use Ceres\Wizard\ShopWizard\Services\SettingsHandlerService;
use Plenty\Modules\ContentCache\Contracts\ContentCacheInvalidationRepositoryContract;
use Plenty\Modules\ContentCache\Contracts\ContentCacheSettingsRepositoryContract;
use Plenty\Modules\Order\Currency\Contracts\CurrencyRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Plugin\Contracts\ConfigurationRepositoryContract;
use Plenty\Modules\Plugin\PluginSet\Contracts\PluginSetRepositoryContract;
use Plenty\Modules\Plugin\PluginSet\Models\PluginSetEntry;
use Plenty\Modules\System\Contracts\WebstoreConfigurationRepositoryContract;
use Plenty\Modules\Webshop\Seo\Contracts\RobotsRepositoryContract;
use Plenty\Modules\Webshop\Seo\Contracts\SitemapConfigurationRepositoryContract;
use Plenty\Modules\Wizard\Contracts\WizardSettingsHandler;


class ShopWizardSettingsHandler implements WizardSettingsHandler
{
    /**
     * @var CountryRepositoryContract
     */
    private $countryRepository;

    private $currencyRepository;

    public function __construct(CountryRepositoryContract $countryRepository, CurrencyRepositoryContract $currencyRepositoryContract)
    {
        $this->countryRepository = $countryRepository;
        $this->currencyRepository = $currencyRepositoryContract;
    }

    public function handle(array $parameters)
    {
        $data = $parameters['data'];
        $optionId = $parameters['optionId'];

        try {
            $webstoreConfig = pluginApp(WebstoreConfigurationRepositoryContract::class);
            $settingsHandlerService = pluginApp(SettingsHandlerService::class);

            list($webstore,$pluginSet) = explode(".", $optionId);

            list($webstorePrefix, $webstoreId) = explode('_', $webstore);
            list($pluginSetPrefix, $pluginSetId) = explode('_', $pluginSet);

            if (empty($webstoreId) && !empty($data['client'])) {
                $webstoreId = $data['client'];
            }

            if (empty($pluginSetId) && $data['pluginSet']!== false) {
                $pluginSetId = $data['pluginSet'];
            }

            //we need to create list of active languages that will be saved into plugin config and system settings

            $activeLanguagesList = count($data['languages_activeLanguages']) ?
                implode(", ", $data['languages_activeLanguages']):
                "";

            if ($webstoreId !=='preview') {

                $plentyId = $settingsHandlerService->getStoreIdentifier($webstoreId);
                $shippingCountryList = [];
                $deliveryCountries = $this->countryRepository->getActiveCountriesList();
                $currencies = $this->currencyRepository->getCurrencyList();
                $currenciesList = [];

                //create default country list
                if (count($deliveryCountries)) {
                    foreach ($deliveryCountries as $country) {
                        $countryData = $country->toArray();
                        $key = 'defSettings_deliveryCountry_' . $countryData['lang'];

                        if(!empty($data[$key])) {
                            $shippingCountryList[$countryData['lang']] = $countryData['id'];
                        }
                    }
                }

                //create default currencies list
                if (count($currencies)) {
                    $languages = LanguagesHelper::getTranslatedLanguages();
                    foreach ($languages as $langCode => $language) {
                        $key = 'currencies_defaultCurrency_' . $langCode;

                        if (!empty($data[$key])) {
                            $currenciesList[$langCode] = $data[$key];
                        }
                    }
                }

                $mappingService = pluginApp(MappingService::class);

                $shippingData = [
                    "defaultShippingCountryList" => $shippingCountryList
                ];

                $currenciesData = [
                    "defaultCurrencyList" => $currenciesList
                ];
                $globalData = $mappingService->processGlobalMappingData($data, "store");

                $intermediarBrowserLanguage = $globalData['browserLanguage'];
                $globalData['browserLanguage'] = [
                    'other' => $intermediarBrowserLanguage
                ];
                foreach ($data as $dataKey => $dataValue){
                    if (strpos($dataKey, "languages_browserLang_") !== false) {
                        $key = end(explode("_", $dataKey));
                        $globalData['browserLanguage'][$key] = $dataValue;
                    }
                }

                $webstoreData = array_merge($shippingData, $currenciesData, $globalData);

                if (!empty($activeLanguagesList)) {
                    $webstoreData['languageList'] = $activeLanguagesList;
                }

                $webstoreConfig->updateByPlentyId($webstoreData, $plentyId);

                // we save robotsTxt
                if (!empty($data["seo_robotsTxt"])) {
                    $robotsRepo = pluginApp(RobotsRepositoryContract::class);
                    $robotsRepo->updateByWebstoreId($webstoreId, $data["seo_robotsTxt"]);

                }

                //save sitemap xml

                if (isset($data['seo_siteMapConfig'])) {
                    $siteMapConfig = [
                      "contentCategory" => 0,
                      "itemCategory" => 0,
                      "item" => 0,
                      "blog" => 0
                    ];

                    foreach($siteMapConfig as $siteMapKey => $siteMapValue) {
                        if (in_array($siteMapKey, $data['seo_siteMapConfig'])) {
                            $siteMapConfig[$siteMapKey] = 1;
                        }
                    }
                    $siteMapRepo = pluginApp(SitemapConfigurationRepositoryContract::class);
                    $siteMapRepo->updateByWebstoreId($webstoreId, $siteMapConfig);
                }

                //we handle settings for shopping booster

                if (isset($data["performance_shopBooster"])) {
                    $cacheRepo = pluginApp(ContentCacheSettingsRepositoryContract::class);
                    $cacheRepo->saveSettings($plentyId, (bool) $data["performance_shopBooster"]);
                }
            }

            $configRepo = pluginApp(ConfigurationRepositoryContract::class);
            $pluginSetRepo = pluginApp(PluginSetRepositoryContract::class);
            $pluginSets = $pluginSetRepo->list();
            $pluginId = '';

            if (count($pluginSets)) {
                foreach($pluginSets as $pluginSet) {
                    foreach ($pluginSet->pluginSetEntries as $pluginSetEntry) {
                        if ($pluginSetEntry instanceof PluginSetEntry && $pluginSetEntry->plugin->name === 'Ceres' && $pluginSetEntry->pluginSetId == $pluginSetId) {
                            $pluginId = $pluginSetEntry->pluginId;
                        }
                    }
                }
            }

            $mappingService = pluginApp(MappingService::class);
            $pluginData = $mappingService->processPluginMappingData($data, "store");

            if (count($pluginData)) {
                $configData = [];

                foreach ($pluginData as $itemKey => $itemVal) {
                    $configData[] = [
                        'key' => $itemKey,
                        'value' => $itemVal
                    ];
                }

                $configRepo->saveConfiguration($pluginId, $configData, $pluginSetId);

                // we set the preview config entry

                $previewConfigRepo = pluginApp(ShopWizardConfigRepository::class);
                $previewConfData = [
                    "pluginSetId" => $pluginSetId,
                    "deleted" => false
                ];

                $previewConf = $previewConfigRepo->getConfig($pluginSetId);
                if ($previewConf instanceof ShopWizardPreviewConfiguration) {
                    $previewConfigRepo->updateConfig($pluginSetId, $previewConfData);
                } else {
                    $previewConfigRepo->createConfig($previewConfData);
                }


            }

            //invalidate caching
            $cacheInvalidRepo = pluginApp(ContentCacheInvalidationRepositoryContract::class);
            $cacheInvalidRepo->invalidateAll();

        } catch (\Exception $exception) {

            return false;
        }

        return true;
    }
}
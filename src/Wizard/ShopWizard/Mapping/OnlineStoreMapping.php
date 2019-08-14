<?php
/**
 * Created by PhpStorm.
 * User: Victor Albulescu
 * Date: 09/08/2019
 * Time: 14:31
 */

namespace Ceres\Wizard\ShopWizard\Mapping;


class OnlineStoreMapping
{
    public static $fieldsMapped = [
        "onlineStore_itemBundles" => [
            "field" => "dontSplitItemBundle",
            "type" => "integer",
            "global" => true,
            "optional" => false,
        ],
        "onlineStore_storeName" => [
            "field" => "header.company_name",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_storeFavicon" => [
            "field" => "global.favicon",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_categoryTypes" => [
            "field" => "header.show_category_types",
            "type" => "concatenated",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_toTopButton" => [
            "field" => "footer.to_top_button",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_confirmationLinkLoginRedirect" => [
            "field" => "my_account.confirmation_link_login_redirect",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_confirmationLinkExpiration" => [
            "field" => "my_account.confirmation_link_expiration",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_globalUserDataHashMaxAge" => [
            "field" => "global.user_data_hash_max_age",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_daysOfReturn" => [
            "field" => "my_account.order_return_days",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_statusReturn" => [
            "field" => "my_account.order_return_initial_status",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
        "onlineStore_enableCalisto" => [
            "field" => "global.enableOldUrlPattern",
            "type" => "string",
            "global" => false,
            "optional" => false,
        ],
    ];

    public static function getFieldsMapped()
    {
        return self::$fieldsMapped;
    }
}
// item script extends category script
import "./category";

import AddToWishList from "./app/components/item/AddToWishList.vue";
Vue.component("add-to-wish-list", AddToWishList);
import GraduatedPrices from "./app/components/item/GraduatedPrices.vue";
Vue.component("graduated-prices", GraduatedPrices);
import ItemDataTable from "./app/components/item/ItemDataTable.vue";
Vue.component("item-data-table", ItemDataTable);
import "./app/components/item/ItemImageCarousel";
import "./app/components/item/OrderProperties";
import "./app/components/item/OrderPropertyList";
import "./app/components/item/SingleItem";

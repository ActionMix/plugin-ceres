import Vue from "vue";
import { mapState, mapMutations, mapActions } from "vuex";
import { navigateTo } from "../../services/UrlService";
import TranslationService from "../../services/TranslationService";

Vue.component("order-return", {

    props: {
        template:
        {
            type: String,
            default: "#vue-order-return"
        },
        initOrderData:
        {
            type: Object,
            required: true
        }
    },

    data()
    {
        return {
            isLoading: false
        };
    },

    created()
    {
        this.$store.commit("setOrderReturnData", this.initOrderData);
    },

    computed: mapState({
        orderData: state => state.orderReturn.orderData,
        orderReturnItems: state => state.orderReturn.orderReturnItems,
        isDisabled: state => state.orderReturn.orderReturnItems.length === 0
    }),

    methods:
    {
        showConfirmationModal()
        {
            $(this.$refs.orderReturnConfirmation).modal("show");
        },

        sendReturnItems()
        {
            this.isLoading = true;

            this.sendOrderReturn().then(
                response =>
                {
                    $(this.$refs.orderReturnConfirmation).modal("hide");
                    navigateTo(window.location.origin);

                    NotificationService.success(
                        TranslationService.translate("Ceres::Template.returnConfirmationInfo")
                    ).closeAfter(3000);
                },
                error =>
                {
                    this.isLoading = false;
                    $(this.$refs.orderReturnConfirmation).modal("hide");
                });
        },

        selectAllItems()
        {
            vueEventHub.$emit("select-all-items");
        },

        ...mapMutations([
            "updateOrderReturnNote"
        ]),

        ...mapActions([
            "sendOrderReturn"
        ])
    }
});

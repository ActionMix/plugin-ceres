import TranslationService from "../../services/TranslationService";
import ValidationService from "../../services/ValidationService";
import Vue from "vue";

const ApiService          = require("../../services/ApiService");
const NotificationService = require("../../services/NotificationService");

Vue.component("newsletter-unsubscribe-input", {
    props: {
        template:
        {
            type: String,
            default: "#vue-newsletter-unsubscribe-input"
        }
    },

    data()
    {
        return {
            email: "",
            isDisabled: false
        };
    },

    methods: {
        validateData()
        {
            this.isDisabled = true;

            ValidationService.validate($("#newsletter-unsubscribe-input-form_" + this._uid))
                .done(() =>
                {
                    this.save();
                })
                .fail(invalidFields =>
                {
                    ValidationService.markInvalidFields(invalidFields, "error");

                    this.isDisabled = false;
                });
        },
        save()
        {
            ApiService.del("/rest/io/customer/newsletter/" + this.email)
                .done(() =>
                {
                    NotificationService.success(
                        TranslationService.translate("Ceres::Template.newsletterOptOutSuccessMessage")
                    ).closeAfter(3000);
                    this.resetInputs();
                })
                .fail(() =>
                {
                    NotificationService.error(
                        TranslationService.translate("Ceres::Template.newsletterOptOutErrorMessage")
                    ).closeAfter(5000);
                })
                .always(() =>
                {
                    this.isDisabled = false;
                });
        },
        resetInputs()
        {
            this.email = "";
        }
    }
});

import TranslationService from "services/TranslationService";
import ValidationService from "services/ValidationService";
const ApiService          = require("services/ApiService");
const NotificationService = require("services/NotificationService");

Vue.component("newsletter-input", {
    props: {
        template:
        {
            type: String,
            default: "#vue-newsletter-input"
        },
        title:
        {
            type: String,
            default: ""
        },
        subTitle:
        {
            type: String,
            default: ""
        },
        showNameInputs:
        {
            type: Boolean,
            default: false
        },
        showPrivacyPolicyCheckbox:
        {
            type: Boolean,
            default: true
        }
    },

    data()
    {
        return {
            firstName: "",
            lastName: "",
            email: "",
            isDisabled: false,
            privacyPolicyValue: false
        };
    },

    created()
    {
        this.$options.template = this.template;
    },

    mounted()
    {
        this.$nextTick(() =>
        {
            this.inputFields = $(".container").find(".input-unit");
        });
    },

    methods: {
        validateData()
        {

            ValidationService.validate($("#newsletter-input-form"))
                .done(() =>
                {
                    this.save();
                })
                .fail(invalidFields =>
                {
                    ValidationService.markInvalidFields(invalidFields, "error");
                });
        },
        save()
        {
            ApiService.post("/rest/io/customer/newsletter", {email: this.email, firstName: this.firstName, lastName: this.lastName})
                .done(() =>
                {
                    NotificationService.success(
                        TranslationService.translate("Ceres::Template.newsletterSuccessMessage")
                    ).closeAfter(3000);
                    this.resetInputs();
                })
                .fail(() =>
                {
                    NotificationService.error(
                        TranslationService.translate("Ceres::Template.newsletterErrorMessage")
                    ).closeAfter(5000);
                });
        },
        resetInputs()
        {
            this.firstName = "";
            this.lastName = "";
            this.email = "";
            this.privacyPolicyValue = false;
        }
    }
});

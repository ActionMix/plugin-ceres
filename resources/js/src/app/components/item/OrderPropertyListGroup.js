Vue.component("order-property-list-group", {

    props:
    {
        template:
        {
            type: String,
            default: "#vue-order-property-list-group"
        },
        propertyGroup: Object
    },

    computed:
    {
        isShownOnItemPageCount()
        {
            const properties = this.propertyGroup.properties.filter(property => property.property.isShownOnItemPage);

            return properties.length;
        }
    },

    created()
    {
        this.$options.template = this.template;
    },

    methods:
    {
        unsetDeselectedRadios(propertyId)
        {
            const propertiesToUnselect = this.propertyGroup.properties.filter(property => property.property.id !== propertyId && this.isPropertyCheckbox(property));

            for (const property of propertiesToUnselect)
            {
                this.setVariationOrderProperty({propertyId: property.property.id, value: null});
            }
        },

        isPropertyCheckbox(property)
        {
            const orderPropertyGroupingType = property.group ? property.group.orderPropertyGroupingType : null;
            const valueType = property.property.valueType;

            if (valueType === "empty" && orderPropertyGroupingType === "single")
            {
                return true;
            }

            return false;
        },

        ...Vuex.mapMutations([
            "setVariationOrderProperty"
        ])
    }
});

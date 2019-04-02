
Vue.component("tab-item", {

    render(createElement)
    {
        return createElement(
            "div",
            {
                staticClass: "tab-pane",
                attrs: {
                    role: "tabpanel"
                }
            },
            [this.$slots.default]
        );
    },

    props: {
        active:
        {
            type: Boolean,
            default: false
        },
        title:
        {
            type: String,
            default: null
        }
    },

    data()
    {
        return {

        };
    },

    computed: Vuex.mapState({

    }),

    created()
    {

    },

    methods:
    {
        
    }
});

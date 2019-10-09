import Vue from "vue";
import { mapState } from "vuex";


Vue.component("tags", {

    props: {
        template:
        {
            type: String,
            default: "#vue-tags"
        }
    },
    computed: {
        ...mapState({
            tags: state => state.item.variation.documents[0].data.tags
        })
    },
    methods:
        {
            getFontColorBasedOnBackground(bgColor)
            {
                const color = (bgColor.charAt(0) === "#") ? bgColor.substring(1, 7) : bgColor;
                const r = parseInt(color.substring(0, 2), 16);
                const g = parseInt(color.substring(2, 4), 16);
                const b = parseInt(color.substring(4, 6), 16);

                return (((r * 0.299) + (g * 0.587) + (b * 0.114)) > 186) ? "#000000" : "#FFFFFF";
            },
            getStyles(tag)
            {
                return {
                    backgroundColor: tag.color,
                    color: this.getFontColorBasedOnBackground(tag.color)
                };
            }
        }
});

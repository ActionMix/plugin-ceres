import Vue from "vue";
import ApiService from "../../services/ApiService";

class SidenavigationChildrenLoader
{
    constructor(element, categoryId, currentUrl, openClassName)
    {
        this.categoryId = categoryId;
        this.element = element;
        this.currentUrl = currentUrl;
        this.openClassName = openClassName || "is-open";

        this.template = "";
    }

    get parent()
    {
        return this.element.parentElement;
    }

    loadChildren()
    {
        return new Promise(resolve =>
        {
            ApiService.get("/rest/io/categorytree/children", { categoryId: this.categoryId, currentUrl: this.currentUrl })
                .then(result =>
                {
                    this.template = result;
                    resolve(this.template);
                });
        });
    }

    createChildren()
    {
        for (const template of this.getSplitMarkup())
        {
            const ul = document.createElement("ul");

            this.parent.appendChild(ul);

            const compiled = Vue.compile(template);

            new Vue({
                store: window.ceresStore,
                render: compiled.render,
                staticRenderFns: compiled.staticRenderFns
            }).$mount(ul);
        }
    }

    getSplitMarkup()
    {
        const fragment = document.createRange().createContextualFragment(this.template);
        const elements = fragment.children;
        const data = [];

        for (const element of elements)
        {
            data.push(element.outerHTML);
        }

        return data;
    }

    toggle()
    {
        if (!this.firstChildrenLoad)
        {
            this.firstChildrenLoad = true;
            this.loadChildren().then(() =>
            {
                this.createChildren();
            });
        }

        this.parent.classList.toggle(this.openClassName);
        this.element.classList.toggle("fa-caret-down");
        this.element.classList.toggle("fa-caret-right");
    }
}

Vue.directive("sidenavigation-children", {
    bind(el, binding)
    {
        const categoryId = binding.value.categoryId;
        const currentUrl = binding.value.currentUrl;

        const sidenavigationChildrenLoader = new SidenavigationChildrenLoader(el, categoryId, currentUrl);

        el.addEventListener("click", () =>
        {
            sidenavigationChildrenLoader.toggle();
        });
    }
});

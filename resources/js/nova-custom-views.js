import routes from './router/routes'
Nova.booting((Vue, router) => {
    router.beforeEach((to, from, next) => {
        console.log(to.name)
        let customComponent = null;
        let resourceCustomViews = (window.config.novaCustomViews)? window.config.novaCustomViews[to.params.resourceName] : null
        let globalViews = ['dashboard', '403', '404'];
        if(globalViews.includes(to.name)) {
            customComponent = window.config["novaCustom" + to.name.charAt(0).toUpperCase() + to.name.slice(1)]
        } else {
            customComponent = (resourceCustomViews && resourceCustomViews[to.name])? resourceCustomViews[to.name]['name'] : null
        }
        if(customComponent && Vue.options.components[customComponent]) {
            next({
                name: 'custom-' + to.name,
                params: Object.assign({},to.params, {component: customComponent}),
                query: to.query
            });
        } else {
            next();
        }
    })
    router.addRoutes(routes)
})


Nova.booting((Vue, router) => {

    const registeredViews =  JSON.parse('{{ registeredViews }}')
    Object.keys(registeredViews).forEach(function(key) {
        Vue.component(registeredViews[key]['name'], require('./views/' + registeredViews[key]['component']))
    })
})

import Tool from './components/Tool'
import RolesResourceTool from './components/RolesResourceTool'

App.booting((Vue, router, store) => {
    Vue.component('roles-resource-tool', RolesResourceTool)

    router.addRoutes([
        {
            name: 'permissions-tool',
            path: '/permissions-tool',
            components: {
                default: Tool,
                header: Vue.component('app-header'),
                sidebar: Vue.component('app-sidebar')
            },
            props: {
                default: true
            }
        },
    ])
})

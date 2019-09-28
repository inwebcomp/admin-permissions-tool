import Tool from './components/Tool'
import AddRole from './components/AddRole'
import RolesResourceTool from './components/RolesResourceTool'

App.booting((Vue, router, store) => {
    Vue.component('roles-resource-tool', RolesResourceTool)
    Vue.component('add-role', AddRole)

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

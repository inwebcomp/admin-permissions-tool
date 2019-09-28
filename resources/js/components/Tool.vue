<template>
    <div class="p-8">
        <heading class="mb-6">{{ __('Права доступа пользователей') }}</heading>

        <card v-for="(role, $i) in roles" :key="$i" class="card--form" :caption="role.title">
            <div class="p-4 flex flex-wrap">
                <div v-for="(resource, uid) in role.resources" :key="uid">
                    <table class="border mr-4 mb-4">
                        <caption class="font-bold py-1 mb-1">{{ resource.title }}</caption>
                        <tr v-for="(permission) in resource.permissions" :key="'action-' + permission.action"
                            class="hover:bg-grey-lighter">
                            <td class="py-2 px-4 border-b">{{ permission.title || permission.action }}</td>
                            <td class="py-2 px-4 border-b cursor-pointer"
                                @click="update(role, resource.uid, permission)">
                                <i v-if="permission.permitted" class="far fa-check text-success"></i>
                                <i v-if="! permission.permitted" class="far fa-times-circle text-danger"></i>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </card>
    </div>
</template>

<script>
    const root = 'tool/permissions-tool'

    export default {
        data() {
            return {
                loading: true,
                roles: [],
            }
        },

        mounted() {
            this.fetch()
        },

        methods: {
            async fetch() {
                this.loading = true

                App.api.request({
                    url: root
                }).then(data => {
                    this.roles = data
                    this.loading = false
                })
            },

            async update(role, resource, permission) {
                this.loading = true

                let action = permission.action
                let value = !permission.permitted

                App.api.request({
                    method: 'post',
                    url: root + '/' + role.id + '/' + resource + '/' + action,
                    data: {
                        value
                    }
                }).then(({permitted}) => {
                    this.loading = false

                    this.roles[role.id].resources[resource].permissions[action].permitted = permitted

                    this.$toasted.success(this.__('Права обновлены'))
                })
            }
        },
    }
</script>
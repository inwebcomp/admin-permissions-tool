<template>
    <div class="p-8">
        <heading class="mb-6 flex justify-between items-center">
            {{ __('Права доступа пользователей') }}

            <app-button @click.native="addRole" type="link">{{ __('Добавить роль') }}</app-button>
        </heading>

        <card v-for="(role, $i) in roles" :key="$i" class="card--form">
            <div class="card__header flex items-center">
                <h2 class="mr-auto">{{ role.title }}</h2>

                <i class="far fa-times-circle text-grey-light p-4 cursor-pointer hover:text-danger"
                   :title="__('Запретить все действия')"
                   @click="updateAllForRole(role, false)"></i>

                <i class="far fa-check text-grey-light p-4 cursor-pointer hover:text-success"
                   :title="__('Разрешить все действия')"
                   @click="updateAllForRole(role, true)"></i>

                <i class="far fa-trash-alt cursor-pointer p-4 px-6 hover:text-danger ml-auto"
                   :title="__('Удалить роль')"
                   @click="removeRole(role)"></i>
            </div>


            <div class="p-4 flex flex-wrap">
                <div v-for="(resource, uid) in role.resources" :key="uid">
                    <table class="border mr-4 mb-4">
                        <caption class="font-bold py-1 mb-1">
                            <div class="flex justify-between whitespace-no-wrap px-4">
                                <i class="far fa-times-circle text-grey-light cursor-pointer hover:text-danger"
                                   :title="__('Запретить все действия')"
                                   @click="updateAll(role, resource.uid, false)"></i>

                                <div class="px-2">{{ resource.title }}</div>

                                <i class="far fa-check text-grey-light cursor-pointer hover:text-success"
                                   :title="__('Разрешить все действия')"
                                   @click="updateAll(role, resource.uid, true)"></i>
                            </div>
                        </caption>

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

            App.$on('add-role', this.fetch)
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
            },

            async updateAll(role, resource, value) {
                this.loading = true

                App.api.request({
                    method: 'post',
                    url: root + '/' + role.id + '/' + resource + '/all',
                    data: {
                        value
                    }
                }).then(({permitted}) => {
                    this.loading = false

                    Object.keys(this.roles[role.id].resources[resource].permissions).forEach((action) => {
                        this.roles[role.id].resources[resource].permissions[action].permitted = permitted
                    })

                    this.$toasted.success(this.__('Права обновлены'))
                })
            },

            async updateAllForRole(role, value) {
                this.loading = true

                App.api.request({
                    method: 'post',
                    url: root + '/' + role.id + '/all',
                    data: {
                        value
                    }
                }).then(({permitted}) => {
                    this.loading = false

                    Object.keys(this.roles[role.id].resources).forEach((resource) => {
                        Object.keys(this.roles[role.id].resources[resource].permissions).forEach((action) => {
                            this.roles[role.id].resources[resource].permissions[action].permitted = permitted
                        })
                    })

                    this.$toasted.success(this.__('Права обновлены'))
                })
            },

            addRole() {
                this.$showPopup('add-role')
            },

            async removeRole(role) {
                if (! confirm(this.__('Вы уверены что хотите удалить роль?')))
                    return

                this.loading = true

                App.api.request({
                    method: 'delete',
                    url: root + '/roles/' + role.id,
                }).then(() => {
                    this.loading = false

                    this.fetch()

                    this.$toasted.success(this.__('Роль удалена'))
                })
            },
        },
    }
</script>
<template>
    <form @submit.prevent.stop="add" class="flex -mx-2">
        <div class="w-1/2 mx-2">
            <form-select-field v-model="role" :field="{ name: __('Добавить роль'), options: roles, value: role }"/>

            <app-button type="save" submit>{{ __('Добавить') }}</app-button>
        </div>
        <div class="w-1/2 mx-2">
            <div class="form__group__label">{{ __('Роли') }}</div>

            <div class="flex flex-wrap">
                <div v-for="(role, $i) in userRoles" :key="$i"
                     class="bg-grey-lighter rounded inline-block pl-4 py-2 mb-2 mr-2">
                    {{ role.title }}
                    <i class="far fa-trash-alt p-2 px-4 hover:text-danger cursor-pointer" @click="remove(role)"></i>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    const root = 'tool/permissions-tool'

    export default {
        props: {
            resourceId: {},
        },
        data() {
            return {
                loading: true,
                role: null,
                roles: [],
                userRoles: [],
            }
        },

        async mounted() {
            await this.fetch()
            await this.fetchRoles()
        },

        methods: {
            async fetch() {
                this.loading = true

                App.api.request({
                    url: root + '/user/roles'
                }).then(data => {
                    this.userRoles = data
                    this.loading = false
                })
            },

            async fetchRoles() {
                this.loading = true

                App.api.request({
                    url: root + '/roles'
                }).then(data => {
                    this.roles = data
                    this.loading = false
                })
            },

            async add() {
                this.loading = true

                App.api.request({
                    method: 'post',
                    url: root + '/roles/admin-user/' + this.resourceId,
                    data: {
                        role: this.role
                    }
                }).then(data => {
                    this.userRoles = data
                    this.loading = false

                    this.$toasted.success(this.__('Роль присвоена'))
                })
            },

            async remove(role) {
                this.loading = true

                App.api.request({
                    method: 'delete',
                    url: root + '/roles/admin-user/' + this.resourceId,
                    data: {
                        role: role.id
                    }
                }).then(data => {
                    this.userRoles = data
                    this.loading = false

                    this.$toasted.success(this.__('Роль удалена'))
                })
            },
        },
    }
</script>
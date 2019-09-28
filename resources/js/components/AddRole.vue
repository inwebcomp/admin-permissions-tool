<template>
    <form @submit.prevent.stop="add">
        <form-text-field v-model="role" :field="{ name: __('Название роли'), value: role }"/>

        <app-button type="save" submit>{{ __('Добавить') }}</app-button>
    </form>
</template>

<script>
    const root = 'tool/permissions-tool'

    export default {
        data() {
            return {
                loading: true,
                role: null,
            }
        },

        methods: {
            async add() {
                this.loading = true

                App.api.request({
                    method: 'post',
                    url: root + '/roles',
                    data: {
                        role: this.role
                    }
                }).then(data => {
                    this.userRoles = data
                    this.loading = false

                    App.$emit('add-role')
                    this.$toasted.success(this.__('Роль присвоена'))
                    this.$closePopup()
                })
            },
        },
    }
</script>
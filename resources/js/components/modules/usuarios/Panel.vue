<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card flat height="200px">
                <v-toolbar dense dark class="blue darken-1">
                    <v-toolbar-title>
                        Usuarios
                        <small class="caption">
                            Registro y gestión
                        </small>
                    </v-toolbar-title>
                    <v-spacer/>
                    <v-tooltip top v-if="rol === 1">
                        <v-btn icon slot="activator" @click.stop="newUser">
                            <v-icon>add</v-icon>
                        </v-btn>
                        <span>Crear usuario</span>
                    </v-tooltip>
                </v-toolbar>
                <data-table
                    ref="tablaUsuarios"
                    v-model="dataTable"
                    @resetOption="item => resetOptions(item)"
                    @resetPassword="item => resetPassword(item)"
                ></data-table>
            </v-card>
        </v-flex>
        <confirmation-dialog
            ref="dialogConfirm"
            heading="¿Seguro de continuar?"
            :message="`¿Está seguro de continuar con el reestablecimiento de la contraseña del usuario <strong>${selectedItem && selectedItem.name}</strong>?`"
            @onConfirm="submitResetPassword"
        >
        </confirmation-dialog>
        <register-dialog ref="registerDialog"></register-dialog>
    </v-layout>
</template>
<script>
    import { mapState } from 'vuex'
    export default {
		name: "Panel",
        components: {
            DataTable: resolve => {require(['../../general/DataTable'], resolve)},
            RegisterDialog: resolve => {require(['./RegisterDialog'], resolve)},
            ConfirmationDialog: resolve => {require(['../../general/ConfirmationDialog'], resolve)}
        },
		data: () => ({
            selectedItem: null,
            dataTable: {
                nameItemState: 'tablaUsuarios',
                route: 'usuarios/panel',
                makeHeaders: [
                    {
                        text: 'Alias',
                        align: 'left',
                        sortable: true,
                        value: 'name'
                    },
                    {
                        text: 'Correo Electrónico',
                        align: 'left',
                        sortable: true,
                        value: 'email'
                    },
                    {
                        text: 'Identificación',
                        align: 'left',
                        sortable: false,
                        value: 'empleado.identificacion'
                    },
                    {
                        text: 'Nombre',
                        align: 'left',
                        sortable: false,
                        value: 'empleado.nombre'
                    },
                    {
                        text: 'Celular',
                        align: 'left',
                        sortable: false,
                        value: 'empleado.celular'
                    },
                    {
                        text: 'Opciones',
                        align: 'center',
                        sortable: false,
                        actions: true,
                        singlesActions: true,
                        classData: 'text-xs-center'
                    }
                ]
            }
		}),
        computed: mapState({
            rol: state => state.user.currentRol
        }),
        methods: {
            resetOptions (item) {
                item.options = []
                if (this.rol === 1) item.options.push({event: 'resetPassword', icon: 'settings_backup_restore', tooltip: 'Reestablecer contraseña', color: 'success'})
                return item
            },
            resetPassword (item) {
                this.selectedItem = item
                this.$refs.dialogConfirm.open()
            },
            submitResetPassword () {
                this.$store.commit('LOADING', true)
                this.axios.post('usuarios/resetpassword', {id: this.selectedItem.id})
                    .then(response => {
                        this.$store.commit('LOADING', false)
                        this.$refs.dialogConfirm.close()
                        this.$store.commit('SNACKBAR', {color: 'success', message: `La contraseña del usuario ${this.selectedItem.name} se reestableció correctamente`})
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al reestablecer la contraseña del usuario ${this.selectedItem.name}`, error: error})
                    })
            },
		    newUser () {
                this.$refs.registerDialog.register()
            }
        }
    }
</script>

<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card flat height="200px">
                <v-toolbar dense dark class="blue darken-1">
                    <v-toolbar-title>
                        Eventos
                        <small class="caption">
                            Registro y gestión
                        </small>
                    </v-toolbar-title>
                    <v-spacer/>
                    <v-tooltip top>
                        <v-btn icon slot="activator" @click.stop="newEvent">
                            <v-icon>add</v-icon>
                        </v-btn>
                        <span>Crear evento</span>
                    </v-tooltip>
                </v-toolbar>
                <data-table
                    ref="tablaEventos"
                    v-model="dataTable"
                    @resetOption="item => resetOptions(item)"
                    @editEvent="item => editEvent(item)"
                ></data-table>
            </v-card>
        </v-flex>
        <confirmation-dialog
            ref="dialogConfirm"
            heading="¿Seguro de continuar?"
            :message="`¿Está seguro de continuar con la eliminación del evento <strong>${selectedItem && selectedItem.id}</strong>?`"
            @onConfirm="submitDeleteEvent"
        >
        </confirmation-dialog>
        <!--<register-dialog ref="registerDialog"></register-dialog>-->
    </v-layout>
</template>
<script>
    export default {
		name: "Panel",
        components: {
            DataTable: resolve => {require(['../../general/DataTable'], resolve)},
            // RegisterDialog: resolve => {require(['./RegisterDialog'], resolve)},
            ConfirmationDialog: resolve => {require(['../../general/ConfirmationDialog'], resolve)}
        },
		data: () => ({
            selectedItem: null,
            dataTable: {
                nameItemState: 'tablaEventos',
                route: 'eventos/panel',
                makeHeaders: [
                    {
                        text: 'Concecutivo',
                        align: 'center',
                        sortable: true,
                        value: 'id',
                        classData: 'text-xs-center'
                    },
                    {
                        text: 'EP',
                        align: 'center',
                        sortable: true,
                        value: 'evento_padre_id',
                        classData: 'text-xs-center',
                        tooltip: 'Evento principal'
                    },
                    {
                        text: 'Tipo',
                        align: 'left',
                        sortable: false,
                        value: 'tipo_evento.nombre'
                    },
                    {
                        text: 'Fecha inicio',
                        align: 'left',
                        sortable: false,
                        value: 'fecha_inicio'
                    },
                    {
                        text: 'Fecha fin',
                        align: 'left',
                        sortable: false,
                        value: 'fecha_fin'
                    },
                    {
                        text: 'Equipo',
                        align: 'left',
                        sortable: false,
                        value: 'equipo.nombre'
                    },
                    {
                        text: 'Estado',
                        align: 'left',
                        sortable: false,
                        value: 'estado'
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
        methods: {
            resetOptions (item) {
                item.options = []
                item.options.push({event: 'editEvent', icon: 'edit', tooltip: 'Editar evento', color: 'warning'})
                return item
            },
            resetPassword (item) {
                this.selectedItem = item
                this.$refs.dialogConfirm.open()
            },
            submitDeleteEvent () {
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
            editEvent (evento) {
                console.log('el evento', evento)
            },
		    newEvent () {
                this.$refs.registerDialog.register()
            }
        }
    }
</script>

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
                    @cancelEvent="item => cancelEvent(item)"
                    @commentEvent="item => commentEvent(item)"
                    @detailEvent="item => detailEvent(item)"
                ></data-table>
            </v-card>
        </v-flex>
        <confirmation-dialog
            ref="dialogConfirm"
            heading="¿Seguro de continuar?"
            :message="`¿Está seguro de continuar con la anulación del evento <strong>${selectedItem && selectedItem.id}</strong>?`"
            @onConfirm="submitCancelEvent"
        >
        </confirmation-dialog>
        <register-dialog ref="registerDialog"></register-dialog>
        <detail-dialog ref="detailDialog"></detail-dialog>
    </v-layout>
</template>
<script>
    export default {
		name: "Panel",
        components: {
            DataTable: resolve => {require(['../../general/DataTable'], resolve)},
            RegisterDialog: resolve => {require(['./RegisterDialog'], resolve)},
            ConfirmationDialog: resolve => {require(['../../general/ConfirmationDialog'], resolve)},
            DetailDialog: resolve => {require(['./DetailDialog'], resolve)}
        },
		data: () => ({
            selectedItem: null,
            dataTable: {
                nameItemState: 'tablaEventos',
                route: 'eventos/panel',
                makeHeaders: [
                    {
                        text: 'Id',
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
                    item.options.push({event: 'detailEvent', icon: 'details', tooltip: 'Detalle evento', color: 'primary'})
                if (item.estado === 'Registrado') item.options.push({event: 'editEvent', icon: 'edit', tooltip: 'Editar evento', color: 'warning'})
                if (item.estado === 'Registrado') item.options.push({event: 'commentEvent', icon: 'comment', tooltip: 'Comentar evento', color: 'info'})
                if (item.estado === 'Registrado') item.options.push({event: 'cancelEvent', icon: 'cancel', tooltip: 'Anular evento', color: 'error'})
                return item
            },
            detailEvent (evento) {
                this.$refs.detailDialog.register(evento.id)
            },
            commentEvent (evento) {
                this.$refs.detailDialog.register(evento.id, 'Comentarios')
            },
            cancelEvent (item) {
                this.selectedItem = item
                this.$refs.dialogConfirm.open()
            },
            submitCancelEvent () {
                this.$store.commit('LOADING', true)
                this.axios.post('eventos/cancelevent', {id: this.selectedItem.id})
                    .then(response => {
                        this.$store.commit('LOADING', false)
                        this.$refs.dialogConfirm.close()
                        this.$store.commit('SNACKBAR', {color: 'success', message: `El evento número ${this.selectedItem.name} se anuló correctamente`})
                        this.$store.commit('RELOAD_TABLE', 'tablaEventos')
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al anular el evento número ${this.selectedItem.id}`, error: error})
                    })
            },
            editEvent (evento) {
                this.$refs.registerDialog.register(evento.id)
            },
		    newEvent () {
                this.$refs.registerDialog.register()
            }
        }
    }
</script>

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
                    <v-btn v-if="rol === 1" color="blue-grey" @click.stop="newEvent">
                        <v-icon left>fas fa-calendar-plus</v-icon>
                        Crear evento
                    </v-btn>
                    <dinamic-list></dinamic-list>
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
    import { mapState } from 'vuex'
    import DinamicList from './DinamicList'
    export default {
		name: "Panel",
        components: {
            DinamicList,
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
                        sortable: false,
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
                        text: 'Fecha registro',
                        align: 'left',
                        sortable: false,
                        value: 'fecha_registro'
                    },
                    {
                        text: 'Ubicación',
                        align: 'left',
                        sortable: false,
                        value: 'equipo',
                        component: {
                            template:
                                `
                        <div>
                            <v-chip small color="green" text-color="white">
                                <v-avatar class="green darken-4">C</v-avatar>
                                {{value.equipo.sistema.planta.campo.nombre}}
                            </v-chip>
                            <v-chip small color="orange" text-color="white">
                                <v-avatar class="orange darken-4">P</v-avatar>
                                {{value.equipo.sistema.planta.nombre}}
                            </v-chip>
                            <v-chip small color="teal" text-color="white">
                                <v-avatar class="teal darken-4">S</v-avatar>
                                {{value.equipo.sistema.nombre}}
                            </v-chip>
                        </div>
                      `,
                            props: ['value']
                        }
                    },
                    {
                        text: 'Equipo',
                        align: 'left',
                        sortable: false,
                        value: 'equipo',
                        component: {
                            template:
                                `
                        <div>
                          <v-list-tile class="content-v-list-tile-p0">
                            <v-list-tile-content>
                              <v-list-tile-title class="body-1"> {{value.equipo.nombre}} - Tag:{{value.equipo.tag}}</v-list-tile-title>
                              <v-list-tile-sub-title class="caption">Número: {{value.equipo.numero_equipo}}</v-list-tile-sub-title>
                            </v-list-tile-content>
                          </v-list-tile>
                        </div>
                      `,
                            props: ['value']
                        }
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
                    item.options.push({event: 'detailEvent', icon: 'details', tooltip: 'Detalle evento', color: 'primary'})
                if (item.estado === 'Registrado' && this.rol === 1) item.options.push({event: 'editEvent', icon: 'edit', tooltip: 'Editar evento', color: 'warning'})
                if (item.estado === 'Registrado' && this.rol === 1) item.options.push({event: 'commentEvent', icon: 'comment', tooltip: 'Comentar evento', color: 'info'})
                if (item.estado === 'Registrado' && this.rol === 1) item.options.push({event: 'cancelEvent', icon: 'cancel', tooltip: 'Anular evento', color: 'error'})
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

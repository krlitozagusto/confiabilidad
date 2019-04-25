<template>
    <v-layout row justify-center>
        <v-dialog v-model="open" fullscreen hide-overlay transition="dialog-bottom-transition" persistent>
            <v-card>
                <v-card-title>
                    <span class="headline">Detalle del evento No. <strong>{{evento.id}}</strong></span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container class="pa-0" fluid grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm6 md3>
                                <v-switch
                                    readonly
                                    ripple
                                    label="Es evento principal"
                                    :false-value="0"
                                    :true-value="1"
                                    v-model="esPrincipal"
                                ></v-switch>
                            </v-flex>
                            <input-detail-flex
                                flex-class="xs12 sm6 md3"
                                label="Tipo evento"
                                :text="evento.tipo_evento && evento.tipo_evento.nombre"
                            />
                            <input-detail-flex
                                flex-class="xs12 sm6 md3"
                                prepend-icon="event"
                                label="Fecha registro"
                                :text="evento.fecha_registro"
                            />
                            <input-detail-flex
                                v-if="!esPrincipal"
                                flex-class="xs12 sm6 md3"
                                prepend-icon="beenhere"
                                label="Evento principal"
                                :text="evento.evento_padre.id"
                            />
                            <input-detail-flex
                                flex-class="xs12 sm12 md6"
                                label="Equipo"
                                :text="evento.equipo && evento.equipo.nombre"
                                :hint="evento.equipo && evento.equipo.tag"
                            />
                            <input-detail-flex
                                flex-class="xs12 sm6 md3"
                                prepend-icon="event"
                                label="Fecha inicio"
                                :text="evento.fecha_inicio"
                            />
                            <input-detail-flex
                                flex-class="xs12 sm6 md3"
                                prepend-icon="event"
                                label="Fecha fin"
                                :text="evento.fecha_fin"
                            />
                            <input-detail-flex
                                flex-class="xs12 sm6 md2"
                                label="Tipo mantenimiento"
                                :text="evento.tipo_mantenimiento && evento.tipo_mantenimiento.nombre"
                            />
                            <v-flex dflex>
                                <v-switch
                                    readonly
                                    ripple
                                    label="Contractual"
                                    :false-value="0"
                                    :true-value="1"
                                    v-model="evento.contractual"
                                ></v-switch>
                            </v-flex>
                            <v-flex dflex>
                                <v-switch
                                    readonly
                                    ripple
                                    label="Programado"
                                    :false-value="0"
                                    :true-value="1"
                                    v-model="evento.programado"
                                ></v-switch>
                            </v-flex>
                            <input-detail-flex
                                flex-class="xs12 sm6 md3"
                                prepend-icon="event"
                                label="Fecha inicio reparación"
                                :text="evento.fecha_inicio_reparacion"
                            />
                            <input-detail-flex
                                flex-class="xs12 sm6 md3"
                                prepend-icon="event"
                                label="Fecha fin reparación"
                                :text="evento.fecha_fin_reparacion"
                            />
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn flat @click="close">Cancelar</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" dark @click="submit">Registrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
    export default {
		name: "DetailDialog",
        components: {
            PostuladorV2: resolve => {require(['../../general/PostuladorV2'], resolve)},
            InputDetailFlex: resolve => {require(['../../general/InputDetailFlex'], resolve)}
        },
		data: () => ({
            fechaRegistro: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            horaRegistro: {
                menu: false
            },
            fechaInicio: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            horaInicio: {
                menu: false
            },
            fechaFin: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            horaFin: {
                menu: false
            },
            fechaInicioReparacion: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            horaInicioReparacion: {
                menu: false
            },
            fechaFinReparacion: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            horaFinReparacion: {
                menu: false
            },
            evento: null,
            makeEvento: {
                id: null,
                fecha_registro: null,
                hora_registro: null,
                fecha_inicio: null,
                hora_inicio: null,
                fecha_fin: null,
                hora_fin: null,
                fecha_inicio_reparacion: null,
                hora_inicio_reparacion: null,
                fecha_fin_reparacion: null,
                hora_fin_reparacion: null,
                downtime: null,
                estado: 'Registrado',
                contractual: 0,
                programado: 0,
                tipo_evento_id: null,
                tipo_mantenimiento_id: null,
                equipo_id: null,
                evento_padre_id: null,
                user_id: null,
                // Auxiliares
                eventos_hijos: [],
                evento_padre: null,
                equipo: null,
                tipo_evento: null,
                tipo_mantenimiento: null
            },
            esPrincipal: 1,
            open: false
		}),
		computed: {
		},
        whatch: {
		    'open' (val) {
            }
        },
		created () {
		    this.resetModel()
		},
        methods: {
            register (id) {
                this.open = true
                this.$store.commit('LOADING', true)
                this.axios.post(`eventos/get`, {id: id})
                    .then(response => {
                        console.log('detalle', response)
                        this.esPrincipal = response.data.evento.evento_padre_id ? 0 : 1
                        this.evento = response.data.evento
                        this.$store.commit('LOADING', false)
                        this.open = true
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al traer los datos para el formulario de registro de evento`, error: error})
                    })
            },
            close () {
                this.open = false
                setTimeout(() => {
                    this.resetModel()
                }, 300)
            },
            submit () {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.$store.commit('LOADING', true)
                        let data = {
                            id: this.evento.id,
                            fecha_registro: this.evento.fecha_registro && this.evento.hora_registro ? `${this.evento.fecha_registro} ${this.evento.hora_registro}` : null,
                            fecha_inicio: this.evento.fecha_inicio && this.evento.hora_inicio ? `${this.evento.fecha_inicio} ${this.evento.hora_inicio}` : null,
                            fecha_fin: this.evento.fecha_fin && this.evento.hora_fin ? `${this.evento.fecha_fin} ${this.evento.hora_fin}` : null,
                            fecha_inicio_reparacion: this.evento.fecha_inicio_reparacion && this.evento.hora_inicio_reparacion ? `${this.evento.fecha_inicio_reparacion} ${this.evento.hora_inicio_reparacion}` : null,
                            fecha_fin_reparacion: this.evento.fecha_fin_reparacion && this.evento.hora_fin_reparacion ? `${this.evento.fecha_fin_reparacion} ${this.evento.hora_fin_reparacion}` : null,
                            downtime: null,
                            estado: 'Registrado',
                            contractual: this.evento.contractual,
                            programado: this.evento.programado,
                            tipo_evento_id: this.evento.tipo_evento_id,
                            tipo_mantenimiento_id: this.evento.tipo_mantenimiento_id,
                            equipo_id: this.evento.equipo_id,
                            evento_padre_id: !this.esPrincipal ? this.evento.evento_padre_id : null,
                            user_id: this.evento.user_id
                        }
                        axios.post(`eventos/registerevent`, data)
                            .then(response => {
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'success', message: `evento registrado correctamente`})
                                this.$store.commit('RELOAD_TABLE', 'tablaEventos')
                                this.close()
                            })
                            .catch(error => {
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'error', message: `al registrar el evento`, error: error})
                            })
                    }
                })
            },
            resetModel () {
                this.evento = window.lodash.clone(this.makeEvento)
                this.$validator.reset()
            }
        }
    }
</script>

<template>
    <v-layout>
        <v-dialog v-model="open" persistent max-width="700" scrollable>
            <v-card>
                <v-card-title class="headline">{{evento.id ? 'Edición de evento' : 'Nuevo evento'}}</v-card-title>
                <v-card-text>
                    <v-container class="pa-0" fluid grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm4 md4>
                                <v-switch
                                    ripple
                                    label="Evento principal"
                                    :false-value="0"
                                    :true-value="1"
                                    v-model="evento.esPrincipal"
                                ></v-switch>
                            </v-flex>
                            <template v-if="evento.esPrincipal">
                                <v-flex xs12 sm4 md4>
                                    <v-select
                                        ke="selectTipo"
                                        label="Tipo evento"
                                        :items="tiposEvento"
                                        v-model="evento.tipo_evento_id"
                                        item-value="id"
                                        item-text="nombre"
                                        name="Tipo evento"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Tipo evento')"
                                    ></v-select>
                                </v-flex>
                                <v-flex xs12 sm4 md4>
                                    <v-menu
                                        ref="fechaRegistroEvento"
                                        v-model="fechaRegistro.menu"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        lazy
                                        transition="scale-transition"
                                        offset-y
                                        full-width
                                        min-width="290px"
                                    >
                                        <v-text-field
                                            slot="activator"
                                            label="Fecha registro"
                                            v-model="evento.fecha_registro"
                                            name="Fecha registro"
                                            v-validate="'required|date_format:yyyy-MM-dd|date_between:' + fechaRegistro.minDate + ',' + fechaRegistro.maxDate + ',true'"
                                            :error-messages="errors.collect('Fecha registro')"
                                            readonly
                                        ></v-text-field>
                                        <v-date-picker
                                            ref="picker"
                                            v-model="evento.fecha_registro"
                                            locale="es-co"
                                            :min="fechaRegistro.minDate"
                                            :max="fechaRegistro.maxDate"
                                            @input="$refs.fechaRegistroEvento.save(evento.fecha_registro)"
                                            @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Fecha registro')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                            </template>
                            <v-flex xs12 sm8 md8 v-else>
                                <postulador-v2
                                    ref="postuladorEventos"
                                    key="eventoP"
                                    no-data="Busqueda por número o nombre de equipo."
                                    item-text="id"
                                    label="Evento primario"
                                    entidad="eventos/postulador"
                                    v-model="evento.eventoPrincipal"
                                    no-btn-create
                                    no-btn-edit
                                    name="Evento primario"
                                    route-params="filter[estado]=Registrado"
                                    rules="required"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('Evento primario')"
                                    :slot-data='{
                                      template:`
                                      <v-list-tile>
                                      <v-list-tile-action>
                                        <v-chip
                                        color="indigo lighten-2"
                                        label
                                        small
                                        >
                                        {{ value.id }}
                                        </v-chip>
                                        </v-list-tile-action>
                                        <v-list-tile-content>
                                          <v-list-tile-title>{{value.equipo.nombre}}</v-list-tile-title>
                                          <v-list-tile-sub-title class=caption>{{ value.equipo.descripcion }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                      </v-list-tile>
                                      `,
                                      props: [`value`]
                                     }'
                                ></postulador-v2>
                            </v-flex>
                            <v-flex xs12>
                                <postulador-v2
                                    ref="postuladorEquipos"
                                    no-data="Busqueda por nombre, tag, ubucación técnica o número de equipo."
                                    item-text="nombre"
                                    label="Equipo"
                                    entidad="equipos/postulador"
                                    v-model="evento.equipo"
                                    no-btn-create
                                    no-btn-edit
                                    name="Equipo"
                                    rules="required"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('Equipo')"
                                    :slot-data='{
                                      template:`
                                      <v-list-tile>
                                        <v-list-tile-content>
                                          <v-list-tile-title>{{value.nombre}}</v-list-tile-title>
                                          <v-list-tile-sub-title class=caption>Tag: {{ value.tag.codigo }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                      </v-list-tile>
                                      `,
                                      props: [`value`]
                                     }'
                                ></postulador-v2>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="fechaInicioEvento"
                                    v-model="fechaInicio.menu"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px"
                                >
                                    <v-text-field
                                        slot="activator"
                                        label="Fecha inicio"
                                        v-model="evento.fecha_inicio"
                                        name="Fecha inicio"
                                        v-validate="'required|date_format:yyyy-MM-dd|date_between:' + fechaInicio.minDate + ',' + fechaInicio.maxDate + ',true'"
                                        :error-messages="errors.collect('Fecha inicio')"
                                        readonly
                                    ></v-text-field>
                                    <v-date-picker
                                        ref="picker"
                                        v-model="evento.fecha_inicio"
                                        locale="es-co"
                                        :min="fechaInicio.minDate"
                                        :max="fechaInicio.maxDate"
                                        @input="$refs.fechaInicioEvento.save(evento.fecha_inicio)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Fecha inicio')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-date-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="fechaFinEvento"
                                    v-model="fechaFin.menu"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    lazy
                                    transition="scale-transition"
                                    offset-y
                                    full-width
                                    min-width="290px"
                                >
                                    <v-text-field
                                        slot="activator"
                                        label="fecha fin"
                                        v-model="evento.fecha_fin"
                                        name="fecha fin"
                                        v-validate="'required|date_format:yyyy-MM-dd|date_between:' + fechaFin.minDate + ',' + fechaFin.maxDate + ',true'"
                                        :error-messages="errors.collect('fecha fin')"
                                        readonly
                                    ></v-text-field>
                                    <v-date-picker
                                        ref="picker"
                                        v-model="evento.fecha_fin"
                                        locale="es-co"
                                        :min="fechaFin.minDate"
                                        :max="fechaFin.maxDate"
                                        @input="$refs.fechaFinEvento.save(evento.fecha_fin)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'fecha fin')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-date-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-select
                                    label="Tipo mantenimiento"
                                    :items="tiposMantenimiento"
                                    v-model="evento.tipo_mantenimiento_id"
                                    item-value="id"
                                    item-text="nombre"
                                    name="Tipo mantenimiento"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('Tipo mantenimiento')"
                                ></v-select>
                            </v-flex>
                            <v-flex dflex>
                                <v-switch
                                    ripple
                                    label="Contractual"
                                    :false-value="0"
                                    :true-value="1"
                                    v-model="evento.contractual"
                                ></v-switch>
                            </v-flex>
                            <v-flex dflex>
                                <v-switch
                                    ripple
                                    label="Programado"
                                    :false-value="0"
                                    :true-value="1"
                                    v-model="evento.programado"
                                ></v-switch>
                            </v-flex>
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
		name: "RegisterDialog",
        components: {
            PostuladorV2: resolve => {require(['../../general/PostuladorV2'], resolve)}
        },
		data: () => ({
            fechaRegistro: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            fechaInicio: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            fechaFin: {
                menu: false,
                minDate: '1900-01-01',
                maxDate: new Date().toISOString().substr(0, 10)
            },
            evento: null,
            makeEvento: {
                id: null,
                fecha_registro: null,
                fecha_inicio: null,
                fecha_fin: null,
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
                esPrincipal: 1,
                eventoPrincipal: null,
                equipo: null
            },
            tiposEvento: [],
            tiposMantenimiento: [],
            open: false,
            filterEmpleados (item, queryText) {
                const hasValue = val => val != null ? val : ''
                const text = hasValue(item.identificacion + ' ' + item.nombre)
                const query = hasValue(queryText)
                return text.toString().toLowerCase().indexOf(query.toString().toLowerCase()) > -1
            }
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
                let petition = id ? this.axios.post(`eventos/editevent`, id) : this.axios.post(`eventos/newevent`)
                petition
                    .then(response => {
                        this.$store.commit('LOADING', false)
                        this.open = true
                        if (response.data.evento) this.evento = response.data.evento
                        this.tiposEvento = response.data.tiposEvento
                        this.tiposMantenimiento = response.data.tiposMantenimiento
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
                        axios.post(`eventos/${this.evento.id ? 'registernewevent' : 'registereditevent' }`, this.evento)
                            .then(response => {
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'success', message: `evento registrado correctamente`})
                                this.$store.commit('RELOAD_TABLE', 'tablaUsuarios')
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
                this.tiposEvento = []
                this.tiposMantenimiento = []
                this.$validator.reset()
            }
        }
    }
</script>

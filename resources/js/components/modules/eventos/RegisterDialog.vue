<template>
    <v-layout>
        <v-dialog v-model="open" persistent max-width="900" scrollable>
            <v-card>
                <v-card-title>
                    <span class="headline">{{evento.id ? 'Edición de evento' : 'Nuevo evento'}}</span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-container class="pa-0" fluid grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm6 md3>
                                <v-switch
                                    :readonly="!!evento.eventos_hijos.length"
                                    ripple
                                    label="Evento principal"
                                    :false-value="0"
                                    :true-value="1"
                                    v-model="esPrincipal"
                                ></v-switch>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
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
                            <v-flex xs12 sm6 md3>
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
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="horaRegistroEvento"
                                    v-model="horaRegistro.menu"
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
                                        label="Hora registro"
                                        v-model="evento.hora_registro"
                                        name="Hora registro"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Hora registro')"
                                        readonly
                                    ></v-text-field>
                                    <v-time-picker
                                        v-if="horaRegistro.menu"
                                        v-model="evento.hora_registro"
                                        locale="es-co"
                                        format="24hr"
                                        @click:minute="$refs.horaRegistroEvento.save(evento.hora_registro)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Hora registro')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-time-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex xs12 v-if="!esPrincipal">
                                <postulador-v2
                                    ref="postuladorEventos"
                                    key="eventoP"
                                    no-data="Busqueda por número o nombre de equipo."
                                    item-value="id"
                                    item-text="id"
                                    label="Evento principal"
                                    entidad="eventos/postulador"
                                    v-model="evento.evento_padre"
                                    @changeid="val => evento.evento_padre_id = val"
                                    no-btn-create
                                    no-btn-edit
                                    name="Evento primario"
                                    route-params="filter[estado]=Registrado"
                                    rules="required"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('Evento primario')"
                                    :slot-data='{
                                      template:`
                                      <v-list-tile v-if="value.equipo">
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
                                    @changeid="val => evento.equipo_id = val"
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
                                    ref="horaInicioEvento"
                                    v-model="horaInicio.menu"
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
                                        label="Hora inicio"
                                        v-model="evento.hora_inicio"
                                        name="Hora inicio"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Hora inicio')"
                                        readonly
                                    ></v-text-field>
                                    <v-time-picker
                                        v-if="horaInicio.menu"
                                        v-model="evento.hora_inicio"
                                        locale="es-co"
                                        format="24hr"
                                        @click:minute="$refs.horaInicioEvento.save(evento.hora_inicio)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Hora inicio')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-time-picker>
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
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="horaFinEvento"
                                    v-model="horaFin.menu"
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
                                        label="Hora fin"
                                        v-model="evento.hora_fin"
                                        name="Hora fin"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Hora fin')"
                                        readonly
                                    ></v-text-field>
                                    <v-time-picker
                                        v-if="horaFin.menu"
                                        v-model="evento.hora_fin"
                                        locale="es-co"
                                        format="24hr"
                                        @click:minute="$refs.horaFinEvento.save(evento.hora_fin)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Hora fin')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-time-picker>
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
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="fechaInicioReparacion"
                                    v-model="fechaInicioReparacion.menu"
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
                                        label="fecha inicio reparación"
                                        v-model="evento.fecha_inicio_reparacion"
                                        name="fecha inicio reparación"
                                        v-validate="'required|date_format:yyyy-MM-dd|date_between:' + fechaInicioReparacion.minDate + ',' + fechaInicioReparacion.maxDate + ',true'"
                                        :error-messages="errors.collect('fecha inicio reparación')"
                                        readonly
                                    ></v-text-field>
                                    <v-date-picker
                                        ref="picker"
                                        v-model="evento.fecha_inicio_reparacion"
                                        locale="es-co"
                                        :min="fechaInicioReparacion.minDate"
                                        :max="fechaInicioReparacion.maxDate"
                                        @input="$refs.fechaInicioReparacion.save(evento.fecha_inicio_reparacion)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'fecha inicio reparacion')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-date-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="horaInicioReparacion"
                                    v-model="horaInicioReparacion.menu"
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
                                        label="Hora inicio reparación"
                                        v-model="evento.hora_inicio_reparacion"
                                        name="Hora inicio reparación"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Hora inicio reparación')"
                                        readonly
                                    ></v-text-field>
                                    <v-time-picker
                                        v-if="horaInicioReparacion.menu"
                                        v-model="evento.hora_inicio_reparacion"
                                        locale="es-co"
                                        format="24hr"
                                        @click:minute="$refs.horaInicioReparacion.save(evento.hora_inicio_reparacion)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Hora inicio reparación')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-time-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="fechaFinReparacion"
                                    v-model="fechaFinReparacion.menu"
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
                                        label="fecha fin reparación"
                                        v-model="evento.fecha_fin_reparacion"
                                        name="fecha fin reparación"
                                        v-validate="'required|date_format:yyyy-MM-dd|date_between:' + fechaFinReparacion.minDate + ',' + fechaFinReparacion.maxDate + ',true'"
                                        :error-messages="errors.collect('fecha fin reparación')"
                                        readonly
                                    ></v-text-field>
                                    <v-date-picker
                                        ref="picker"
                                        v-model="evento.fecha_fin_reparacion"
                                        locale="es-co"
                                        :min="fechaFinReparacion.minDate"
                                        :max="fechaFinReparacion.maxDate"
                                        @input="$refs.fechaFinReparacion.save(evento.fecha_fin_reparacion)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'fecha inicio reparacion')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-date-picker>
                                </v-menu>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-menu
                                    ref="horaFinReparacion"
                                    v-model="horaFinReparacion.menu"
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
                                        label="Hora fin reparación"
                                        v-model="evento.hora_fin_reparacion"
                                        name="Hora fin reparación"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Hora fin reparación')"
                                        readonly
                                    ></v-text-field>
                                    <v-time-picker
                                        v-if="horaFinReparacion.menu"
                                        v-model="evento.hora_fin_reparacion"
                                        locale="es-co"
                                        format="24hr"
                                        @click:minute="$refs.horaFinReparacion.save(evento.hora_fin_reparacion)"
                                        @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Hora fin reparación')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                    ></v-time-picker>
                                </v-menu>
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
                equipo: null
            },
            esPrincipal: 1,
            tiposEvento: [],
            tiposMantenimiento: [],
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
                let petition = id ? this.axios.post(`eventos/editevent`, {id: id}) : this.axios.post(`eventos/newevent`)
                petition
                    .then(response => {
                        if (response.data.evento) {
                            if (response.data.evento.fecha_registro) {
                                response.data.evento.hora_registro = this.moment(response.data.evento.fecha_registro).format('HH:mm')
                                response.data.evento.fecha_registro = this.moment(response.data.evento.fecha_registro).format('YYYY-MM-DD')
                            }
                            if (response.data.evento.fecha_inicio) {
                                response.data.evento.hora_inicio = this.moment(response.data.evento.fecha_inicio).format('HH:mm')
                                response.data.evento.fecha_inicio = this.moment(response.data.evento.fecha_inicio).format('YYYY-MM-DD')
                            }
                            if (response.data.evento.fecha_fin) {
                                response.data.evento.hora_fin = this.moment(response.data.evento.fecha_fin).format('HH:mm')
                                response.data.evento.fecha_fin = this.moment(response.data.evento.fecha_fin).format('YYYY-MM-DD')
                            }
                            if (response.data.evento.fecha_inicio_reparacion) {
                                response.data.evento.hora_inicio_reparacion = this.moment(response.data.evento.fecha_inicio_reparacion).format('HH:mm')
                                response.data.evento.fecha_inicio_reparacion = this.moment(response.data.evento.fecha_inicio_reparacion).format('YYYY-MM-DD')
                            }
                            if (response.data.evento.fecha_fin_reparacion) {
                                response.data.evento.hora_fin_reparacion = this.moment(response.data.evento.fecha_fin_reparacion).format('HH:mm')
                                response.data.evento.fecha_fin_reparacion = this.moment(response.data.evento.fecha_fin_reparacion).format('YYYY-MM-DD')
                            }
                            this.esPrincipal = response.data.evento.evento_padre_id ? 0 : 1
                            this.evento = response.data.evento
                        }
                        this.tiposEvento = response.data.tiposEvento
                        this.tiposMantenimiento = response.data.tiposMantenimiento
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
                this.tiposEvento = []
                this.tiposMantenimiento = []
                this.$validator.reset()
            }
        }
    }
</script>

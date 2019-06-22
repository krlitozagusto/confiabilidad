<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12 sm6 md3>
            <v-switch
                :readonly="!!value.eventos_hijos.length"
                ripple
                label="value principal"
                :false-value="0"
                :true-value="1"
                v-model="esPrincipal"
            ></v-switch>
        </v-flex>
        <v-flex xs12 sm6 md3>
            <v-select
                label="Tipo"
                :items="complementos.tiposEvento"
                v-model="value.tipo_evento_id"
                item-value="id"
                item-text="nombre"
                name="Tipo"
                v-validate="'required'"
                :error-messages="errors.collect('Tipo')"
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
                    v-model="value.fecha_registro"
                    name="Fecha registro"
                    v-validate="'required|date_format:yyyy-MM-dd|date_between:' + fechaRegistro.minDate + ',' + fechaRegistro.maxDate + ',true'"
                    :error-messages="errors.collect('Fecha registro')"
                    readonly
                ></v-text-field>
                <v-date-picker
                    ref="picker"
                    v-model="value.fecha_registro"
                    locale="es-co"
                    :min="fechaRegistro.minDate"
                    :max="fechaRegistro.maxDate"
                    @input="$refs.fechaRegistroEvento.save(value.fecha_registro)"
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
                    v-model="value.hora_registro"
                    name="Hora registro"
                    v-validate="value.fecha_registro ? 'required' : ''"
                    :error-messages="errors.collect('Hora registro')"
                    readonly
                ></v-text-field>
                <v-time-picker
                    v-if="horaRegistro.menu"
                    v-model="value.hora_registro"
                    locale="es-co"
                    format="24hr"
                    @click:minute="$refs.horaRegistroEvento.save(value.hora_registro)"
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
                v-model="value.evento_padre"
                @changeid="val => value.evento_padre_id = val"
                no-btn-create
                no-btn-edit
                name="evento principal"
                route-params="filter[estado]=Registrado"
                rules="required"
                v-validate="'required'"
                :error-messages="errors.collect('evento principal')"
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
                v-model="value.equipo"
                @changeid="val => value.equipo_id = val"
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
                                          <v-list-tile-sub-title class=caption>Tag: {{ value.tag }}</v-list-tile-sub-title>
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
                    v-model="value.fecha_inicio"
                    name="Fecha inicio"
                    v-validate="'required|date_format:yyyy-MM-dd|date_between:' + fechaInicio.minDate + ',' + fechaInicio.maxDate + ',true'"
                    :error-messages="errors.collect('Fecha inicio')"
                    readonly
                ></v-text-field>
                <v-date-picker
                    ref="picker"
                    v-model="value.fecha_inicio"
                    locale="es-co"
                    :min="fechaInicio.minDate"
                    :max="fechaInicio.maxDate"
                    @input="$refs.fechaInicioEvento.save(value.fecha_inicio)"
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
                    v-model="value.hora_inicio"
                    name="Hora inicio"
                    v-validate="value.fecha_inicio ? 'required' : ''"
                    :error-messages="errors.collect('Hora inicio')"
                    readonly
                ></v-text-field>
                <v-time-picker
                    v-if="horaInicio.menu"
                    v-model="value.hora_inicio"
                    locale="es-co"
                    format="24hr"
                    @click:minute="$refs.horaInicioEvento.save(value.hora_inicio)"
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
                    v-model="value.fecha_fin"
                    name="fecha fin"
                    v-validate="(soloGuardar ? '' : 'required|') + 'date_format:yyyy-MM-dd|date_between:' + fechaFin.minDate + ',' + fechaFin.maxDate + ',true'"
                    :error-messages="errors.collect('fecha fin')"
                    readonly
                ></v-text-field>
                <v-date-picker
                    ref="picker"
                    v-model="value.fecha_fin"
                    locale="es-co"
                    :min="fechaFin.minDate"
                    :max="fechaFin.maxDate"
                    @input="$refs.fechaFinEvento.save(value.fecha_fin)"
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
                    v-model="value.hora_fin"
                    name="Hora fin"
                    v-validate="value.fecha_fin ? 'required' : ''"
                    :error-messages="errors.collect('Hora fin')"
                    readonly
                ></v-text-field>
                <v-time-picker
                    v-if="horaFin.menu"
                    v-model="value.hora_fin"
                    locale="es-co"
                    format="24hr"
                    @click:minute="$refs.horaFinEvento.save(value.hora_fin)"
                    @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Hora fin')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                ></v-time-picker>
            </v-menu>
        </v-flex>
        <v-flex xs12 sm6 md9>
            <v-select
                label="Tipo mantenimiento"
                :items="complementos.tiposMantenimiento"
                v-model="value.tipo_mantenimiento_id"
                item-value="id"
                item-text="nombre"
                name="Tipo mantenimiento"
                v-validate="'required'"
                :error-messages="errors.collect('Tipo mantenimiento')"
            ></v-select>
        </v-flex>
        <v-flex xs12 sm6 md3>
            <v-switch
                ripple
                label="Contractual"
                :false-value="0"
                :true-value="1"
                v-model="value.contractual"
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
                    v-model="value.fecha_inicio_reparacion"
                    name="fecha inicio reparación"
                    v-validate="(soloGuardar ? '' : 'required|') + 'date_format:yyyy-MM-dd|date_between:' + fechaInicioReparacion.minDate + ',' + fechaInicioReparacion.maxDate + ',true'"
                    :error-messages="errors.collect('fecha inicio reparación')"
                    readonly
                ></v-text-field>
                <v-date-picker
                    ref="picker"
                    v-model="value.fecha_inicio_reparacion"
                    locale="es-co"
                    :min="fechaInicioReparacion.minDate"
                    :max="fechaInicioReparacion.maxDate"
                    @input="$refs.fechaInicioReparacion.save(value.fecha_inicio_reparacion)"
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
                    v-model="value.hora_inicio_reparacion"
                    name="Hora inicio reparación"
                    v-validate="value.fecha_inicio_reparacion ? 'required' : ''"
                    :error-messages="errors.collect('Hora inicio reparación')"
                    readonly
                ></v-text-field>
                <v-time-picker
                    v-if="horaInicioReparacion.menu"
                    v-model="value.hora_inicio_reparacion"
                    locale="es-co"
                    format="24hr"
                    @click:minute="$refs.horaInicioReparacion.save(value.hora_inicio_reparacion)"
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
                    v-model="value.fecha_fin_reparacion"
                    name="fecha fin reparación"
                    v-validate="(soloGuardar ? '' : 'required|') + 'date_format:yyyy-MM-dd|date_between:' + fechaFinReparacion.minDate + ',' + fechaFinReparacion.maxDate + ',true'"
                    :error-messages="errors.collect('fecha fin reparación')"
                    readonly
                ></v-text-field>
                <v-date-picker
                    ref="picker"
                    v-model="value.fecha_fin_reparacion"
                    locale="es-co"
                    :min="fechaFinReparacion.minDate"
                    :max="fechaFinReparacion.maxDate"
                    @input="$refs.fechaFinReparacion.save(value.fecha_fin_reparacion)"
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
                    v-model="value.hora_fin_reparacion"
                    name="Hora fin reparación"
                    v-validate="value.fecha_fin_reparacion ? 'required' : ''"
                    :error-messages="errors.collect('Hora fin reparación')"
                    readonly
                ></v-text-field>
                <v-time-picker
                    v-if="horaFinReparacion.menu"
                    v-model="value.hora_fin_reparacion"
                    locale="es-co"
                    format="24hr"
                    @click:minute="$refs.horaFinReparacion.save(value.hora_fin_reparacion)"
                    @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'Hora fin reparación')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                ></v-time-picker>
            </v-menu>
        </v-flex>
        <v-flex xs12 v-if="value.id">
            <input-file
                class="mb-2"
                label="Soporte"
                v-model="value.archivo_soporte"
                accept="application/pdf"
                hint="Extenciones aceptadas: pdf"
                prepend-icon="description"
                :loading="loadingFile"
                @input="cargaSoporte(value.archivo_soporte)"
                :append-button="value.archivo_soporte ? {tooltip: 'descargar archivo', icon: 'arrow_downward', color: 'primary'} : null"
                @appendButtonClick="downloadFile('/eventos/downloadsoporte/' + value.id)"
            ></input-file>
        </v-flex>
    </v-layout>
</template>
<script>
    import InputFile from '../../../../general/InputFile'
    export default {
        props: ['value', 'esPrincipal', 'soloGuardar', 'complementos'],
		name: "RegisterGeneral",
        components: {
            PostuladorV2: resolve => {require(['../../../../general/PostuladorV2'], resolve)},
            InputFile
        },
		data: () => ({
            loadingFile: false,
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
            }
		}),
		computed: {
		},
        whatch: {
        },
		created () {
		},
        methods: {
            cargaSoporte (file) {
                this.loadingFile = true
                let data = new FormData()
                data.append('evento_id', this.value.id)
                data.append('archivo_soporte', file)
                this.axios.post(`eventos/loadfile`, data)
                    .then(response => {
                        this.value.archivo_soporte = response.data.archivo
                        this.loadingFile = false
                    })
                    .catch(e => {
                        this.loadingFile = false
                        this.$nextTick(() => {
                            this.value.archivo_soporte = null
                        })
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al cargar el archivo`, error: e})
                    })
            },
            async reset () {
                await this.$validator.reset()
            }
        }
    }
</script>

<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition" persistent>
            <v-card>
                <v-card-title class="py-0">
                    <span class="headline">Disponibilidad</span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="dialog = false">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container class="pa-0" fluid grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm4 md2>
                                <v-select
                                    :items="['Equipo','Sistema', 'Planta']"
                                    label="Taxonomía"
                                    v-model="data.tipoTaxonomia"
                                ></v-select>
                            </v-flex>
                            <v-flex xs12 sm8 md6>
                                <postulador-v2
                                    v-if="data.tipoTaxonomia === 'Equipo'"
                                    key="postulaEquipo"
                                    ref="postuladorEquipos"
                                    no-data="Busqueda por nombre, tag, ubucación técnica o número de equipo."
                                    item-text="nombre"
                                    hint="tag"
                                    label="Equipo"
                                    entidad="equipos/postulador"
                                    v-model="data.taxonomia"
                                    @changeid="val => data.taxonomia_id = val"
                                    no-btn-create
                                    no-btn-edit
                                    name="Equipo"
                                    rules="required"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('Equipo')"
                                    :slot-item='{
                                      template:`
                                      <v-list-tile class="content-v-list-tile-p0" style="width: 100% !important">
                                        <v-list-tile-content>
                                          <v-list-tile-title>{{value.nombre}}</v-list-tile-title>
                                          <v-list-tile-sub-title class=caption>Tag: {{ value.tag }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                      </v-list-tile>
                                      `,
                                      props: [`value`]
                                     }'
                                ></postulador-v2>
                                <postulador-v2
                                    v-if="data.tipoTaxonomia === 'Sistema'"
                                    key="postulaSistema"
                                    ref="postuladorSistemas"
                                    no-data="Busqueda por nombre, tag o nombre de planta."
                                    item-text="nombre"
                                    hint="tag"
                                    label="Sistema"
                                    entidad="sistemas/postulador"
                                    v-model="data.taxonomia"
                                    @changeid="val => data.taxonomia_id = val"
                                    no-btn-create
                                    no-btn-edit
                                    name="Sistema"
                                    rules="required"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('Sistema')"
                                    :slot-item='{
                                      template:`
                                      <v-list-tile class="content-v-list-tile-p0" style="width: 100% !important">
                                        <v-list-tile-content>
                                          <v-list-tile-title>{{value.nombre}}</v-list-tile-title>
                                          <v-list-tile-sub-title class=caption>Tag: {{ value.tag }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                      </v-list-tile>
                                      `,
                                      props: [`value`]
                                     }'
                                ></postulador-v2>
                                <postulador-v2
                                    v-if="data.tipoTaxonomia === 'Planta'"
                                    key="postulaPlanta"
                                    ref="postuladorPlantas"
                                    no-data="Busqueda por nombre, emplazamiento o nombre de campo."
                                    item-text="nombre"
                                    hint="emplazamiento"
                                    label="Planta"
                                    entidad="plantas/postulador"
                                    v-model="data.taxonomia"
                                    @changeid="val => data.taxonomia_id = val"
                                    no-btn-create
                                    no-btn-edit
                                    name="Planta"
                                    rules="required"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('Planta')"
                                    :slot-item='{
                                      template:`
                                      <v-list-tile class="content-v-list-tile-p0" style="width: 100% !important">
                                        <v-list-tile-content>
                                          <v-list-tile-title>{{value.nombre}}</v-list-tile-title>
                                          <v-list-tile-sub-title class=caption>Emplazamiento: {{ value.emplazamiento }}</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                      </v-list-tile>
                                      `,
                                      props: [`value`]
                                     }'
                                ></postulador-v2>
                            </v-flex>
                            <v-flex xs12 sm12 md2>
                                <v-layout row wrap style="position: absolute !important;">
                                    <label>Fecha inicio</label>
                                </v-layout>
                                <v-layout row wrap style="margin-top: -4px !important;">
                                    <v-flex xs12 sm6 md6>
                                        <v-menu
                                            ref="fechaInicio"
                                            v-model="menuInicio"
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
                                                placeholder="Fecha"
                                                v-model="data.fechaInicio"
                                                name="fecha inicio"
                                                v-validate="'required|date_format:yyyy-MM-dd|date_between:' + minDate + ',' + maxDate + ',true'"
                                                :error-messages="errors.collect('fecha inicio')"
                                                readonly
                                            ></v-text-field>
                                            <v-date-picker
                                                ref="picker"
                                                v-model="data.fechaInicio"
                                                locale="es-co"
                                                :min="minDate"
                                                :max="maxDate"
                                                @input="$refs.fechaInicio.save(data.fechaInicio)"
                                                @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'fecha inicio')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-flex>
                                    <v-flex xs12 sm6 md6>
                                        <v-menu
                                            ref="menuHoraInicio"
                                            v-model="menuHoraInicio"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            :return-value.sync="data.horaInicio"
                                            lazy
                                            transition="scale-transition"
                                            offset-y
                                            full-width
                                            max-width="290px"
                                            min-width="290px"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.horaInicio"
                                                    placeholder="Hora"
                                                    readonly
                                                    v-on="on"
                                                    name="hora inicio"
                                                    v-validate="'required'"
                                                    :error-messages="errors.collect('hora inicio')"
                                                ></v-text-field>
                                            </template>
                                            <v-time-picker
                                                v-if="menuHoraInicio"
                                                v-model="data.horaInicio"
                                                format="24hr"
                                                full-width
                                                @click:minute="$refs.menuHoraInicio.save(data.horaInicio)"
                                            ></v-time-picker>
                                        </v-menu>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12 sm12 md2>
                                <v-layout row wrap style="position: absolute !important;">
                                    <label>Fecha fin</label>
                                </v-layout>
                                <v-layout row wrap style="margin-top: -4px !important;">
                                    <v-flex xs12 sm6 md6>
                                        <v-menu
                                            ref="fechaFin"
                                            v-model="menuFin"
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
                                                placeholder="Fecha"
                                                v-model="data.fechaFin"
                                                name="fecha fin"
                                                v-validate="'required|date_format:yyyy-MM-dd|date_between:' + minDate + ',' + maxDate + ',true'"
                                                :error-messages="errors.collect('fecha fin')"
                                                readonly
                                            ></v-text-field>
                                            <v-date-picker
                                                ref="picker"
                                                v-model="data.fechaFin"
                                                locale="es-co"
                                                :min="minDate"
                                                :max="maxDate"
                                                @input="$refs.fechaFin.save(data.fechaFin)"
                                                @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'fecha fin')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-flex>
                                    <v-flex xs12 sm6 md6>
                                        <v-menu
                                            ref="menuHoraFin"
                                            v-model="menuHoraFin"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            :return-value.sync="data.horaFin"
                                            lazy
                                            transition="scale-transition"
                                            offset-y
                                            full-width
                                            max-width="290px"
                                            min-width="290px"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.horaFin"
                                                    placeholder="Hora"
                                                    readonly
                                                    v-on="on"
                                                    name="hora fin"
                                                    v-validate="'required'"
                                                    :error-messages="errors.collect('hora fin')"
                                                ></v-text-field>
                                            </template>
                                            <v-time-picker
                                                v-if="menuHoraFin"
                                                v-model="data.horaFin"
                                                format="24hr"
                                                full-width
                                                @click:minute="$refs.menuHoraFin.save(data.horaFin)"
                                            ></v-time-picker>
                                        </v-menu>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <v-select
                                    label="Tipo de evento"
                                    :items="tiposEvento"
                                    item-value="id"
                                    item-text="nombre"
                                    v-model="data.tipoEvento"
                                    name="tipo de evento"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('tipo de evento')"
                                    multiple
                                    chips
                                    deletable-chips
                                    small-chips
                                    hide-selected
                                    no-data-text="No hay tipos de evento para seleccionar"
                                ></v-select>
                            </v-flex>
                            <v-flex xs12 sm4 md2>
                                <v-switch
                                    label="Multiples rangos"
                                    v-model="data.rangos"
                                ></v-switch>
                            </v-flex>
                            <template v-if="data.rangos">
                                <v-flex xs12 sm4 md2>
                                    <v-select
                                        :items="['Acumulado','Periódico']"
                                        label="Tipo resultado"
                                        v-model="data.tipoResultado"
                                    ></v-select>
                                </v-flex>
                                <v-flex xs12 sm4 md2>
                                    <v-layout row wrap style="position: absolute !important;">
                                        <label>Frecuencia</label>
                                    </v-layout>
                                    <v-layout row wrap style="margin-top: -4px !important;">
                                        <v-flex xs12 sm6 md6>
                                            <v-text-field
                                                type="number"
                                                min="1"
                                                v-model.number="data.frecuenciaCantidad"
                                                name="cantidad frecuencia"
                                                v-validate="'required|numeric|min_value:1'"
                                                :error-messages="errors.collect('cantidad frecuencia')"
                                            ></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm6 md6>
                                            <v-select
                                                :items="[{value: 'days', text: 'Día(s)'}, {value: 'months', text: 'Mes(es)'}]"
                                                v-model="data.frecuenciaTipo"
                                                item-value="value"
                                                item-text="text"
                                                name="tipo frecuencia"
                                                v-validate="'required'"
                                                :error-messages="errors.collect('tipo frecuencia')"
                                            ></v-select>
                                        </v-flex>
                                    </v-layout>
                                </v-flex>
                            </template>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn flat :loading="loading" @click="dialog = false">Cerrar</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" :loading="loading" class="white--text" :disabled="!!result" @click="submit">Ejecutar</v-btn>
                </v-card-actions>

                <v-card-text>
                    <v-container class="pa-0" fluid grid-list-md>
                        <template v-if="result">
                            <template v-if="result.request">
                                <result-rangos-multiples v-model="result"></result-rangos-multiples>
                            </template>
                            <template v-else>
                                <result-equipo v-if="result.equipo" :result="result"></result-equipo>
                                <result-sistema v-if="result.sistema" :result="result"></result-sistema>
                                <result-planta v-if="result.planta" :result="result"></result-planta>
                            </template>
                        </template>
                        <v-flex xs12 v-else>
                            <div class="title text-xs-center grey--text" >Click en ejecutar para obtener resultados.</div>
                        </v-flex>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
    import ResultEquipo from './disponibilidad/ResultEquipo'
    import ResultSistema from './disponibilidad/ResultSistema'
    import ResultPlanta from './disponibilidad/ResultPlanta'
    import ResultRangosMultiples from './disponibilidad/ResultRangosMultiples'
	export default {
		name: 'Disponibilidad',
        components: {
            PostuladorV2: resolve => {require(['../../../general/PostuladorV2'], resolve)},
            ResultEquipo,
            ResultSistema,
            ResultPlanta,
            ResultRangosMultiples
        },
        data: () => ({
            loading: false,
            dialog: false,
            menuInicio: false,
            menuFin: false,
            menuHoraInicio: false,
            menuHoraFin: false,
            minDate: '1900-01-01',
            maxDate: new Date().toISOString().substr(0, 10),
            result: null,
            data: {
                fechaInicio: null,
                fechaFin: null,
                horaInicio: null,
                horaFin: null,
                taxonomia: null,
                taxonomia_id: null,
                tipoEvento: [],
                tipoTaxonomia: 'Equipo',
                rangos: false,
                tipoResultado: 'Periódico',
                frecuencia: '',
                frecuenciaTipo: 'months',
                frecuenciaCantidad: 1
            },
            tiposEvento: []
        }),
        watch: {
            'data.frecuencia' (val) {
                val && (this.result = null)
            },
            'data.tipoResultado' (val) {
                val && (this.result = null)
            },
            'data.rangos' (val) {
                this.result = null
            },
		    'data.tipoTaxonomia' (val) {
                this.data.taxonomia = null
                this.data.taxonomia_id = null
                val && (this.result = null)
            },
		  'data.taxonomia_id' (val) {
              val && (this.result = null)
          },
            'data.fechaInicio' (val) {
              val && (this.result = null)
          },
            'data.fechaFin' (val) {
              val && (this.result = null)
          },
            'data.horaInicio' (val) {
                val && (this.result = null)
            },
            'data.horaFin' (val) {
                val && (this.result = null)
            }
        },
        created () {
		    this.getTiposEvento()
        },
        methods: {
		    open () {
		        this.dialog = true
            },
            submit () {
                this.result = null
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.loading = true
                        this.data.frecuencia = `${this.data.frecuenciaCantidad} ${this.data.frecuenciaTipo}`
                        axios.post(`reportes/tiempomedio`, this.data)
                            .then(response => {
                                console.log('el response del reporte', response.data)
                                this.result = response.data
                                this.loading = false
                            })
                            .catch(error => {
                                this.loading = false
                                this.$store.commit('SNACKBAR', {color: 'error', message: `al generar el reporte`, error: error})
                            })
                    }
                })
            },
            getTiposEvento () {
                axios.post(`eventos/tipos`)
                    .then(response => {
                        console.log('el response de los tipos', response.data)
                        this.tiposEvento = response.data.tiposEvento
                    })
                    .catch(error => {
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al traer los tipos de evento`, error: error})
                    })
            }
        }
	}
</script>

<style scoped>

</style>

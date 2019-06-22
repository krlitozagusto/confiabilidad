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
                                    :items="['Equipo','Sistema']"
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
                                    :slot-data='{
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
                                    :slot-data='{
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
                            </v-flex>
                            <v-flex xs12 sm12 md2>
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
                                        label="Fecha inicio"
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
                            <v-flex xs12 sm12 md2>
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
                                        label="Fecha fin"
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
                                    <v-select
                                        :items="[{id: 1, value: 15, tipo: 'Día', text: '15 días'}, {id: 2, value: 1, tipo: 'Mes', text: '1 mes'}, {id: 3, value: 2, tipo: 'Mes', text: '2 meses'}, {id: 4, value: 3, tipo: 'Mes', text: '3 meses'}, {id: 5, value: 6, tipo: 'Mes', text: '6 meses'}, {id: 6, value: 12, tipo: 'Mes', text: '12 meses'}]"
                                        label="Frecuencia"
                                        v-model="data.frecuencia"
                                        item-value="id"
                                        item-text="text"
                                        return-object
                                    ></v-select>
                                </v-flex>
                            </template>
                        </v-layout>
                        <template v-if="result">
                            <result-equipo v-if="result.equipo" :result="result"></result-equipo>
                            <result-sistema v-if="result.sistema" :result="result"></result-sistema>
                        </template>
                        <v-flex xs12 v-else>
                            <div class="title text-xs-center grey--text" >Click en ejecutar para obtener resultados.</div>
                        </v-flex>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn flat :loading="loading" @click="dialog = false">Cerrar</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" :loading="loading" class="white--text" @click="submit">Ejecutar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
    import ResultEquipo from './disponibilidad/ResultEquipo'
    import ResultSistema from './disponibilidad/ResultSistema'
	export default {
		name: 'Disponibilidad',
        components: {
            PostuladorV2: resolve => {require(['../../../general/PostuladorV2'], resolve)},
            ResultEquipo,
            ResultSistema
        },
        data: () => ({
            loading: false,
            dialog: false,
            menuInicio: false,
            menuFin: false,
            minDate: '1900-01-01',
            maxDate: new Date().toISOString().substr(0, 10),
            result: null,
            data: {
                fechaInicio: null,
                fechaFin: null,
                taxonomia: null,
                taxonomia_id: null,
                tipoTaxonomia: 'Equipo',
                rangos: false,
                tipoResultado: 'Acumulado',
                frecuencia: {id: 2, value: 1, tipo: 'Mes', text: '1 mes'}
            }
        }),
        watch: {
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
          }
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
            }
        }
	}
</script>

<style scoped>

</style>

<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" scrollable persistent max-width="900px">
            <v-card>
                <v-card-title class="title"><strong>Tiempo medio</strong></v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container class="pa-0" fluid grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <postulador-v2
                                    ref="postuladorEquipos"
                                    no-data="Busqueda por nombre, tag, ubucación técnica o número de equipo."
                                    item-text="nombre"
                                    label="Equipo"
                                    entidad="equipos/postulador"
                                    v-model="data.equipo"
                                    @changeid="val => data.equipo_id = val"
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
                                    :slot-selection='{
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
                            <v-flex xs12 sm12 md6>
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
                            <v-flex xs12 sm12 md6>
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
                        </v-layout>
                        <v-expansion-panel v-if="result">
                            <v-expansion-panel-content>
                                <template slot="header">
                                    <span class="body-2">Disponibilidad: {{result.disponibilidad}}</span>
                                    <span class="body-2">MTBF: {{result.total_tiempo_mtbf}}</span>
                                    <span class="body-2">MTTR: {{result.total_tiempo_mttr}}</span>
                                </template>
                                <v-divider></v-divider>
                                <v-card>
                                    <v-card-title><strong>Tiempo intervalo: {{result.total_tiempo_intervalo}}</strong></v-card-title>
                                    <v-data-table
                                            v-if="result.registros_fallas"
                                        :headers="headers"
                                        :items="result.registros_fallas"
                                        no-data-text="No hay eventos en el rango seleccionado."
                                        hide-actions
                                    >
                                        <template slot="items" slot-scope="props">
                                            <td>{{props.item.tipo_evento.nombre}}</td>
                                            <td>
                                                <span><strong>Inicio: </strong>{{props.item.fecha_inicio ? moment(props.item.fecha_inicio).format('YYYY-MM-DD HH:mm') : ''}}</span><br/>
                                                <span><strong>Fin: </strong>{{props.item.fecha_fin ? moment(props.item.fecha_fin).format('YYYY-MM-DD HH:mm') : ''}}</span>
                                            </td>
                                            <td>
                                                <span><strong>Inicio: </strong>{{props.item.fecha_inicio_reparacion ? moment(props.item.fecha_inicio_reparacion).format('YYYY-MM-DD HH:mm') : ''}}</span><br/>
                                                <span><strong>Fin: </strong>{{props.item.fecha_fin_reparacion ? moment(props.item.fecha_fin_reparacion).format('YYYY-MM-DD HH:mm') : ''}}</span>
                                            </td>
                                            <td>{{props.item.tiempo_falla}}</td>
                                            <td>{{props.item.tiempo_reparacion}}</td>
                                        </template>
                                        <template slot="footer">
                                            <td :colspan="3" class="text-xs-right"><strong>Total</strong></td>
                                            <td :colspan="1">{{result.total_tiempo_falla}}</td>
                                            <td :colspan="1">{{result.total_tiempo_reparacion}}</td>
                                        </template>
                                    </v-data-table>
                                </v-card>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
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
	export default {
		name: 'TiempoMedio',
        components: {
            PostuladorV2: resolve => {require(['../../../general/PostuladorV2'], resolve)}
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
                equipo: null,
                equipo_id: null
            },
            headers: [
                {
                    text: 'Tipo',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Fechas evento',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Fechas reparación',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Tiempo falla',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Tiempo reparación',
                    align: 'left',
                    sortable: false
                }
            ]
        }),
        watch: {
		  'data.equipo_id' (val) {
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

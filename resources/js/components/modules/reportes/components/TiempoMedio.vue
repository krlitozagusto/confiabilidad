<template>
    <v-layout row justify-center>
        <v-dialog v-model="dialog" scrollable persistent max-width="600px">
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
                                      <v-list-tile>
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
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn flat @click="dialog = false">Cerrar</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" class="white--text" @click="submit">Ejecutar</v-btn>
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
            dialog: false,
            menuInicio: false,
            menuFin: false,
            minDate: '1900-01-01',
            maxDate: new Date().toISOString().substr(0, 10),
            data: {
                fechaInicio: null,
                fechaFin: null,
                equipo: null,
                equipo_id: null
            }
        }),
        methods: {
		    open () {
		        this.dialog = true
            },
            submit () {
                axios.post(`reportes/tiempomedio`, this.data)
                    .then(response => {
                        console.log('el response del reporte', response)
                    })
                    .catch(error => {
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al generar el reporte`, error: error})
                    })
            }
        }
	}
</script>

<style scoped>

</style>

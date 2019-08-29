<template>
    <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition" persistent>
        <template v-slot:activator="{ on }">
            <v-btn color="green" dark v-on="on">
                <v-icon left>fas fa-file-excel</v-icon>
                Generar listado
            </v-btn>
        </template>
        <v-card>
            <v-card-title class="py-0">
                <span class="headline">Reporte de eventos</span>
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
                        <v-flex xs12 sm12 md4>
                            <v-layout row wrap style="position: absolute !important;">
                                <label>Fecha inicio de evento</label>
                            </v-layout>
                            <v-layout row wrap style="margin-top: -4px !important;">
                                <v-flex xs12 sm6 md6>
                                    <v-menu
                                        ref="fechaRangoInicial"
                                        v-model="menuRangoInicial"
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
                                            single-line
                                            label="Rango inicial"
                                            v-model="data.rangoInicial"
                                            name="rango inicial"
                                            v-validate="'required|date_format:yyyy-MM-dd|date_between:' + minDate + ',' + maxDate + ',true'"
                                            :error-messages="errors.collect('rango inicial')"
                                            readonly
                                        ></v-text-field>
                                        <v-date-picker
                                            ref="picker"
                                            v-model="data.rangoInicial"
                                            locale="es-co"
                                            :min="minDate"
                                            :max="maxDate"
                                            @input="$refs.fechaRangoInicial.save(data.rangoInicial)"
                                            @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'rango inicial')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                                <v-flex xs12 sm6 md6>
                                    <v-menu
                                        ref="fechaRangoFinal"
                                        v-model="menuRangoFinal"
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
                                            single-line
                                            label="Rango final"
                                            v-model="data.rangoFinal"
                                            name="rango final"
                                            v-validate="'required|date_format:yyyy-MM-dd|date_between:' + minDate + ',' + maxDate + ',true'"
                                            :error-messages="errors.collect('rango final')"
                                            readonly
                                        ></v-text-field>
                                        <v-date-picker
                                            ref="picker"
                                            v-model="data.rangoFinal"
                                            locale="es-co"
                                            :min="minDate"
                                            :max="maxDate"
                                            @input="$refs.fechaRangoFinal.save(data.rangoFinal)"
                                            @change="() => {
                                          let index = $validator.errors.items.findIndex(x => x.field === 'rango final')
                                          $validator.errors.items.splice((index !== -1) ? index : 0, (index !== -1) ? 1 : 0)
                                        }"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-flex>
                            </v-layout>
                        </v-flex>
                    </v-layout>
                    <result-list v-if="result" :result="result"></result-list>
                    <v-flex xs12 v-else>
                        <div v-if="loading" class="title text-xs-center grey--text" >Procesando...</div>
                        <div v-else class="title text-xs-center grey--text" >Click en ejecutar para obtener resultados.</div>
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
</template>

<script>
    import ResultList from './ResultList'
	export default {
		name: 'DinamicList',
        components: {
            PostuladorV2: resolve => {require(['../../general/PostuladorV2'], resolve)},
            ResultList
        },
        data: () => ({
            loading: false,
            dialog: false,
            menuRangoInicial: false,
            menuRangoFinal: false,
            minDate: '1900-01-01',
            maxDate: new Date().toISOString().substr(0, 10),
            result: null,
            data: {
                rangoInicial: null,
                rangoFinal: null,
                taxonomia: null,
                taxonomia_id: null,
                tipoTaxonomia: 'Equipo'
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
            'data.rangoInicial' (val) {
              val && (this.result = null)
          },
            'data.rangoFinal' (val) {
              val && (this.result = null)
          }
        },
        methods: {
            submit () {
                this.result = null
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.loading = true
                        axios.post(`eventos/dinamiclist`, this.data)
                            .then(response => {
                                console.log('el response del reporte', response.data)
                                this.result = response.data.data
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

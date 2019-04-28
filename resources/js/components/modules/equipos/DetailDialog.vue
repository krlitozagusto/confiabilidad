<template>
    <v-layout row justify-center>
        <v-dialog v-model="open" fullscreen hide-overlay transition="dialog-bottom-transition" persistent>
            <v-card v-if="equipo">
                <v-card-title>
                    <span class="headline">Detalle del equipo No. <strong>{{equipo.id}}</strong></span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container class="pa-0" fluid grid-list-md>
                        <v-layout>
                            <v-flex xs12>
                                <v-tabs
                                    v-model="tabActiva"
                                    centered
                                    color="cyan"
                                    slider-color="yellow"
                                    dark
                                >
                                    <v-tab
                                        v-for="(tab, index) in tabs"
                                        :key="index"
                                        :href="`#tab-${index}`"
                                    >
                                        {{ tab.title }}
                                    </v-tab>
                                </v-tabs>
                                <v-tabs-items v-model="tabActiva">
                                    <v-tab-item
                                        v-for="(tab, index) in tabs"
                                        :key="index"
                                        :value="`tab-${index}`"
                                        lazy
                                    >
                                        <v-card flat>
                                            <v-card-text>
                                                <component :is="tab.component" v-model="equipo" :type="tab.type"></component>
                                            </v-card-text>
                                        </v-card>
                                    </v-tab-item>
                                </v-tabs-items>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn flat @click="close">Cerrar</v-btn>
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
            tabActiva: '0',
            tabs: [],
            equipo: null,
            makeEquipo: {
                id: null,
                nombre: null,
                denominacion: null,
                descripcion: null,
                tag: null,
                numero_equipo: null,
                valoracion_ram_id: null,
                centro_costo_id: null,
                ubicacion_tecnica_id: null,
                // Auxiliares
                eventos: [],
                valoracion_ram: null,
                centro_costo: null,
                ubicacion_tecnica: null,
                planes_mantenimiento: []
            },
            open: false
		}),
		computed: {
		},
        whatch: {
        },
		created () {
		    this.resetModel()
            this.tabs.push({type: 'Detalle', title: 'Datos generales', component: resolve => {require(['./components/DetailGeneral'], resolve)}})
            this.tabs.push({type: 'Detalle', title: 'Eventos', component: resolve => {require(['./components/DetailEventos'], resolve)}})
		},
        methods: {
            register (id, type = 'Detalle') {
                this.open = true
                this.$store.commit('LOADING', true)
                this.axios.post(`equipos/get`, {id: id})
                    .then(response => {
                        this.equipo = response.data.equipo
                        this.$store.commit('LOADING', false)
                        this.tabActiva = 'tab-0'
                        this.open = true
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al traer los datos del equipo ${id}`, error: error})
                    })
            },
            close () {
                this.open = false
                setTimeout(() => {
                    this.resetModel()
                }, 300)
            },
            resetModel () {
                this.equipo = window.lodash.clone(this.makeEquipo)
                this.$validator.reset()
            }
        }
    }
</script>

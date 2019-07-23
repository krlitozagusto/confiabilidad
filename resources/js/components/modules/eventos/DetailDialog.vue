<template>
    <v-layout row justify-center>
        <v-dialog v-model="open" fullscreen hide-overlay transition="dialog-bottom-transition" persistent>
            <v-card>
                <v-card-title class="py-0">
                    <span class="headline">Detalle del evento No. <strong>{{evento.id}}</strong></span>
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
                                                <component :is="tab.component" v-model="evento" :es-principal="esPrincipal" :type="tab.type"></component>
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
    import DetailGeneral from './components/detail/DetailGeneral'
    import DetailOrdenTrabajo from './components/detail/DetailOrdenTrabajo'
    import DetailFallas from './components/detail/DetailFallas'
    import DetailImpactos from './components/detail/DetailImpactos'
    import DetailGastos from './components/detail/DetailGastos'
    import DetailComentarios from './components/detail/DetailComentarios'
    import DetailSecundarios from './components/detail/DetailSecundarios'
    export default {
		name: "DetailDialog",
        components: {
        },
		data: () => ({
            tabActiva: 'tab-0',
            tabs: [],
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
                tipo_evento_id: null,
                tipo_mantenimiento_id: null,
                equipo_id: null,
                evento_padre_id: null,
                user_id: null,
                // Auxiliares
                eventos_hijos: [],
                fallas: [],
                impactos: [],
                orden_trabajos: [],
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
        },
		created () {
		    this.resetModel()
		},
        methods: {
            register (id, type = 'Detalle') {
                this.$store.commit('LOADING', true)
                this.axios.post(`eventos/get`, {id: id})
                    .then(response => {
                        this.esPrincipal = response.data.evento.evento_padre_id ? 0 : 1
                        if (type === 'Comentarios') this.tabs.push({type: type, title: 'Comentarios', component: DetailComentarios})
                        this.tabs.push({type: type, title: 'Datos generales', component: DetailGeneral})
                        if (response.data.evento.eventos_hijos.length) this.tabs.push({type: type, title: 'Eventos secundarios', component: DetailSecundarios})
                        this.tabs.push({type: type, title: 'Orden de trabajo', component: DetailOrdenTrabajo})
                        this.tabs.push({type: type, title: 'Fallas', component: DetailFallas})
                        this.tabs.push({type: type, title: 'Impactos', component: DetailImpactos})
                        this.tabs.push({type: type, title: 'Gastos', component: DetailGastos})
                        if (type !== 'Comentarios')this.tabs.push({type: type, title: 'Comentarios', component: DetailComentarios})
                        this.evento = response.data.evento
                        this.$store.commit('LOADING', false)
                        this.open = true
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al traer los datos del evento ${id}`, error: error})
                    })
            },
            close () {
                this.open = false
                setTimeout(() => {
                    this.resetModel()
                }, 300)
            },
            resetModel () {
                this.evento = window.lodash.clone(this.makeEvento)
                this.tabs = []
                this.$validator.reset()
            }
        }
    }
</script>

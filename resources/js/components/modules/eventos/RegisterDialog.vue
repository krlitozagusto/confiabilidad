<template>
    <v-layout>
        <v-dialog v-model="open" fullscreen hide-overlay transition="dialog-bottom-transition" persistent>
            <v-card>
                <v-card-title class="py-0">
                    <span class="headline">{{evento.id ? 'Edición de evento' : 'Nuevo evento'}}</span>
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
                                    >
                                        <v-card flat>
                                            <v-card-text>
                                                <component ref="componentsRegister" :is="tab.component" :solo-guardar="soloGuardar" v-model="evento" :complementos="complementos"></component>
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
                    <v-btn flat @click="close">Cancelar</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" dark @click="saveAndEventClose">Registrar y cerrar evento</v-btn>
                    <v-btn color="primary" dark @click="save">Registrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <confirmation-dialog
            ref="dialogCloseConfirm"
            heading="¿Seguro de continuar?"
            :message="`¿Está seguro de continuar con el cierre de éste evento?`"
            @onConfirm="submit"
        >
        </confirmation-dialog>
    </v-layout>
</template>
<script>
    import RegisterGeneral from './components/register/RegisterGeneral'
    import RegisterOrdenTrabajo from './components/register/RegisterOrdenTrabajo'
    import RegisterFallas from './components/register/RegisterFallas'
    import RegisterImpactos from './components/register/RegisterImpactos'
    import RegisterGastos from './components/register/RegisterGastos'
    import ConfirmationDialog from '../../general/ConfirmationDialog'
    export default {
		name: "RegisterDialog",
        components: {
            ConfirmationDialog
        },
		data: () => ({
            open: false,
            tabActiva: 'tab-0',
            tabs: [],
            soloGuardar: 1,
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
                archivo_soporte: null,
                // Auxiliares
                eventos_hijos: [],
                orden_trabajos: [],
                fallas: [],
                impactos: [],
                gastos: [],
                evento_padre: null,
                equipo: null,
                tipo_evento: null,
                tipo_mantenimiento: null,
                // ayudas
                esPrincipal: 1,
                campo_id: null
            },
            complementos:{
                tiposEvento: [],
                tiposMantenimiento: [],
                puestosTrabajo: [],
                modosFalla: [],
                tiposImpacto: [],
                tiposGasto: [],
                campos: []
            }
		}),
		computed: {
		},
        whatch: {
		    'open' (val) {
            }
        },
		created () {
            this.tabs.push({title: 'Datos generales', component: RegisterGeneral})
            this.tabs.push({title: 'Orden de trabajo', component: RegisterOrdenTrabajo})
            this.tabs.push({title: 'Fallas', component: RegisterFallas})
            this.tabs.push({title: 'Impactos', component: RegisterImpactos})
            this.tabs.push({title: 'Gastos', component: RegisterGastos})
            this.resetModel()
		},
        methods: {
            register (id) {
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
                            response.data.evento.esPrincipal = response.data.evento.evento_padre_id ? 0 : 1
                            response.data.evento.campo_id = response.data.evento.equipo.sistema.planta.campo_id
                            this.evento = response.data.evento
                        }
                        this.complementos.tiposEvento = response.data.tiposEvento
                        this.complementos.tiposMantenimiento = response.data.tiposMantenimiento
                        this.complementos.puestosTrabajo = response.data.puestosTrabajo
                        this.complementos.modosFalla = response.data.modosFalla
                        this.complementos.tiposImpacto = response.data.tiposImpacto
                        this.complementos.tiposGasto = response.data.tiposGasto
                        this.complementos.campos = response.data.campos
                        this.$store.commit('LOADING', false)
                        this.open = true
                        this.$nextTick(() => {
                            this.tabActiva = 'tab-0'
                        })
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
            async save () {
                this.soloGuardar = 1
                this.evento.estado = 'Registrado'
                await this.resetForms()
                this.submit()
            },
            async saveAndEventClose () {
                this.soloGuardar = 0
                this.evento.estado = 'Cerrado'
                await this.resetForms()
                if (await this.validateForms()) this.$refs.dialogCloseConfirm.open()
            },
            async submit () {
                if (await this.validateForms()) {
                    this.$store.commit('LOADING', true)
                    let data = {
                        id: this.evento.id,
                        fecha_registro: this.evento.fecha_registro && this.evento.hora_registro ? `${this.evento.fecha_registro} ${this.evento.hora_registro}` : null,
                        fecha_inicio: this.evento.fecha_inicio && this.evento.hora_inicio ? `${this.evento.fecha_inicio} ${this.evento.hora_inicio}` : null,
                        fecha_fin: this.evento.fecha_fin && this.evento.hora_fin ? `${this.evento.fecha_fin} ${this.evento.hora_fin}` : null,
                        fecha_inicio_reparacion: this.evento.fecha_inicio_reparacion && this.evento.hora_inicio_reparacion ? `${this.evento.fecha_inicio_reparacion} ${this.evento.hora_inicio_reparacion}` : null,
                        fecha_fin_reparacion: this.evento.fecha_fin_reparacion && this.evento.hora_fin_reparacion ? `${this.evento.fecha_fin_reparacion} ${this.evento.hora_fin_reparacion}` : null,
                        downtime: null,
                        estado: this.evento.estado,
                        contractual: this.evento.contractual,
                        tipo_evento_id: this.evento.tipo_evento_id,
                        tipo_mantenimiento_id: this.evento.tipo_mantenimiento_id,
                        equipo_id: this.evento.equipo_id,
                        evento_padre_id: !this.evento.esPrincipal ? this.evento.evento_padre_id : null,
                        user_id: this.evento.user_id,
                        orden_trabajos: this.evento.orden_trabajos,
                        fallas: this.evento.fallas,
                        impactos: this.evento.impactos,
                        gastos: this.evento.gastos
                    }
                    axios.post(`eventos/registerevent`, data)
                        .then(response => {
                            this.$store.commit('SNACKBAR', {color: 'success', message: `evento registrado correctamente`})
                            this.$store.commit('RELOAD_TABLE', 'tablaEventos')
                            this.$refs.dialogCloseConfirm.close()
                            this.close()
                        })
                        .catch(error => {
                            this.$store.commit('LOADING', false)
                            this.$store.commit('SNACKBAR', {color: 'error', message: `al registrar el evento`, error: error})
                        })
                }
            },
            resetModel () {
                this.evento =  JSON.parse(JSON.stringify(this.makeEvento))
                this.complementos.tiposEvento = []
                this.complementos.tiposMantenimiento = []
                this.complementos.puestosTrabajo = []
                this.complementos.modosFalla = []
                this.complementos.tiposImpacto = []
                this.complementos.tiposGasto = []
                this.complementos.campos = []
                this.soloGuardar = 1
                this.resetForms()
            },
            async resetForms () {
                if (this.$refs['componentsRegister']) {
                    await this.$refs.componentsRegister[0].reset()
                    await this.$refs.componentsRegister[1].reset()
                    await this.$refs.componentsRegister[2].reset()
                    await this.$refs.componentsRegister[3].reset()
                    await this.$refs.componentsRegister[4].reset()
                }
            },
            async validateForms () {
                if (await this.validaUnit(0)) {
                    if (await this.validaUnit(1)) {
                        if (await this.validaUnit(2)) {
                            if (await this.validaUnit(3)) {
                                if (await this.validaUnit(4)) {
                                    return true
                                } else {
                                    this.tabActiva = `tab-4`
                                    return false
                                }
                            } else {
                                this.tabActiva = `tab-3`
                                return false
                            }
                        } else {
                            this.tabActiva = `tab-2`
                            return false
                        }
                    } else {
                        this.tabActiva = `tab-1`
                        return false
                    }
                } else {
                    this.tabActiva = `tab-0`
                    return false
                }
            },
            async validaUnit (index) {
                return await this.$refs.componentsRegister[index].$validator.validateAll()
            }
        }
    }
</script>

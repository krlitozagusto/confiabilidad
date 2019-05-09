<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card flat height="200px">
                <v-toolbar dark class="blue darken-1">
                    <v-toolbar-title>
                        Equipos
                        <small class="caption">
                            Consulta y detalle
                        </small>
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        label="Buscar..."
                        dark
                        flat
                        solo-inverted
                        hide-details
                        clearable
                        clear-icon="cancel"
                    ></v-text-field>
                </v-toolbar>
                <v-container fluid grid-list-md class="pa-2">
                    <v-layout row wrap>
                        <v-flex xs12 sm12 md6>
                            <v-treeview
                                ref="arbol"
                                :active.sync="active"
                                :items="items"
                                :search="search"
                                :open.sync="open"
                                activatable
                                active-class="primary--text"
                                class="grey lighten-5 cursor-pointer"
                                transition
                                return-object
                            >
                                <template slot="prepend" slot-scope="{ item, active, open }">
                                    <v-icon v-text="item.icon"></v-icon>
                                </template>
                            </v-treeview>
                            <div v-if="!arbolVisible" class="text-xs-center title grey--text">No hay coincidencias de busqueda</div>
                        </v-flex>
                        <v-flex xs12 sm12 md6>
                            El detalle
                        </v-flex>
                    </v-layout>
                </v-container>
<!--                <data-table-->
<!--                    ref="tablaEquipos"-->
<!--                    v-model="dataTable"-->
<!--                    @resetOption="item => resetOptions(item)"-->
<!--                    @detailMachine="item => detailMachine(item)"-->
<!--                ></data-table>-->
<!--                <v-treeview-->
<!--                    :active.sync="active"-->
<!--                    :items="items"-->
<!--                    :load-children="getItems"-->
<!--                    :open.sync="open"-->
<!--                    activatable-->
<!--                    active-class="primary&#45;&#45;text"-->
<!--                    class="grey lighten-5"-->
<!--                    open-on-click-->
<!--                    transition-->
<!--                >-->
<!--                    <template slot="prepend" slot-scope="{ item, active }">-->
<!--                        <v-icon-->
<!--                            v-if="!item.children"-->
<!--                            :color="active ? 'primary' : ''"-->
<!--                        >-->
<!--                            add-->
<!--                        </v-icon>-->
<!--                    </template>-->
<!--                </v-treeview>-->
            </v-card>
        </v-flex>
        <detail-dialog ref="detailDialog"></detail-dialog>
    </v-layout>
</template>
<script>
    export default {
		name: "Panel",
        components: {
            DataTable: resolve => {require(['../../general/DataTable'], resolve)},
            DetailDialog: resolve => {require(['./DetailDialog'], resolve)}
        },
		data: () => ({
            nodos: [],
            items: [],
            search: null,
            active: [],
            open: [],
            openAll: true,
            selectedItem: null,
            arbolVisible: true,
            dataTable: {
                nameItemState: 'tablaEquipos',
                route: 'equipos/panel',
                makeHeaders: [
                    {
                        text: 'Concecutivo',
                        align: 'center',
                        sortable: true,
                        value: 'id',
                        classData: 'text-xs-center'
                    },
                    {
                        text: 'Nombre',
                        align: 'left',
                        sortable: true,
                        value: 'nombre'
                    },
                    {
                        text: 'Denominación',
                        align: 'left',
                        sortable: false,
                        value: 'denominacion'
                    },
                    {
                        text: 'Tag',
                        align: 'left',
                        sortable: false,
                        value: 'tag'
                    },
                    {
                        text: 'Núm. equipo',
                        align: 'left',
                        sortable: false,
                        value: 'numero_equipo',
                        tooltip: 'Número de equipo'
                    },
                    {
                        text: 'Val. RAM',
                        align: 'left',
                        sortable: false,
                        value: 'valoracion_ram.nombre',
                        tooltip: 'Valoración Ram'
                    },
                    {
                        text: 'Opciones',
                        align: 'center',
                        sortable: false,
                        actions: true,
                        singlesActions: true,
                        classData: 'text-xs-center'
                    }
                ]
            }
		}),
        watch: {
		  async 'search' (val) {
              this.open = val ? this.nodos : []
              if (this.$refs && this.$refs.arbol) {
                  this.arbolVisible = !val || (val && (await this.$refs.arbol.excludedItems.size) < this.nodos.length)
              }
          },
            'active' (val) {
		      // this.selected =
              console.log('activo wt', val)
          }
        },
        created () {
		  this.getContratos()
        },
        methods: {
            getContratos () {
                axios.post(`equipos/getcontratos`,)
                    .then(response => {
                        this.items = response.data.contratos.map(co => {
                            let contrato = {
                                id: `contrato${co.id}`,
                                type: `contrato`,
                                name: `Contrato No. ${co.numero} de ${co.fecha}`,
                                objeto: co,
                                icon: 'folder',
                                children: !co.campos.length ? [] : co.campos.map(ca => {
                                    let campo = {
                                        id: `campo${ca.id}`,
                                        type: `campo`,
                                        name: `Campo código ${ca.codigo} - ${ca.nombre}`,
                                        objeto: ca,
                                        icon: 'place',
                                        children: !ca.plantas.length ? [] : ca.plantas.map(pl => {
                                            let planta = {
                                                id: `planta${pl.id}`,
                                                type: `planta`,
                                                name: `${pl.nombre}`,
                                                objeto: pl,
                                                icon: 'domain',
                                                children: !pl.sistemas.length ? [] : pl.sistemas.map(sis => {
                                                    let sistema = {
                                                        id: `sistema${sis.id}`,
                                                        type: `sistema`,
                                                        name: `Tag: ${sis.tag} - ${sis.nombre}`,
                                                        objeto: sis,
                                                        icon: 'device_hub',
                                                        children: !sis.ubicacion_tecnicas.length ? [] : sis.ubicacion_tecnicas.map(ut => {
                                                            let utecnica = {
                                                                id: `utecnica${ut.id}`,
                                                                type: `ut`,
                                                                name: `Tag: ${ut.tag} - ${ut.nombre}`,
                                                                objeto: ut,
                                                                icon: 'my_location',
                                                                children: !ut.equipos.length ? [] : ut.equipos.map(eq => {
                                                                    let equipo = {
                                                                        id: `equipo${eq.id}`,
                                                                        type: `equipo`,
                                                                        name: `Tag: ${eq.tag} - ${eq.nombre}`,
                                                                        objeto: eq,
                                                                        icon: 'developer_board'
                                                                    }
                                                                    this.nodos.push(equipo)
                                                                    return equipo
                                                                })
                                                            }
                                                            this.nodos.push(utecnica)
                                                            return utecnica
                                                        })
                                                    }
                                                    this.nodos.push(sistema)
                                                    return sistema
                                                })
                                            }
                                            this.nodos.push(planta)
                                            return planta
                                        })
                                    }
                                    this.nodos.push(campo)
                                    return campo
                                })
                            }
                            this.nodos.push(contrato)
                            return contrato
                        })
                    })
                    .catch(error => {
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al traer los contratos para el arbol`, error: error})
                    })
            },
            resetOptions (item) {
                item.options = []
                item.options.push({event: 'detailMachine', icon: 'details', tooltip: 'Detalle equipo', color: 'primary'})
                return item
            },
            detailMachine (equipo) {
                this.$refs.detailDialog.register(equipo.id)
            }
        }
    }
</script>

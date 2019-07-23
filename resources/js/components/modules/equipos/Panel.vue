<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card flat>
                <v-toolbar dark class="blue darken-1">
                    <v-toolbar-title>
                        Taxonomía
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
                        <v-flex xs12 sm12 md7>
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
                        <v-flex xs12 sm12 md5>
                            <template v-if="details">
                                <v-list-tile>
                                    <v-list-tile-avatar>
                                        <v-icon color="info" left>{{details.icon}}</v-icon>
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        <v-list-tile-title>Detalle de {{details.type}} </v-list-tile-title>
                                    </v-list-tile-content>
                                    <v-list-tile-action v-if="details.type === 'equipo'">
                                        <v-tooltip top>
                                            <v-btn flat icon color="indigo" slot="activator" @click.stop="detailEquipo(details.id)">
                                                <v-icon>details</v-icon>
                                            </v-btn>
                                            <span>Mas detalle</span>
                                        </v-tooltip>
                                    </v-list-tile-action>
                                </v-list-tile>
                                <v-divider class="mb-3"></v-divider>
                                <input-detail-flex
                                        v-for="(prop, index) in details.registros"
                                        :key="`prop${index}`"
                                        :flex-class="prop.flexClass"
                                        :label="prop.label"
                                        :text="prop.text"
                                        :hint="prop.hint"
                                        :prepend-icon="prop.icon"
                                />
                            </template>
                            <div v-else class="text-xs-center title grey--text mt-4">No hay un item seleccionado</div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card>
        </v-flex>
        <detail-dialog ref="detailDialog"></detail-dialog>
    </v-layout>
</template>
<script>
    export default {
		name: "Panel",
        components: {
            InputDetailFlex: resolve => {require(['../../general/InputDetailFlex'], resolve)},
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
            details: null,
            arbolVisible: true
		}),
        watch: {
		  async 'search' (val) {
              this.open = val ? this.nodos : []
              if (this.$refs && this.$refs.arbol) {
                  this.arbolVisible = !val || (val && (await this.$refs.arbol.excludedItems.size) < this.nodos.length)
              }
          },
            'active' (val) {
		      if (val.length) {
		          let registro = val[0]
                  this.details = {
                      id: registro.objeto.id,
                      icon: registro.icon,
                      type: registro.type,
                      registros: []
                  }
		          switch (registro.type) {
                      case 'contrato': {
                          this.details.registros = [
                              {label: 'Número', text: registro.objeto.numero, hint: null, icon: null, flexClass: null},
                              {label: 'Fecha', text: registro.objeto.fecha, hint: null, icon: null, flexClass: null},
                              {label: 'Descripción', text: registro.objeto.descripcion, hint: null, icon: null, flexClass: null}
                          ]
                          break
                      }
                      case 'campo': {
                          this.details.registros = [
                              {label: 'Código', text: registro.objeto.codigo, hint: null, icon: null, flexClass: null},
                              {label: 'Nombre', text: registro.objeto.nombre, hint: null, icon: null, flexClass: null}
                          ]
                          break
                      }
                      case 'planta': {
                          this.details.registros = [
                              {label: 'Nombre', text: registro.objeto.nombre, hint: null, icon: null, flexClass: null},
                              {label: 'Eplazamiento', text: registro.objeto.emplazamiento, hint: `Centro: ${!!registro.objeto.centro_emplazamiento ? registro.objeto.centro_emplazamiento : 'No registra'}`, icon: null, flexClass: null},
                              {label: 'Descripción', text: registro.objeto.descripcion, hint: null, icon: null, flexClass: null}
                          ]
                          break
                      }
                      case 'sistema': {
                          this.details.registros = [
                              {label: 'Nombre', text: registro.objeto.nombre, hint: null, icon: null, flexClass: null},
                              {label: 'Tag', text: registro.objeto.tag, hint: null, icon: null, flexClass: null},
                              {label: 'Descripción', text: registro.objeto.descripcion, hint: null, icon: null, flexClass: null}
                          ]
                          break
                      }
                      case 'ubicación técnica': {
                          this.details.registros = [
                              {label: 'Nombre', text: registro.objeto.nombre, hint: null, icon: null, flexClass: null},
                              {label: 'Tag', text: registro.objeto.tag, hint: null, icon: null, flexClass: null},
                              {label: 'Descripción', text: registro.objeto.descripcion, hint: null, icon: null, flexClass: null}
                          ]
                          break
                      }
                      case 'equipo': {
                          this.details.registros = [
                              {label: 'Nombre', text: registro.objeto.nombre, hint: null, icon: null, flexClass: null},
                              {label: 'Criticidad', text: registro.objeto.criticidad, hint: null, icon: null, flexClass: null},
                              {label: 'Tag', text: registro.objeto.tag, hint: null, icon: null, flexClass: null},
                              {label: 'Número', text: registro.objeto.numero_equipo, hint: null, icon: null, flexClass: null},
                              {label: 'Denominación', text: registro.objeto.denominacion, hint: null, icon: null, flexClass: null}
                          ]
                          break
                      }
                  }
              } else {
                  this.details = null
              }
          }
        },
        created () {
		  this.getContratos()
        },
        methods: {
		    detailEquipo (id) {
                this.$refs.detailDialog.register(id)
            },
            getContratos () {
                this.$store.commit('LOADING', true)
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
                                        name: `Campo ${ca.codigo} - ${ca.nombre}`,
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
                                                        children: !sis.equipos.length ? [] : sis.equipos.map(eq => {
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
                        this.$store.commit('LOADING', false)
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al traer los contratos para el arbol`, error: error})
                    })
            }
        }
    }
</script>

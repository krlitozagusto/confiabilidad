<template>
  <div id="flex-create-rol">
    <v-expansion-panel key="panel1" v-if="filters" v-model="activeAdvance" expand readonly>
          <v-expansion-panel-content
                  hide-actions
          >
              <v-container
                      fluid
                      grid-list-md
                      slot="header"
                      class="py-1 px-2"
              >
                  <v-layout row wrap>
                      <v-flex xs5 sm3 md6 class="text-xs-right" style="height: 66px !important;">
                          <v-tooltip top>
                              <v-btn slot="activator" icon large @click="reloadPage">
                                  <v-icon color="grey" medium>cached</v-icon>
                              </v-btn>
                              <span>Recargar Registros</span>
                          </v-tooltip>
                      </v-flex>
                      <v-flex xs7 sm3 md2 style="height: 66px !important;">
                          <v-select
                                  label="Registros por página"
                                  v-model="value.pagination.per_page"
                                  :items="value.optionsPerPage"
                                  item-text="text"
                                  item-value="value"
                                  @change="reloadCurrentPage"
                                  hide-details
                          />
                      </v-flex>
                      <v-flex xs12 sm6 md4 style="height: 66px !important;">
                          <v-text-field
                                  v-model="value.search"
                                  prepend-icon="search"
                                  label="Buscar"
                                  clearable
                                  hide-details
                          >
                              <v-tooltip
                                      v-if="value.filters"
                                      slot="append-outer"
                                      left
                              >
                                  <v-btn flat @click="activeAdvance = !activeAdvance[0] ? [true] : [false]" icon slot="activator" style="top:-6px !important;" class="ma-0">
                                      <v-icon v-if="!activeAdvance[0]">keyboard_arrow_down</v-icon>
                                      <v-icon color="primary" v-else>keyboard_arrow_up</v-icon>
                                  </v-btn>
                                  {{!activeAdvance[0] ? 'Mostrar busqueda avanzada' : 'Ocultar busqueda avan'}}
                              </v-tooltip>
                          </v-text-field>
                      </v-flex>
                  </v-layout>
              </v-container>
              <v-divider/>
              <v-container
                      v-if="value.filters"
                      fluid
                      grid-list-md
                      class="pa-2"
              >
                  <v-layout row wrap>
                      <v-flex xs12>
                          <h6>
                              <v-icon>loupe</v-icon>
                              Busqueda avanzada
                          </h6>
                      </v-flex>
                      <v-flex xs12>
                          <v-container
                                  fluid
                                  grid-list-sm
                                  class="py-0 px-3"
                          >
                              <v-layout row wrap>
                                  <template v-for="(filter, index) in value.filters">
                                      <v-flex class="pa-2" xs12 sm6 md3 v-if="filter.type === 'v-range' && filter.items.type === 'number'">
                                          <v-layout align-center class="ma-0">
                                              <p class="caption mb-0">{{filter.label}}</p>
                                          </v-layout>
                                          <v-layout align-center class="ma-0">
                                              <v-text-field
                                                      type="number"
                                                      v-model.number="filter.items.itemMin.vModel"
                                                      :label="filter.items.itemMin.label"
                                                      @input="filterReloadPage"
                                                      clearable
                                              />
                                              <v-text-field
                                                      type="number"
                                                      v-model.number="filter.items.itemMax.vModel"
                                                      :label="filter.items.itemMax.label"
                                                      @input="filterReloadPage"
                                                      clearable
                                              />
                                          </v-layout>
                                      </v-flex>
                                      <v-flex class="pa-2" xs12 sm6 md3 v-if="filter.type === 'v-range' && filter.items.type === 'date'">
                                          <v-layout align-center  class="ma-0">
                                              <p class="caption mb-0">{{filter.label}}</p>
                                          </v-layout>
                                          <v-layout align-center  class="ma-0">
                                              <v-flex xs6 class="pa-0 ma-0">
                                                  <v-menu
                                                          class="mt-0"
                                                          :key="'fechaMenu' + index"
                                                          :ref="filter.items.itemMin.label + index"
                                                          :close-on-content-click="false"
                                                          v-model="filter.items.itemMin.menuDate"
                                                          :nudge-right="40"
                                                          :return-value.sync="filter.items.itemMin.vModel"
                                                          lazy
                                                          transition="scale-transition"
                                                          offset-y
                                                          full-width
                                                          min-width="290px"
                                                  >
                                                      <v-text-field
                                                              :key="'fechaText' + index"
                                                              slot="activator"
                                                              v-model="filter.items.itemMin.vModel"
                                                              :label="filter.items.itemMin.label"
                                                              clearable
                                                              @input="filterReloadPage"
                                                              readonly
                                                      />
                                                      <v-date-picker
                                                              locale="es-co"
                                                              :key="'fechaPicker' + index"
                                                              v-model="filter.items.itemMin.vModel"
                                                              @input="$refs[filter.items.itemMin.label + index][0].save(filter.items.itemMin.vModel)"
                                                              @change="filterReloadPage"
                                                      />
                                                  </v-menu>
                                              </v-flex>
                                              <v-flex xs6 class="pa-0 ma-0">
                                                  <v-menu
                                                          class="mt-0"
                                                          :key="'fechaMenuMax' + index"
                                                          :ref="filter.items.itemMax.label + index"
                                                          :close-on-content-click="false"
                                                          v-model="filter.items.itemMax.menuDate"
                                                          :nudge-right="40"
                                                          :return-value.sync="filter.items.itemMax.vModel"
                                                          lazy
                                                          transition="scale-transition"
                                                          offset-y
                                                          full-width
                                                          min-width="290px"
                                                  >
                                                      <v-text-field
                                                              :key="'fechaTextMax' + index"
                                                              slot="activator"
                                                              v-model="filter.items.itemMax.vModel"
                                                              :label="filter.items.itemMax.label"
                                                              clearable
                                                              @input="filterReloadPage"
                                                              readonly
                                                      />
                                                      <v-date-picker
                                                              locale="es-co"
                                                              :key="'fechaPickerMax' + index"
                                                              v-model="filter.items.itemMax.vModel"
                                                              @input="$refs[filter.items.itemMax.label + index][0].save(filter.items.itemMax.vModel)"
                                                              @change="filterReloadPage"
                                                      />
                                                  </v-menu>
                                              </v-flex>
                                          </v-layout>
                                      </v-flex>
                                      <v-flex class="pa-2" xs12 sm6 md3 v-if="filter.type === 'v-select' || filter.type === 'v-autocomplete'">
                                          <v-layout align-center>
                                              <p class="caption mb-0">&nbsp;</p>
                                          </v-layout>
                                          <v-layout align-center>
                                              <component
                                                      :is="filter.type"
                                                      :key="'filter' + index"
                                                      :label="filter.label"
                                                      :multiple="filter.multiple"
                                                      v-model="filter.vModel"
                                                      :item-text="filter.itemText"
                                                      :item-value="filter.itemValue"
                                                      :items="filter.items()"
                                                      return-object
                                                      @change="filterReloadPage"
                                                      :clearable="filter.clearable"
                                              >
                                                  <template
                                                          slot="selection"
                                                          slot-scope="{ item, index }"
                                                  >
                                                      <v-chip small v-if="index === 0">
                                                          <span>{{ item[filter.itemText] }}</span>
                                                      </v-chip>
                                                      <span
                                                              v-if="index === 1"
                                                              class="grey--text caption"
                                                      >(+{{filter.vModel.length - 1 }} más)</span>
                                                  </template>
                                              </component>
                                          </v-layout>
                                      </v-flex>
                                  </template>
                              </v-layout>
                          </v-container>
                      </v-flex>
                      <v-flex xs12>
                          <v-container
                                  fluid
                                  grid-list-sm
                                  class="py-0 px-3"
                          >
                              <v-layout row wrap>
                                  <v-flex class="pa-2" xs12>
                                      <v-layout aling-center>
                                          <v-select
                                                  label="Columnas"
                                                  multiple
                                                  v-model="value.headers"
                                                  :items="value.makeHeaders"
                                                  item-text="text"
                                                  item-value="id"
                                                  return-object
                                                  chips
                                                  deletable-chips
                                                  small-chips
                                                  hide-selected
                                          ></v-select>
                                      </v-layout>
                                  </v-flex>
                              </v-layout>
                          </v-container>
                      </v-flex>
                  </v-layout>
              </v-container>
          </v-expansion-panel-content>
      </v-expansion-panel>
    <v-data-table
      :headers="value.headersTable"
      :items="value.items"
      :search="value.search"
      :pagination.sync="value.pagination"
      hide-actions
      :total-items="value.items.length"
      :loading="value.visibleLoading && value.loading"
      class="elevation-0 pa-0 ma-0 mt-1"
    >
      <template slot="headerCell" slot-scope="props">
        <v-tooltip top :disabled="!props.header.tooltip">
        <span slot="activator" :class="props.header.tooltip ? 'subrayado-dot cursor-pointer' : ''">
          {{ props.header.text }}
        </span>
          <span>
          {{ props.header.tooltip }}
        </span>
        </v-tooltip>
      </template>
      <template slot="items" slot-scope="props" >
        <tr @click="value.expand ? (props.expanded = !props.expanded) : ''" :class="value.expand ? 'cursor-pointer' : ''">
          <td
            v-for="(datHeader, index) in value.makeHeaders"
            v-if="value.headers.find(x => x.id === (index))"
            :key="'registro' + index"
            :class="datHeader.classData"
            :width="datHeader.width"
          >
            <div v-if="!datHeader.actions && !datHeader.component" v-html="getProperty(props.item, datHeader.value.split('.'))"></div>
            <template v-if="!datHeader.actions && datHeader.component">
              <component
                :is="datHeader.component"
                v-model="props.item"
              />
            </template>
            <template v-if="datHeader.actions">
              <template v-if="datHeader.singlesActions">
                <v-tooltip v-for="(option, i) in props.item.options" :key="'option' + index + i" top>
                  <v-btn
                    class="ma-0"
                    flat
                    icon
                    :small="option.size === 'small'"
                    :large="option.size === 'large'"
                    :color="option.color ? option.color : 'accent'"
                    slot="activator"
                    @click.stop="$emit(option.event, props.item)"
                  >
                    <v-icon
                      :color="option.color ? option.color : 'accent'"
                      :size="option.size ? option.size : ''"
                    >
                      {{option.icon}}
                    </v-icon>
                  </v-btn>
                  <span>{{option.tooltip}}</span>
                </v-tooltip>
              </template>
              <template v-else>
                <v-tooltip
                  left
                  :disabled="props.item.options && props.item.options.length > 0"
                >
                  <v-speed-dial
                    slot="activator"
                    v-model="props.item.show"
                    direction="left"
                    open-on-hover
                    transition="slide-x-transition"
                  >
                    <v-btn
                      v-model="props.item.show"
                      class="mx-0"
                      :color="!props.item.options || !props.item.options.length ? 'grey lighten-1 elevation-0' : 'blue darken-2'"
                      dark
                      fab
                      slot="activator"
                      small
                    >
                      <template v-if="props.item.options && props.item.options.length > 0">
                        <v-icon>chevron_left</v-icon>
                        <v-icon>close</v-icon>
                      </template>
                      <v-icon v-else>close</v-icon>
                    </v-btn>

                    <v-tooltip v-for="(option, i) in props.item.options" :key="'option' + index + i" top>
                      <v-btn
                        class="mx-0 mr-1"
                        color="white"
                        fab
                        slot="activator"
                        small
                        @click.stop="$emit(option.event, props.item)"
                      >
                        <v-icon
                          :color="option.color ? option.color : 'accent'"
                          :small="option.size === 'small'"
                          :size="!option.size ? '20px' : ''"
                          :medium="option.size === 'medium'"
                          :large="option.size === 'large'"
                        >
                          {{option.icon}}
                        </v-icon>
                      </v-btn>
                      <span>{{option.tooltip}}</span>
                    </v-tooltip>
                  </v-speed-dial>
                  <span>Sin opciones</span>
                </v-tooltip>
              </template>
            </template>
          </td>
        </tr>
      </template>
      <template v-if="value.expand" slot="expand" slot-scope="props">
        <component
          v-if="value.expand.component"
          :is="value.expand.component"
          v-model="props.item"
        />
      </template>
      <template slot="no-data">
        <v-alert  v-if="!value.loading" :value="true" color="warning" icon="warning">
          No tenemos registros para mostrar. <v-icon>sentiment_very_dissatisfied</v-icon>
        </v-alert>
        <v-alert v-else :value="true" color="info" icon="info">
          Estamos cargando los registros. <v-icon>sentiment_satisfied_alt</v-icon>
        </v-alert>
      </template>
      <template slot="footer">
        <td colspan="100%" class="text-xs-center">
          <v-layout align-center justify-space-between row fill-height v-if="value.simplePaginate">
            <v-flex class="text-xs-left">
              <strong>Página actual: {{value.pagination.current_page}}</strong>
            </v-flex>
            <v-flex>
              <v-tooltip top :disabled="!value.pagination.prev">
                <button
                  slot="activator"
                  type="button"
                  class="v-pagination__navigation theme--light"
                  :class="!value.pagination.prev ? 'v-pagination__navigation--disabled' : ''"
                  style="position: static;"
                  @click="() => {
                        value.pagination.current_page--
                        reloadPage()
                        }"
                >
                  <div class="v-btn__content">
                    <i aria-hidden="true" class="v-icon material-icons theme--light">
                      keyboard_arrow_left
                    </i>
                  </div>
                </button>
                <span>Anterior</span>
              </v-tooltip>
              <v-tooltip top :disabled="!value.pagination.next">
                <button
                  slot="activator"
                  type="button"
                  class="v-pagination__navigation theme--light"
                  :class="!value.pagination.next ? 'v-pagination__navigation--disabled' : ''"
                  style="position: static;"
                  @click="() => {
                        value.pagination.current_page++
                        reloadPage()
                        }"
                >
                  <div class="v-btn__content">
                    <i aria-hidden="true" class="v-icon material-icons theme--light">
                      keyboard_arrow_right
                    </i>
                  </div>
                </button>
                <span>Siguiente</span>
              </v-tooltip>
            </v-flex>
            <v-flex>
            </v-flex>
          </v-layout>
          <v-pagination v-else v-model="value.pagination.current_page" :total-visible="7" :length="value.pagination.last_page" @input="reloadPage"></v-pagination>
        </td>
      </template>
    </v-data-table>
  </div>
</template>
<script>
  // import lodash from 'lodash'
  // window.lodash = lodash
  export default {
    name: 'DataTable',
    props: {
      value: Object,
      filters: {
        type: Boolean,
        default: true
      }
    },
    data () {
      return {
        activePetition: true,
        activeAdvance: [false]
      }
    },
    computed: {
      stateItem () {
        if (this.value.nameItemState) return this.value.nameItemState
      },
      item () {
        return JSON.parse(JSON.stringify(this.stateItem ? this.$store.state.general.tablas[this.stateItem] : {}))
      }
    },
    watch: {
      'item' (val) {
          val && this.reloadPage()
      },
      'value.headers' (item) {
        this.value.headersTable = JSON.parse(JSON.stringify(item)).sort((a, b) => {
          if (a.id > b.id) {
            return 1
          }
          if (a.id < b.id) {
            return -1
          }
          return 0
        })
      },
      'value.pagination': {
        handler: window.lodash.debounce(function () {
          this.value.pagination.current_page = 1
          this.reloadPage()
        }, 500)
      }
    },
    created () {
      this.$set(this.value, 'items', [])
      this.$set(this.value, 'loading', true)
      this.$set(this.value, 'search', '')
      this.$set(this.value, 'headers', [])
      this.$set(this.value, 'headersTable', [])
      if (!this.value.visibleLoading) this.$set(this.value, 'visibleLoading', true)
      if (!this.value.simplePaginate) this.$set(this.value, 'simplePaginate', false)
      if (!this.value.optionsPerPage) {
        this.$set(this.value, 'optionsPerPage', [
          {
            text: 15,
            value: 15
          },
          {
            text: 50,
            value: 50
          },
          {
            text: 100,
            value: 100
          }
        ])
      }
      this.$set(this.value, 'pagination', {
        current_page: 1,
        from: 1,
        last_page: 0,
        per_page: this.value.optionsPerPage[0].value,
        to: 15,
        total: 0,
        next: null,
        prev: null,
        descending: null,
        sortBy: null
      })
      this.reloadPage()
    },
    methods: {
      getProperty: (item, arr) => window.toProperty(item, arr),
      reloadHeaders () {
        this.value.makeHeaders.forEach((item, index) => {
          item.id = index
        })
        this.value.headers = JSON.parse(JSON.stringify(this.value.makeHeaders))
      },
      reloadCurrentPage: window.lodash.debounce(function () {
        this.value.pagination.current_page = 1
        this.reloadPage()
      }, 500),
      stringFilters () {
        let stringFilters = ''
        this.value.filters && this.value.filters.forEach(filtro => {
          let copiaModel = []
          if (filtro.type === 'v-select' || filtro.type === 'v-autocomplete') {
            JSON.parse(JSON.stringify(filtro.vModel)).forEach(item => {
              copiaModel.push(item.value)
            })
            stringFilters = stringFilters + (copiaModel.length ? '&' + filtro.value + '=' + (copiaModel.join(',')) : '')
          } else if (filtro.type === 'v-range') {
            if (filtro.items.range) {
              if (filtro.items.itemMin.vModel && !filtro.items.itemMax.vModel) {
                stringFilters = stringFilters + ('&' + (filtro.items.itemMin.value + '=(ge)' + filtro.items.itemMin.vModel))
              }
              if (filtro.items.itemMin.vModel && filtro.items.itemMax.vModel) {
                copiaModel.push(filtro.items.itemMin.value + '[]=(ge)' + filtro.items.itemMin.vModel)
                copiaModel.push(filtro.items.itemMax.value + '[]=(le)' + filtro.items.itemMax.vModel)
                stringFilters = stringFilters + (copiaModel.length ? '&' + (copiaModel.join('&')) : '')
              }
              if (!filtro.items.itemMin.vModel && filtro.items.itemMax.vModel) {
                stringFilters = stringFilters + ('&' + (filtro.items.itemMax.value + '=(le)' + filtro.items.itemMax.vModel))
              }
            }
          }
        })
        return stringFilters
      },
      filterReloadPage: window.lodash.debounce(function () {
        if (this.value.pagination.current_page === 1) {
          this.reloadPage()
        } else {
          this.value.pagination.current_page = 1
        }
      }, 500),
      async reloadPage () {
        if (this.activePetition) {
            this.$store.commit('LOADING', true)
          this.value.loading = true
          this.activePetition = false
          await this.reloadHeaders()
          let stringFilters = await this.stringFilters()
          let stringSort = this.value.pagination.sortBy ? (`&sort=${(this.value.pagination.descending ? '-' : '')}${this.value.pagination.sortBy}`) : ''
          this.axios.post(this.value.route + (this.value.route.indexOf('?') > -1 ? '&' : '?') + 'per_page=' + this.value.pagination.per_page + stringFilters + stringSort + '&page=' + this.value.pagination.current_page + '&filter[search]=' + ((this.value.search === null || typeof this.value.search === 'undefined') ? '' : this.value.search))
            .then((response) => {
                console.log('el response del datatable', response)
              response.data.data.forEach(item => {
                this.$emit('resetOption', item)
              })
              response.data.per_page = this.value.optionsPerPage.find(page => page.value === parseInt(response.data.per_page)) ? parseInt(response.data.per_page) : -1
              this.value.pagination.last_page = response.data.last_page
              this.value.pagination.next = response.data.next_page_url
              this.value.pagination.prev = response.data.last_page_url
              this.value.items = response.data.data
              setTimeout(() => {
                  this.$store.commit('LOADING', false)
                this.value.loading = false
                this.activePetition = true
              }, 300)
            })
            .catch(e => {
                this.$store.commit('LOADING', false)
              this.value.loading = false
              this.activePetition = true
                this.$store.commit('SNACKBAR', {color: 'error', message: `al hacer la busqueda de registros`, error: e})
            })
        }
      }
    }
  }
</script>

<style>
  #flex-create-rol div.v-expansion-panel__header{
    cursor: default !important;
    padding: 0 !important;
  }
</style>

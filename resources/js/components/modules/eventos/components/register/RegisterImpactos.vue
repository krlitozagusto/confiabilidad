<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card>
                <v-card-title class="py-0">
                    <span class="title">Impactos registrados</span>
                    <v-spacer></v-spacer>
                    <v-tooltip top>
                        <v-btn flat icon color="pink" slot="activator" @click="newVisible = true">
                            <v-icon>add</v-icon>
                        </v-btn>
                        <span>Crear impacto</span>
                    </v-tooltip>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-expand-transition>
                        <v-card v-if="newVisible" >
                            <v-card-text>
                                <v-layout row wrap>
                                    <v-flex xs12 sm8 md10>
                                        <v-select
                                            key=""
                                            label="Tipo impacto"
                                            :items="complementos.tiposImpacto"
                                            v-model="impacto.tipo_impacto"
                                            item-value="id"
                                            item-text="nombre"
                                            name="Tipo impacto"
                                            v-validate="'required'"
                                            return-object
                                            persistent-hint
                                            :hint="impacto.tipo_impacto ? 'Medida: ' + impacto.tipo_impacto.medida : ''"
                                            :error-messages="errors.collect('Tipo impacto')"
                                            @input="item => impacto.tipo_impacto_id = item.id"
                                        >
                                            <template slot="item" slot-scope="data">
                                                <div style="width: 100% !important;">
                                                    <v-list-tile>
                                                        <v-list-tile-content>
                                                            <v-list-tile-title>{{ data.item.nombre}} - {{data.item.medida}}</v-list-tile-title>
                                                            <v-list-tile-sub-title>{{ data.item.descripcion}}</v-list-tile-sub-title>
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                </div>
                                            </template>
                                        </v-select>
                                    </v-flex>
                                    <v-flex xs12 sm4 md2>
                                        <v-text-field
                                            label="Cantidad"
                                            v-model.number="impacto.cantidad"
                                            :min="0"
                                            name="cantidad"
                                            v-validate="'required|min_value:0'"
                                            :error-messages="errors.collect('cantidad')"
                                        ></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn small flat @click="close">Cancelar</v-btn>
                                <v-btn small color="primary" dark @click="saveImpacto">
                                    <v-icon left>save</v-icon>
                                    Guardar impacto
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-expand-transition>
                </v-card-text>
                <v-card-text class="pa-0">
                    <v-data-table
                            :headers="headers"
                            :items="value.impactos"
                            no-data-text="No hay impactos registrados para éste evento."
                            hide-actions
                    >
                        <template slot="items" slot-scope="props">
                            <td>
                                <v-list-tile>
                                    <v-list-tile-content>
                                        <v-list-tile-title>{{props.item.tipo_impacto.nombre}}</v-list-tile-title>
                                        <v-list-tile-sub-title class="caption">{{props.item.tipo_impacto.descripcion}}</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </td>
                            <td class="text-xs-center">{{ props.item.tipo_impacto.medida }}</td>
                            <td>{{ props.item.cantidad }}</td>
                            <td class="text-xs-center">
                                <v-tooltip top>
                                    <v-btn flat icon color="pink" slot="activator" @click="value.impactos.splice(props.index, 1)">
                                        <v-icon>delete_forever</v-icon>
                                    </v-btn>
                                    <span>Eliminar impacto</span>
                                </v-tooltip>
                            </td>
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'soloGuardar', 'complementos'],
    name: "RegisterImpactos",
        components: {
        },
		data: () => ({
            headers: [
                {
                    text: 'Tipo',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Unidad de medida',
                    align: 'center',
                    sortable: false
                },
                {
                    text: 'cantidad',
                    align: 'left',
                    sortable: false
                },
                {
                    text: '',
                    align: 'center',
                    sortable: false
                }
            ],
            impacto: null,
            newVisible: false,
            makeImpacto: {
                cantidad: null,
                tipo_impacto_id: null,
                tipo_impacto: null
            }
		}),
		computed: {
		},
        whatch: {
        },
		created () {
            this.reset()
		},
        methods: {
            async saveImpacto () {
                if (await this.$validator.validateAll()) {
                    this.value.impactos.push(JSON.parse(JSON.stringify(this.impacto)))
                    this.close()
                }
            },
            close () {
                this.reset()
                this.newVisible = false

            },
            reset () {
                this.impacto = JSON.parse(JSON.stringify(this.makeImpacto))
                this.$validator.reset()
            }
        }
    }
</script>

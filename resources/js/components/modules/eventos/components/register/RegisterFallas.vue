<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card>
                <v-card-title class="py-0">
                    <span class="title">Fallas registradas</span>
                    <v-spacer></v-spacer>
                    <v-tooltip top>
                        <v-btn flat icon color="pink" slot="activator" @click="newVisible = true">
                            <v-icon>add</v-icon>
                        </v-btn>
                        <span>Crear falla</span>
                    </v-tooltip>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="newVisible">
                    <v-card>
                        <v-card-text>
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                        label="Número orden"
                                        v-model="falla.sintoma"
                                        name="Número orden"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Número orden')"
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md3>
                                <v-text-field
                                        label="Número aviso"
                                        v-model="falla.sistema"
                                        name="Número aviso"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('Número aviso')"
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm12 md6>
                                <v-select
                                        key="modo_falla_id"
                                        label="Modo falla"
                                        :items="complementos.modosFalla"
                                        v-model="falla.modo_falla_id"
                                        item-value="id"
                                        item-text="descripcion"
                                        name="Modo falla"
                                        v-validate="'required'"
                                        persisten-hint
                                        :hint="falla.modo_falla_id ? 'Código: ' + complementos.modosFalla.find(x => x.id === falla.modo_falla_id).codigo : ''"
                                        :error-messages="errors.collect('Modo falla')"
                                >
                                    <template slot="item" slot-scope="data">
                                        <div style="width: 100% !important;">
                                            <v-list-tile>
                                                <v-list-tile-content>
                                                    <v-list-tile-title>{{data.item.codigo}}-{{ data.item.descripcion}}</v-list-tile-title>
                                                </v-list-tile-content>
                                            </v-list-tile>
                                        </div>
                                    </template>
                                </v-select>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                                <v-textarea
                                        label="Descripción"
                                        auto-grow
                                        rows="1"
                                        v-model="falla.accion_correctiva"
                                        name="descripción"
                                        v-validate="'required'"
                                        :error-messages="errors.collect('descripción')"
                                />
                            </v-flex>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn small flat @click="newVisible = false">Cancelar</v-btn>
                            <v-btn small color="primary" dark @click="saveFalla">
                                <v-icon left>save</v-icon>
                                Guardar falla
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-card-text>
                <v-card-text class="pa-0">
                    <v-data-table
                            :headers="headers"
                            :items="value.fallas"
                            no-data-text="No hay fallas registradas para éste evento."
                            hide-actions
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.modo_falla.codigo }} - {{props.item.modo_falla.descripcion}}</td>
                            <td>{{ props.item.sistema }}</td>
                            <td>{{ props.item.parte }}</td>
                            <td>{{ props.item.sintoma }}</td>
                            <td>{{ props.item.accion_correctiva }}</td>
                            <td class="text-xs-center">
                                <v-tooltip top>
                                    <v-btn flat icon color="pink" slot="activator" @click="value.fallas.splice(props.index, 1)">
                                        <v-icon>delete_forever</v-icon>
                                    </v-btn>
                                    <span>Eliminar falla</span>
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
    name: "RegisterFallas",
        components: {
        },
		data: () => ({
            headers: [
                {
                    text: 'Modo',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Sistema',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Parte',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Sintoma',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Acción correctiva',
                    align: 'left',
                    sortable: false
                },
                {
                    text: '',
                    align: 'center',
                    sortable: false
                }
            ],
            falla: null,
            newVisible:false,
            makeFalla: {
                sintoma: null,
                sistema: null,
                parte: null,
                accion_correctiva: null,
                modo_falla_id: null
            }
		}),
		computed: {
		},
        whatch: {
            'newVisible' (val) {
                !val && this.reset()
            }
        },
		created () {
            this.reset()
		},
        methods: {
            declineFalla () {
                this.falla = JSON.parse(JSON.stringify(this.makeFalla))
            },
            saveFalla () {
                this.falla = JSON.parse(JSON.stringify(this.makeFalla))
            },
            reset () {
                this.falla = JSON.parse(JSON.stringify(this.makeFalla))
                this.$validator.reset()
            }
        }
    }
</script>

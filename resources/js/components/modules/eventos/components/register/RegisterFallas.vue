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
                <v-card-text>
                    <v-expand-transition>
                        <v-card v-if="newVisible" >
                            <v-card-text>
                                <v-layout row wrap>
                                    <v-flex xs12 sm6 md6>
                                        <v-text-field
                                            label="Síntoma"
                                            v-model="falla.sintoma"
                                            name="síntoma"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('síntoma')"
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm6 md3>
                                        <v-text-field
                                            label="Sistema"
                                            v-model="falla.sistema"
                                            name="sistema"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('sistema')"
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm6 md3>
                                        <v-text-field
                                            label="Parte"
                                            v-model="falla.parte"
                                            name="parte"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('parte')"
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-select
                                            key="modo_falla_id"
                                            label="Modo falla"
                                            :items="complementos.modosFalla"
                                            v-model="falla.modo_falla"
                                            item-value="id"
                                            item-text="descripcion"
                                            name="modo falla"
                                            v-validate="'required'"
                                            return-object
                                            persistent-hint
                                            :hint="falla.modo_falla ? 'Código: ' + falla.modo_falla.codigo : ''"
                                            :error-messages="errors.collect('modo falla')"
                                            @input="item => falla.modo_falla_id = item.id"
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
                                            label="Acción correctiva"
                                            auto-grow
                                            rows="1"
                                            v-model="falla.accion_correctiva"
                                            name="acción correctiva"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('acción correctiva')"
                                        />
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn small flat @click="close">Cancelar</v-btn>
                                <v-btn small color="primary" dark @click="saveFalla">
                                    <v-icon left>save</v-icon>
                                    Guardar falla
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-expand-transition>
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
            newVisible: false,
            makeFalla: {
                sintoma: null,
                sistema: null,
                parte: null,
                accion_correctiva: null,
                modo_falla_id: null,
                modo_falla: null
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
            async saveFalla () {
                if (await this.$validator.validateAll()) {
                    this.value.fallas.push(JSON.parse(JSON.stringify(this.falla)))
                    this.close()
                }
            },
            close () {
                this.reset()
                this.newVisible = false

            },
            reset () {
                this.falla = JSON.parse(JSON.stringify(this.makeFalla))
                this.$validator.reset()
            }
        }
    }
</script>

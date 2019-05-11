<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card>
                <v-card-title class="py-0">
                    <span class="title">Gastos registrados</span>
                    <v-spacer></v-spacer>
                    <v-tooltip top>
                        <v-btn flat icon color="pink" slot="activator" @click="newVisible = true">
                            <v-icon>add</v-icon>
                        </v-btn>
                        <span>Nuevo gasto</span>
                    </v-tooltip>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-expand-transition>
                        <v-card v-if="newVisible" >
                            <v-card-text>
                                <v-layout row wrap>
                                    <v-flex xs12 sm12 md6>
                                        <v-select
                                            label="Tipo"
                                            :items="complementos.tiposGasto"
                                            v-model="gasto.tipo_gasto"
                                            item-value="id"
                                            item-text="nombre"
                                            name="tipo"
                                            v-validate="'required'"
                                            return-object
                                            :error-messages="errors.collect('tipo')"
                                            @input="item => gasto.tipo_gasto_id = item.id"
                                        ></v-select>
                                    </v-flex>
                                    <v-flex xs12 sm6 md3>
                                        <v-text-field
                                            label="Cantidad"
                                            v-model.number="gasto.cantidad"
                                            :min="0"
                                            name="cantidad"
                                            v-validate="'required|min_value:0'"
                                            :error-messages="errors.collect('cantidad')"
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm6 md3>
                                        <v-text-field
                                            prepend-icon="attach_money"
                                            label="Valor"
                                            v-model.number="gasto.valor"
                                            :min="0"
                                            name="valor"
                                            v-validate="'required|min_value:0'"
                                            :error-messages="errors.collect('valor')"
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm12 md12>
                                        <v-textarea
                                            label="Observaciones"
                                            auto-grow
                                            rows="1"
                                            v-model="gasto.observaciones"
                                            name="observaciones"
                                            v-validate="'required'"
                                            :error-messages="errors.collect('observaciones')"
                                        />
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn small flat @click="close">Cancelar</v-btn>
                                <v-btn small color="primary" dark @click="saveGasto">
                                    <v-icon left>save</v-icon>
                                    Guardar gasto
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-expand-transition>
                </v-card-text>
                <v-card-text class="pa-0">
                    <v-data-table
                            :headers="headers"
                            :items="value.gastos"
                            no-data-text="No hay gastos registrados para éste evento."
                            hide-actions
                    >
                        <template slot="items" slot-scope="props">
                            <td>{{ props.item.tipo_gasto.nombre}}</td>
                            <td class="text-xs-center">{{ props.item.cantidad }}</td>
                            <td class="text-xs-right">{{ currency(props.item.valor) }}</td>
                            <td class="text-xs-justify">{{ props.item.observaciones }}</td>
                            <td class="text-xs-center">
                                <v-tooltip top>
                                    <v-btn flat icon color="pink" slot="activator" @click="value.gastos.splice(props.index, 1)">
                                        <v-icon>delete_forever</v-icon>
                                    </v-btn>
                                    <span>Eliminar gasto</span>
                                </v-tooltip>
                            </td>
                        </template>
                        <template slot="footer">
                            <td :colspan="headers.length - 1" class="text-xs-right">
                                <strong>Total gastos</strong>
                            </td>
                            <td class="text-xs-right">
                                <strong>{{currency(totalGastos)}}</strong>
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
    name: "RegisterGastos",
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
                    text: 'Cantidad',
                    align: 'center',
                    sortable: false
                },
                {
                    text: 'Valor',
                    align: 'right',
                    sortable: false
                },
                {
                    text: 'Observaciones',
                    align: 'left',
                    sortable: false
                },
                {
                    text: '',
                    align: 'center',
                    sortable: false
                }
            ],
            gasto: null,
            newVisible: false,
            makeGasto: {
                cantidad: null,
                valor: null,
                observaciones: null,
                tipo_gasto_id: null,
                tipo_gasto: null
            }
		}),
		computed: {
            totalGastos () {
                return this.value.gastos.length ? window.lodash.sumBy(this.value.gastos, 'valor') : 0
            }
		},
        whatch: {
        },
		created () {
            this.reset()
		},
        methods: {
            async saveGasto () {
                if (await this.$validator.validateAll()) {
                    this.value.gastos.push(JSON.parse(JSON.stringify(this.gasto)))
                    this.close()
                }
            },
            close () {
                this.reset()
                this.newVisible = false

            },
            reset () {
                this.gasto = JSON.parse(JSON.stringify(this.makeGasto))
                this.$validator.reset()
            }
        }
    }
</script>

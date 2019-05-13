<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12>
            <v-card>
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
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'type'],
		name: "DetailGastos",
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
                }
            ]
		}),
		computed: {
            totalGastos () {
                return this.value.gastos.length ? window.lodash.sumBy(this.value.gastos, 'valor') : 0
            }
		},
        whatch: {
        },
		created () {
		},
        methods: {
        }
    }
</script>

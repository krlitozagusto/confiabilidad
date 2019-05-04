<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12>
            <v-card>
                <v-data-table
                    :headers="headers"
                    :items="value.impactos"
                    no-data-text="No hay impactos registradas para éste evento"
                    hide-actions
                >
                    <template slot="items" slot-scope="props">
                        <td>{{ props.item.modo_falla.codigo }} - {{props.item.modo_falla.descripcion}}</td>
                        <td>{{ props.item.sistema }}</td>
                        <td>{{ props.item.parte }}</td>
                        <td>{{ props.item.sintoma }}</td>
                        <td>{{ props.item.accion_correctiva }}</td>
                    </template>
                </v-data-table>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'type'],
		name: "DetailImpactos",
        components: {
        },
		data: () => ({
            headers: [
                {
                    text: 'Impacto',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Cantidad',
                    align: 'center',
                    sortable: false
                },
                {
                    text: 'Unidad medida',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Costo total',
                    align: 'left',
                    sortable: false
                }
            ]
		}),
		computed: {
		},
        whatch: {
        },
		created () {
		},
        methods: {
        }
    }
</script>

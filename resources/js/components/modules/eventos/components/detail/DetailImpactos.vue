<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12>
            <v-card>
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

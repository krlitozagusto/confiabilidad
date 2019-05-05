<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12>
            <v-card>
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
                    </template>
                </v-data-table>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'type'],
		name: "DetailFallas",
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

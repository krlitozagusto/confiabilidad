<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12>
            <v-card>
                <v-data-table
                    :headers="headers"
                    :items="value.eventos_hijos"
                    no-data-text="No hay eventos secundarios relacionados con éste evento."
                    hide-actions
                >
                    <template slot="items" slot-scope="props">
                        <td>{{props.item.id}}</td>
                        <td>{{props.item.tipo_evento.nombre}}</td>
                        <td>{{props.item.fecha_inicio}}</td>
                        <td>{{props.item.fecha_fin}}</td>
                        <td>
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>{{props.item.equipo.nombre}}</v-list-tile-title>
                                    <v-list-tile-sub-title class="caption">Tag: {{props.item.equipo.tag}}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </td>
                        <td>{{ props.item.estado }}</td>
                        <td class="text-xs-center">
                            <v-tooltip top>
                                <v-btn flat icon color="primary" slot="activator" @click="detailEvent(props.item)">
                                    <v-icon>details</v-icon>
                                </v-btn>
                                <span>Detalle evento</span>
                            </v-tooltip>
                        </td>
                    </template>
                </v-data-table>
            </v-card>
        </v-flex>
        <detail-dialog ref="detailDialog"></detail-dialog>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'type'],
		name: "DetailSecundarios",
        components: {
            DetailDialog: resolve => {require(['../../DetailDialog'], resolve)}
        },
		data: () => ({
            headers: [
                {
                    text: 'id',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Tipo',
                    align: 'center',
                    sortable: false
                },
                {
                    text: 'Fecha inicio',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Fecha fin',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Equipo',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Estado',
                    align: 'left',
                    sortable: false
                },
                {
                    text: '',
                    align: 'center',
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
            detailEvent (evento) {
                this.$refs.detailDialog.register(evento.id)
            }
        }
    }
</script>

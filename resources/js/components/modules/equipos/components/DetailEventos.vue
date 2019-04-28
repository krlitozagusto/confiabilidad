<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12>
            <v-card>
                <v-card-title>
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Filtrar"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                    :headers="headers"
                    :items="value.eventos"
                    :search="search"
                    no-data-text="No hay eventos registrados para éste equipo"
                    rows-per-page-text="Registros por página"
                >
                    <template slot="items" slot-scope="props">
                        <td>{{ props.item.id }}</td>
                        <td>{{ props.item.fecha_registro ? moment(props.item.fecha_registro).format('YYYY-MM-DD HH:mm') : ''}}</td>
                        <td>{{ props.item.tipo_evento.nombre }}</td>
                        <td>{{ props.item.fecha_inicio ? moment(props.item.fecha_inicio).format('YYYY-MM-DD HH:mm') : ''}}</td>
                        <td>{{ props.item.fecha_fin ? moment(props.item.fecha_fin).format('YYYY-MM-DD HH:mm') : ''}}</td>
                        <td class="text-xs-center">{{ props.item.estado }}</td>
                        <td class="text-xs-center">
                            <v-tooltip top>
                                <v-btn flat icon color="primary" slot="activator" @click="detailEvent(props.item)">
                                    <v-icon>details</v-icon>
                                </v-btn>
                                <span>Detalle evento</span>
                            </v-tooltip>
                        </td>
                    </template>
                    <template slot="no-results">
                        <v-alert :value="true" color="error" icon="warning">
                            No hay registros del filtro "{{ search }}".
                        </v-alert>
                    </template>
                    <template slot="pageText" slot-scope="props">
                        registros del {{ props.pageStart }} al {{ props.pageStop }} de {{ props.itemsLength }}
                    </template>
                </v-data-table>
            </v-card>
        </v-flex>
        <detail-evento ref="detailEvento"></detail-evento>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'type'],
		name: "DetailGeneral",
        components: {
            DetailEvento: resolve => {require(['../../eventos/DetailDialog'], resolve)}
        },
		data: () => ({
            search: null,
            headers: [
                {
                    text: 'id',
                    align: 'left',
                    sortable: false,
                    value: 'id'
                },
                {
                    text: 'Fecha registro',
                    align: 'left',
                    sortable: false,
                    value: 'fecha_registro'
                },
                {
                    text: 'Tipo',
                    align: 'left',
                    sortable: false,
                    value: 'tipo_evento.nombre'
                },
                {
                    text: 'Fecha inicio',
                    align: 'left',
                    sortable: false,
                    value: 'fecha_inicio'
                },
                {
                    text: 'Fecha fin',
                    align: 'left',
                    sortable: false,
                    value: 'fecha_fin'
                },
                {
                    text: 'Estado',
                    align: 'center',
                    sortable: false,
                    value: 'estado'
                },{
                    text: 'Opciones',
                    align: 'center',
                    sortable: false,
                    value: 'id'
                }
            ]
		}),
		computed: {
		},
        whatch: {
            'value' (val) {
                val && (this.search = null)
            }
        },
		created () {
		},
        methods: {
            detailEvent (evento) {
                this.$refs.detailEvento.register(evento.id)
            }
        }
    }
</script>

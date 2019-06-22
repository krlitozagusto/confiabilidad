<template>
    <v-expansion-panel>
        <v-expansion-panel-content>
            <template slot="header">
                <v-layout row wrap>
                    <v-flex xs12>
                        <span class="subheading">{{result.equipo.nombre}} - Tag:{{result.equipo.tag}}</span>
                    </v-flex>
                    <v-layout row justify-space-between class="px-2">
                        <span class="body-2">Disponibilidad: {{result.data.disponibilidad}}</span>
                        <span class="body-2" v-if="result.data.mtbf">MTBF: {{result.data.mtbf.horas}}H:{{result.data.mtbf.minutos}}M</span>
                        <span class="body-2" v-if="result.data.mttr">MTTR: {{result.data.mttr.horas}}H:{{result.data.mttr.minutos}}M</span>
                    </v-layout>
                </v-layout>
            </template>
            <v-divider></v-divider>
            <v-card>
                <v-card-title v-if="!delegado"><strong>Tiempo intervalo: {{result.data.intervalo.total_horas}}Horas</strong></v-card-title>
                <v-data-table
                        v-if="result.data.registros.length"
                        :headers="headers"
                        :items="result.data.registros"
                        no-data-text="No hay eventos en el rango seleccionado."
                        hide-actions
                >
                    <template slot="items" slot-scope="props">
                        <td>{{props.item.tipo_evento.nombre}}</td>
                        <td>
                            <span><strong>Inicio: </strong>{{props.item.fecha_inicio ? moment(props.item.fecha_inicio).format('YYYY-MM-DD HH:mm') : ''}}</span><br/>
                            <span><strong>Fin: </strong>{{props.item.fecha_fin ? moment(props.item.fecha_fin).format('YYYY-MM-DD HH:mm') : ''}}</span>
                        </td>
                        <td>
                            <span><strong>Inicio: </strong>{{props.item.fecha_inicio_reparacion ? moment(props.item.fecha_inicio_reparacion).format('YYYY-MM-DD HH:mm') : ''}}</span><br/>
                            <span><strong>Fin: </strong>{{props.item.fecha_fin_reparacion ? moment(props.item.fecha_fin_reparacion).format('YYYY-MM-DD HH:mm') : ''}}</span>
                        </td>
                        <td>{{props.item.falla.horas}}H:{{props.item.falla.minutos}}M</td>
                        <td>{{props.item.reparacion.horas}}H:{{props.item.reparacion.minutos}}M</td>
                    </template>
                    <template slot="footer">
                        <td :colspan="3" class="text-xs-right"><strong>Total</strong></td>
                        <td :colspan="1">{{result.data.falla.horas}}H:{{result.data.falla.minutos}}M</td>
                        <td :colspan="1">{{result.data.reparacion.horas}}H:{{result.data.reparacion.minutos}}M</td>
                    </template>
                </v-data-table>
            </v-card>
        </v-expansion-panel-content>
    </v-expansion-panel>
</template>

<script>
    export default {
        name: 'ResultEquipo',
        props: {
            result: {
                type: Object,
                default: null
            },
            delegado: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            headers: [
                {
                    text: 'Tipo',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Fechas evento',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Fechas reparación',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Tiempo falla',
                    align: 'left',
                    sortable: false
                },
                {
                    text: 'Tiempo reparación',
                    align: 'left',
                    sortable: false
                }
            ]
        })
    }
</script>

<style scoped>

</style>

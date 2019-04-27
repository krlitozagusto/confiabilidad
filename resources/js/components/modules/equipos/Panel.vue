<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card flat height="200px">
                <v-toolbar dense dark class="blue darken-1">
                    <v-toolbar-title>
                        Equipos
                        <small class="caption">
                            Consulta y detalle
                        </small>
                    </v-toolbar-title>
                    <!--<v-spacer/>-->
                    <!--<v-tooltip top>-->
                        <!--<v-btn icon slot="activator" @click.stop="newEvent">-->
                            <!--<v-icon>add</v-icon>-->
                        <!--</v-btn>-->
                        <!--<span>Crear evento</span>-->
                    <!--</v-tooltip>-->
                </v-toolbar>
                <data-table
                    ref="tablaEquipos"
                    v-model="dataTable"
                    @resetOption="item => resetOptions(item)"
                    @detailMachine="item => detailMachine(item)"
                ></data-table>
            </v-card>
        </v-flex>
        <detail-dialog ref="detailDialog"></detail-dialog>
    </v-layout>
</template>
<script>
    export default {
		name: "Panel",
        components: {
            DataTable: resolve => {require(['../../general/DataTable'], resolve)},
            DetailDialog: resolve => {require(['./DetailDialog'], resolve)}
        },
		data: () => ({
            selectedItem: null,
            dataTable: {
                nameItemState: 'tablaEquipos',
                route: 'equipos/panel',
                makeHeaders: [
                    {
                        text: 'Concecutivo',
                        align: 'center',
                        sortable: true,
                        value: 'id',
                        classData: 'text-xs-center'
                    },
                    {
                        text: 'Nombre',
                        align: 'left',
                        sortable: true,
                        value: 'nombre'
                    },
                    {
                        text: 'Denominación',
                        align: 'left',
                        sortable: false,
                        value: 'denominacion'
                    },
                    {
                        text: 'Tag',
                        align: 'left',
                        sortable: false,
                        value: 'tag'
                    },
                    {
                        text: 'Núm. equipo',
                        align: 'left',
                        sortable: false,
                        value: 'numero_equipo',
                        tooltip: 'Número de equipo'
                    },
                    {
                        text: 'Val. RAM',
                        align: 'left',
                        sortable: false,
                        value: 'valoracion_ram.nombre',
                        tooltip: 'Valoración Ram'
                    },
                    {
                        text: 'Opciones',
                        align: 'center',
                        sortable: false,
                        actions: true,
                        singlesActions: true,
                        classData: 'text-xs-center'
                    }
                ]
            }
		}),
        methods: {
            resetOptions (item) {
                item.options = []
                item.options.push({event: 'detailMachine', icon: 'details', tooltip: 'Detalle equipo', color: 'primary'})
                return item
            },
            detailMachine (equipo) {
                this.$refs.detailDialog.register(equipo.id)
            }
        }
    }
</script>

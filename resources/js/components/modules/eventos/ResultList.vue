<template>
    <v-layout>
        <v-flex xs12 v-if="!items.length">
            <div class="title text-xs-center grey--text" >No hay registros para mostrar.</div>
        </v-flex>
        <v-flex xs12 v-else>
            <v-card style="overflow-x: scroll !important; overflow-y: hidden !important;">
                <v-card-title class="py-0">
                    <span class="headline">Listado de eventos</span>
                    <span class="caption font-weight-bold" style="margin-top: 6px !important; margin-left: 5px !important;">{{items.length}} Registro{{items.length === 1 ? '' : 's'}}</span>
                    <v-spacer></v-spacer>
                    <v-btn @click="exportar" color="green" class="white--text">
                        <v-icon left>fas fa-file-excel</v-icon>
                        exportar
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <table border="1" style="min-width: 100% !important;" name="tablaitems" id="tablaitems">
                    <thead>
                    <tr>
                        <template v-for="(header, hindex) in headers">
                            <th>
                                {{ header.title }}
                            </th>
                        </template>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, rindex) in items">
                        <td>
                            {{row.id}}
                        </td>
                        <td>
                            {{row.equipo.tag}}
                        </td>
                        <td>
                            {{moment(row.fecha_inicio).format('YYYY-MM-DD HH:mm')}}
                        </td>
                        <td>
                            {{row.fecha_fin}}
                        </td>
                        <td>
                            {{row.downtime}}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table border="1" style="min-width: 100% !important; visibility: collapse !important;" name="tablaitemsexcel" id="tablaitemsexcel">
                    <thead>
                    <tr>
                        <template v-for="(header, hindex) in headers">
                            <th>
                                {{ header.title }}
                            </th>
                        </template>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, rindex) in items">
                        <td>
                            '{{row.id}}'
                        </td>
                        <td>
                            '{{row.equipo.tag}}'
                        </td>
                        <td>
                            '{{moment(row.fecha_inicio).format('YYYY-MM-DD HH:mm')}}'
                        </td>
                        <td>
                            '{{row.fecha_fin}}'
                        </td>
                        <td>
                            '{{row.downtime}}'
                        </td>
                    </tr>
                    </tbody>
                </table>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    import XLSX from 'xlsx'
    export default {
        name: 'ResultList',
        props: {
            result: {
                type: Array,
                default: () => []
            }
        },
        data: () => ({
            headers: [
                {letra: 'A', title: 'Evento'},
                {letra: 'B', title: 'Equipo'},
                {letra: 'C', title: 'Fecha inicial'},
                {letra: 'D', title: 'Fecha final'},
                {letra: 'E', title: 'Downtime'}
            ],
            items: []
        }),
        watch: {
            'result' (val) {
                val && this.resolveData()
            }
        },
        created () {
            this.resolveData()
        },
        methods: {
            exportar () {
                // let elt = document.getElementById('tablaitems')
                // let sh = XLSX.utils.json_to_sheet(this.items)
                // let wb = XLSX.utils.book_new()
                // XLSX.utils.book_append_sheet(wb, sh, 'listadoEventos')
                // this.headers.forEach(x => {
                //     // wb.Sheets['listadoEventos'][`${x.letra}1`].v = x.title
                // })
                // return XLSX.writeFile(wb, 'Eventos.xlsx', { bookType: 'xlsx', bookSST: true})

                let elt = document.getElementById('tablaitemsexcel')
                let wb = XLSX.utils.table_to_book(elt, {sheet: 'tablaitemsexcel'})
                for (const cell in wb.Sheets['tablaitemsexcel']) {
                    if (wb.Sheets['tablaitemsexcel'][cell].v) wb.Sheets['tablaitemsexcel'][cell].v = wb.Sheets['tablaitemsexcel'][cell].v.replace(new RegExp(/(')/i, 'g'), '')
                }
                return XLSX.writeFile(wb, 'Eventos.xls')
            },
            resolveData () {
                this.items = this.result
            }
        }
    }
</script>

<style scoped>

</style>

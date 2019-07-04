<template>
    <v-card v-if="result" style="overflow-x: scroll !important;">
        <v-btn @click="exportar">exportar</v-btn>
        <table border="1" style="min-width: 100% !important;" name="tabla" id="tabla">
            <thead>
            <tr>
                <template v-for="(header, hindex) in result.headers">
                    <th
                        :width="hindex === 0 ? '300' : ''"
                        :colspan="hindex === 0 ? '' : '3'"
                        :rowspan="hindex === 0 ? '2' : ''"
                    >
                        {{ header.title }}
                    </th>
                </template>
            </tr>
            <tr>
                <template v-for="(header, hindex) in result.headers" v-if="hindex > 0">
                    <th>MTBF</th>
                    <th>MTTR</th>
                    <th>Disp.</th>
                </template>
            </tr>
            </thead>
            <tbody>
            <template>
                <tr v-for="(row, rindex) in result.items">
                    <template v-for="(col, cindex) in row">
                        <td width="300" v-if="cindex === 0" :style="'background:' + col.background">
                            {{col.title}}
                        </td>
                        <template v-else>
                            <td :style="'background:' + col.background">
                                {{col.mtbf && col.disp !== '100%' ? `${col.mtbf.horas}H:${col.mtbf.minutos}M` : ''}}
                            </td>
                            <td :style="'background:' + col.background">
                                {{col.mttr && col.disp !== '100%' ? `${col.mttr.horas}H:${col.mttr.minutos}M` : ''}}
                            </td>
                            <td :style="'background:' + col.background">
                                {{col.disp}}
                            </td>
                        </template>
                    </template>
                </tr>
            </template>
            </tbody>
        </table>
    </v-card>
</template>

<script>
    import XLSX from 'xlsx'
    export default {
        name: 'ResultRangosMultiples',
        props: {
            value: {
                type: Object,
                default: null
            },
            delegado: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            result: null
        }),
        watch: {
          'value' (val) {
              val && this.resolveData()
          }
        },
        created () {
            this.resolveData()
        },
        methods: {
            exportar () {
                let elt = document.getElementById('tabla')
                let wb = XLSX.utils.table_to_book(elt, {sheet: 'Disponibilidad'})
                for (const cell in wb.Sheets.Disponibilidad) {
                    if (wb.Sheets.Disponibilidad[cell].v && !isNaN(wb.Sheets.Disponibilidad[cell].v)) wb.Sheets.Disponibilidad[cell].z = '0.00%'
                }
                return XLSX.writeFile(wb, 'disponibilidad.xls')
            },
            resolveData () {
                if (this.value) {
                    let headers = []
                    let items = []
                    let cols = []
                    headers.push({title: 'Taxonomía', subtitle: ''})
                    cols.push({
                        background:  this.value.request.tipoTaxonomia === 'Planta' ? 'aquamarine' : this.value.request.tipoTaxonomia === 'Sistema' ? 'antiquewhite' : '',
                        title: `${this.value.request.tipoTaxonomia === 'Planta' ? this.value.request.taxonomia.emplazamiento : this.value.request.taxonomia.tag} - ${this.value.request.taxonomia.nombre}`,
                        mtbf: null,
                        mttr: null,
                        disp: null
                    })
                    this.value.data.forEach(item => {
                        headers.push({title: `${item.fecha_inicial} al ${item.fecha_final}`, subtitle: ''})
                        cols.push({
                            background:  this.value.request.tipoTaxonomia === 'Planta' ? 'aquamarine' : this.value.request.tipoTaxonomia === 'Sistema' ? 'antiquewhite' : '',
                            title: null,
                            mtbf: item.data.mtbf,
                            mttr: item.data.mttr,
                            disp: item.data.disponibilidad
                        })
                    })
                    items.push(cols)
                    if (this.value.request.tipoTaxonomia === 'Sistema') {
                        let length = this.value.data[0].data.registros.length
                        for (let j = 0; j < length; j++) {
                            let colsS = []
                            colsS.push({
                                background:  '',
                                title: `${this.value.data[0].data.registros[j].equipo.tag} - ${this.value.data[0].data.registros[j].equipo.nombre}`,
                                mtbf: null,
                                mttr: null,
                                disp: null
                            })
                            this.value.data.forEach(item => {
                                colsS.push({
                                    background:  '',
                                    title: null,
                                    mtbf: item.data.registros[j].data.mtbf,
                                    mttr: item.data.registros[j].data.mttr,
                                    disp: item.data.registros[j].data.disponibilidad
                                })
                            })
                            items.push(colsS)
                        }
                    }
                    if (this.value.request.tipoTaxonomia === 'Planta') {
                        let length = this.value.data[0].data.registros.length
                        for (let j = 0; j < length; j++) {
                            let colsS = []
                            colsS.push({
                                background:  'antiquewhite',
                                title: `${this.value.data[0].data.registros[j].sistema.tag} - ${this.value.data[0].data.registros[j].sistema.nombre}`,
                                mtbf: null,
                                mttr: null,
                                disp: null
                            })
                            this.value.data.forEach(item => {
                                colsS.push({
                                    background:  'antiquewhite',
                                    title: null,
                                    mtbf: item.data.registros[j].data.mtbf,
                                    mttr: item.data.registros[j].data.mttr,
                                    disp: item.data.registros[j].data.disponibilidad
                                })
                            })
                            if (colsS.length) items.push(colsS)
                            
                            //dsdsdsdsdd
                            let lengthe = this.value.data[0].data.registros[j].data.registros.length
                            for (let z = 0; z < lengthe; z++) {
                                let colsSE = []
                                colsSE.push({
                                    title: `${this.value.data[0].data.registros[j].data.registros[z].equipo.tag} - ${this.value.data[0].data.registros[j].data.registros[z].equipo.nombre}`,
                                    mtbf: null,
                                    mttr: null,
                                    disp: null
                                })
                                this.value.data.forEach(item => {
                                    colsSE.push({
                                        title: null,
                                        mtbf: item.data.registros[j].data.registros[z].data.mtbf,
                                        mttr: item.data.registros[j].data.registros[z].data.mttr,
                                        disp: item.data.registros[j].data.registros[z].data.disponibilidad
                                    })
                                })
                                if(colsSE.length) items.push(colsSE)
                            }
                        }
                    }
                    this.result = {headers: headers, items: items, request: this.value.request}
                }
            }
        }
    }
</script>

<style scoped>
    table th {
        padding-left: 2px !important;
        padding-right: 2px !important;
        /*display: flex !important;*/
    }

    table td {
        padding-left: 2px !important;
        padding-right: 2px !important;
    }
</style>

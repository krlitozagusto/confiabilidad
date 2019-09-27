<template>
    <v-layout>
        <v-flex xs12 v-if="value && value.data && (!value.data.length || (value.data[0].planta && !value.data[0].data.registros.length))">
            <div class="title text-xs-center grey--text" >No hay registros para mostrar.</div>
        </v-flex>
        <v-flex xs12 v-else>
            <v-card>
                <v-card-title class="py-0">
                    <span class="headline">Resultados de la ejecución</span>
                    <v-spacer></v-spacer>
                    <v-btn @click="exportar" color="green" class="white--text">
                        <v-icon left>fas fa-file-excel</v-icon>
                        exportar
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-tabs
                    v-model="active"
                    color="cyan"
                    dark
                    slider-color="yellow"
                >
                    <v-tab ripple>
                        {{result.request.typeKpi}}

                    </v-tab>
                    <v-tab-item>
                        <v-card flat style="overflow-x: scroll !important; overflow-y: hidden !important;">
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
                                        <th>{{result.request.typeKpi === 'Disponibilidad' ? 'Disp.' : 'Conf.'}}</th>
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
                    </v-tab-item>
                    <v-tab ripple>
                        Gráfica

                    </v-tab>
                    <v-tab-item>
                        <apexchart v-if="grafica" width="70%" :type="result.request.rangos ? 'line' : 'bar'" :options="grafica.options" :series="grafica.series"></apexchart>
                    </v-tab-item>
                    <v-tab ripple>
                        Eventos
                    </v-tab>
                    <v-tab-item>
                        <v-card flat style="overflow-x: scroll !important; overflow-y: hidden !important;">
                            <table border="1" style="min-width: 100% !important;" name="tablaitems" id="tablaitems">
                                <thead>
                                <tr>
                                    <th class="pa-2" :style="`min-width: ${header === 'Comentarios' ? '500' : '120'}px !important;`" v-for="header in headers.map(x => x.title)">
                                        {{ header }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="row in itemsEventos">
                                    <td class="pa-2" v-for="propiedad in headers.map(x => x.propiedad)">
                                        {{row[propiedad]}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table style="visibility: collapse !important;" name="tablaitemsexcel" id="tablaitemsexcel">
                                <tr>
                                    <th v-for="header in headers.map(x => x.title)">
                                        {{ header }}
                                    </th>
                                </tr>
                                <tr v-for="row in itemsEventos">
                                    <td v-for="propiedad in headers.map(x => x.propiedad)">
                                        '{{row[propiedad]}}'
                                    </td>
                                </tr>
                            </table>
                        </v-card>
                    </v-tab-item>
                </v-tabs>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    import XLSX from 'xlsx'
    var es = require("apexcharts/dist/locales/es.json")
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
            },
            tipos: {
                type: Object,
                default: () => {}
            }
        },
        data: () => ({
            grafica: null,
            active: null,
            result: null,
            eventos: [],
            headers: [
                {propiedad: 'id', title: 'Evento'},
                {propiedad: 'contrato', title: 'Contrato'},
                {propiedad: 'campo', title: 'Campo'},
                {propiedad: 'planta', title: 'Planta'},
                {propiedad: 'sistema', title: 'Sistema'},
                {propiedad: 'equipo', title: 'Equipo'},
                {propiedad: 'tipoEvento', title: 'Tipo evento'},
                {propiedad: 'tipoMantenimiento', title: 'Tipo mantenimiento'},
                {propiedad: 'fechaInicio', title: 'Fecha inicio'},
                {propiedad: 'fechaFin', title: 'Fecha fin'},
                {propiedad: 'downtime', title: 'Downtime'},
                {propiedad: 'fechaInicioReparacion', title: 'Fecha inicio reparación'},
                {propiedad: 'fechaFinReparacion', title: 'Fecha fin reparación'},
                {propiedad: 'contractual', title: 'Es contractual'}
            ],
            itemsEventos: []
        }),
        watch: {
          'value' (val) {
              val && this.resolveData()
          },
            'eventos' (val) {
                val && this.resolveDataEventos()
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

                let disponibilidad = document.getElementById('tabla')
                let eventos = document.getElementById('tablaitemsexcel')
                let wb = XLSX.utils.book_new()
                let sh1 = XLSX.utils.table_to_sheet(disponibilidad)
                let sh2 = XLSX.utils.table_to_sheet(eventos)
                let name = this.result.request.typeKpi
                XLSX.utils.book_append_sheet(wb, sh1, name)
                for (const cell in wb.Sheets[name]) {
                    if (wb.Sheets[name][cell].v && !isNaN(wb.Sheets[name][cell].v)) wb.Sheets[name][cell].z = '0.00%'
                }
                XLSX.utils.book_append_sheet(wb, sh2, 'eventos')
                for (const cell in wb.Sheets['eventos']) {
                    if (wb.Sheets['eventos'][cell].v && wb.Sheets['eventos'][cell].v.indexOf(`'`) > -1) wb.Sheets['eventos'][cell].v = wb.Sheets['eventos'][cell].v.replace(new RegExp(/(')/i, 'g'), '')
                    if (wb.Sheets['eventos'][cell].v && wb.Sheets['eventos'][cell].v.indexOf('=') > -1) {
                        wb.Sheets['eventos'][cell] = { f: wb.Sheets['eventos'][cell].v.split('=')[1] }
                    }

                }
                return XLSX.writeFile(wb, `${name}.xls`)
            },
            resolveData () {
                if (this.value) {
                    let headers = []
                    let items = []
                    let cols = []
                    this.eventos = this.value.eventos
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
                        if (this.value.data.length) {
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
                    }
                    if (this.value.request.tipoTaxonomia === 'Planta') {
                        if (this.value.data.length && this.value.data[0].data.registros.length) {
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
                    }
                    this.result = {headers: headers, items: items, request: this.value.request}
                    this.grafica = {
                        series: this.result.request.rangos ? this.result.items.map(z => {return {name: z[0].title, data: z.filter(x => x.disp).map(f => f.disp)}}) : this.result.items.map(z => {return {name: z[0].title, data: [z[1].disp]}}),
                        options: {
                            chart: {
                                locales: [es],
                                defaultLocale: 'es',
                                id: 'Chart',
                                shadow: {
                                    enabled: true,
                                    color: '#000',
                                    top: 18,
                                    left: 7,
                                    blur: 10,
                                    opacity: 1
                                },
                                toolbar: {
                                    show: true
                                }
                            },
                            dataLabels: {
                                enabled: true,
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            title: {
                                text: `${this.result.request.typeKpi} - ${this.result.request.tipoTaxonomia}: ${this.result.items[0][0].title}`,
                                align: 'left'
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                                    opacity: 1
                                },
                            },
                            markers: {

                                size: 6
                            },
                            xaxis: {
                                categories: this.result.request.rangos ? this.result.headers.filter(x => x.title !== 'Taxonomía').map(a => a.title) : this.result.headers.filter((x, i) => i !== 0).map(z => z.title),
                                title: {
                                    text: 'Periodos'
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Porcentaje'
                                }
                            },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'right',
                                floating: false,
                                x: 10,
                                offsetY: -10,
                                offsetX: -10
                            }
                        }

                        // options: {
                        //     chart: {
                        //         locales: [es],
                        //         defaultLocale: 'es',
                        //         id: 'Chart'
                        //     },
                        //     xaxis: {
                        //         categories: this.result.headers.filter((x, i) => i !== 0).map(z => z.title)
                        //     }
                        // },
                        // series: this.result.items.map(z => {return {name: z[0].title, data: [z[1].disp]} } )
                    }
                }
            },
            resolveDataEventos () {
                // Orden de trabajo
                this.headers.push(
                    {propiedad: 'ordenNumero', title: 'número orden'},
                    {propiedad: 'ordenNumeroAviso', title: 'número aviso'},
                    {propiedad: 'ordenPuestoTrabajo', title: 'puesto trabajo'},
                    {propiedad: 'ordenDescripcion', title: 'descripción'}
                )
                // Modo de falla
                this.headers.push(
                    {propiedad: 'fallaSintoma', title: 'Síntoma'},
                    {propiedad: 'fallaSistema', title: 'Sistema'},
                    {propiedad: 'fallaParte', title: 'Parte'},
                    {propiedad: 'fallaAccionCorrectiva', title: 'Acción correctiva'}
                )
                // Impactos
                this.tipos.tiposImpacto.forEach(x => {
                    this.headers.push({propiedad: `impacto${x.id}`, title: x.nombre})
                })
                // Gastos
                this.tipos.tiposGasto.forEach(x => {
                    this.headers.push({propiedad: `gasto${x.id}`, title: x.nombre})
                })
                // Registro
                this.headers.push(
                    {propiedad: 'comentarios', title: 'Comentarios'},
                    {propiedad: 'estado', title: 'Estado'},
                    {propiedad: 'usuario', title: 'Usuario registra'},
                    {propiedad: 'fechaRegistro', title: 'Fecha registra'}
                )

                this.itemsEventos = this.eventos.map(x => {
                    let orden = x.orden_trabajos.length ? x.orden_trabajos[0] : null
                    let falla = x.fallas.length ? x.fallas[0] : null
                    let registro = {
                        id: x.id,
                        contrato: `${x.equipo.sistema.planta.campo.contrato.numero} - ${x.equipo.sistema.planta.campo.contrato.descripcion}`,
                        campo: `${x.equipo.sistema.planta.campo.codigo} - ${x.equipo.sistema.planta.campo.nombre}`,
                        planta: `${x.equipo.sistema.planta.descripcion} - ${x.equipo.sistema.planta.nombre}`,
                        sistema: `${x.equipo.sistema.tag} - ${x.equipo.sistema.nombre}`,
                        equipo: `${x.equipo.tag} - ${x.equipo.nombre}`,
                        tipoEvento: `${x.tipo_evento.abreviado} - ${x.tipo_evento.nombre}`,
                        tipoMantenimiento: x.tipo_mantenimiento.nombre,
                        fechaInicio: x.fecha_inicio,
                        fechaFin: x.fecha_fin,
                        downtime: this.downTime(x.fecha_inicio, x.fecha_fin),
                        fechaInicioReparacion: x.fecha_inicio_reparacion,
                        fechaFinReparacion: x.fecha_fin_reparacion,
                        contractual: x.contractual ? 'SI' : 'NO',
                        // Orden de trabajo
                        ordenNumero: orden ? orden.numero_orden : null,
                        ordenNumeroAviso: orden ? orden.numero_aviso : null,
                        ordenPuestoTrabajo: orden ? orden.puesto_trabajo.nombre : null,
                        ordenDescripcion: orden ? orden.descripcion : null,
                        // Modo de falla
                        fallaSintoma: falla ? falla.sintoma : null,
                        fallaSistema: falla ? falla.sistema : null,
                        fallaParte: falla ? falla.parte : null,
                        fallaModo: falla ? falla.modo_falla.descripcion : null,
                        fallaAccionCorrectiva: falla ? falla.accion_correctiva : null,
                        estado: x.estado,
                        usuario: x.user.name,
                        fechaRegistro: x.fecha_registro,
                        comentarios: x.comentarios.map(c => {
                            return `${this.moment(c.fecha).format('YYYY-MM-DD HH-mm')} >> ${c.user.name} >> ${c.descripcion}`
                        }).join(', ')
                    }
                    this.tipos.tiposImpacto.forEach(y => {
                        registro[`impacto${y.id}`] = !!x.impactos.find(z => z.tipo_impacto_id === y.id) ? (x.impactos.find(z => z.tipo_impacto_id === y.id).cantidad + ' ' + y.medida) : ''
                    })
                    this.tipos.tiposGasto.forEach(y => {
                        registro[`gasto${y.id}`] = !!x.gastos.find(z => z.tipo_gasto_id === y.id) ? this.currency(x.gastos.find(z => z.tipo_gasto_id === y.id).valor) : ''
                    })
                    return registro
                })
            },
            downTime (fi, ff) {
                if (fi) {
                    let fechaInicio = fi.split(' ')[0]
                    let horaInicio = fi.split(' ')[1] || '00:00'
                    let fechaFin = ff.split(' ')[0] || this.moment().format('YYYY-MM-DD')
                    let horaFin = ff.split(' ')[1] || (ff.split(' ')[0] ? '00:00' : this.moment().format('HH:mm'))

                    let hi = this.moment(`${fechaInicio} ${horaInicio}`)
                    let hf = this.moment(`${fechaFin} ${horaFin}`)
                    return String(Math.round((parseFloat(hf.diff(hi, 'hours', true)))*100)/100)
                }
                return '0'
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

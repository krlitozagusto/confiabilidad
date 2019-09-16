<template>
    <v-layout>
        <v-flex xs12 v-if="!items.length">
            <div class="title text-xs-center grey--text" >No hay registros para mostrar.</div>
        </v-flex>
        <v-flex xs12 v-else>
            <v-card>
                <v-card-title class="py-0">
                    <span class="headline">Listado de eventos</span>
                    <span class="caption font-weight-bold" style="margin-top: 6px !important; margin-left: 5px !important;">{{items.length}} Registro{{items.length === 1 ? '' : 's'}}</span>
                    <v-spacer></v-spacer>
                    <v-btn @click="exportar" color="green" class="white--text">
                        <v-icon left>fas fa-file-excel</v-icon>
                        exportar
                    </v-btn>
                </v-card-title>
            </v-card>
            <v-card style="overflow-x: scroll !important; overflow-y: hidden !important;">
                <table border="1" style="min-width: 100% !important;" name="tablaitems" id="tablaitems">
                    <thead>
                    <tr>
                        <th class="pa-2" :style="`min-width: ${header === 'Comentarios' ? '500' : '120'}px !important;`" v-for="(header, hindex) in headers.map(x => x.title)">
                            {{ header }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="row in items">
                        <td  class="pa-2" v-for="propiedad in headers.map(x => x.propiedad)">
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
                    <tr v-for="row in items">
                        <td v-for="propiedad in headers.map(x => x.propiedad)">
                            '{{row[propiedad]}}'
                        </td>
                    </tr>
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
            },
            tipos: {
                type: Object,
                default: () => {}
            }
        },
        data: () => ({
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
                let elt = document.getElementById('tablaitemsexcel')
                let wb = XLSX.utils.table_to_book(elt, {sheet: 'tablaitemsexcel'})
                for (const cell in wb.Sheets['tablaitemsexcel']) {
                    if (wb.Sheets['tablaitemsexcel'][cell].v && wb.Sheets['tablaitemsexcel'][cell].v.indexOf(`'`) > -1) wb.Sheets['tablaitemsexcel'][cell].v = wb.Sheets['tablaitemsexcel'][cell].v.replace(new RegExp(/(')/i, 'g'), '')
                    if (wb.Sheets['tablaitemsexcel'][cell].v && wb.Sheets['tablaitemsexcel'][cell].v.indexOf('=') > -1) {
                        wb.Sheets['tablaitemsexcel'][cell] = { f: wb.Sheets['tablaitemsexcel'][cell].v.split('=')[1] }
                    }

                }
                return XLSX.writeFile(wb, 'Eventos.xlsx', {cellFormula: true})
            },
            resolveData () {
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
                this.items = this.result.map(x => {
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

</style>

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
                        <th v-for="(header, hindex) in headers.map(x => x.title)">
                            {{ header }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="row in items">
                        <td v-for="propiedad in headers.map(x => x.propiedad)">
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
                {propiedad: 'contractual', title: 'Es contractual'},
                {propiedad: 'estado', title: 'Estado'},
                {propiedad: 'usuario', title: 'Usuario registra'},
                {propiedad: 'fechaRegistro', title: 'Fecha registra'}
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
                console.log('el book', wb.Sheets['tablaitemsexcel'])
                return XLSX.writeFile(wb, 'Eventos.xlsx', {cellFormula: true})
            },
            resolveData () {

                this.items = this.result.map(x => {
                    return {
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
                        estado: x.estado,
                        usuario: x.user.name,
                        fechaRegistro: x.fecha_registro
                    }
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

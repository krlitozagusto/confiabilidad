<template>
    <v-layout row wrap v-if="value">
        <v-flex xs12 sm6 md3>
            <v-switch
                readonly
                ripple
                label="Es evento principal"
                :false-value="0"
                :true-value="1"
                v-model="esPrincipal"
            ></v-switch>
        </v-flex>
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            label="Tipo evento"
            :text="value.tipo_evento && value.tipo_evento.nombre"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            prepend-icon="event"
            label="Fecha registro"
            :text="value.fecha_registro"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            label="Estado"
            :text="value.estado"
        />
        <input-detail-flex
            v-if="!esPrincipal"
            flex-class="xs12 sm6 md3"
            prepend-icon="beenhere"
            label="Evento principal"
            :text="value.evento_padre.id"
        />
        <input-detail-flex
            flex-class="xs12 sm12 md6"
            label="Equipo"
            :text="value.equipo && (value.equipo.nombre + ' - Tag: ' + value.equipo.tag)"
            :hint="value.equipo && ('Número: ' + value.equipo.numero_equipo + ' - Campo: ' + value.equipo.sistema.planta.campo.nombre)"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            prepend-icon="event"
            label="Fecha inicio"
            :text="value.fecha_inicio"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            prepend-icon="event"
            label="Fecha fin"
            :text="value.fecha_fin"
        />
        <v-flex xs12 sm6 md3>
            <v-switch
                readonly
                ripple
                label="Contractual"
                :false-value="0"
                :true-value="1"
                v-model="value.contractual"
            ></v-switch>
        </v-flex>
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            label="Tipo mantenimiento"
            :text="value.tipo_mantenimiento && value.tipo_mantenimiento.nombre"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            prepend-icon="event"
            label="Fecha inicio reparación"
            :text="value.fecha_inicio_reparacion"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md3"
            prepend-icon="event"
            label="Fecha fin reparación"
            :text="value.fecha_fin_reparacion"
        />
        <input-detail-flex
                flex-class="xs12 sm3 md2"
                label="DownTime"
                :text="downTime"
        />
        <input-detail-flex
            flex-class="xs12 sm9 md10"
            label="Soporte"
            :text="value.archivo_soporte"
            :append-button="value.archivo_soporte ? {tooltip: 'descargar archivo', icon: 'arrow_downward', color: 'primary'} : null"
            @appendButtonClick="downloadFile('/eventos/downloadsoporte/' + value.id)"
        />
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'type'],
		name: "DetailGeneral",
        components: {
            InputDetailFlex: resolve => {require(['../../../../general/InputDetailFlex'], resolve)}
        },
		data: () => ({
		}),
		computed: {
            downTime () {
                if (this.value.fecha_inicio) {
                    let fechaInicio = this.value.fecha_inicio.split(' ')[0]
                    let horaInicio = this.value.fecha_inicio.split(' ')[1] || '00:00'
                    let fechaFin = this.value.fecha_fin.split(' ')[0] || this.moment().format('YYYY-MM-DD')
                    let horaFin = this.value.fecha_fin.split(' ')[1] || (this.value.fecha_fin.split(' ')[0] ? '00:00' : this.moment().format('HH:mm'))

                    let hi = this.moment(`${fechaInicio} ${horaInicio}`)
                    let hf = this.moment(`${fechaFin} ${horaFin}`)
                    return String(Math.round((parseFloat(hf.diff(hi, 'hours', true)))*100)/100)
                }
                return '0'
            }
		},
        whatch: {
        },
		created () {
		},
        methods: {
        }
    }
</script>

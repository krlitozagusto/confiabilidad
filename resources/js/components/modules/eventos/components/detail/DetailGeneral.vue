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
            :text="value.equipo && value.equipo.nombre"
            :hint="value.equipo && value.equipo.tag"
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
		},
        whatch: {
        },
		created () {
		},
        methods: {
        }
    }
</script>

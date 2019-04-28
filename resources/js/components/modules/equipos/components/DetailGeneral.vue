<template>
    <v-layout row wrap v-if="value">
        <input-detail-flex
            flex-class="xs12 sm12 md6"
            label="Ubicación técnica"
            :text="value.ubicacion_tecnica && value.ubicacion_tecnica.nombre"
            :hint="`Tag: ${value.ubicacion_tecnica && value.ubicacion_tecnica.tag}`"
        />
        <input-detail-flex
            flex-class="xs12 sm12 md6"
            label="Nombre"
            :text="value.nombre"
        />
        <input-detail-flex
            flex-class="xs12 sm12 md6"
            label="Denominación"
            :text="value.denominacion"
        />
        <input-detail-flex
            flex-class="xs12 sm12 md6"
            label="Descripción"
            :text="value.descripcion"
        />
        <input-detail-flex
            flex-class="xs12 sm12 md6"
            label="Tag"
            :text="value.tag"
        />
        <input-detail-flex
            flex-class="xs12 sm12 md6"
            label="Número de equipo"
            :text="value.numero_equipo"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md4"
            label="Valoración RAM"
            :text="value.valoracion_ram && value.valoracion_ram.nombre"
        />
        <input-detail-flex
            flex-class="xs12 sm6 md4"
            label="Centro de costo"
            :text="value.centro_costo && value.centro_costo.nombre"
        />
        <v-flex xs12>
            <div class="title text-xs-center grey--text" v-if="!value.planes_mantenimiento.length">No hay planes de mantenimiento registrados para éste equipo.</div>
            <v-list three-line v-else>
                <template v-for="(plan, index) in value.planes_mantenimiento">
                    <v-list-tile
                        :key="index"
                        avatar
                        @click=""
                    >
                        <v-list-tile-avatar color="teal" class="white--text">
                            {{index + 1}}
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title v-html="plan.frecuencia"></v-list-tile-title>
                            <v-list-tile-sub-title v-html="plan.numero_plan"></v-list-tile-sub-title>
                            <v-list-tile-sub-title v-html="plan.descripcion"></v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-divider></v-divider>
                </template>
            </v-list>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'type'],
		name: "DetailGeneral",
        components: {
            InputDetailFlex: resolve => {require(['../../../general/InputDetailFlex'], resolve)}
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

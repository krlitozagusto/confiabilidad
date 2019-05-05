<template>
    <v-layout row wrap>
        <template v-if="orden">
            <input-detail-flex
                flex-class="xs12 sm6 md2"
                label="Número de orden"
                :text="orden.numero_orden"
            />
            <input-detail-flex
                flex-class="xs12 sm6 md2"
                label="Número de aviso"
                :text="orden.numero_aviso"
            />
            <input-detail-flex
                flex-class="xs12 sm12 md8"
                label="Puesto de trabajo"
                :text="orden.puesto_trabajo && (orden.puesto_trabajo.codigo + ' - ' + orden.puesto_trabajo.nombre)"
                :hint="orden.puesto_trabajo && orden.puesto_trabajo.descripcion"
            />
            <input-detail-flex
                flex-class="xs12"
                label="Descripción"
                :text="orden.descripcion"
            />
        </template>
        <v-flex xs12 v-else>
            <div class="title text-xs-center grey--text" >No hay orden de trabajo registrada para éste evento.</div>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'type'],
		name: "DetailOrdenTrabajo",
        components: {
            InputDetailFlex: resolve => {require(['../../../../general/InputDetailFlex'], resolve)}
        },
		data: () => ({
		}),
		computed: {
            orden () {
                return this.value.orden_trabajos[0]
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

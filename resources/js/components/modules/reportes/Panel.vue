<template>
    <v-layout align-center justify-center row fill-height>
        <v-flex xs12>
            <v-list two-line>
                <template v-for="(item, index) in items">
                    <v-list-tile
                        :key="index"
                        avatar
                        @click=""
                    >
                        <v-list-tile-avatar color="purple" class="white--text">
                            {{index + 1}}
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                            <v-list-tile-sub-title>{{ item.subtitle }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-btn ripple color="teal" class="white--text" @click.stop="ejecutarReporte(item)">Ejecutar</v-btn>
                        </v-list-tile-action>
                    </v-list-tile>
                    <v-divider></v-divider>
                </template>
            </v-list>
        </v-flex>
        <disponibilidad ref="Disponibilidad"></disponibilidad>
    </v-layout>
</template>

<script>
    import Disponibilidad from './components/Disponibilidad'
	export default {
		name: 'PanelReportes',
        components: {
            Disponibilidad
        },
        data: () => ({
            items: [
                {title: 'Disponibilidad', subtitle: 'Cálculo de disponibilidad, tiempo medio entre fallas y reparaciones.', ref: 'Disponibilidad', type: 'Disponibilidad' },
                {title: 'Confiabilidad', subtitle: 'Cálculo de confiabilidad, tiempo medio entre fallas y reparaciones.', ref: 'Disponibilidad', type: 'Confiabilidad' }
            ]
        }),
        methods: {
            ejecutarReporte (item) {
                this.$refs[item.ref].open(item.type)
            }
        }
	}
</script>

<style scoped>

</style>

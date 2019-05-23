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
        <tiempo-medio ref="TiempoMedio"></tiempo-medio>
    </v-layout>
</template>

<script>
    import TiempoMedio from './components/TiempoMedio'
	export default {
		name: 'PanelReportes',
        components: {
            TiempoMedio
        },
        data: () => ({
            items: [
                {title: 'Tiempo medio', subtitle: 'Calcular el tiempo medio entre fallas y reparaciones.', ref: 'TiempoMedio' }
            ]
        }),
        methods: {
            ejecutarReporte (item) {
                this.$refs[item.ref].open()
            }
        }
	}
</script>

<style scoped>

</style>

<template>
    <v-expansion-panel v-if="result">
        <v-expansion-panel-content>
            <template slot="header">
                <v-layout row wrap>
                    <v-flex xs12>
                        <span class="subheading">{{result.planta.nombre}}</span>
                    </v-flex>
                    <v-layout row justify-space-between class="px-2">
                        <span class="body-2">Disponibilidad: {{result.data.disponibilidad}}</span>
                        <span class="body-2" v-if="result.data.mtbf">MTBF: {{result.data.mtbf.horas}}H:{{result.data.mtbf.minutos}}M</span>
                        <span class="body-2" v-if="result.data.mttr">MTTR: {{result.data.mttr.horas}}H:{{result.data.mttr.minutos}}M</span>
                    </v-layout>
                </v-layout>
            </template>
            <v-divider></v-divider>
            <v-card>
                <v-card-title><strong>Tiempo intervalo: {{result.data.intervalo.total_horas}}Horas</strong></v-card-title>
                <v-card-text class="pt-0">
                    <template v-for="(resultSistema, index) in result.data.registros">
                        <result-sistema
                            delegado
                            :result="resultSistema"
                            :key="'resultSistema' + index"
                        ></result-sistema>
                        <v-divider v-if="index < (result.data.registros.length - 1)"></v-divider>
                    </template>
                </v-card-text>
            </v-card>
        </v-expansion-panel-content>
    </v-expansion-panel>
</template>

<script>
    export default {
        name: 'ResultPlanta',
        props: ['result'],
        components: {
            ResultSistema: resolve => {require(['./ResultSistema'], resolve)}
        },
        data: () => ({
        })
    }
</script>

<style scoped>

</style>

<template>
    <v-layout row wrap>
        <template v-if="value && value.orden_trabajos[0]">
            <v-flex xs12 sm6 md3>
                <v-text-field
                        label="Número orden"
                        v-model="value.orden_trabajos[0].numero_orden"
                        name="Número orden"
                        v-validate="'required'"
                        :error-messages="errors.collect('Número orden')"
                ></v-text-field>
            </v-flex>
            <v-flex xs12 sm6 md3>
                <v-text-field
                        label="Número aviso"
                        v-model="value.orden_trabajos[0].numero_aviso"
                        name="Número aviso"
                        v-validate="'required'"
                        :error-messages="errors.collect('Número aviso')"
                ></v-text-field>
            </v-flex>
            <v-flex xs12 sm12 md6>
                <v-select
                        ke="selectTipo"
                        label="Puesto de trabajo"
                        :items="complementos.puestosTrabajo"
                        v-model="value.orden_trabajos[0].puesto_trabajo_id"
                        item-value="id"
                        item-text="nombre"
                        name="puesto de trabajo"
                        v-validate="'required'"
                        :error-messages="errors.collect('puesto de trabajo')"
                >
                    <template slot="item" slot-scope="data">
                        <div style="width: 100% !important;">
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>{{data.item.codigo}}-{{ data.item.nombre}}</v-list-tile-title>
                                    <v-list-tile-sub-title class=caption>{{data.item.descripcion}}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </div>
                    </template>
                </v-select>
            </v-flex>
            <v-flex xs12 sm12 md12>
                <v-textarea
                        label="Descripción"
                        auto-grow
                        rows="1"
                        v-model="value.orden_trabajos[0].descripcion"
                        name="descripción"
                        v-validate="'required'"
                        :error-messages="errors.collect('descripción')"
                />
            </v-flex>
        </template>
        <v-flex xs12 v-else>
            <div class="title text-xs-center grey--text" >
                No hay orden de trabajo registrada para éste evento.
                <v-tooltip top>
                    <v-btn flat icon color="pink" slot="activator" @click="addOrden">
                        <v-icon>add</v-icon>
                    </v-btn>
                    <span>Crear orden de trabajo</span>
                </v-tooltip>
            </div>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'soloGuardar', 'complementos'],
		name: "RegisterOrdenTrabajo",
        components: {
        },
		data: () => ({
            makeOrden: {
                numero_orden: null,
                descripcion: null,
                numero_aviso: null,
                puesto_trabajo_id: null
            }
		}),
		computed: {
		},
        whatch: {
        },
		created () {
		},
        methods: {
            addOrden () {
                this.value.orden_trabajos.push(window.lodash.clone(this.makeOrden))
            },
            async reset () {
                await this.$validator.reset()
            }
        }
    }
</script>

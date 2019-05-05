<template>
    <v-layout column v-if="value">
        <v-flex xs12>
            <v-card>
                <v-card-text>
                    <v-textarea
                        v-if="type === 'Comentarios'"
                        v-model="comentario"
                        prepend-icon="comment"
                        box
                        clearable
                        label="Comentario"
                        rows="1"
                        auto-grow
                        name="Comentario"
                        v-validate="'required'"
                        :error-messages="errors.collect('Comentario')"
                    >
                        <v-tooltip
                            slot="append-outer"
                            top
                        >
                            <v-btn
                                style="top: -12px"
                                slot="activator"
                                flat
                                icon
                                color="info"
                                @click.native="enviarComentario"
                            >
                                <v-icon>send</v-icon>
                            </v-btn>
                            <span>Enviar comentario</span>
                        </v-tooltip>
                    </v-textarea>
                    <div class="title text-xs-center grey--text" v-if="!value.comentarios.length">No hay comentarios registrados para éste evento.</div>
                    <v-timeline
                        v-else
                        align-top
                        dense
                    >
                        <v-timeline-item
                            v-for="(comentario, index) in value.comentarios"
                            :key="index"
                            color="pink"
                            small
                        >
                            <v-layout pt-3>
                                <v-flex xs3>
                                    <strong>{{moment(comentario.fecha).format('YYYY-MM-DD')}}</strong>
                                    <strong>{{moment(comentario.fecha).format('HH:mm')}}</strong>
                                </v-flex>
                                <v-flex>
                                    <div class="caption">{{comentario.descripcion}}</div>
                                    <strong>{{comentario.usuario.name}}</strong>
                                </v-flex>
                            </v-layout>
                        </v-timeline-item>
                    </v-timeline>
                </v-card-text>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'type'],
		name: "DetailComentarios",
        components: {
        },
		data: () => ({
            comentario: null
		}),
		computed: {
		},
        whatch: {
        },
		created () {
            this.resetModel()
		},
        methods: {
            resetModel () {
                this.comentario = null
                this.$validator.reset()
            },
            enviarComentario () {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.$store.commit('LOADING', true)
                        axios.post('eventos/registercomment', {evento_id: this.value.id, descripcion: this.comentario})
                            .then(response => {
                                console.log('el response del comentario', response)
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'success', message: `Comentartio registrado correctamente`})
                                let eventCopy = window.lodash.clone(this.value)
                                eventCopy.comentarios.push(response.data.comentario)
                                this.$emit('input', eventCopy)
                                this.resetModel()
                            })
                            .catch(error => {
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'error', message: `al registrar el comentario`, error: error})
                            })
                    }
                })
            }
        }
    }
</script>

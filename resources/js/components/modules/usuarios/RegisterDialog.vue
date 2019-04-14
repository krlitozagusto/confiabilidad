<template>
    <v-layout>
        <v-dialog v-model="open" persistent max-width="500" scrollable>
            <v-card>
                <v-card-title class="headline">{{usuario.id ? 'Edición de usuario' : 'Nuevo usuario'}}</v-card-title>
                <v-card-text>
                    <v-text-field
                        label="Nombre de usuario"
                        v-model="usuario.name"
                        name="Nombre de usuario"
                        v-validate="'required'"
                        :error-messages="errors.collect('Nombre de usuario')"
                    ></v-text-field>
                    <v-text-field
                        label="Correo electrónico"
                        v-model="usuario.email"
                        name="Correo electrónico"
                        v-validate="'required|email'"
                        :error-messages="errors.collect('Correo electrónico')"
                    ></v-text-field>
                    <v-autocomplete
                        label="Empleado"
                        v-model="usuario.empleado"
                        :items="empleados"
                        name="Empleado"
                        v-validate="'required'"
                        :error-messages="errors.collect('Empleado')"
                        :filter="filterEmpleados"
                        no-data-text="No hay resultados para mostrar"
                    >
                        <template slot="selection" slot-scope="data">
                            <div style="width: 100% !important;">
                                <v-list-tile>
                                    <v-list-tile-content>
                                        <v-list-tile-title>{{ data.item.nombre }}</v-list-tile-title>
                                        <v-list-tile-sub-title class=caption>{{ data.item.identificacion }} - {{ data.item.email }}</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </div>
                        </template>
                        <template slot="item" slot-scope="data">
                            <div style="width: 100% !important;">
                                <v-list-tile>
                                    <v-list-tile-content>
                                        <v-list-tile-title>{{ data.item.nombre }}</v-list-tile-title>
                                        <v-list-tile-sub-title class=caption>{{ data.item.identificacion }} - {{ data.item.email }}</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </div>
                        </template>
                    </v-autocomplete>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn flat @click="close">Cancelar</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" dark @click="submit">Registrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
    export default {
		name: "RegisterDialog",
		data: () => ({
            usuario: null,
            makeUsuario: {
                id: null,
                name: null,
                email: null,
                avatar: null,
                empleado: null
            },
            roles: null,
            empleados: [],
            open: false,
            showPassword: false,
            filterEmpleados (item, queryText) {
                const hasValue = val => val != null ? val : ''
                const text = hasValue(item.identificacion + ' ' + item.nombre)
                const query = hasValue(queryText)
                return text.toString().toLowerCase().indexOf(query.toString().toLowerCase()) > -1
            }
		}),
		computed: {
		},
        whatch: {
		    'open' (val) {
		        !val && (this.showPassword = false)
            }
        },
		created () {
		    this.resetModel()
            this.avatars = this.$store.state.general.avatars
		},
        methods: {
            register () {
                this.$store.commit('LOADING', true)
                this.axios.post('usuarios/newuser')
                    .then(response => {
                        this.$store.commit('LOADING', false)
                        this.open = true
                        if (response.data.usuario) this.usuario = response.data.usuario
                        this.empleado = response.data.empleado
                        this.roles = response.data.roles
                        this.empleados = response.data.empleados
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        this.$store.commit('SNACKBAR', {color: 'error', message: `al traer los colplementos del formulario de registro de usuario`, error: error})
                    })
            },
            close () {
                this.open = false
                setTimeout(() => {
                    this.resetModel()
                }, 300)
            },
            submit () {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.$store.commit('LOADING', true)
                        axios.post('usuarios/registernewuser', this.usuario)
                            .then(response => {
                                console.log('el response', response)
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'success', message: `Usuario registrado correctamente`})
                                this.$store.commit('REGISTER_USER', response.data.usuario)
                                this.close()
                            })
                            .catch(error => {
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'error', message: `al registrar el usuario`, error: error})
                            })
                    }
                })
            },
            resetModel () {
                this.usuario = window.lodash.clone(this.makeUsuario)
                this.empleado = null
                this.roles = null
                this.empleados = []
                this.showPassword = false
                this.$validator.reset()
            }
        }
    }
</script>

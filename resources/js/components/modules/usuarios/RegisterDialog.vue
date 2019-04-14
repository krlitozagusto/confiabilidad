<template>
    <v-layout>
        <v-dialog v-model="open" persistent max-width="500" scrollable>
            <v-card>
                <v-card-title class="headline">{{user.id ? 'Edición de usuario' : 'Nuevo usuario'}}</v-card-title>
                <v-card-text>
                    <v-text-field
                        label="Nombre de usuario"
                        v-model="user.name"
                        name="Nombre de usuario"
                        v-validate="'required'"
                        :error-messages="errors.collect('Nombre de usuario')"
                    ></v-text-field>
                    <v-text-field
                        label="Correo electrónico"
                        v-model="user.email"
                        name="Correo electrónico"
                        v-validate="'required|email'"
                        :error-messages="errors.collect('Correo electrónico')"
                    ></v-text-field>
                    <v-text-field
                        label="Contraseña"
                        v-model="user.password"
                        :append-icon="showPassword ? 'visibility' : 'visibility_off'"
                        :type="showPassword ? 'text' : 'password'"
                        name="Contraseña"
                        v-validate="'required'"
                        :error-messages="errors.collect('Contraseña')"
                        @click:append="showPassword = !showPassword"
                    ></v-text-field>
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
            user: null,
            makeUser: {
              id: null,
              name: null,
              email: null,
              password: null,
              avatar: null
            },
            roles: null,
            empleados: null,
            open: false,
            showPassword: false
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
		},
        methods: {
            register (id) {
                this.$store.commit('LOADING', true)
                axios.post('usuarios/newuser')
                    .then(response => {
                        this.$store.commit('LOADING', false)
                        this.open = true
                        if (response.data.user) this.user = response.data.user
                        this.roles = response.data.roles
                        this.empleados = response.data.empleados
                    })
                    .catch(error => {
                        this.$store.commit('LOADING', false)
                        commit(SNACKBAR, {color: 'error', message: `al traer los colplementos del formulario de registro de usuario`, error: error})
                    })
            },
            close () {
                this.open = false
                setTimeout(() => {
                    this.resetModel()
                }, 300)
            },
            submit () {
            },
            resetModel () {
                this.user = window.lodash.clone(this.makeUser)
                this.$validator.reset()
            }
        }
    }
</script>

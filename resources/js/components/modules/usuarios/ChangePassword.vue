<template>
    <v-layout>
        <v-dialog v-model="open" persistent max-width="400" scrollable>
            <v-card>
                <v-card-title class="headline">Cambio de contraseña</v-card-title>
                <v-card-text>
                    <v-text-field
                        label="Contraseña actual"
                        v-model="password"
                        :append-icon="showPassword ? 'visibility' : 'visibility_off'"
                        :type="showPassword ? 'text' : 'password'"
                        name="Contraseña actual"
                        v-validate="'required'"
                        :error-messages="errors.collect('Contraseña actual')"
                        @click:append="showPassword = !showPassword"
                    ></v-text-field>
                    <v-text-field
                        label="Nueva contraseña"
                        v-model="password1"
                        :append-icon="showPassword1 ? 'visibility' : 'visibility_off'"
                        :type="showPassword1 ? 'text' : 'password'"
                        name="Nueva contraseña"
                        v-validate="{min:6, required: true, is_not:password}"
                        :error-messages="errors.collect('Nueva contraseña')"
                        @click:append="showPassword1 = !showPassword1"
                    ></v-text-field>
                    <v-text-field
                        label="Contraseña de confirmación"
                        v-model="password2"
                        :append-icon="showPassword2 ? 'visibility' : 'visibility_off'"
                        :type="showPassword2 ? 'text' : 'password'"
                        name="Contraseña de confirmación"
                        v-validate="{min:6, required: true, is:password1, is_not:password}"
                        :error-messages="errors.collect('Contraseña de confirmación')"
                        @click:append="showPassword2 = !showPassword2"
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
    import {mapState} from 'vuex'
    export default {
		name: "ChangePassword",
		data: () => ({
            password: null,
            password1: null,
            password2: null,
            open: false,
            showPassword: false,
            showPassword1: false,
            showPassword2: false
		}),
        computed: {
            ...mapState({
                user: state => state.user.currentUser
            }),

        },
        whatch: {
        },
		created () {
		    this.resetModel()
		},
        methods: {
            close () {
                this.open = false
                setTimeout(() => {
                    this.resetModel()
                }, 300)
            },
            register () {
              this.open = true
            },
            submit () {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.$store.commit('LOADING', true)
                        axios.post('usuarios/changepassword', {user_id: this.user.id, current_password: this.password, password: this.password1, password_confirmation: this.password2})
                            .then(response => {
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'success', message: `La contraseña se ha cambiado correctamente`})
                                this.close()
                            })
                            .catch(error => {
                                this.$store.commit('LOADING', false)
                                this.$store.commit('SNACKBAR', {color: 'error', message: `al cambiar la contraseña`, error: error})
                            })
                    }
                })
            },
            resetModel () {
                this.password = null
                this.password1 = null
                this.password2 = null
                this.showPassword = false
                this.showPassword1 = false
                this.showPassword2 = false
                this.$validator.reset()
            }
        }
    }
</script>

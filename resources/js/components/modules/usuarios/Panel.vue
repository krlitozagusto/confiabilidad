<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card flat height="200px">
                <v-toolbar dense dark class="blue darken-1">
                    <v-toolbar-title>
                        Usuarios
                        <small class="caption">
                            Registro y gestión
                        </small>
                    </v-toolbar-title>
                    <v-spacer/>
                    <v-tooltip top>
                        <v-btn icon slot="activator" @click.stop="newUser">
                            <v-icon>add</v-icon>
                        </v-btn>
                        <span>Crear usuario</span>
                    </v-tooltip>
                </v-toolbar>
                <v-card-title>
                    <v-spacer/>
                    <v-text-field
                            v-model="search"
                            append-icon="search"
                            label="Buscar"
                            single-line
                            hide-details
                    />
                </v-card-title>
                <v-data-table
                        :headers="headers"
                        :items="items"
                        :search="search"
                        rows-per-page-text="Registros por página:"
                        :rows-per-page-items="[10,20,50,{'text':'Todos','value':-1}]"
                >
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left">{{ props.item.name }}</td>
                        <td class="text-xs-left">{{ props.item.email }}</td>
                        <td class="text-xs-left">{{ props.item.empleado && props.item.empleado.celular }}</td>
                        <td class="text-xs-left">{{ props.item.empleado && props.item.empleado.direccion }}</td>
                        <!--<td class="text-xs-center">-->
                            <!--<v-tooltip top>-->
                                <!--<v-btn icon slot="activator" @click.stop="">-->
                                    <!--<v-icon color="primary">mode_edit</v-icon>-->
                                <!--</v-btn>-->
                                <!--<span>Editar usuario</span>-->
                            <!--</v-tooltip>-->
                        <!--</td>-->
                    </template>
                    <v-alert  slot="no-results" :value="true" color="error" icon="warning">
                        Lo sentimos, no tenemos registros para mostrar. <v-icon>sentiment_very_dissatisfied</v-icon>
                    </v-alert>
                </v-data-table>
            </v-card>
        </v-flex>
        <register-dialog ref="registerDialog"></register-dialog>
    </v-layout>
</template>
<script>
	import {mapState} from 'vuex'
    export default {
		name: "Panel",
        components: {
            RegisterDialog: resolve => {require(['./RegisterDialog'], resolve)}
        },
		data: () => ({
			search: '',
			headers: [
				{ text: 'Nombre', value: 'name' },
				{ text: 'Correo electrónico', value: 'email' },
                { text: 'Celular', value: 'empleado.celular', sortable: false },
                { text: 'Dirección', value: 'empleado.direccion', sortable: false }
				// { text: 'Opciones', value: 'id', sortable: false, align: 'center' }
			],
		}),
		computed: {
			...mapState({
				items: state => state.user.model.usuarios
			})
		},
		beforeCreate () {
			this.$store.dispatch('USER_PANEL')
		},
        methods: {
		    newUser () {
                this.$refs.registerDialog.register()
            }
        }
    }
</script>

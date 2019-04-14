import axios from 'axios'
import {LOADING, SNACKBAR} from './general'
export const USER_PANEL = 'USER_PANEL'
export const CURRENT_USER = 'CURRENT_USER'
export const ASSIGN_MODEL = 'ASSIGN_MODEL'
export const REGISTER_USER = 'REGISTER_USER'
export default {
	state: {
		model: {
			usuarios: []
		},
		makeUsuario: {
			id: null,
			name: null,
			email: null
		},
		usuario: {},
        currentUser: null
	},
	getters: {
	},
	mutations: {
		[ASSIGN_MODEL]: (state, model) => {
			state.model = model
		},
        [CURRENT_USER]: (state, data) => {
            state.currentUser = data
        },
        [REGISTER_USER]: (state, user) => {
            state.model.usuarios.push(user)
        }
	},
	actions: {
        [CURRENT_USER]: ({commit, dispatch}) => {
            return new Promise(() => {
                axios.post('usuarios/current')
                    .then(response => {
                        commit(CURRENT_USER, response.data.currentUser)
                    })
                    .catch(error => {
                        commit(SNACKBAR, {color: 'error', message: `al recuperar el usuario actual`, error: error})
                    })
            })
        },
		[USER_PANEL]: ({commit, dispatch}) => {
            commit(LOADING, true)
			return new Promise(() => {
				axios.post('usuarios/panel')
					.then(response => {
                        commit(LOADING, false)
                        commit(ASSIGN_MODEL, response.data)
					})
					.catch(error => {
                        commit(LOADING, false)
                        commit(SNACKBAR, {color: 'error', message: `al traer el listado de usuarios`, error: error})
					})
			})
		}
	}
}

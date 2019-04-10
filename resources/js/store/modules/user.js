import axios from 'axios'
import {LOADING} from './general'
export const USER_PANEL = 'USER_PANEL'
export const ASSIGN_MODEL = 'ASSIGN_MODEL'
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
		usuario: {}
	},
	getters: {
	},
	mutations: {
		[ASSIGN_MODEL]: (state, model) => {
			state.model = model
		}
	},
	actions: {
		[USER_PANEL]: ({commit, dispatch}) => {
			dispatch(LOADING, true)
			return new Promise(() => {
				axios.post('usuarios/panel')
					.then(response => {
						commit(ASSIGN_MODEL, response.data)
						dispatch(LOADING, false)
					})
					.catch(error => {
						console.log('No se pudieron traer los usuarios. ' + error)
						dispatch(LOADING, false)
					})
			})
		}
	}
}

export const LOADING = 'LOADING'
export const COMMIT_DRAWER = 'COMMIT_DRAWER'
export default {
	state: {
		loading: false,
		drawer: true
	},
	getters: {
	},
	mutations: {
		[LOADING]: (state, show) => {
			state.loading = show
		},
		[COMMIT_DRAWER]: state => {
            state.drawer = !state.drawer
		}
	},
	actions: {
		[LOADING]: ({commit, dispatch}, show) => {
			commit(LOADING, show)
		}
	}
}

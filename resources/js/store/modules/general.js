export const LOADING = 'LOADING'
export const COMMIT_DRAWER = 'COMMIT_DRAWER'
export const SNACKBAR = 'SNACKBAR'
export default {
	state: {
		loading: false,
        snackbar: null,
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
		},
        [SNACKBAR]: (state, data) => {
            let timeout = 8000
            let message = ''
            if (data.error) {
                if (data.error.response) {
                    if (data.error.response && data.error.response.data && data.error.response.data.errors) {
                        let errorList = data.error.response.data.errors
                        let items = []
                        errorList && Object.values(errorList).forEach((value, index) => {
                            items.push((index + 1) + `: ${value}`)
                        })
                        timeout = Object.keys(errorList).length * timeout
                        message = (`Hay ${Object.keys(errorList).length} ${Object.keys(errorList).length !== 1 ? 'errores' : 'error'}${data.message} <br> ${items.join('<br>')}`)
                    } else if (data.error.response && data.error.response.data && (data.error.response.data.error || (data.error.response.data.message && typeof data.error.response.data.message === 'string'))) {
                        message = `Hay un error ${data.message} => ${data.error.response.data.error ? data.error.response.data.error : data.error.response.data.message} `
                    } else {
                        message = `Hay un error ${data.message} => ${data.error.response.status}: ` + (data.error.response.data && data.error.response.data.message && data.error.response.data.message.Message ? data.error.response.data.message.Message : data.error.response.statusText)
                    }
                } else {
                    message = `Hay un error ${data.message}.`
                }
            } else {
                message = data.message
            }
            state.snackbar = {
                timeout: timeout,
                message: message,
                color: data.color
            }
        }
	},
	actions: {
	}
}

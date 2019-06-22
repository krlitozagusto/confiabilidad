
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue'
import Vuetify from 'vuetify'
import router from './router/index'
import axios from 'axios'
import VueAxios from 'vue-axios'
import store from './store/index'
import moment from 'moment'
import es from 'vee-validate/dist/locale/es'
import VeeValidate, { Validator } from 'vee-validate'

// axios.defaults.baseURL = 'http://confiabilidad.test/'
axios.defaults.baseURL = window.location.origin
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
let token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
}

require('./bootstrap')

window.Vue = require('vue')
window.lodash = require('lodash')
window.toProperty = (obj, array) => {
    if (obj && array) {
        while (array.length) {
            if (!obj) return null
            obj = obj[array.shift()]
        }
        return obj
    } else {
        return null
    }
}
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
import 'vuetify/dist/vuetify.min.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'

Vue.use(Vuetify)
Vue.use(VueAxios, axios)
Validator.localize('es', es)
Vue.use(VeeValidate, {locale: 'es'})
moment.locale('es')
Vue.prototype.moment = moment

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.mixin({
    methods: {
        downloadFile (url) {
            window.location.href = url
        },
        currency: (valor) => {
            return new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP'}).format(valor)
        }
    }
})

const app = new Vue({
    el: '#app',
    store,
    router
})

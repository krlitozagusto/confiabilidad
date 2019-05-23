import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)
const titleApp = 'Confiabilidad'
const router = new Router({
    mode: 'history',
    scrollBehavior: () => ({ y: 0 }),
    routes: [
        {
			path: '/',
			redirect: '/home',
            name: 'Home',
			component: resolve => {require(['../components/general/Layout'], resolve)},
			children: [
				{
					path: 'home',
					name: 'HomeBoard',
					meta: {
						title: 'Panel principal'
					},
					component: resolve => {require(['../components/modules/home/Panel'], resolve)}
				},
				{
					path: 'users',
					name: 'UsersBoard',
					meta: {
						title: 'Panel de usuarios'
					},
					component: resolve => {require(['../components/modules/usuarios/Panel'], resolve)}
				},
                {
                    path: 'events',
                    name: 'EventsBoard',
                    meta: {
                        title: 'Panel de eventos'
                    },
                    component: resolve => {require(['../components/modules/eventos/Panel'], resolve)}
                },
                {
                    path: 'machines',
                    name: 'MachinesBoard',
                    meta: {
                        title: 'Panel de equipos'
                    },
                    component: resolve => {require(['../components/modules/equipos/Panel'], resolve)}
                },
                {
                    path: 'reports',
                    name: 'ReportsBoard',
                    meta: {
                        title: 'Panel de reportes'
                    },
                    component: resolve => {require(['../components/modules/reportes/Panel'], resolve)}
                },
                {
                    path: 'login',
                    name: 'login',
                    meta: {
                        title: 'Login'
                    }
                },
                {
                    path:'*',
                    redirect:'/home'
                }
			]
        },
		{
			path:'*',
			redirect:'/home'
		}
  ]
})

router.afterEach(route => {
	document.title = route.meta.title + ' | ' + titleApp
})
export default router

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
				}
			]
        },
		{
			path:'/404',
			component: resolve => {require(['../components/errors/Error404'], resolve)}
		},
		{
			path:'*',
			redirect:'/404'
		}
  ]
})

router.afterEach(route => {
	document.title = route.meta.title + ' | ' + titleApp
})
export default router

<template>
    <v-navigation-drawer
        ref="navigationDrawer"
            fixed
            :clipped="true"
            app
            :value="drawer"
    >
        <v-list dense class="py-0">
            <template v-for="(item, key) in items">
                <v-subheader
                    v-if="item.subheader"
                    :key="key"
                >
                    {{ item.text }}
                </v-subheader>
                <v-list-tile
                    v-else
                    :key="key"
                    avatar
                    ripple
                    :class="$route.name === item.nameRoute ? 'blue darken-2 text--withe' : ''"
                    :to="{ name: item.nameRoute}"
                >
                    <v-list-tile-avatar>
                        <v-icon :class="$route.name === item.nameRoute ? 'white--text' : ''">{{item.icon}}</v-icon>
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title :class="$route.name === item.nameRoute ? 'white--text' : ''" v-html="item.text"></v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-divider v-if="!item.subheader"></v-divider>
            </template>
        </v-list>
    </v-navigation-drawer>
</template>
<script>
	import {mapState} from 'vuex'
    export default {
		name: "Navigation",
		data: () => ({
			items: [
				{
				    icon: 'home',
                    text: 'Inicio',
                    subheader: false,
                    nameRoute: 'HomeBoard'
                },
                {
                    icon: 'today',
                    text: 'Eventos',
                    subheader: false,
                    nameRoute: 'EventsBoard'
                },
                {
                    icon: 'today',
                    text: 'Equipos',
                    subheader: false,
                    nameRoute: 'MachinesBoard'
                },
                {
                    icon: 'assignment_returned',
                    text: 'Reportes',
                    subheader: false,
                    nameRoute: 'ReportsBoard'
                },
                {
                    icon: null,
                    text: 'Administración',
                    subheader: true,
                    nameRoute: null
                },
				{
					icon: 'supervisor_account',
					text: 'Usuarios',
                    subheader: false,
                    nameRoute: 'UsersBoard'
				}
			]
		}),
        watch: {
		    'drawer' () {
                if(!this.$refs.navigationDrawer.isActive) this.$nextTick(() => {this.$refs.navigationDrawer.isActive = true})
            }
        },
		computed: {
			...mapState({
				drawer: state => state.general.drawer
			})
		}
    }
</script>

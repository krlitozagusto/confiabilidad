<template>
    <v-app id="inspire">
        <loading></loading>
        <navigation></navigation>
        <toolbar></toolbar>
        <v-content>
            <v-container fluid fill-height class="px-2">
                <router-view></router-view>
            </v-container>
        </v-content>
        <snackbar></snackbar>
    </v-app>
</template>
<script>
    import Loading from './Loading'
    import Navigation from './Navigation'
    import Toolbar from './Toolbar'
    import Snackbar from './Snackbar'
    export default {
    	name: 'Layout',
		components: {
			Loading,
			Navigation,
			Toolbar,
            Snackbar
		},
		watch: {
		},
		data: () => ({
		}),
        beforeCreate () {
            this.$store.dispatch('CURRENT_USER')
        },
        methods: {
        }
    }
</script>

<style>
    .content-v-list-tile-p0>.v-list__tile{
        padding: 0 !important;
    }

    .btn--floating .icon{
        height: auto;
    }

    .truncate-content {
        contain: size !important;
    }

    .cursor-pointer{
        cursor: pointer !important;
    }
</style>

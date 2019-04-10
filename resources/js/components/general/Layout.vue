<template>
    <v-app id="inspire">
        <vue-snotify/>
        <loading/>
        <navigation/>
        <toolbar/>
        <v-content>
            <v-container fluid fill-height>
                <router-view/>
            </v-container>
        </v-content>
    </v-app>
</template>
<script>
    import Loading from './Loading'
    import Navigation from './Navigation'
    import Toolbar from './Toolbar'
    export default {
    	name: 'Layout',
		components: {
			Loading,
			Navigation,
			Toolbar
		},
		watch: {
		},
		data: () => ({
		}),
        created () {
        },
        mounted () {
    		console.log(this.$snotify)
			this.$snotify.success('Example body content')
        },
        methods: {
        },
		props: {
			source: String
		}
    }
</script>

<style>
    .btn--floating .icon{
        height: auto;
    }
</style>
<template>
    <v-toolbar
            color="blue darken-3"
            dark
            app
            :clipped-left="$vuetify.breakpoint.mdAndUp"
            fixed
    >
        <v-toolbar-title style="width: 300px" class="ml-0 pl-3">
            <v-toolbar-side-icon @click.stop="COMMIT_DRAWER"/>
            <span class="hidden-sm-and-down">Confiabilidad</span>
        </v-toolbar-title>
        <!--<v-text-field-->
                <!--flat-->
                <!--solo-inverted-->
                <!--prepend-icon="search"-->
                <!--label="Search"-->
                <!--class="hidden-sm-and-down"-->
        <!--/>-->
        <v-spacer/>
        <v-btn icon>
            <v-icon>apps</v-icon>
        </v-btn>
        <v-btn icon>
            <v-icon>notifications</v-icon>
        </v-btn>
        <v-toolbar-items>
            <v-menu
                    transition="slide-y-transition"
                    bottom
                    offset-y
            >
                <v-btn dark color="primary" slot="activator">
                    <v-avatar size="32px" tile>
                        <img
                                src="../../../img/avatars/avatar01.png"
                                alt="Avatar"
                        />
                    </v-avatar>
                    &nbsp;Carlos Sandoval
                </v-btn>
                <form ref="logout" :action="axios.defaults.baseURL + '/logout'" method="POST" v-show="false">
                    <input type="hidden" name="_token" :value="axios.defaults.headers.common['X-CSRF-TOKEN']" />
                </form>
                <v-list class="py-0">
                    <v-list-tile @click="$refs.logout.submit()">
                        <v-icon class="mr-2">exit_to_app</v-icon>
                        <v-list-tile-content>
                            <v-list-tile-title>Cerrar sesión</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-menu>
        </v-toolbar-items>
    </v-toolbar>
</template>
<script>
	import {mapMutations} from 'vuex'
    export default {
		name: "Navigation",
		data: () => ({
		}),
        methods: {
            ...mapMutations(['COMMIT_DRAWER'])
        }
    }
</script>

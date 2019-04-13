<template>
    <v-toolbar
            color="blue darken-3"
            dark
            app
            :clipped-left="$vuetify.breakpoint.mdAndUp"
            fixed
    >
        <v-toolbar-title style="width: 300px" class="ml-0 pl-3">
            <v-toolbar-side-icon @click.stop="COMMIT_DRAWER"></v-toolbar-side-icon>
            <span class="hidden-sm-and-down">Confiabilidad</span>
        </v-toolbar-title>
        <v-spacer></v-spacer>
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
                <v-btn dark color="primary" slot="activator" class="pa-0" :loading="!user" :disabled="!user">
                    <v-list-tile v-if="user">
                        <v-list-tile-avatar size="32px">
                            <img
                                src="../../../img/avatars/avatar01.png"
                                alt="Avatar"
                            />
                        </v-list-tile-avatar>
                        <v-list-tile-content class="truncate-content"  style="width: 220px !important;">
                            <v-list-tile-title class="body-2">{{user.name}}</v-list-tile-title>
                            <v-list-tile-sub-title class="caption">{{user.email}}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
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
	import {mapMutations, mapState} from 'vuex'
    export default {
		name: "Navigation",
		data: () => ({
		}),
        computed: {
            ...mapState({
                user: state => state.user.currentUser
            })
        },
        methods: {
            ...mapMutations(['COMMIT_DRAWER'])
        }
    }
</script>

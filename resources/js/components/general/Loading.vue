<template>
    <div class="text-xs-center">
        <v-dialog
                v-model="dialog"
                persistent
                width="300"
        >
            <v-card
                    color="primary"
                    dark
            >
                <v-card-text class="text-xs-center">
                    Please stand by
                    <v-progress-linear
                            indeterminate
                            color="white"
                            class="mb-0"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
	import {mapState} from 'vuex'
    export default {
		name: "Loading",
		computed: {
			...mapState({
				dialog: state => state.general.loading
			})
		}
    }
</script>
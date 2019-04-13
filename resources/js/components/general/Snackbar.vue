<template>
	<v-snackbar auto-height multi-line top :timeout="timeout" v-model="show" :color="color">
		<span v-html="message"></span>
		<v-btn icon @click="show = false">
			<v-icon small>close</v-icon>
		</v-btn>
	</v-snackbar>
</template>

<script>
export default {
    data: () => ({
        timeout: 0,
        show: false,
		color: 'info',
        message: ''
	}),
	computed: {
      snackbar () {
          return this.$store.state.general.snackbar
	  }
	},
	watch: {
        'snackbar' (val) {
            this.show = false
            this.$nextTick(() => {
                this.color = val.color
                this.message = val.message
                this.timeout = val.timeout
                this.show = true
            })
		}
	},
	methods: {
  	}
}
</script>

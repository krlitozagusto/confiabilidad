<template>
	<v-dialog v-model="openD" max-width="500" persistent>
		<v-card>
			<v-card-title v-if="heading" class="headline" v-html="heading"></v-card-title>
			<v-card-text v-if="message" class="text-xs-justify" v-html="message"></v-card-text>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn
						@click="close"
						:disabled="load"
				>
					Cancelar
				</v-btn>
				<v-btn
						:color="color ? color : 'success'"
						:loading="load"
						:disabled="load"
						@click="$emit('onConfirm')"
				>
					Si
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
export default {
  	props: ['heading', 'message', 'color'],
    data: () => ({
        openD: false,
        load: false
	}),
	methods: {
		open () {
		  this.openD = true
		},
		loading (val = true) {
		  this.load = val
		},
		close () {
		  	this.openD = false
            this.$emit('onCancel')
            setTimeout(() => {
                this.load = false
            }, 300)
		}
  	}
}
</script>
<template>
    <v-layout row wrap v-if="value && value.orden_trabajos[0]">
        <v-flex xs12 sm6 md3>
            <v-text-field
                label="Número orden"
                v-model="value.orden_trabajos[0].numero_orden"
                name="Número orden"
                v-validate="'required'"
                :error-messages="errors.collect('Número orden')"
            ></v-text-field>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        props: ['value', 'esPrincipal', 'soloGuardar', 'complementos'],
		name: "RegisterOrdenTrabajo",
        components: {
        },
		data: () => ({
		}),
		computed: {
		},
        whatch: {
        },
		created () {
		},
        methods: {
            async reset () {
                await this.$validator.reset()
            }
        }
    }
</script>

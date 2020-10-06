<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="40%">
      <v-card>
        <v-toolbar color="gray" dark dense>
          <v-toolbar-title>
            Add custom product
          </v-toolbar-title>
        </v-toolbar>
        <v-form
            ref="form"
        >
          <v-card-text>
            <v-text-field
                v-model="name"
                name="name"
                label="Name"
                type="text"
                autocomplete="off"
                color="teal"
                :rules="nameRules"
            >
            </v-text-field>

            <v-text-field
                v-model="kcal"
                name="calories"
                label="Calories"
                type="text"
                autocomplete="off"
                color="teal"
                :rules="kcalRules"
            >
            </v-text-field>

            <v-text-field
                v-model="protein"
                name="protein"
                label="Protein"
                type="text"
                autocomplete="off"
                color="teal"
                :rules="proteinRules"
            >
            </v-text-field>

            <v-text-field
                v-model="carbs"
                name="carbs"
                label="Carbs"
                type="text"
                autocomplete="off"
                color="teal"
                :rules="carbsRules"
            >
            </v-text-field>

            <v-text-field
                v-model="fat"
                name="fat"
                label="Fat"
                type="text"
                autocomplete="off"
                color="teal"
                :rules="fatRules"
            >
            </v-text-field>
          </v-card-text>
          <v-divider light></v-divider>
          <v-card-actions>
            <v-btn text color="blue" dark @click.prevent="open=false">
              Back
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="teal" dark @click.prevent="submit()">
              Add
            </v-btn>
          </v-card-actions>

        </v-form>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
export default {
  name: 'productModal',
  data: () => ({
    name: '',
    nameRules: [
      v => !!v || 'Name is required',
      v => (v && v.length <= 20) || 'Name must be less than 20 characters',
      v => (v && v.length > 2) || 'Name must be at least 3 characters long',
    ],
    kcal: '',
    kcalRules: [
      v => !!v || 'Kcal field is required',
      v => (v && v >= 0) || 'Kcal must be a positive number',
      v => (v && v < 1e6) || 'Number is too big',
    ],
    protein: '',
    proteinRules: [
      v => !!v || 'Protein field is required',
      v => (v && v >= 0) || 'Protein must be a positive number',
      v => (v && v < 1e6) || 'Number is too big',
    ],
    carbs: '',
    carbsRules: [
      v => !!v || 'Carbs field is required',
      v => (v && v >= 0) || 'Carbs must be a positive number',
      v => (v && v < 1e6) || 'Number is too big',
    ],
    fat: '',
    fatRules: [
      v => !!v || 'Fat field is required',
      v => (v && v >= 0) || 'Fat must be a positive number',
      v => (v && v < 1e6) || 'Number is too big',
    ],
    open: true
  }),
  methods: {
    async submit() {
      if (this.$refs.form.validate()) {
        await this
            .$store.dispatch('POST_TEMPLATE_PRODUCT', {
              templateId: this.$route.params.id,
              productName: this.name,
              productKcal: this.kcal,
              productProtein: this.protein,
              productCarbs: this.carbs,
              productFat: this.kcal,
            })
            .then(() => {
              this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Product has been added')
              this.$refs.form.reset()
              this.$store.dispatch('GET_TEMPLATE_PRODUCTS', this.$route.params.id)
              this.open = false
            })
            .catch(error => {
              const {message} = {...error.response.data}
              const {status} = {...error.response}
              if (status === 404) {
                this.errorMsg = message
                this.error = true
                this.$store.commit("SET_ERROR_NOTIFICATION", message);
              } else {
                this.$store.commit('SET_UNKNOWN_ERROR_NOTIFICATION')
              }
            })
      }
    }
  },
  watch: {
    open: function (value) {
      if (value === false) {
        this.$router.push({
          name: "singleTemplate",
          params: {
            id: this.$route.params.id
          }
        });
      }
    }
  }
}
</script>
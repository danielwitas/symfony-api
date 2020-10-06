<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="40%">
      <v-card>
        <v-toolbar color="gray" dark dense>
          <v-toolbar-title>
            {{ name }}
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
            <v-btn text color="black" dark @click.prevent="open=false">
              BACK
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="teal" dark @click.prevent="submit()">
              SAVE
            </v-btn>


          </v-card-actions>

        </v-form>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>

export default {
  name: 'ProductEdit',
  data: () => ({
    name: '',
    nameRules: [],
    kcal: 0,
    kcalRules: [],
    protein: 0,
    proteinRules: [],
    carbs: 0,
    carbsRules: [],
    fat: 0,
    fatRules: [],
    open: true
  }),
  methods: {
    async submit() {
      if (this.$refs.form.validate()) {
        await this
            .$store.dispatch('EDIT_PRODUCT', {
              id: this.$route.params.id,
              name: this.name,
              kcal: this.kcal,
              protein: this.protein,
              carbs: this.carbs,
              fat: this.fat,
            })
            .then(() => {
              this.$refs.form.reset()
              this.$store.dispatch('GET_USER_PRODUCTS')
              this.open = false
              this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Product has been updated')
            })
            .catch(error => {
              this.applyValidationRules()
              const {errors} = {...error.response.data}
              const {status} = {...error.response}
              if (status === 400) {
                this.errorMsg = errors[Object.keys(errors)[0]][0]
                this.error = true
                this.$store.commit("SET_ERROR_NOTIFICATION", this.errorMsg);
              } else {
                this.$store.commit('SET_UNKNOWN_ERROR_NOTIFICATION')
              }
            })
      }
    },
    applyValidationRules() {
      this.nameRules = [
        v => !!v.trim() || 'Name is required',
        v => (v && v.length <= 20) || 'Name must be less than 20 characters',
        v => (v && v.length > 2) || 'Name must be at least 3 characters long',
      ]
      this.kcalRules = [
        v => !!v || 'Kcal field is required',
        v => (v && v >= 0) || 'Kcal cannot be less than 0',
        v => (v && v < 1e6) || 'Number is too big',
      ]
      this.proteinRules = [
        v => !!v || 'Protein field is required',
        v => (v && v >= 0) || 'Protein cannot be less than 0',
        v => (v && v < 1e6) || 'Number is too big',
      ]
      this.carbsRules = [
        v => !!v || 'Carbs field is required',
        v => (v && v >= 0) || 'Carbs cannot be less than 0',
        v => (v && v < 1e6) || 'Number is too big',
      ]
      this.fatRules = [
        v => !!v || 'Fat field is required',
        v => (v && v >= 0) || 'Fat cannot be less than 0',
        v => (v && v < 1e6) || 'Number is too big',
      ]
    },
  },
  watch: {
    open: function (value) {
      if (value === false) {
        this.$router.push({
          name: 'products',
        });
      }
    },
  },
  mounted() {
    this.$store.dispatch('GET_SINGLE_PRODUCT', this.$route.params.id).then(() => {
      this.name = this.$store.getters.GET_SINGLE_PRODUCT.name
      this.kcal = this.$store.getters.GET_SINGLE_PRODUCT.kcal
      this.protein = this.$store.getters.GET_SINGLE_PRODUCT.protein
      this.carbs = this.$store.getters.GET_SINGLE_PRODUCT.carbs
      this.fat = this.$store.getters.GET_SINGLE_PRODUCT.fat
    })
    this.applyValidationRules()
  },
}
</script>
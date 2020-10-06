<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="40%">
      <v-card>
        <v-toolbar color="gray" dark dense>
          <v-toolbar-title>
            Add your product
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
            <v-btn text color="teal" dark @click.prevent="addProduct()">
              SAVE
            </v-btn>


          </v-card-actions>

        </v-form>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
import validator from "../../services/product-validator";
export default {
  name: 'addUserProductModal',
  data: () => ({
    name: '',
    nameRules: [
      v => !!v.trim() || 'Name is required',
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
  computed: {
    getProduct() {
      return this.$store.getters.GET_PRODUCT(this.$route.params.id)
    }
  },
  methods: {
    async addProduct() {
      if(this.$refs.form.validate()) {
        await this
            .$store.dispatch('POST_USER_PRODUCT', {
              productName: this.name,
              productKcal: this.kcal,
              productProtein: this.protein,
              productCarbs: this.carbs,
              productFat: this.fat,
            })
            .then(() => {
              this.$refs.form.reset()
              this.$store.dispatch('GET_USER_PRODUCTS')
              this.open = false
              this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Product has been added')
            })
            .catch(error => {
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
  },
  watch: {
    open: function (value) {
      if (value === false) {
        this.$router.push({
          name: 'products',
        });
      }
    }
  }
}
</script>
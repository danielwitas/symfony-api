<template>
  <div>
    <v-form
        ref="form"
        v-model="valid"
    >
    <v-list>
      <v-list-item>
        <v-row>
          <v-col>
            <v-list-item-content>
              <v-list-item-title>{{ product.name }}</v-list-item-title>
              <v-list-item-subtitle>
                Calories:
                <AnimateNumber v-bind:value="this.kcal"/>
                Protein:
                <AnimateNumber v-bind:value="this.protein"/>
                Carbs:
                <AnimateNumber v-bind:value="this.carbs"/>
                Fat:
                <AnimateNumber v-bind:value="this.fat"/>
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-col>

            <v-col cols="12" sm="6" md="2" xs="3">
              <v-text-field cols="3" sm="2" md="1"
                            label="Weight"
                            :rules="weightRules"
                            v-model="weight"
                            color="teal"
              >
              </v-text-field>
            </v-col>
            <v-list-item-action>

              <v-btn
                  icon
                  @click="editProduct()"
                  :disabled="!valid"
              >
                <v-icon color="teal">done</v-icon>
              </v-btn>

            </v-list-item-action>

          <v-list-item-action>
            <v-btn icon @click="deleteProduct()">
              <v-icon color="red accent-2">delete</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-row>
      </v-list-item>
      <v-divider></v-divider>
    </v-list>
    </v-form>
  </div>
</template>


<script>
import AnimateNumber from "./AnimateNumber";

export default {
  name: 'product',
  components: {AnimateNumber},
  props: {
    product: Object
  },
  data: () => ({
    valid: true,
    weight: 100,
    weightRules: [
      v => !!v || 'Weight field is required',
      v => (v && v >= 0) || 'Weight must be a positive number',
      v => (v && v < 1e6) || 'Number is too big',
    ],
  }),
  computed: {
    kcal() {
      return Math.ceil((this.product.kcal * this.product.weight) / 100)
    },
    protein() {
      return Math.ceil((this.product.protein * this.product.weight) / 100)
    },
    carbs() {
      return Math.ceil((this.product.carbs * this.product.weight) / 100)
    },
    fat() {
      return Math.ceil((this.product.fat * this.product.weight) / 100)
    },

  },
  methods: {
    async editProduct() {
      if (this.$refs.form.validate()) {
        this.product.weight = this.weight
        await this
            .$store.dispatch('EDIT_PRODUCT', this.product)
            .then(() => {
              this.$store.dispatch('GET_TEMPLATE_PRODUCTS', this.$route.params.id)
              this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Product weight has been updated!')
            })
            .catch(() => {
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
    async deleteProduct() {
      await this
          .$store.dispatch('DELETE_PRODUCT', this.product.id)
          .then(() => {
            this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Product has been deleted!')
            this.$store.dispatch('GET_TEMPLATE_PRODUCTS', this.$route.params.id)
          })
          .catch(() => {
            this.$store.commit('SET_NOTIFICATION_ERROR_DEFAULT')
          })
    }
  },
  mounted() {
    this.weight = this.product.weight
  },
}
</script>
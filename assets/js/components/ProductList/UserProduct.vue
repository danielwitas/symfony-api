<template>
  <div>
    <v-list>
      <v-list-item>
            <v-list-item-content>
              <v-list-item-title>{{ product.name }}</v-list-item-title>
              <v-list-item-subtitle>
                Calories: {{ product.kcal }}
                Protein: {{ product.protein }}
                Carbs: {{ product.carbs }}
                Fat: {{ product.fat }}
              </v-list-item-subtitle>
            </v-list-item-content>
          <v-list-item-action>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="black"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    icon
                    @click.stop="editProduct()"
                >
                  <v-icon>edit</v-icon>
                </v-btn>
              </template>
              <span>Edit product</span>
            </v-tooltip>
          </v-list-item-action>
          <v-list-item-action>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="red"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    icon
                    @click.stop="deleteProduct()"
                >
                  <v-icon>delete</v-icon>
                </v-btn>
              </template>
              <span>Delete product</span>
            </v-tooltip>
          </v-list-item-action>
      </v-list-item>
      <v-divider></v-divider>
    </v-list>
  </div>
</template>

<script>
import ProductEdit from "./ProductEdit";
export default {
  name: 'UserProduct',
  props: {
    product: Object
  },
  components: {
    ProductEdit
  },
  methods: {
    editProduct() {
      this.$router.push({
        name: "ProductEdit",
        params: { id: this.product.id }
      });
    },
    async deleteProduct() {
      await this.$store.dispatch('DELETE_PRODUCT', this.product.id)
          .then(() => {
            this.$store.dispatch('GET_USER_PRODUCTS')
            this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Product has been deleted')
          })
          .catch(() => {
            this.$store.commit('SET_NOTIFICATION_ERROR_DEFAULT')
          })
    }
  }
}
</script>
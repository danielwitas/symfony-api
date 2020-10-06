<template>
  <div>
      <v-list-item
          @click.stop="addToTemplate()"
      >
        <v-list-item-content>
          <v-list-item-title>{{ product.name }}</v-list-item-title>
          <v-list-item-subtitle>
            Calories: {{ product.kcal }}
            Protein: {{ product.protein }}
            Carbs: {{ product.carbs }}
            Fat: {{ product.fat }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
  </div>
</template>


<script>

export default {
  name: 'UserProduct',
  props: {
    product: Object
  },
  methods: {
    async addToTemplate() {
      await this
          .$store.dispatch('POST_TEMPLATE_PRODUCT', {
            templateId: this.$route.params.id,
            productName: this.product.name,
            productKcal: this.product.kcal,
            productProtein: this.product.protein,
            productCarbs: this.product.carbs,
            productFat: this.product.kcal,
          })
          .then(() => {
            this.$store.dispatch('GET_TEMPLATE_PRODUCTS', this.$route.params.id)
            this.$store.commit('SET_DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE', false)
          })
          .catch(() => {
            this.$store.commit('SET_NOTIFICATION_ERROR_DEFAULT')
          })

    }
  },

}
</script>
<template>
  <v-row no-gutters>
    <v-col>
      <v-card
          class="pa-2"
          outlined
          tile
      >
        Calories: <AnimateNumber  v-bind:value="this.kcal" />
      </v-card>
    </v-col>
    <v-col>
      <v-card
          class="pa-2"
          outlined
          tile
      >
        Protein: <AnimateNumber  v-bind:value="this.protein" />
      </v-card>
    </v-col>
    <v-col>
      <v-card
          class="pa-2"
          outlined
          tile
      >
        Carbs: <AnimateNumber  v-bind:value="this.carbs" />
      </v-card>
    </v-col>
    <v-col>
      <v-card
          class="pa-2"
          outlined
          tile
      >
        Fat: <AnimateNumber  v-bind:value="this.fat" />
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import {mapGetters} from "vuex";
import AnimateNumber from "./AnimateNumber";

export default {
  name: 'Summary',
  components: {AnimateNumber},
  computed: {
    ...mapGetters(['TEMPLATE_PRODUCTS']),
    kcal() {
      return this.calculateTotal(this.TEMPLATE_PRODUCTS, 'kcal')
    },
    protein() {
      return this.calculateTotal(this.TEMPLATE_PRODUCTS, 'protein')
    },
    carbs() {
      return this.calculateTotal(this.TEMPLATE_PRODUCTS, 'carbs')
    },
    fat() {
      return this.calculateTotal(this.TEMPLATE_PRODUCTS, 'fat')
    }
  },
  methods: {
    calculateTotal(array, key) {
      let value = 0
      for(let i = 0; i<array.length; i++){
        value += Math.ceil((array[i][`${key}`] * array[i].weight) / 100)
      }
      return value
    }
  }

}
</script>
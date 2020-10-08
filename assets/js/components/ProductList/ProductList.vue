<template>
  <div>
    <v-card>
      <v-toolbar color="gray" dark dense>
        <v-toolbar-title v-if="!DISPLAY_PRODUCT_SEARCH_LIST">Your Products</v-toolbar-title>
        <Search v-if="DISPLAY_PRODUCT_SEARCH_LIST"/>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="white"
                dark
                v-bind="attrs"
                v-on="on"
                icon
                @click.stop="addProduct()"
            >
              <v-icon>add</v-icon>
            </v-btn>
          </template>
          <span>Add new product</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="white"
                dark
                v-bind="attrs"
                v-on="on"
                icon
                @click.stop="toggleSearchList()"
            >
              <v-icon>search</v-icon>
            </v-btn>
          </template>
          <span>Search your products</span>
        </v-tooltip>
      </v-toolbar>
      <v-list style="height: 796px;">
      <template
            v-for="(product, key) in USER_PRODUCTS.products"
        >
          <UserProduct v-bind:key="key" :product="product" :index="key"/>
        </template>

      </v-list>

      <v-pagination
          color="teal"
          v-model="page"
          :length="totalPages"
          :total-visible="6"
          prev-icon="keyboard_arrow_left"
          next-icon="keyboard_arrow_right"
          @input="showPage()"
      ></v-pagination>
    </v-card>
    <router-view :key="$route.fullPath" name="ProductAdd"></router-view>
    <router-view :key="$route.fullPath" name="ProductEdit" ></router-view>

  </div>
</template>
<script>
import {mapGetters} from 'vuex'
import ProductAdd from "./ProductAdd";
import UserProduct from "./UserProduct";
import ProductEdit from "./ProductEdit";
import Search from "./Search";
export default {
  name: 'ProductList',
  data: () => ({
    page: 1,
  }),
  components: {
    Search,
    UserProduct,
    ProductAdd,
    ProductEdit
  },
  computed: {
    ...mapGetters(['USER_PRODUCTS', 'DISPLAY_PRODUCT_SEARCH_LIST']),
    totalPages: function () {
      if (!this.$store.getters.USER_PRODUCTS.total) {
        return 0
      }
      return Math.ceil(this.$store.getters.USER_PRODUCTS.total / 10)
    },
  },
  methods: {
    showPage() {
      this.$store.dispatch('GET_USER_PRODUCTS', this.page)
    },
    addProduct() {
      this.$router.push({
        name: "ProductAdd",
      });
    },
    toggleSearchList() {
      this.$store.commit('SET_DISPLAY_PRODUCT_SEARCH_LIST', !this.DISPLAY_PRODUCT_SEARCH_LIST)
    },
  },
  mounted() {
    this.$store.dispatch('GET_USER_PRODUCTS')
  }
}
</script>
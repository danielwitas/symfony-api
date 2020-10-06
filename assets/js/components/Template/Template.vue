<template>
  <div>
    <v-card>
      <v-toolbar color="gray" dark dense>
        <v-toolbar-title>
          {{ TEMPLATE_TITLE }}
        </v-toolbar-title>
        <v-spacer></v-spacer>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="white"
                dark
                v-bind="attrs"
                v-on="on"
                icon
                @click.stop="addTemplateProduct()"
            >
              <v-icon>add</v-icon>
            </v-btn>
          </template>
          <span>Add custom product</span>
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="white"
                dark
                v-bind="attrs"
                v-on="on"
                icon
                @click.stop="editTemplateName()"
            >
              <v-icon>edit</v-icon>
            </v-btn>
          </template>
          <span>Edit name</span>
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="red accent-2"
                dark
                v-bind="attrs"
                v-on="on"
                icon
                @click="deleteTemplate()"
            >
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          <span>Delete template</span>
        </v-tooltip>
      </v-toolbar>

      <Summary />

      <v-card-actions>
        <v-layout>
          <v-flex>
            <NewProduct/>
          </v-flex>
        </v-layout>
      </v-card-actions>
      <v-divider></v-divider>

      <v-list style="height: 720px; overflow-y: scroll" v-show="!DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE">
      <template
            v-for="(product, key) in TEMPLATE_PRODUCTS"
        >

          <Product v-bind:key="key" :product="product" :index="key"/>
        </template>

      </v-list>

    </v-card>
    <router-view :key="$route.fullPath" name="addTemplateProduct"></router-view>
    <router-view :key="$route.fullPath" name="editTemplateName"></router-view>
  </div>
</template>

<script>
import ProductModal from './ProductsModal'
import Product from './Product'
import NewProduct from "./SearchProduct";
import Templates from '../TemplateList/TemplateList';
import ProductsModal from './ProductsModal';
import {mapGetters} from "vuex";
import Summary from "./Summary";

export default {
  name: 'Template',
  components: {
    Summary,
    Templates,
    Product,
    NewProduct,
    ProductsModal,
    ProductModal
  },
  computed: {
    ...mapGetters(['TEMPLATE_PRODUCTS', 'DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE']),
    TEMPLATE_TITLE() {
      return this.$store.getters.TEMPLATE_TITLE(this.$route.params.id)
    },
    PRODUCTS () {
      return this.$store.getters.PRODUCTS
    }
  },
  methods: {
    async deleteTemplate() {
      await this.$store.dispatch('DELETE_TEMPLATE', this.$route.params.id)
          .then(() => {
            this.$store.commit("SET_SUCCESS_NOTIFICATION", "Template has been deleted!")
            this.$router.push("/templates")
          })
          .catch(() => {
            this.$store.commit('SET_NOTIFICATION_ERROR_DEFAULT')
          })
    },
    addTemplateProduct() {
      this.$router.push({
        name: "addTemplateProduct",
        params: { id: this.$route.params.id }
      });
    },
    editTemplateName() {
      this.$router.push({
        name: "editTemplateName",
        params: { taskId: this.$route.params.id }
      });
    }
  },
  async mounted() {
    await this.$store.dispatch('GET_TEMPLATE_PRODUCTS', this.$route.params.id)
  }
}
</script>
<style>
</style>
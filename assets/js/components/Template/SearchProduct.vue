<template>
  <div>
  <v-text-field
      autocomplete="off"
      type="text"
      placeholder="Search..."
      v-model.trim="input"
      v-on:input="search()"
      ref="input"
      clearable
      clear-icon="clear"
      @click:clear="display()"
      @blur="display()"
      color="teal"
  ></v-text-field>
      <v-list style="height: 720px; overflow-y: scroll" v-show="DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE" rounded>
        <template
            v-for="(product, key) in USER_PRODUCTS.products"
        >
          <UserProduct v-bind:key="key" :product="product" :index="key"/>
        </template>

      </v-list>
  </div>

</template>

<script>
import {mapGetters} from 'vuex'
import UserProduct from "./UserProduct";
export default {
  name: "TemplateListSearch",
  components: {UserProduct},
  data: () => ({
    input: '',
    searchTimeout: null
  }),
  computed: {
    ...mapGetters(['USER_PRODUCTS', 'DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE'])
  },
  methods: {
    display() {
      const visible = !!this.input
      this.$store.commit('SET_DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE', visible)
    },
    search() {
      this.display()
      if(this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      this.searchTimeout = setTimeout(() => {
        this.$store.dispatch('SEARCH_USER_PRODUCTS', this.input)
            .then(response => {
            })
            .catch(error => {
            })
        this.searchTimeout = null
      }, 200)

    },
  }
}
</script>
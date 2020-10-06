<template>
  <v-text-field
      autocomplete="off"
      type="text"
      placeholder="Search..."
      v-model.trim="input"
      v-on:input="search()"
      ref="input"
      @blur="closeSearchBar()"
  ></v-text-field>
</template>

<script>
export default {
  name: "TemplateListSearch",
  data: () => ({
    input: '',
    searchTimeout: null
  }),
  methods: {
    search() {
      if(this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      this.searchTimeout = setTimeout(() => {
        this.$store.dispatch('SEARCH_TEMPLATES', this.input)
        .then(response => {
        })
        .catch(error => {
        })
        this.searchTimeout = null
      }, 200)
    },
    closeSearchBar() {
      this.$store.dispatch('GET_TEMPLATES')
      this.$store.commit('SET_DISPLAY_SEARCH_LIST', false)
    }
  }
}
</script>
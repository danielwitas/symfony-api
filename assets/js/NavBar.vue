<template>
  <div>

    <v-app-bar
        color="teal"
        dark
        dense

    >
      <v-row
          justify="center"
      >
        <v-btn v-if="!loggedIn" text to="/login">LOGIN</v-btn>
        <v-btn v-if="!loggedIn" text to="/register">REGISTER</v-btn>
        <v-btn v-if="loggedIn" text to="/account">ACCOUNT</v-btn>
        <v-btn v-if="loggedIn" text to="/templates">TEMPLATES</v-btn>
        <v-btn v-if="loggedIn" text to="/products">PRODUCTS</v-btn>
        <v-btn v-if="loggedIn" text @click="logout()">LOGOUT</v-btn>
      </v-row>


    </v-app-bar>
  </div>
</template>
<script>
export default {
  name: 'navbar',
  methods: {
    logout() {
      this
          .$store.dispatch('LOGOUT')
          .then(() => {
            this.$router.push('/login')
          })
    }
  },
  computed: {
    loggedIn() {
      return this.$store.getters.GET_TOKEN
    }
  }
}
</script>
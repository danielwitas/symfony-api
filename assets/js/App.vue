<template>
  <v-app>
    <NavBar/>
    <v-container fill-height>
      <v-layout align-center justify-center>
        <v-flex xs12 sm8 md8>

          <router-view></router-view>
        </v-flex>
      </v-layout>
    </v-container>

    <Notification/>
  </v-app>
</template>
<script>
import NavBar from "./NavBar";
import Notification from "./Notification";
import Templates from './components/TemplateList/TemplateList'
import Products from './components/Template/Template'
export default {
  name: 'app',
  components: {
    Templates, Products, Notification, NavBar
  },
}
</script>


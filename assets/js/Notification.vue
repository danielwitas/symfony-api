<template>
  <v-snackbar
      :color="NOTIFICATION.class"
      v-model="snackbar"
      top right
      dense
      :timeout="NOTIFICATION.timeout"
  >
    <v-spacer></v-spacer>
    <v-icon>{{ NOTIFICATION.class==='error' ? 'error' : 'done' }}</v-icon>
    {{ NOTIFICATION.text }}

  </v-snackbar>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
  name: 'notification',
  computed: {
    ...mapGetters(['NOTIFICATION']),
    snackbar: {
      get() {
        return this.$store.getters.NOTIFICATION.display
      },
      set(value) {
        this.$store.commit('SET_NOTIFICATION', {
          display: false
        })
      }
    }
  }
}
</script>
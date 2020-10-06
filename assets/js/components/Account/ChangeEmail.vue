<template>
  <v-container fill-height>
    <v-layout align-center justify-center>
      <v-flex xs12 sm8 md8>
        <v-form
            ref="form"
        >
          <v-card class="elevation-12">
            <v-toolbar dark color="gray" dense>
              <v-toolbar-title>Change email</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
              <v-alert
                  color="error"
                  :value="error"
                  icon="error"
                  dark
              >
                {{ errorMsg }}
              </v-alert>
              <v-text-field
                  color="teal"
                  autocomplete="off"
                  prepend-icon="email"
                  name="email"
                  label="Email"
                  :rules="emailRules"
                  v-model="email"
              >
              </v-text-field>
            </v-card-text>
            <v-divider light></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="teal" dark @click.prevent="submit()">
                SAVE
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
      </v-flex>
    </v-layout>
  </v-container>
</template>
<script>
export default {
  name: 'changeEmail',
  data: () => ({
    error: false,
    errorMsg: '',
    email: '',
    emailRules: [
      v => !!v || 'E-mail is required',
      v => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v)
          || 'E-mail must be valid',
    ],
  }),
  methods: {
    submit() {

      if (this.$refs.form.validate()) {
        this
            .$store.dispatch('CHANGE_EMAIL', this.email)
            .then(() => {
              this.error = false
              this.$refs.form.reset()
              this.$store.commit('SET_SUCCESS_NOTIFICATION', ' E-mail has been updated')
            })
            .catch(error => {
              const {title} = {...error.response.data}
              const {status} = {...error.response}
              if (status === 400) {
                this.errorMsg = title
                this.error = true
                this.$store.commit("SET_ERROR_NOTIFICATION", title);
              } else {
                this.$store.commit('SET_UNKNOWN_ERROR_NOTIFICATION')
              }
            })
      }

    }
  }


}
</script>

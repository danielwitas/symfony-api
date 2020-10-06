<template>
  <v-container fill-height>
    <v-layout align-center justify-center>
      <v-flex xs12 sm8 md8>
        <v-form
            ref="form"
        >
          <v-card class="elevation-12">
            <v-toolbar dark color="gray" dense>
              <v-toolbar-title>Change password</v-toolbar-title>
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
                  v-model="password"
                  prepend-icon="lock"
                  name="password"
                  label="Current password"
                  type="password"
                  autocomplete="off"
                  :rules="passwordRules"
              >
              </v-text-field>

              <v-text-field
                  color="teal"
                  autocomplete="off"
                  prepend-icon="lock"
                  name="newPassword"
                  label="New password"
                  :rules="newPasswordRules"
                  type="password"
                  v-model="newPassword"
              >
              </v-text-field>

              <v-text-field
                  color="teal"
                  autocomplete="off"
                  prepend-icon="lock"
                  name="password"
                  label="Repeat new password"
                  :rules="[passwordsMatch]"
                  type="password"
                  v-model="repeatNewPassword"
              >
              </v-text-field>
            </v-card-text>
            <v-divider light></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="teal"
                  dark
                  @click.prevent="submit()">
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
  name: 'changePassword',
  data: () => ({
    error: false,
    errorMsg: '',
    password: '',
    passwordRules: [
      v => !!v || 'Password is required'
    ],
    newPassword: '',
    newPasswordRules: [
      v => !!v || 'Password is required',
      v => /(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/.test(v)
          || 'Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter',
    ],
    repeatNewPassword: '',
  }),
  computed: {
    passwordsMatch() {
      return () => (this.newPassword === this.repeatNewPassword) || 'Passwords do not match'
    },
  },
  methods: {
    submit() {
      if (this.$refs.form.validate()) {
        this
            .$store.dispatch('CHANGE_PASSWORD', {
              password: this.password,
              newPassword: this.newPassword,
              repeatNewPassword: this.repeatNewPassword,
            })
            .then(() => {
              this.error = false
              this.$refs.form.reset()
              this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Password has been updated')
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

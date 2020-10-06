<template>
  <v-container fluid fill-height>
    <v-layout align-center justify-center>
      <v-flex xs12 sm8 md8>
        <v-card class="elevation-12">
          <v-toolbar dark color="gray">
            <v-toolbar-title>Register</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form
                ref="form"
                v-model="valid"
            >
              <v-alert
                  :value="error"
                  color="error"
                  icon="error"
                  dark
              >
                {{ errorMsg }}
              </v-alert>
              <v-text-field
                  color="teal"
                  autocomplete="off"
                  name="login"
                  label="Login"
                  prepend-icon="person"
                  v-model="username"
                  :rules="usernameRules"
              >
              </v-text-field>
              <v-text-field
                  color="teal"
                  autocomplete="off"
                  prepend-icon="email"
                  name="email"
                  label="Email"
                  v-model="email"
                  :rules="emailRules"
              >
              </v-text-field>
              <v-text-field
                  color="teal"
                  autocomplete="off"
                  prepend-icon="lock"
                  name="password"
                  label="Password"
                  type="password"
                  v-model="password"
                  :rules="passwordRules"
              >
              </v-text-field>
              <v-text-field
                  color="teal"
                  autocomplete="off"
                  prepend-icon="lock"
                  name="password"
                  label="Confirm Password"
                  type="password"
                  v-model="confirm_password"
                  :rules="[passwordsMatch]"
              >
              </v-text-field>
            </v-form>
          </v-card-text>
          <v-divider light></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
                color="teal"
                dark
                @click.prevent="register"
            >
              Register
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-flex>
    </v-layout>

  </v-container>

</template>
<script>
export default {
  name: 'register',
  data: () => ({
    error: false,
    errorMsg: '',
    username: '',
    usernameError: '',
    usernameRules: [
      v => !!v.trim() || 'Username is required',
      v => (v && v.length <= 20) || 'Username must be less than 50 characters',
      v => (v && v.length >= 3) || 'Username must be at least 3 characters long',
    ],
    email: '',
    emailError: '',
    emailRules: [
      v => !!v || 'E-mail is required',
      v => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v)
          || 'E-mail must be valid',
    ],
    password: '',
    passwordError: '',
    passwordRules: [
      v => !!v || 'Password is required',
      v => /(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/.test(v)
          || 'Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter',
    ],
    confirm_password: '',
  }),
  computed: {
    passwordsMatch() {
      return () => (this.password === this.confirm_password) || 'Passwords do not match'
    },
  },
  methods: {
    register() {
      if (this.$refs.form.validate()) {
        this
            .$store.dispatch('REGISTER', {
              username: this.username,
              email: this.email,
              password: this.password,
              repeatPassword: this.confirm_password
            })
            .then(() => {
              this.error = false
              this.$refs.form.reset()
              this.$store.commit(
                  'SET_SUCCESS_NOTIFICATION',
                  'Success! Check your email to complete registration')
            })
            .catch(error => {
              const {errors} = {...error.response.data}
              const {status} = {...error.response}
              if (status === 400) {
                this.errorMsg = errors[Object.keys(errors)[0]][0]
                this.error = true
                this.$store.commit("SET_ERROR_NOTIFICATION", this.errorMsg);
              } else {
                this.$store.commit('SET_UNKNOWN_ERROR_NOTIFICATION')
              }
            })
      }

    },
  }
}
</script>
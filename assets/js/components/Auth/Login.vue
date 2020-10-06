<template>
  <div>


    <v-container fill-height>
      <v-layout align-center justify-center>
        <v-flex xs12 sm8 md8>
          <v-form
              ref="form"
          >
            <v-card class="elevation-12">
              <v-toolbar dark color="gray">
                <v-toolbar-title>Login</v-toolbar-title>
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
                    v-model="username"
                    prepend-icon="person"
                    name="login"
                    label="Login"
                    type="text"
                    autocomplete="off"
                    :rules="usernameRules"
                >


                </v-text-field>

                <v-text-field
                    color="teal"
                    v-model="password"
                    prepend-icon="lock"
                    name="password" label="Password"
                    type="password"
                    autocomplete="off"
                    :rules="passwordRules"
                >

                </v-text-field>
              </v-card-text>
              <v-divider light></v-divider>
              <v-card-actions>
                <v-btn to="/password-reset" color="blue-grey" dark>Forgot password</v-btn>
                <v-spacer></v-spacer>
                <TestAccount />
                <v-spacer></v-spacer>
                <v-btn
                    class="mr-4"
                    color="teal"
                    dark
                    @click.prevent="login()">Login
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
        </v-flex>
      </v-layout>
    </v-container>

  </div>
</template>

<script>
import TestAccount from "./TestAccount";
export default {
  name: 'login',
  components: {TestAccount},
  data: () => ({
    error: false,
    errorMsg: '',
    username: '',
    usernameRules: [
      v => !!v.trim() || 'Login is required',
    ],
    password: '',
    passwordRules: [
      v => !!v.trim() || 'Password is required',
    ],
  }),
  methods: {
    async login() {
      if (this.$refs.form.validate()) {
        await this
            .$store.dispatch('LOGIN', {
              username: this.username,
              password: this.password
            })
            .then(() => {
              this.error = false
              this.$refs.form.reset()
              this.$store.commit('SET_SUCCESS_NOTIFICATION', 'You have logged successfully')
              this.$router.push("/templates")
            })
            .catch(error => {
              const {message} = {...error.response.data}
              const {status} = {...error.response}
              if (status === 401) {
                this.errorMsg = message
                this.error = true
                this.$store.commit("SET_ERROR_NOTIFICATION", message);
              } else {
                this.$store.commit('SET_UNKNOWN_ERROR_NOTIFICATION')
              }
            })
      }
    },
  },
}
</script>

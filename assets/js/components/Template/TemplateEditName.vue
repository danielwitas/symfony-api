<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="40%">
      <v-card>
        <v-toolbar color="gray" dark dense>
          <v-toolbar-title>
            Edit template name
          </v-toolbar-title>
        </v-toolbar>
        <v-form
            ref="form"
        >
          <v-card-text>
            <v-text-field
                v-model="name"
                name="name"
                label="Name"
                type="text"
                autocomplete="off"
                color="teal"
                :rules="nameRules"
            >
            </v-text-field>
          </v-card-text>
          <v-divider light></v-divider>
          <v-card-actions>
            <v-btn text color="blue" dark @click.prevent="open=false">
              BACK
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="teal" dark @click.prevent="submit()">
              SAVE
            </v-btn>
          </v-card-actions>

        </v-form>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
export default {
  name: "TemplateEditName",
  data: () => ({
    name: '',
    nameRules: [
      v => !!v.trim() || 'Name is required',
      v => (v && v.length <= 20) || 'Name must be less than 20 characters',
      v => (v && v.length > 2) || 'Name must be at least 3 characters long',
    ],
    open: true
  }),
  methods: {
    async submit() {
      if (this.$refs.form.validate()) {
        await this
            .$store.dispatch('EDIT_TEMPLATE', {
              id: this.$route.params.id,
              name: this.name,
            })
            .then(() => {
              this.$refs.form.reset()
              this.open = false
              this.$store.commit("SET_SUCCESS_NOTIFICATION", "Template name has been updated!")
              this.$store.dispatch('GET_TEMPLATES')
              this.$router.push({
                name: "singleTemplate",
                params: {
                  id: this.$route.params.id
                }
              });
            })
            .catch(() => {
              this.$store.commit('SET_NOTIFICATION_ERROR_DEFAULT')
            })
      }
    }
  },
  watch: {
    open: function (value) {
      if (value === false) {
        this.$router.push({
          name: "singleTemplate",
          params: {
            id: this.$route.params.id
          }
        });
      }
    }
  }
}
</script>

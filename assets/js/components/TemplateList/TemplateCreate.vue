<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="40%">
      <v-card>
        <v-toolbar color="gray" dark dense>
          <v-toolbar-title>
            Create new template
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
            <v-btn text color="black" dark @click.prevent="open=false">
              BACK
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="teal" dark @click.prevent="createTemplate()">
              CREATE
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
  </v-layout>
</template>
<script>
export default {
  name: 'TemplateCreateModal',
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
    async createTemplate() {
      if (this.$refs.form.validate()) {
        this
            .$store.dispatch('POST_TEMPLATE', {
              name: this.name
            })
            .then(() => {
              this.$store.commit('SET_SUCCESS_NOTIFICATION', 'Template has been created!')
              this.title = ''
              this.$store.dispatch('GET_TEMPLATES')
              this.$refs.form.reset()
              this.open = false
            })
            .catch(() => {
              this.$store.commit('SET_NOTIFICATION_ERROR_DEFAULT')
            })
      }
    },
  },
  watch: {
    open: function (value) {
      if (value === false) {
        this.$router.push({
          name: 'templates',
        });
      }
    }
  },
}
</script>
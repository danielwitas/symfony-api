<template>
  <div>
    <v-list>
      <v-list-item>

        <v-list-item-content>
          <v-list-item-title>{{ template.name }}</v-list-item-title>
          <v-list-item-subtitle>{{ template.createdAt }}</v-list-item-subtitle>
        </v-list-item-content>


        <v-list-item-action>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                  color="black"
                  dark
                  v-bind="attrs"
                  v-on="on"
                  icon
                  @click.stop="editTemplate()"
              >
                <v-icon>edit</v-icon>
              </v-btn>
            </template>
            <span>Edit template</span>
          </v-tooltip>
        </v-list-item-action>


        <v-list-item-action>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                  color="red"
                  dark
                  v-bind="attrs"
                  v-on="on"
                  icon
                  @click.stop="deleteTemplate()"
              >
                <v-icon>delete</v-icon>
              </v-btn>
            </template>
            <span>Delete template</span>
          </v-tooltip>
        </v-list-item-action>


      </v-list-item>
      <v-divider></v-divider>
    </v-list>


  </div>
</template>
<script>
export default {
  name: 'UserTemplate',
  props: {
    template: Object,
  },
  methods: {
    async deleteTemplate() {
      await this
          .$store.dispatch('DELETE_TEMPLATE', this.template.id)
          .then(() => {
            this.$store.commit("SET_SUCCESS_NOTIFICATION", "Template has been deleted!");
            this.$store.dispatch('GET_TEMPLATES')
          })
          .catch(() => {
            this.$store.commit('SET_NOTIFICATION_ERROR_DEFAULT')
          })
    },
    editTemplate() {
      this.$router.push({
        name: 'singleTemplate',
        params: {
          id: this.template.id
        }
      })
    }
  }
}
</script>
<template>
  <div>
    <v-card>
      <v-toolbar color="gray" dark dense>
        <v-toolbar-title v-if="!DISPLAY_SEARCH_LIST">Your Templates</v-toolbar-title>
        <SearchBar v-if="DISPLAY_SEARCH_LIST"/>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="white"
                dark
                v-bind="attrs"
                v-on="on"
                icon
                @click.stop="createTemplate()"
            >
              <v-icon>add</v-icon>
            </v-btn>
          </template>
          <span>Create new template</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="white"
                dark
                v-bind="attrs"
                v-on="on"
                icon
                @click.stop="toggleSearchList()"
            >
              <v-icon>search</v-icon>
            </v-btn>
          </template>
          <span>Search your templates</span>
        </v-tooltip>
      </v-toolbar>
      <v-list style="height: 796px;">
        <template
            v-for="(template, key) in USER_TEMPLATES.templates"
        >
          <UserTemplate v-bind:key="key" :template="template" :index="key"/>
        </template>
      </v-list>
      <v-pagination
          color="teal"
          v-model="page"
          :length="totalPages"
          :total-visible="6"
          prev-icon="keyboard_arrow_left"
          next-icon="keyboard_arrow_right"
          @input="showPage()"
      ></v-pagination>
    </v-card>
    <router-view :key="$route.fullPath" name="templateCreateModal"></router-view>
  </div>
</template>
<script>
import TemplateCreateModal from "./TemplateCreate";
import UserTemplate from "./Template";
import SearchBar from "./TemplateListSearch";
import {mapGetters} from 'vuex'

export default {

  name: 'templates',
  components: { UserTemplate, TemplateCreateModal, SearchBar },
  data: () => ({
    page: 1,
  }),
  computed: {
    ...mapGetters(['DISPLAY_SEARCH_LIST', 'USER_TEMPLATES']),
    totalPages: function () {
      if (!this.$store.getters.USER_TEMPLATES.total) {
        return 0
      }
      return Math.ceil(this.$store.getters.USER_TEMPLATES.total / 10)
    },
  },
  methods: {
    createTemplate() {
        this.$router.push({
          name: "templateCreateModal",
        });
    },
    showPage() {
      this.$store.dispatch('GET_TEMPLATES', this.page)
    },
    toggleSearchList() {
      this.$store.commit('SET_DISPLAY_SEARCH_LIST', !this.DISPLAY_SEARCH_LIST)
    },
  },
  mounted() {
    this.$store.dispatch('GET_TEMPLATES')
  }
}
</script>
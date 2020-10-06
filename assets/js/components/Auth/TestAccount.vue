<template>
  <div class="text-center">
    <v-bottom-sheet
        v-model="sheet"
        inset
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
            color="orange"
            dark
            v-bind="attrs"
            v-on="on"
        >
          Test Account
        </v-btn>
      </template>
      <v-sheet
          class="text-center"
          height="200px"
      >
        <v-btn
            class="mt-6"
            text
            color="error"
            @click="sheet = !sheet"
        >
          close
        </v-btn>
        <div class="my-3">
          <p>Don't want to register? No problem. Use our test account:</p>
          <p>Login: jan_kowalski</p>
          <p>Password: Password123</p>




        </div>
      </v-sheet>
    </v-bottom-sheet>
  </div>
</template>
<script>
export default {
  name: 'TestAccount',
  data: () => ({
    sheet: false,
  }),
}
</script>
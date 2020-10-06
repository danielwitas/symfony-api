<template>
</template>
<script>
export default {
  created() {
    this.$store.dispatch('LOGOUT')
  }
}
</script>
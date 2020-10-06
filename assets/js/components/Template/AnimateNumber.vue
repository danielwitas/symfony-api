<template>
  <span>{{ animatedNumber }}</span>
</template>
<script>
import { gsap } from 'gsap';
export default {
  props: {
    value: {
      type: Number,
      required: true
    }
  },
  data: () => ({
    tweenedNumber: 0
  }),
  computed: {
    animatedNumber: function() {
      return this.tweenedNumber.toFixed(0);
    },
  },
  watch: {
    value: function(newValue) {
      gsap.to(this.$data, { duration: 0.5, tweenedNumber: newValue });
    }
  },
  mounted() {
    this.tweenedNumber = this.value
  }
}
</script>
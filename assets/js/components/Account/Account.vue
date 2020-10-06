<template>
  <div>
    <change-email />
    <change-password />
  </div>

</template>
<script>
import ChangeEmail from "./ChangeEmail";
import ChangePassword from "./ChangePassword";
export default {
  name: 'account',
  components: {ChangePassword, ChangeEmail}
}
</script>
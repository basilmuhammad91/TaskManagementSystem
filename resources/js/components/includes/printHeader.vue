<template>
<v-app>
  <div class="print-header">
    <img class="printLogo" v-bind:src="'images/logo/'+$store.getters.settings.logo" alt="Logo">
    <div class="printCompanyName">{{$store.getters.settings.company_name}}</div>
</div>
</v-app>
</template>
<script>
export default {
  name: "printHeader",
  data() {
    return {
    };
  },
  mounted() {
      this.$store.dispatch('getSettingsFromAPI');
  },
};
</script>
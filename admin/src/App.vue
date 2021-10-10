<template>
  <div id="q-app">
    <div v-if="!prepared" class="q-ma-xl flex justify-center">
      <q-spinner size="xl" />
    </div>
    <router-view v-if="prepared" />
    <div id="version">V 4.4.3</div>
  </div>
</template>

<script>
export default {
  name: 'Kusikusi',
  data () {
    return {
      prepared: false
    }
  },
  async created () {
    this.$api.axiosInstance.defaults.withCredentials = true
    this.$api.axiosInstance.defaults.headers.common['Access-Control-Allow-Credentials'] = true
    this.$api.baseURL = process.env.API_URL
    await this.$api.get('/csrf-cookie')
    const config = await this.$api.get('/cms/config')
    if (config.success) {
      this.$store.commit('setConfig', config.data)
    }
    await this.$store.dispatch('getLocalSession')
    if (this.$store.getters.hasAuthtoken) {
      const meResult = await this.$api.get('/user/me')
      if (meResult.status >= 400 && this.$route.name !== 'login') {
        this.prepared = true
        this.$router.push({ name: 'login' })
      } else {
        this.$store.commit('setUser', meResult.data)
        this.prepared = true
      }
    } else {
      this.prepared = true
      if (this.$route.name !== 'login') {
        this.$router.push({ name: 'login' })
      }
    }
  }
}
</script>

<style>
  #version {
    position: fixed;
    right: 0.5em;
    bottom: 0.5em;
    opacity: 0.25;
  }
</style>

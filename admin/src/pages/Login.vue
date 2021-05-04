<template>
  <nq-page max-width="sm" class="flex flex-center">
    <nq-form cancel-label="" :submit-label="$t('login.button')" @submit="handleLogin">
      <q-img src="~assets/logo.svg" alt="Retryver" height="72px" class="q-my-md" contain />
      <p class="text-h3 text-center col-12">{{ $store.getters.title() }}</p>
      <nq-input :autofocus="true"
                class="col-12"
                v-model="form.email"
                :label="$t('login.email')"
                :rules="[
                  $rules.required($t('login.invalidEmail')),
                  $rules.email($t('login.invalidEmail'))
                ]"/>
      <nq-input v-model="form.password"
                class="col-12"
                :type="isPwd ? 'password' : 'text'"
                :label="$t('login.password')"
                @keyup.enter="handleLogin"
                :rules="[
                  $rules.required($t('login.invalidPassword'))
                ]">
        <template v-slot:append>
          <q-icon
            :name="isPwd ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            @click="isPwd = !isPwd"
          />
        </template>
      </nq-input>
    </nq-form>
  </nq-page>
</template>
<script>

export default {
  name: 'Login',
  beforeCreate () {
    this.$store.dispatch('resetUserData')
  },
  mounted () {
    // this.$store.commit('ui/setTitle', this.$t('login.title'))
  },
  data () {
    return {
      form: {
        email: 'admin@kusiksui.com',
        password: 'admin'
      },
      message: '',
      isPwd: true
    }
  },
  methods: {
    handleLogin: async function () {
      // const sessionResult = await this.$axios.get('http://local.kusikusi.com/sanctum/csrf-cookie')
      const loginResult = await this.$api.post('/auth/login', this.form)
      const xsrfToken = loginResult.config.headers['X-XSRF-TOKEN']
      this.$api.axiosInstance.defaults.withCredentials = true
      this.$api.axiosInstance.defaults.headers.common['X-XSRF-TOKEN'] = xsrfToken
      if (loginResult.success) {
        const user = await this.$api.get('/user', this.form)
        // await this.$store.commit('setAuthtoken', loginResult.data.token)
        this.$store.commit('setUser', user.data)
        // this.$router.push({ name: 'content', params: { entity_id: 'home' } })
      } else {
        if (loginResult.status === 401) {
          this.$q.notify({
            position: 'top',
            color: 'negative',
            message: this.$t('login.loginInvalid')
          })
        } else {
          this.$q.notify({
            position: 'top',
            color: 'negative',
            message: `${this.$t('login.loginError')} (${loginResult.status})`
          })
        }
      }
    }
  }
}
</script>
<style>
  .login-card {
    max-width: 40em;
  }
</style>

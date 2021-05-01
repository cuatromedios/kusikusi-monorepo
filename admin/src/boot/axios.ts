import axios, { AxiosInstance } from 'axios'
import { boot } from 'quasar/wrappers'

declare module 'vue/types/vue' {
  interface Vue {
    $axios: AxiosInstance;
  }
}

export default boot(({ Vue }) => {
  axios.defaults.withCredentials = true
  axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  Vue.prototype.$axios = axios
})

import Vue from 'vue'
import store from '@/store'
import router from '../router/index';

// axios
import axios from 'axios'

const axiosIns = axios.create({
  // You can add your headers here
  // ================================
  // baseURL: 'http://127.0.0.1:8000/',
  baseURL: 'https://www.webarpro.com/',
  // timeout: 1000,
  // headers: {'X-Custom-Header': 'foobar'}
})

axiosIns.interceptors.request.use(
  config => {
    // Do something before request is sent

    const accessToken = localStorage.getItem('accessToken')

    // eslint-disable-next-line no-param-reassign
    if (accessToken) config.headers.Authorization = `Bearer ${accessToken}`

    return config
  },
  error => Promise.reject(error),
)

axiosIns.interceptors.response.use(response => {
  return response;
}, function(error, config) {
  // log all failed/errored requests with their config details

  if (error.response.status === 403) {

    store.commit('auth/setAuthenticated', false)
    store.commit('auth/setUserAbility', null)
    store.commit('auth/setUserData', null)

    router.push({ name: 'auth-login' })

  }

  return Promise.reject(error)
})

Vue.prototype.$http = axiosIns

export default axiosIns

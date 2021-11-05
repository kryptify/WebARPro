import { validJSONParse } from '@core/utils'
export default {
  namespaced: true,
  state: {
    authenticated: localStorage.getItem('authenticated') === 'true',
    userData: validJSONParse(localStorage.getItem('userData')),
    userAbility: validJSONParse(localStorage.getItem('userAbility')),
  },
  getters: {},
  mutations: {
    setAuthenticated(state, payload) {
      state.authenticated = payload
      localStorage.setItem('authenticated', payload)
    },
    setUserData(state, payload) {
      state.userData = payload
      localStorage.setItem('userData', JSON.stringify(payload))
    },
    setUserAbility(state, payload) {
      state.userAbility = payload
      localStorage.setItem('userAbility', JSON.stringify(payload))
    },
  },
  actions: {},
}

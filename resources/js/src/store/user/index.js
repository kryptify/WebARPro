import { keysToCamel } from '@core/utils'
import axios from '@axios'

export default {
  namespaced: true,
  state: {
    planData: [],
    roleData: [],
    planBenefitData: [],
  },
  getters: {},
  mutations: {
    setPlanData(state, payload) {
      state.planData = payload
    },
    setRoleData(state, payload) {
      state.roleData = payload
    },
    setPlanBenefitData(state, payload) {
      state.planBenefitData = payload
    }
  },
  actions: {
    fetchUser(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/api/user/fetchUser`, { id })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    fetchPlanData(ctx) {
      return new Promise((resolve, reject) => {
        axios
          .get('/api/user/plans')
          .then(response => {

            let planData =  keysToCamel(response.data.plans)

            // ? Set plan data in localStorage for reusage.
            ctx.commit('setPlanData', planData)

            return resolve(planData)
          })
          .catch(error => reject(error))
      })
    },
    fetchRoleData(ctx) {
      return new Promise((resolve, reject) => {
        axios
          .get('/api/user/roles')
          .then(response => {

            let roleData =  keysToCamel(response.data.roles)

            // ? Set plan data in localStorage for reusage.
            ctx.commit('setRoleData', roleData)

            return resolve(roleData)
          })
          .catch(error => reject(error))
      })
    },
    fetchPlanBenefitData(ctx) {
      return new Promise((resolve, reject) => {
        axios
          .get('/api/user/planBenefits')
          .then(response => {

            let planBenefitData =  keysToCamel(response.data.planBenefits)

            // ? Set plan data in localStorage for reusage.
            ctx.commit('setPlanBenefitData', planBenefitData)

            return resolve(planBenefitData)
          })
          .catch(error => reject(error))
      })
    },
  },
}

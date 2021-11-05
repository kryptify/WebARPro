import axios from '@axios'

export default {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    fetchUsers(ctx, queryParams) {
      return new Promise((resolve, reject) => {
        axios
          .post('/api/admin/fetchUsers', { ...queryParams })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    saveUser(ctx, userData) {
      return new Promise((resolve, reject) => {
        axios
          .post('/api/admin/saveUser', { ...userData })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}

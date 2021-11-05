import { canNavigate } from '@/plugins/acl/routeProtection'
import store from '@/store'

export const beforeEach = (to, _, next) => {
  const userData = store.state.auth.userData
  const userAbility = store.state.auth.userAbility

  const isLoggedIn = userData && userAbility && localStorage.getItem('accessToken')

  if (!canNavigate(to)) {
    // Redirect to login if not logged in
    if (!isLoggedIn) return next({ name: 'auth-login', query: { marketplace: to.query.marketplace } })

    // If logged in => not authorized
    return next({ name: 'not-authorized' })

  }

  // Redirect if logged in
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    next('/')
  }

  return next()
}

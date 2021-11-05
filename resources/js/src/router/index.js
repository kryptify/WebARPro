import { beforeEach, beforeAdmin } from './middleware'
import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
  // ? We are redirecting to different pages based on role.
  // NOTE: Role is just for UI purposes. ACL is based on abilities.
  // custom routes
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/dashboard/Dashboard.vue'),
    meta: {
      layout: 'dashboard',
      resource: 'Public',
    },
  },
  {
    path: '/error-404',
    name: 'error-404',
    component: () => import('@/views/Error404.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
    },
  },
  {
    path: '/login',
    name: 'auth-login',
    component: () => import('@/views/Login.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
      // redirectIfLoggedIn: true,
    },
  },
  {
    path: '/register',
    name: 'auth-register',
    component: () => import('@/views/Register.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
      // redirectIfLoggedIn: true,
    },
  },
  {
    path: '/forgot-password',
    name: 'auth-forgot-password',
    component: () => import('@/views/ForgotPassword.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
      redirectIfLoggedIn: true,
    },
  },
  {
    path: '/reset-password/:token',
    name: 'auth-reset-password',
    component: () => import('@/views/ResetPassword.vue'),
    props: true,
    meta: {
      layout: 'blank',
      resource: 'Public',
      redirectIfLoggedIn: true,
    },
  },
  {
    path: '/pricing/subscription/',
    name: 'pricing-subscription',
    component: () => import('@/views/pricing/Subscription.vue'),
    props: true,
    meta: {
      layout: 'content',
      resource: 'user',
      action: 'access',
    }
  },
  {
    path: '/pricing/plans',
    name: 'pricing-plans',
    component: () => import('@/views/pricing/Pricing.vue'),
    meta: {
      layout: 'content',
      resource: 'Public',
    },
  },
  {
    path: '/pricing/paymentOptions',
    name: 'pricing-payment-options',
    component: () => import('@/views/pricing/SelectPayment.vue'),
    props: true,
    meta: {
      layout: 'content',
      resource: 'user',
      action: 'access',
    },
  },
  {
    path: '/features',
    name: 'features',
    component: () => import('@/views/features/Features.vue'),
    meta: {
      layout: 'content',
      resource: 'Public',
    },
  },
  {
    path: '/exmpales',
    name: 'examples',
    component: () => import('@/views/examples/Examples.vue'),
    meta: {
      layout: 'content',
      resource: 'Public',
    },
  },
  {
    path: '/faq',
    name: 'faq',
    component: () => import('@/views/faq/Faq.vue'),
    meta: {
      layout: 'content',
      resource: 'Public',
    },
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/user/user-view/Profile.vue'),
    meta: {
      layout: 'content',
      resource: 'user',
      action: 'access',
    },
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('@/views/user/user-list/UserList.vue'),
    meta: {
      layout: 'content',
      resource: 'all',
      action: 'manage'
    },
  },
  {
    path: '/admin/user/:id',
    name: 'admin-user',
    component: () => import('@/views/user/user-list/UserView.vue'),
    props: true,
    meta: {
      layout: 'content',
      resource: 'all',
      action: 'manage'
    },
  },
  {
    path: '/admin/pricing-plans',
    name: 'admin-pricing-plans',
    component: () => import('@/views/pricing/PlanList.vue'),
    meta: {
      layout: 'content',
      resource: 'all',
      action: 'manage'
    },
  },
  {
    path: '/not-authorized',
    name: 'not-authorized',
    component: () => import('@/views/miscellaneous/NotAuthorized.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
    },
  },
  {
    path: '/coming-soon',
    name: 'coming-soon',
    component: () => import('@/views/miscellaneous/ComingSoon.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
    },
  },
  {
    path: '*',
    redirect: 'error-404',
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
})

router.beforeEach(beforeEach);

export default router

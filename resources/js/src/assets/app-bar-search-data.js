import {
  mdiAccountCogOutline,
  mdiAccountPlusOutline,
  mdiCurrencyUsd,
  mdiFeatureSearch, mdiFileDocumentEditOutline,
  mdiHelp,
  mdiHomeOutline,
  mdiKeyOutline,
  mdiLockOutline,
} from '@mdi/js'

/* eslint-disable */
// prettier-ignore
export default [
  { header: 'Pages' },

  // dashboard
  { title: 'Home', to: { name: 'home' }, icon: mdiHomeOutline },

  // Features
  { title: 'Features', to: { name: 'features' }, icon: mdiFeatureSearch },

  // Pricing
  { title: 'Pricing', to: { name: 'pricing-plans' }, icon: mdiCurrencyUsd },

  // Help
  { title: 'Help', to: { name: 'faq' }, icon: mdiHelp },

  // authentication
  { title: 'Login', to: { name: 'auth-login' }, icon: mdiLockOutline },
  { title: 'Register', to: { name: 'auth-register' }, icon: mdiAccountPlusOutline },
  { title: 'Forgot Password', to: { name: 'auth-forgot-password' }, icon: mdiKeyOutline },
  { title: 'Reset Password', to: { name: 'auth-reset-password' }, icon: mdiKeyOutline },

  // admin
  // { title: 'Admin Users', to: { name: 'admin-users' }, icon: mdiAccountCogOutline },
  // { title: 'Admin Plans', to: { name: 'admin-pricing-plans' }, icon: mdiFileDocumentEditOutline },

  // Contacts
  // { header: 'Contacts' },
  // { avatar: require('@/assets/images/avatars/avatar-1.png').default, title: 'Mardell Channey', email: 'peseta@myrica.com', time: '11/11/2019' },
]

// Array of sections
import {
  mdiFeatureSearch,
  mdiCurrencyUsd,
  mdiHelp,
  mdiHomeOutline,
  mdiAccountCogOutline,
  mdiFileDocumentEditOutline,
  mdiCloud,
} from '@mdi/js'

export default [
  {
    title: 'Home',
    icon: mdiHomeOutline,
    to: 'home',
    resource: 'Public',
    action: 'read'
  },
  {
    title: 'Features',
    icon: mdiFeatureSearch,
    to: 'features',
    resource: 'Public',
    action: 'read'
  },
  {
    title: 'Pricing',
    icon: mdiCurrencyUsd,
    to: 'pricing-plans',
    resource: 'Public',
    action: 'read'
  },
  {
    title: 'Examples',
    icon: mdiCloud,
    to: 'examples',
    resource: 'Public',
    action: 'read'
  },
  {
    title: 'FAQ',
    icon: mdiHelp,
    to: 'faq',
    resource: 'Public',
    action: 'read'
  },
  {
    subheader: 'ADMIN',
  },
  {
    title: 'Users',
    icon: mdiAccountCogOutline,
    to: 'admin-users',
    resource: 'all',
    action: 'manage'
  },
  {
    title: 'Pricing Plans',
    icon: mdiFileDocumentEditOutline,
    to: 'admin-pricing-plans',
    resource: 'all',
    action: 'manage'
  },
]

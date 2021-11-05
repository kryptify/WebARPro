import store from '@/store'
import { keysToCamel, isEmpty, getVueNotification } from '@core/utils'
import {
  mdiAccountCheckOutline,
  mdiAccountOutline,
  mdiAccountPlusOutline,
  mdiAccountRemoveOutline,
  mdiCogOutline,
  mdiDatabaseOutline,
  mdiDnsOutline,
  mdiPencilOutline,
} from '@mdi/js'
import { ref, watch } from '@vue/composition-api'

export default function useUsersList() {
  const userListTable = ref([])
  const $notify = getVueNotification()

  const tableColumns = [
    { text: 'USER', value: 'username' },
    { text: 'EMAIL', value: 'email' },
    { text: 'ROLE', value: 'role', sortable: false },
    { text: 'PLAN', value: 'plan', sortable: false },
    { text: 'STATUS', value: 'status', sortable: false },
    { text: 'PAYMENT', value: 'expiresAt', sortable: false },
    {
      text: 'ACTIONS',
      value: 'actions',
      align: 'center',
      sortable: false,
    },
  ]

  const searchQuery = ref('')
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)
  const totalUserListTable = ref(0)
  const loading = ref(false)
  const options = ref({
    sortBy: ['id'],
    sortDesc: [false],
  })
  const userTotalLocal = ref([])
  const selectedRows = ref([])

  // fetch data
  const fetchUsers = () => {
    // fetch all users
    store
      .dispatch('admin/fetchUsers', {
        q: searchQuery.value,
        options: options.value,
        status: statusFilter.value,
        role: roleFilter.value,
        plan: planFilter.value,
      })
      .then(response => {
        const { data, total, userTotal } = response.data

        userListTable.value = keysToCamel(data)
        totalUserListTable.value = keysToCamel(total)

        // remove loading state
        loading.value = false
      })
      .catch(error => {
        // TODO: Next Update - Show notification
        $notify({
          group: 'user',
          type: 'error',
          text: 'Oops, Unable to fetch users!',
        })
        console.log('error :>> ', error)
      })
  }

  watch([searchQuery, roleFilter, planFilter, statusFilter, options], () => {
    loading.value = true
    selectedRows.value = []
    fetchUsers()
  })

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveUserRoleVariant = role => {
    if (role === 1) return 'warning'
    if (role === 2) return 'success'
    if (role === 3) return 'info'
    if (role === 4) return 'error'

    return 'primary'
  }

  const resolveUserRoleIcon = role => {
    if (role === 1) return mdiCogOutline
    if (role === 2) return mdiDatabaseOutline
    if (role === 3) return mdiPencilOutline
    if (role === 4) return mdiDnsOutline

    return mdiAccountOutline
  }

  const resolveUserStatusVariant = status => {
    if (status === 1) return 'success' // active
    if (status === 2) return 'secondary' // inactive
    if (status === 3) return 'warning' // pending

    return 'primary'
  }

  const resolveUserStatusTitle = status => {
    if (status === 1) return 'active'
    if (status === 2) return 'inactive'
    if (status === 3) return 'pending'

    return 'primary'
  }

  const resolveUserTotalIcon = total => {
    if (total === 'Total Users') return { icon: mdiAccountOutline, color: 'primary' }
    if (total === 'Paid Users') return { icon: mdiAccountPlusOutline, color: 'error' }
    if (total === 'Active Users') return { icon: mdiAccountCheckOutline, color: 'success' }
    if (total === 'Pending Users') return { icon: mdiAccountRemoveOutline, color: 'warning' }

    return { icon: mdiAccountOutline, color: 'primary' }
  }

  const resolveUserPlanTitle = subscriptions => {
     return !isEmpty(subscriptions)? subscriptions[0].plan.title.toUpperCase() + '/' + subscriptions[0].plan.interval: 'FREE'
    }

  const resolveUserPaymentStatusTitle = subscriptions => {
    const today = new Date();
    if (!isEmpty(subscriptions)) {
      let subscription = subscriptions[0]
      if (subscription.stripeStatus === 'active') {
        return 'Subscribed'
      } else {
        return 'Expired'
      }
    } else {
      return 'Not subscribed'
    }

    // expiresAt = new Date(expiresAt);
    // if (dateInPast(expiresAt, today)) {
    //       return 'Expired'
    //   } else {
    //   let timeLeft = daysLeft(expiresAt, today)
    //   if (isToday(expiresAt)) {
    //       return `${timeLeft.hours} hours left`
    //     } else {
    //       return `${timeLeft.days} days left`
    //     }
    //   }
    }

  const resolveUserPaymentStatusVariant = subscription => {
    const today = new Date();
    if (!isEmpty(subscription)) {
      subscription = subscription[0]
      if (subscription.stripeStatus === 'active') {
        return 'success'
      } else {
        return 'error'
      }
    } else {
      return 'warning'
    }
    // expiresAt = new Date(expiresAt);
    // if (dateInPast(expiresAt, today)) {
    //       return 'error'
    //   } else {
    //       return 'primary'
    //   }
    }

  return {
    userListTable,
    tableColumns,
    searchQuery,
    roleFilter,
    planFilter,
    statusFilter,
    totalUserListTable,
    loading,
    options,
    userTotalLocal,
    selectedRows,
    fetchUsers,
    resolveUserRoleVariant,
    resolveUserRoleIcon,
    resolveUserPlanTitle,
    resolveUserStatusVariant,
    resolveUserStatusTitle,
    resolveUserTotalIcon,
    resolveUserPaymentStatusTitle,
    resolveUserPaymentStatusVariant,
  }
}

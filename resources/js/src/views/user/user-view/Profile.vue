<template>
  <div id="user-view">
    <v-row>
      <v-col cols="12" md="5" lg="4">
        <user-bio-panel
          :user-data="userDataLocal"
          :is-plan-upgrade-dialog-visible.sync="isPlanUpgradeDialogVisible"
        ></user-bio-panel>
      </v-col>

      <v-col cols="12" md="7" lg="8">
        <v-tabs v-model="userTab" show-arrows grow class="user-tabs">
          <v-tab v-for="tab in tabs" :key="tab.icon">
            <v-icon size="20" class="me-3">
              {{ tab.icon }}
            </v-icon>
            <span>{{ tab.title }}</span>
          </v-tab>
        </v-tabs>

        <v-tabs-items id="user-tabs-content" v-model="userTab" class="mt-5 pa-1">
          <v-tab-item>
            <user-tab-overview
              :user-data="userDataLocal"
            ></user-tab-overview>
          </v-tab-item>

          <v-tab-item>
            <user-tab-activities
              :key="componentKey"
              :user-data="userDataLocal"
            ></user-tab-activities>
          </v-tab-item>

          <v-tab-item>
            <user-tab-security
              :user-data="userDataLocal"
            ></user-tab-security>
          </v-tab-item>
          <v-tab-item>
            <user-tab-billings-plans
              :user-data="userDataLocal"
              :is-plan-upgrade-dialog-visible.sync="isPlanUpgradeDialogVisible"
            ></user-tab-billings-plans>
          </v-tab-item>
        </v-tabs-items>
      </v-col>
    </v-row>

  </div>
</template>

<script>
  import { ref, watch } from '@vue/composition-api'
import store from '@/store'

// eslint-disable-next-line object-curly-newline
import { mdiAccountOutline, mdiLockOutline, mdiBookmarkOutline, mdiTimeline } from '@mdi/js'
import UserBioPanel from './user-bio-panel/UserBioPanel.vue'
import UserTabOverview from './user-tab-overview/UserTabOverview.vue'
import UserTabSecurity from './user-tab-security/UserTabSecurity.vue'
import UserTabBillingsPlans from './user-tab-billings-plans/UserTabBillingsPlans.vue'
import UserTabActivities from './user-tab-activities/UserTabActivities'

export default {
  components: {
    UserTabActivities,
    UserBioPanel,
    UserTabOverview,
    UserTabSecurity,
    UserTabBillingsPlans,
  },
  setup() {

    const userDataLocal = ref({})
    const userTab = ref(null)
    const selectedPlan = ref('')
    const isPlanUpgradeDialogVisible = ref(false)
    const errorMessages = ref([])
    const componentKey = ref(0)

    var userData = store.state.auth.userData
    userDataLocal.value = userData
    selectedPlan.value = userData.subscription ? userData.subscription.plan.id : null

    const tabs = [
      { icon: mdiAccountOutline, title: 'Overview' },
      { icon: mdiTimeline, title: 'Activity' },
      { icon: mdiLockOutline, title: 'Security' },
      { icon: mdiBookmarkOutline, title: 'Billing & Plan' },
    ]

    watch(userTab, val => {
      if (userTab.value === 1) componentKey.value++
    })

    return {
      tabs,
      userTab,
      componentKey,
      selectedPlan,
      isPlanUpgradeDialogVisible,
      userDataLocal,
      errorMessages,
    }
  },
}
</script>

<style lang="scss">
@import '@resources/sass/preset/pages/user.scss';
</style>

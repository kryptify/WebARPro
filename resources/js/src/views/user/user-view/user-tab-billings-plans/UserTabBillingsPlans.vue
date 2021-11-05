<template>
  <div class="user-tab-billings-plans">
    <!-- current plan -->
    <v-card class="mb-7">
      <v-card-title> Current plan </v-card-title>

      <v-card-text>
        <v-row>
          <v-col cols="12" md="6" align-self="center">
            <div v-if="userData.subscriptions">
              <h4 class="mb-1">
                <span class="font-weight-medium me-1">Your Current Plan is</span>
                <span>{{ resolveUserPlanTitle(userData.subscriptions) }}</span>
              </h4>
              <p>{{ !isEmpty(userData.subscriptions)? userData.subscriptions[0].plan.subtitle : '' }}</p>
<!--              <h4 class="font-weight-medium mb-1">Active until {{ formatDate(expiresAt) }} </h4>-->
              <p>We will send you a notification upon Subscription expiration</p>
            </div>
            <div v-else>
              <h4 class="mb-1">
                <span class="font-weight-medium me-1">You have no plan</span>
                <span>Please update your plan</span>
              </h4>
            </div>
          </v-col>

          <v-col cols="12" class="d-flex align-center flex-wrap">
            <v-btn
              color="primary"
              class="me-3 mb-3 mb-sm-0"
              to="/pricing/plans"
            >
              <span class="me-1">Upgrade Plan</span>
              <v-icon size="15">
                {{ icons.mdiArrowRight }}
              </v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import { mdiPlus, mdiDeleteOutline, mdiSquareEditOutline, mdiArrowRight } from '@mdi/js'
import { dateInPast, isToday, daysLeft, isEmpty } from '@core/utils'
import useUsersList from '../../user-list/useUsersList'
import { formatDate } from '@core/utils/filter'

export default {
  props: {
    userData: {
      type: Object,
      required: true,
    },
    isPlanUpgradeDialogVisible: {
      type: Boolean,
      required: true,
    },
  },
  setup() {

    const today = new Date();

    const resolveDaysLeftVariant = subscriptions => {

      if (resolveExpired(expiresAt)) return null;
      let timeLeft = daysLeft(expiresAt, today)

      if (isToday(expiresAt)) {
        return `${timeLeft.hours} hours left`
      } else {
        return `${timeLeft.days} days left`
      }
    }

    const resolveExpired = expiresAt => dateInPast(expiresAt, today)

    const { resolveUserPlanTitle, resolveUserPaymentStatusVariant } = useUsersList()

    return {
      resolveUserPlanTitle,
      resolveUserPaymentStatusVariant,
      resolveDaysLeftVariant,
      // resolveExpired,
      // expiresAt,
      formatDate,
      isEmpty,
      icons: {
        mdiPlus,
        mdiDeleteOutline,
        mdiSquareEditOutline,
        mdiArrowRight,
      },
    }
  },
}
</script>

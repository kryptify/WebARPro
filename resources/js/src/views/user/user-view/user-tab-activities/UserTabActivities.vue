<template>
  <div class="user-tab-activities">
  <!-- activity timeline -->
  <v-card class="mb-7">
    <v-card-title> Your Activity Timeline </v-card-title>
    <v-card-text>
      <v-timeline dense class="timeline-custom-dense timeline-custom-dots">
        <v-timeline-item v-for="item in activities" :color="resolveUserActivityVariant(item)" :key="item.id" small>
          <div class="d-flex justify-space-between align-center flex-wrap">
            <h4 class="font-weight-medium me-1">{{ item.description }}</h4>
            <small class="text-no-wrap">{{ formatTimeAgo(item.createdAt) }} ago</small>
          </div>
          <p class="mb-0">{{ item.description }} at {{ formatDateToMonthShort(item.createdAt) }}</p>
        </v-timeline-item>
      </v-timeline>
      <v-progress-linear
        v-if="loading"
        indeterminate
        color="secondary"
        rounded
        height="10"
        class="mt-5"
      ></v-progress-linear>
    </v-card-text>
    <v-card-actions class="dense justify-end">
        <v-btn text color="primary" v-if="moreActivities" @click="fetchActivityData"> More... </v-btn>
        <v-btn text color="primary" v-else @click="fetchInitialActivityData"> Less... </v-btn>
    </v-card-actions>
  </v-card>
  </div>
</template>

<script>
  import { ref, onMounted } from '@vue/composition-api'
  import axios from '@axios'
  import { keysToCamel } from '@core/utils'
  import { formatTimeAgo, formatDateToMonthShort } from '@core/utils/filter'
  import { getVueNotification } from '@core/utils'

  export default {
    name: 'UserTabActivities',
    setup() {
      const $notify = getVueNotification()

      const activities = ref([])
      const loading = ref(true)
      const limit = ref(5)
      const offset = ref(0)
      const moreActivities = ref(true)

      const resolveUserActivityVariant = activity => {
        if (activity.type === 'auth') return 'success'
        if (activity.type === 'user') return 'primary'
        if (activity.type === 'payment') return 'error'
        if (activity.type === 'admin') return 'info'

        return 'secondary'
      }

      const fetchActivityData = () => {
        loading.value = true
        axios
          .post('/api/user/activities', {
            limit: limit.value,
            offset: offset.value
          })
          .then(response => {

            if (response.data.success) {
              if  (response.data.activities.length === 0) {
                moreActivities.value = false
                loading.value = false
              } else {
                var activityData = keysToCamel(response.data.activities)
                activities.value = activities.value.concat(activityData)
                loading.value = false
                offset.value = offset.value + activityData.length
              }
            } else {
              console.error('Oops, Unable to fetch activity data!')
              console.log('error :>> ', response)
            }
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            console.error('Oops, Unable to fetch activity data!')
            console.log('error :>> ', error.response)
          })
      }

      const fetchInitialActivityData = () => {
        offset.value = 0
        axios
          .post('/api/user/activities', {
            limit: limit.value,
            offset: offset.value
          })
          .then(response => {

            if (response.data.success) {

              var activityData = keysToCamel(response.data.activities)

              activities.value = activityData
              moreActivities.value = true
              loading.value = false
              offset.value = offset.value + activityData.length
            } else {
              $notify({
                group: 'user',
                type: 'error',
                text: 'Oops, Unable to fetch activity data!'
              })
            }
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            $notify({
              group: 'user',
              type: 'error',
              text: 'Oops, Unable to fetch activity data!'
            })
          })
      }

      onMounted(async () => {
        fetchInitialActivityData()
      })

      return {
        activities,
        loading,
        moreActivities,
        resolveUserActivityVariant,
        fetchActivityData,
        fetchInitialActivityData,
        formatTimeAgo,
        formatDateToMonthShort,
      }
    },
  }
</script>

<template>
  <div class="user-tab-overview">
    <!-- user project list -->
    <v-card class="mb-7">
      <v-card-title> Project List </v-card-title>

      <v-data-table :headers="tableColumnHeaders" :items="projectList" hide-default-footer>
        <!-- project -->
        <template #[`item.project`]="{ item }">
          <div class="d-flex align-center">
            <v-avatar size="33" class="me-3">
              <v-img :src="require(`@/assets/images/icons/project-icons/${item.logo}`).default"></v-img>
            </v-avatar>
            <div class="text-no-wrap">
              <p class="font-weight-medium text--primary mb-n1">
                {{ item.name }}
              </p>
              <span class="text-xs text--disabled">{{ item.project }}</span>
            </div>
          </div>
        </template>

        <!-- progress -->
        <template #[`item.progress`]="{ item }">
          <span>{{ item.progress }}%</span>
          <v-progress-linear
            height="6"
            rounded
            class="project-progress mt-1"
            :color="resolveUserProgressVariant(item.progress)"
            :value="item.progress"
          ></v-progress-linear>
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import { ref, onMounted } from '@vue/composition-api'
import axios from '@axios'
import { keysToCamel } from '@core/utils'

export default {
  setup() {
    const tableColumnHeaders = [
      {
        text: 'PROJECT',
        value: 'project',
        sortable: false,
      },
      { text: 'TOTAL TASK', value: 'totalTask', sortable: false },
      { text: 'PROGRESS', value: 'progress', sortable: false },
      { text: 'HOURS', value: 'hours', sortable: false },
    ]
    const projectList = [
      {
        logo: 'react.png',
        name: 'BGC eCommerce App',
        project: 'React Project',
        totalTask: '122/240',
        progress: 78,
        hours: '18:42',
      },
      {
        logo: 'figma.png',
        name: 'Falcon Logo Design',
        project: 'Figma Project',
        totalTask: '9/56',
        progress: 18,
        hours: '20:42',
      },
      {
        logo: 'vue.png',
        name: 'Dashboard Design',
        project: 'Vuejs Project',
        totalTask: '290/320',
        progress: 62,
        hours: '120:87',
      },
      {
        logo: 'xamarin.png',
        name: 'Foodista mobile app',
        project: 'Xamarin Project',
        totalTask: '290/320',
        progress: 8,
        hours: '120:87',
      },
      {
        logo: 'python.png',
        name: 'Dojo Email App',
        project: 'Python Project',
        totalTask: '120/186',
        progress: 49,
        hours: '230:10',
      },
      {
        logo: 'sketch.png',
        name: 'Blockchain Website',
        project: 'Sketch Project',
        totalTask: '99/109',
        progress: 92,
        hours: '342:41',
      },
      {
        logo: 'html.png',
        name: 'Hoffman Website',
        project: 'HTML Project',
        totalTask: '98/110',
        progress: 88,
        hours: '12:45',
      },
    ]

    const resolveUserProgressVariant = progress => {
      if (progress <= 25) return 'error'
      if (progress > 25 && progress <= 50) return 'warning'
      if (progress > 50 && progress <= 75) return 'primary'
      if (progress > 75 && progress <= 100) return 'success'

      return 'secondary'
    }

    onMounted(() => {
    })

    return {
      tableColumnHeaders,
      projectList,
      resolveUserProgressVariant,
    }
  },
}
</script>

<style lang="scss" scoped>
.project-progress {
  min-width: 4rem;
}
</style>

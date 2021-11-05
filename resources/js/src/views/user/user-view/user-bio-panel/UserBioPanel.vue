<template>
  <v-row class="user-bio-panel">
    <!-- user profile -->
    <v-col cols="12">
      <v-card class="pt-10">
        <v-card-title class="justify-center flex-column">
          <v-avatar
            :color="userData.avatar ? '' : 'primary'"
            :class="userData.avatar ? '' : 'v-avatar-light-bg primary--text'"
            size="120"
            rounded
            class="mb-4"
          >
            <v-img v-if="userData.avatar" :src="require(`@/assets/images/avatars/${userData.avatar}`).default"></v-img>
            <span v-else class="font-weight-semibold text-5xl">{{ avatarText(userData.userame) }}</span>
          </v-avatar>

          <span class="mb-2">{{ userData.username }}</span>

          <v-chip
            label
            small
            :color="resolveUserRoleVariant(userData.role.id)"
            :class="`v-chip-light-bg text-sm font-weight-semibold ${resolveUserRoleVariant(
              userData.role,
            )}--text text-capitalize`"
          >
            {{ userData.role.title }}
          </v-chip>
        </v-card-title>

        <v-card-text>
          <h2 class="text-xl font-weight-semibold mb-2">Details</h2>

          <v-divider></v-divider>

          <v-list>
            <v-list-item dense class="px-0 mb-n2">
              <span class="font-weight-medium me-2">Username:</span>
              <span class="text--secondary">{{ userData.username }}</span>
            </v-list-item>

            <v-list-item dense class="px-0 mb-n2">
              <span class="font-weight-medium text-no-wrap me-2">Billing Email:</span>
              <span class="text--secondary">{{ userData.email }}</span>
            </v-list-item>

            <v-list-item dense class="px-0 mb-n2">
              <span class="font-weight-medium me-2">Status:</span>
              <span class="text--secondary">
                <v-chip
                  small
                  label
                  :color="resolveUserStatusVariant(userData.status)"
                  :class="`v-chip-light-bg ${resolveUserStatusVariant(
                    userData.status,
                  )}--text font-weight-medium text-capitalize`"
                >
                  {{ resolveUserStatusTitle(userData.status) }}
                </v-chip>
              </span>
            </v-list-item>

            <v-list-item dense class="px-0 mb-n2">
              <span class="font-weight-medium me-2">Role:</span>
              <span class="text--secondary text-capitalize">{{ userData.role.title }}</span>
            </v-list-item>

          </v-list>
        </v-card-text>

        <v-card-actions class="justify-center">
          <v-btn color="primary" class="me-3" @click="isBioDialogOpen = !isBioDialogOpen"> Edit </v-btn>
        </v-card-actions>
      </v-card>

      <!-- edit profile dialog data -->
      <user-bio-edit :is-bio-dialog-open.sync="isBioDialogOpen" :user-data="userData"></user-bio-edit>
    </v-col>

  </v-row>
</template>

<script>
import { mdiCheck, mdiBriefcaseVariantOutline, mdiCheckboxBlankCircle } from '@mdi/js'
import { avatarText, kFormatter } from '@core/utils/filter'
import { ref } from '@vue/composition-api'
import UserBioEdit from './UserBioEdit.vue'
import useUsersList from '../../user-list/useUsersList'

export default {
  components: {
    UserBioEdit,
  },
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
    const { resolveUserStatusVariant, resolveUserRoleVariant, resolveUserStatusTitle } = useUsersList()

    const isBioDialogOpen = ref(false)

    return {
      resolveUserStatusVariant,
      resolveUserRoleVariant,
      resolveUserStatusTitle,
      avatarText,
      kFormatter,
      isBioDialogOpen,
      icons: {
        mdiCheck,
        mdiBriefcaseVariantOutline,
        mdiCheckboxBlankCircle,
      },
    }
  },
}
</script>

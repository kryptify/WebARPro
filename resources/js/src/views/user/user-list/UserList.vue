<template>
  <div id="user-list">
    <!-- app drawer -->
    <user-list-add-new
      v-model="isAddNewUserSidebarActive"
      :role-options="roleOptions"
      :plan-options="planOptions"
      @refetch-data="fetchUsers"
    ></user-list-add-new>

    <!-- list filters -->
    <v-card>
      <v-card-title> Search &amp; Filter </v-card-title>
      <v-row class="px-2 ma-0" justify="center">
        <!-- role filter -->
        <v-col cols="12" sm="4">
          <v-select
            v-model="roleFilter"
            placeholder="Select Role"
            :items="roleOptions"
            item-text="title"
            item-value="value"
            outlined
            clearable
            dense
            hide-details
          ></v-select>
        </v-col>

        <!-- plan filter -->
        <v-col cols="12" sm="4">
          <v-select
            v-model="planFilter"
            placeholder="Select Plan"
            :items="planOptions"
            item-text="title"
            item-value="value"
            outlined
            dense
            clearable
            hide-details
          ></v-select>
        </v-col>

        <!-- status filter -->
        <v-col cols="12" sm="4">
          <v-select
            v-model="statusFilter"
            placeholder="Select Status"
            :items="statusOptions"
            item-text="title"
            item-value="value"
            outlined
            dense
            clearable
            hide-details
          ></v-select>
        </v-col>
      </v-row>

      <v-divider class="mt-4"></v-divider>

      <!-- actions -->
      <v-card-text class="d-flex align-center flex-wrap pb-0">
        <!-- search -->
        <v-text-field
          v-model="searchQuery"
          placeholder="Search"
          outlined
          hide-details
          dense
          class="user-search me-3 mb-4"
        >
        </v-text-field>

        <v-spacer></v-spacer>

        <div class="d-flex align-center flex-wrap">
          <v-btn color="primary" class="mb-4 me-3" @click.stop="isAddNewUserSidebarActive = !isAddNewUserSidebarActive">
            <v-icon>{{ icons.mdiPlus }}</v-icon>
            <span>Add New User</span>
          </v-btn>

        </div>
      </v-card-text>

      <!-- table -->
      <v-data-table
        v-model="selectedRows"
        :headers="tableColumns"
        :items="userListTable"
        :options.sync="options"
        :server-items-length="totalUserListTable"
        :loading="loading"
        show-select
      >
        <!-- name -->
        <template #[`item.username`]="{ item }">
          <div class="d-flex align-center">
            <v-avatar
              :color="item.avatar ? '' : 'primary'"
              :class="item.avatar ? '' : 'v-avatar-light-bg primary--text'"
              size="32"
            >
              <v-img v-if="item.avatar" :src="require(`@/assets/images/avatars/${item.avatar}`).default"></v-img>
              <span v-else class="font-weight-medium">{{ avatarText(item.username) }}</span>
            </v-avatar>

            <div class="d-flex flex-column ms-3">
              <router-link
                :to="{ name: 'admin-user', params: { id: item.id } }"
                class="text--primary font-weight-semibold text-truncate cursor-pointer text-decoration-none"
              >
                {{ item.username }}
              </router-link>
              <small>@{{ item.username }}</small>
            </div>
          </div>
        </template>

        <!-- role -->
        <template #[`item.role`]="{ item }">
          <div class="d-flex align-center">
            <v-avatar
              size="30"
              :color="resolveUserRoleVariant(item.role.id)"
              :class="`v-avatar-light-bg ${resolveUserRoleVariant(item.role.id)}--text me-3`"
            >
              <v-icon size="18" :color="resolveUserRoleVariant(item.role.id)">
                {{ resolveUserRoleIcon(item.role.id) }}
              </v-icon>
            </v-avatar>
            <span class="text-capitalize">{{ item.role.title }}</span>
          </div>
        </template>

        <!-- plan -->
        <template #[`item.plan`]="{ item }">
          <span class="text-capitalize font-weight-semibold text--primary">{{ resolveUserPlanTitle(item.subscriptions) }}</span>
        </template>

        <!-- login status -->
        <template #[`item.status`]="{ item }">
          <v-chip
            small
            :color="resolveUserStatusVariant(item.status)"
            :class="`${resolveUserStatusVariant(item.status)}--text`"
            class="v-chip-light-bg font-weight-semibold text-capitalize"
          >
            {{ resolveUserStatusTitle(item.status) }}
          </v-chip>
        </template>

        <!-- payment status -->
        <template #[`item.expiresAt`]="{ item }">
          <v-chip
            small
            :color="resolveUserPaymentStatusVariant(item.subscriptions)"
            :class="`${resolveUserPaymentStatusVariant(item.subscriptions)}--text`"
            class="v-chip-light-bg font-weight-semibold text-capitalize"
          >
            {{ resolveUserPaymentStatusTitle(item.subscriptions) }}
          </v-chip>
        </template>

        <!-- actions -->
        <template #[`item.actions`]="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-bind="attrs" v-on="on">
                <v-icon>{{ icons.mdiDotsVertical }}</v-icon>
              </v-btn>
            </template>

            <v-list>
              <v-list-item :to="{ name: 'admin-user', params: { id: item.id } }">
                <v-list-item-title>
                  <v-icon size="20" class="me-2">
                    {{ icons.mdiFileDocumentOutline }}
                  </v-icon>
                  <span>Details</span>
                </v-list-item-title>
              </v-list-item>

              <v-list-item link @click.stop="onDelete(item.id)">
                <v-list-item-title>
                  <v-icon size="20" class="me-2">
                    {{ icons.mdiDeleteOutline }}
                  </v-icon>
                  <span>Delete</span>
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>

    <!--confirm delete-->
    <confirm-dialog ref="confirm"></confirm-dialog>
  </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import {
  mdiSquareEditOutline,
  mdiDotsVertical,
  mdiPlus,
  mdiFileDocumentOutline,
  mdiDeleteOutline,
  mdiExportVariant,
  mdiAccountOutline,
} from '@mdi/js'
import store from '@/store'
import axios from '@axios'
import { onMounted, ref } from '@vue/composition-api'
import UserListAddNew from './UserListAddNew.vue'
import ConfirmDialog from '@/components/dialog/ConfirmDialog'
import { avatarText } from '@core/utils/filter'
import { isEmpty } from '@core/utils'
import { getVueNotification } from '@core/utils'

import useUsersList from './useUsersList'

export default {
  components: {
    UserListAddNew,
    ConfirmDialog,
  },
  setup() {
    const $notify = getVueNotification()

    var planOptions = ref([]);
    const planDataToOptions = planData => {
      return planOptions = planData.map(plan => ({
        title: `${plan.title.toUpperCase()} ${plan.interval}`,
        value: plan.stripeId
      }))
    }

    var roleOptions = ref([]);
    const roleDataToOptions = roleData => {
      return roleOptions = roleData.map(role => ({
        title: role.title,
        value: role.id
      }))
    }

    const statusOptions = [
      { id: 1, title: 'Active', value: 1 },
      { id: 2, title: 'Inactive', value: 2 },
    ]

    const {
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

      resolveUserRoleVariant,
      resolveUserRoleIcon,
      resolveUserPlanTitle,
      resolveUserStatusVariant,
      resolveUserStatusTitle,
      resolveUserTotalIcon,
      resolveUserPaymentStatusTitle,
      resolveUserPaymentStatusVariant,
      fetchUsers
    } = useUsersList()

    const isAddNewUserSidebarActive = ref(false)
    const confirm = ref(null)

    const onDelete = async userId => {
      if (
        await confirm.value.open(
          "Confirm",
          "Are you sure to delete this user?",
        )
      ) {
        axios
          .post('/api/admin/deleteUser', {
            id: userId,
          }).then(response => {
          if (response.data.success) {
            fetchUsers()
            $notify({
              group: 'user',
              type: 'success',
              text: 'User deleted!',
            })
          } else {
            $notify({
              group: 'user',
              type: 'error',
              text: response.data.message,
            })
          }
        })
        .catch(error => {
          // TODO: Next Update - Show notification
          $notify({
            group: 'user',
            type: 'error',
            text: error.response.data.message,
          })
        })
      }
    }

    onMounted(() => {
      // fetch all plans
      if (isEmpty(store.state.user.planData)) {
        store
          .dispatch('user/fetchPlanData')
          .then(planData => {
            planOptions.value = planDataToOptions(planData)
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            $notify({
              group: 'user',
              type: 'error',
              text: 'Oops, Unable to fetch plan data!',
            })
          })
      } else {
        planOptions.value = planDataToOptions(store.state.user.planData)
      }

      // fetch all roles
      if (isEmpty(store.state.user.roleData)) {
        store
          .dispatch('user/fetchRoleData')
          .then(roleData => {
            roleOptions.value = roleDataToOptions(roleData)
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            $notify({
              group: 'user',
              type: 'error',
              text: 'Oops, Unable to fetch role data!',
            })
          })
      } else {
        roleOptions.value = roleDataToOptions(store.state.user.roleData)
      }
    })

    return {
      userListTable,
      tableColumns,
      searchQuery,
      roleFilter,
      planFilter,
      statusFilter,
      totalUserListTable,
      roleOptions,
      planOptions,
      statusOptions,
      loading,
      options,
      userTotalLocal,
      isAddNewUserSidebarActive,
      selectedRows,
      avatarText,
      confirm,
      resolveUserRoleVariant,
      resolveUserRoleIcon,
      resolveUserPlanTitle,
      resolveUserStatusVariant,
      resolveUserStatusTitle,
      resolveUserTotalIcon,
      resolveUserPaymentStatusTitle,
      resolveUserPaymentStatusVariant,
      fetchUsers,
      onDelete,

      // icons
      icons: {
        mdiSquareEditOutline,
        mdiFileDocumentOutline,
        mdiDotsVertical,
        mdiDeleteOutline,
        mdiPlus,
        mdiExportVariant,
        mdiAccountOutline,
      },
    }
  },
}
</script>

<style lang="scss">
@import '@resources/sass/preset/pages/user.scss';
</style>

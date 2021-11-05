<template>
  <div id="user-view">
    <v-card>
      <v-card-title>
        User Info
        <v-spacer></v-spacer>

        <v-switch
          v-model="switchEdit"
          :label="`${switchEdit.toString()}`"
          inset
          true-value="EDIT"
          false-value="VIEW"
          hide-details
        ></v-switch>

      </v-card-title>
      <v-form ref="userForm" @submit.prevent="handleFormSubmit">
        <v-card-text>
          <v-row>
            <v-col cols="12" md="6">
              <!-- username -->
              <v-row>
                <v-col
                  cols="12"
                  sm="3"
                >
                  <label>Username</label>
                </v-col>
                <v-col
                  cols="12"
                  sm="9"
                >
                  <v-text-field
                    id="username"
                    v-model="username"
                    :prepend-inner-icon="icons.mdiAccountOutline"
                    :rules="[validators.required, validators.alphaValidator]"
                    label="username"
                    outlined
                    dense
                    placeholder="Username"
                    :readonly="!editable"
                  ></v-text-field>
                </v-col>
              </v-row>

              <!-- email -->
              <v-row>
                <v-col
                  cols="12"
                  sm="3"
                >
                  <label>Email</label>
                </v-col>
                <v-col
                  cols="12"
                  sm="9"
                >
                  <v-text-field
                    v-model="email"
                    :prepend-inner-icon="icons.mdiEmailOutline"
                    :rules="[validators.required,validators.emailValidator]"
                    :error-messages="errorMessages.email"
                    label="Email"
                    type="email"
                    outlined
                    dense
                    placeholder="Email"
                    :readonly="!editable"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12" md="6">
              <!-- role filter -->
              <v-row>
                <v-col
                  cols="12"
                  sm="3"
                >
                  <label>Role</label>
                </v-col>
                <v-col
                  cols="12"
                  sm="9"
                >
                  <v-select
                    id="role"
                    v-model="role"
                    placeholder="Select Role"
                    :items="roleOptions"
                    item-text="title"
                    item-value="value"
                    outlined
                    dense
                    hide-details
                    :readonly="!editable"
                  ></v-select>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="justify-end">
          <v-btn color="primary" outlined type="button" class="me-3" to="/admin/users">
            <v-icon size="15" class="me-1">
              {{ icons.mdiArrowLeft }}
            </v-icon><span> User List</span>
          </v-btn>
          <v-btn color="primary" type="submit" class="me-3" :disabled="!editable"> Submit</v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </div>
</template>

<script>
  // eslint-disable-next-line object-curly-newline
  import { onMounted, ref, computed } from '@vue/composition-api'
  import store from '@/store'
  import { isEmpty, keysToCamel } from '@core/utils'

  // eslint-disable-next-line object-curly-newline
  import { mdiAccountOutline, mdiEmailOutline, mdiArrowLeft } from '@mdi/js'
  import UserBioPanel from '../user-view/user-bio-panel/UserBioPanel.vue'
  import UserTabOverview from '../user-view/user-tab-overview/UserTabOverview.vue'
  import UserTabBillingsPlans from '../user-view/user-tab-billings-plans/UserTabBillingsPlans.vue'
  import { required, emailValidator, alphaValidator } from '@core/utils/validation'
  import { getVueNotification } from '@core/utils'

  export default {
    components: {
      UserBioPanel,
      UserTabOverview,
      UserTabBillingsPlans,
    },
    props: {
      id: {
        required: true,
      },
    },
    setup(props) {
      const $notify = getVueNotification()

      const userForm = ref(null)
      const errorMessages = ref([])
      const username = ref(null)
      const email = ref(null)
      const role = ref(null)
      const switchEdit = ref('VIEW')
      const editable = computed(() => switchEdit.value === 'EDIT')

      const fetchUserData = async () => {
        await store
          .dispatch('user/fetchUser', {
            id: props.id
          })
          .then(response => {
            if (response.data.success) {

              const userData = keysToCamel(response.data.userData)
              username.value = userData.username
              email.value = userData.email
              role.value = userData.role.id

              errorMessages.value = [];

            } else {
              // TODO: Next Update - Show notification
              $notify({
                group: 'user',
                type: 'error',
                text: 'Oops, Unable to fetch user data!'
              })
            }
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            $notify({
              group: 'user',
              type: 'error',
              text: 'Oops, Unable to fetch user data!'
            })
          })
      }

      const fetchData = () => {
        // fetch all roles
        if (isEmpty(store.state.user.roleData)) {
          store
            .dispatch('user/fetchRoleData')
            .then(response => {
              roleOptions.value = roleDataToOptions(response)
            })
            .catch(error => {
              // TODO: Next Update - Show notification
              $notify({
                group: 'user',
                type: 'error',
                text: 'Oops, Unable to fetch role data!'
              })
            })
        } else {
          roleOptions.value = roleDataToOptions(store.state.user.roleData)
        }
      }

      var roleOptions = ref([])
      const roleDataToOptions = roleData => {
        return roleOptions = roleData.map(role => ({
          title: role.title,
          value: role.id,
        }))
      }

      onMounted(() => {
        fetchData()
        fetchUserData()
      })

      const handleFormSubmit = () => {
        const isFormValid = userForm.value.validate()

        if (!isFormValid) return

        store
          .dispatch('admin/saveUser', {
            id: props.id,
            username: username.value,
            email: email.value,
            role: role.value,
          })
          .then(response => {
            if (response.data.success) {
              $notify({
                group: 'user',
                type: 'success',
                text: 'User updated!',
              })
            } else {
              errorMessages.value = response.data
              $notify({
                group: 'user',
                type: 'error',
                text: 'Unable to update user!',
              })
            }
          })
          .catch(error => {
            $notify({
              group: 'user',
              type: 'error',
              text: 'Unable to update user!',
            })
          })
      }

      return {
        userForm,
        username,
        email,
        role,
        switchEdit,
        editable,
        errorMessages,
        roleOptions,
        validators: { required, emailValidator, alphaValidator },
        icons: {
          mdiAccountOutline,
          mdiEmailOutline,
          mdiArrowLeft
        },
        handleFormSubmit
      }
    },
  }
</script>

<style lang="scss">
  @import '@resources/sass/preset/pages/user.scss';
</style>

<template>
  <v-navigation-drawer
    :value="isAddNewUserSidebarActive"
    temporary
    touchless
    :right="!$vuetify.rtl"
    :width="$vuetify.breakpoint.smAndUp ? 350 : '100%'"
    app
    @input="(val) => $emit('update:is-add-new-user-sidebar-active', val)"
  >
    <v-card height="100%">
      <div class="drawer-header d-flex align-center mb-4">
        <span class="font-weight-semibold text-base text--primary">Add New User</span>
        <v-spacer></v-spacer>
        <v-btn
          icon
          small
          @click="$emit('update:is-add-new-user-sidebar-active',false)"
        >
          <v-icon size="22">
            {{ icons.mdiClose }}
          </v-icon>
        </v-btn>
      </div>

      <v-card-text>
        <v-form
          ref="form"
          @submit.prevent="onSubmit"
        >

          <v-text-field
            v-model="userData.username"
            :rules="[validators.required, validators.alphaValidator]"
            outlined
            dense
            label="Username"
            placeholder="Username"
            hide-details="auto"
            class="mb-6"
          ></v-text-field>

          <v-text-field
            v-model="userData.email"
            :rules="[validators.required,validators.emailValidator]"
            :error-messages="errorMessages.email"
            outlined
            dense
            type="email"
            label="Email"
            placeholder="Email"
            hide-details="auto"
            class="mb-6"
          ></v-text-field>

          <v-text-field
            v-model="userData.password"
            outlined
            dense
            :type="isPasswordVisible ? 'text' : 'password'"
            label="Password"
            placeholder="Password"
            :rules="[validators.required, validators.passwordValidator]"
            :append-icon="isPasswordVisible ? icons.mdiEyeOffOutline : icons.mdiEyeOutline"
            hide-details="auto"
            class="mb-6"
            @click:append="isPasswordVisible = !isPasswordVisible"
          ></v-text-field>

          <v-select
            v-model="userData.role"
            label="User Role"
            :items="roleOptions"
            item-text="title"
            item-value="value"
            outlined
            dense
            hide-details="auto"
            class="mb-6"
          >
          </v-select>

          <v-btn
            color="primary"
            class="me-3"
            type="submit"
          >
            Add
          </v-btn>
          <v-btn
            color="secondary"
            outlined
            type="reset"
            @click="resetUserData"
          >
            Cancel
          </v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-navigation-drawer>
</template>

<script>
import {
  mdiClose,
  mdiEyeOutline,
  mdiEyeOffOutline,
} from '@mdi/js'
import store from '@/store'
import { ref, onMounted } from '@vue/composition-api'
import { required, emailValidator, alphaValidator, passwordValidator } from '@core/utils/validation'
import { getVueNotification } from '@core/utils'

export default {
  model: {
    prop: 'isAddNewUserSidebarActive',
    event: 'update:is-add-new-user-sidebar-active',
  },
  props: {
    isAddNewUserSidebarActive: {
      type: Boolean,
      required: true,
    },
    roleOptions: {
      type: Array,
      required: true,
    },
    planOptions: {
      type: Array,
      required: true,
    },
  },
  setup(props, { emit }) {
    const blankUserData = {
      username: '',
      email: '',
      password: '',
      role: 1,
    }

    const form = ref(null)
    const isPasswordVisible = ref(false)
    const errorMessages = ref([])
    const $notify = getVueNotification()

    const resetForm = () => {
      form.value.reset()
    }

    const userData = ref(blankUserData)
    const resetUserData = () => {
      userData.value = blankUserData
      resetForm()
      emit('update:is-add-new-user-sidebar-active', false)
    }

    const onSubmit = () => {
      const isFormValid = form.value.validate()

      if (!isFormValid) return

      store
        .dispatch('admin/saveUser', {
          id: userData.id,
          username: userData.value.username,
          email: userData.value.email,
          password: userData.value.password,
          role: userData.value.role,
        })
        .then(response => {
          if (response.data.success) {

            emit('refetch-data')
            emit('update:is-add-new-user-sidebar-active', false)

            resetUserData()
            $notify({
              group: 'user',
              type: 'success',
              text: 'User added!',
            })
          } else {
            errorMessages.value = response.data
            $notify({
              group: 'user',
              type: 'error',
              text: 'Unable to add user!',
            })
          }
        })
        .catch(error => {
          $notify({
            group: 'user',
            type: 'error',
            text: 'Unable to add user!',
          })
        })
    }

    return {
      resetUserData,
      form,
      errorMessages,
      isPasswordVisible,
      onSubmit,
      userData,

      // validation
      validators: { required, emailValidator, alphaValidator, passwordValidator },
      icons: {
        mdiClose,
        mdiEyeOutline,
        mdiEyeOffOutline,
      },
    }
  },
}
</script>

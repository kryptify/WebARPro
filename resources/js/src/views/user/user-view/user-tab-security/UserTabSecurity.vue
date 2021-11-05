<template>
  <div class="user-tab-security">
    <!-- change password -->
    <v-card class="mb-7">
      <v-card-title> Change Password </v-card-title>

      <v-card-text>
        <v-alert color="warning" text>
          <p class="font-weight-semibold mb-1">Ensure that these requirements are met</p>
          <p class="text-sm mb-0">Minimum 8 characters long, uppercase &amp; symbol</p>
        </v-alert>

        <!-- form -->
        <v-form class="multi-col-validation" ref="passwordForm" @submit.prevent="handleFormSubmit">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="newPassword"
                outlined
                dense
                label="New Password"
                :type="isPasswordVisible ? 'text' : 'password'"
                :rules="[validators.passwordValidator]"
                :append-icon="isPasswordVisible ? icons.mdiEyeOutline : icons.mdiEyeOffOutline"
                @click:append="isPasswordVisible = !isPasswordVisible"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="confirmPassword"
                outlined
                dense
                label="Confirm New Password"
                :type="isPasswordConfirmVisible ? 'text' : 'password'"
                :rules="[validators.confirmedValidator(confirmPassword,newPassword)]"
                :append-icon="isPasswordConfirmVisible ? icons.mdiEyeOutline : icons.mdiEyeOffOutline"
                @click:append="isPasswordConfirmVisible = !isPasswordConfirmVisible"
              ></v-text-field>
            </v-col>

            <v-col cols="12">
              <v-btn color="primary" type="submit"> Change Password </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>

  </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import { mdiSquareEditOutline, mdiDeleteOutline, mdiEyeOutline, mdiEyeOffOutline } from '@mdi/js'
import {
  passwordValidator,
  confirmedValidator,
} from '@core/utils/validation'
import { ref } from '@vue/composition-api'
import axios from '@axios'
import { getVueNotification } from '@core/utils'

export default {
  props: {
    userData: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const passwordForm = ref(null)

    const newPassword = ref('')
    const confirmPassword = ref('')
    const isPasswordVisible = ref(false)
    const isPasswordConfirmVisible = ref(false)
    const $notify = getVueNotification()

    const handleFormSubmit = () => {
      const isFormValid = passwordForm.value.validate()

      if (!isFormValid) return

      axios
        .put('/api/user/password', {
          id: props.userData.id,
          password: newPassword.value,
          c_password: confirmPassword.value,
        })
        .then(response => {
          if (response.data.success) {
            passwordForm.value.reset()
            $notify({
              group: 'user',
              type: 'success',
              text: 'Your password updated!',
            })
          } else {
            $notify({
              group: 'user',
              type: 'error',
              text: 'Unable to change your password!',
            })
          }
        })
        .catch(error => {
          // TODO: Next Update - Show notification
          $notify({
            group: 'user',
            type: 'error',
            text: 'Unable to change your password!',
          })
        })
    }

    return {
      passwordForm,
      handleFormSubmit,
      newPassword,
      confirmPassword,
      isPasswordVisible,
      isPasswordConfirmVisible,
      icons: {
        mdiSquareEditOutline,
        mdiDeleteOutline,
        mdiEyeOutline,
        mdiEyeOffOutline,
      },
      validators: {
        passwordValidator,
        confirmedValidator,
      },
    }
  },
}
</script>

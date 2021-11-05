<template>
  <!-- edit profile dialog -->
  <v-dialog
    v-model="isBioDialogOpen"
    max-width="650px"
    @click:outside="$emit('update:is-bio-dialog-open',false)"
  >
    <v-card class="user-edit-info pa-sm-10 pa-3">
      <v-card-title class="justify-center text-h5">
        Edit User Information
      </v-card-title>
      <v-card-text class="text-center mt-n2">
        Updating user details will receive a privacy audit.
      </v-card-text>

      <v-card-text class="mt-5">
        <v-form class="multi-col-validation" ref="userInfoForm" @submit.prevent="onSubmit">
          <v-row>
            <v-col
              cols="12"
              sm="6"
            >
              <v-text-field
                v-model="username"
                prefix="@"
                outlined
                dense
                :error-messages="errorMessages.username"
                :rules="[validators.required, validators.alphaValidator]"
                label="Username"
              ></v-text-field>
            </v-col>
            <v-col
              cols="12"
              sm="6"
            >
              <v-text-field
                v-model="email"
                outlined
                dense
                :error-messages="errorMessages.email"
                :rules="[validators.required, validators.emailValidator]"
                label="Email"
              ></v-text-field>
            </v-col>
            <v-col
              cols="12"
              class="d-flex justify-center mt-3"
            >
              <v-btn
                color="primary"
                class="me-3"
                type="submit"
              >
                submit
              </v-btn>

              <v-btn
                outlined
                color="secondary"
                @click.prevent="$emit('update:is-bio-dialog-open',false)"
              >
                Discard
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { ref, watch } from '@vue/composition-api'
import { keysToCamel } from '@core/utils'
import axios from '@axios'
// eslint-disable-next-line object-curly-newline
import {
  required,
  emailValidator,
  alphaValidator,
} from '@core/utils/validation'
import { getVueNotification } from '@core/utils'

export default {
  props: {
    isBioDialogOpen: {
      type: Boolean,
      required: true,
    },
    userData: {
      type: Object,
      required: true,
    },
  },
  setup(props, { emit }) {
    const userInfoForm = ref(null)

    const username = ref('')
    const email = ref('')
    const errorMessages = ref([])
    const $notify = getVueNotification()
    username.value = props.userData.username
    email.value = props.userData.email

    // on form submit
    const onSubmit = () => {
      const isFormValid = userInfoForm.value.validate()

      if (!isFormValid) return

      axios
        .post('/api/user/updateMe', {
          username: username.value,
          email: email.value,
        })
        .then(response => {
          if (response.data.success) {

            const userData = keysToCamel(response.data.userData);

            var userDataLocal = ref({});
            userDataLocal.value = props.userData;
            userDataLocal.value.username = userData.username;
            userDataLocal.value.email = userData.email;

            // Change the parent prop user data
            emit('update:is-bio-dialog-open', false);
            emit('update:user-data', userDataLocal.value);

            // ? Set user's data in localStorage for UI/Other purpose
            localStorage.setItem('userData', JSON.stringify(userData))
            $notify({
              group: 'user',
              type: 'success',
              text: 'Your user info updated!',
            })
          } else {

            errorMessages.value = response.data
            $notify({
              group: 'user',
              type: 'error',
              text: 'Unable to update user info!',
            })
          }
        })
        .catch(error => {
          // TODO: Next Update - Show notification
          console.error('Oops, Unable to change user info!')
          console.log('error :>> ', error)
          $notify({
            group: 'user',
            type: 'error',
            text: 'Unable to update user info!',
          })
        })
    }

    return {
      userInfoForm,
      username,
      email,
      onSubmit,
      validators: {
        required,
        emailValidator,
        alphaValidator,
      },
      errorMessages,
    }
  },
}
</script>

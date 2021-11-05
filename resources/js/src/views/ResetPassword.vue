<template>
  <div class="auth-wrapper auth-v1">
    <div class="auth-inner">
      <v-card class="auth-card">
        <!-- logo and title -->
        <v-card-title class="d-flex align-center justify-center py-7">
          <router-link to="/" class="d-flex align-center">
            <v-img :src="appLogo" max-height="30px" max-width="30px" alt="logo" contain class="me-3"></v-img>

            <h2 class="text-2xl font-weight-semibold">
              {{ appName }}
            </h2>
          </router-link>
        </v-card-title>

        <v-card-text>
          <p class="text-2xl font-weight-semibold text--primary mb-2">Reset Password 🔒</p>
          <p class="mb-2">Your new password must be different from previously used passwords</p>
        </v-card-text>

        <!-- login form -->
        <v-card-text>
          <v-form ref="resetForm" @submit.prevent="handleFormSubmit">
            <v-text-field
              v-model="email"
              outlined
              label="Email"
              placeholder="john@example.com"
              :error-messages="errorMessages.email"
              :rules="[validators.required, validators.emailValidator]"
              hide-details="auto"
              class="mb-4"
            ></v-text-field>

            <v-text-field
              v-model="password"
              outlined
              :type="isPasswordVisible ? 'text' : 'password'"
              :error-messages="errorMessages.password"
              :rules="[validators.required, validators.passwordValidator]"
              label="New Password"
              placeholder="············"
              :append-icon="isPasswordVisible ? icons.mdiEyeOffOutline : icons.mdiEyeOutline"
              hide-details
              class="mb-3"
              @click:append="isPasswordVisible = !isPasswordVisible"
            ></v-text-field>

            <v-text-field
              v-model="confirmPassword"
              outlined
              :type="isConfirmPasswordVisible ? 'text' : 'password'"
              label="Confirm Password"
              :error-messages="errorMessages.confirmPassword"
              :rules="[validators.required, validators.confirmedValidator(confirmPassword,password)]"
              placeholder="············"
              :append-icon="isConfirmPasswordVisible ? icons.mdiEyeOffOutline : icons.mdiEyeOutline"
              hide-details
              @click:append="isConfirmPasswordVisible = !isConfirmPasswordVisible"
            ></v-text-field>

            <v-btn block color="primary" class="mt-4"> Set New Password </v-btn>
          </v-form>
        </v-card-text>

        <!-- back to login -->
        <v-card-actions class="d-flex justify-center align-center">
          <router-link :to="{ name: 'auth-login-v1' }" class="d-flex align-center text-sm">
            <v-icon size="24" color="primary">
              {{ icons.mdiChevronLeft }}
            </v-icon>
            <span>Back to login</span>
          </router-link>
        </v-card-actions>
      </v-card>
    </div>

    <!-- background triangle shape  -->
    <img
      class="auth-mask-bg"
      height="190"
      :src="require(`@/assets/images/misc/mask-${$vuetify.theme.dark ? 'dark' : 'light'}.png`).default"
    />

    <!-- tree -->
    <v-img class="auth-tree" width="247" height="185" :src="require('@/assets/images/misc/tree.png').default"></v-img>

    <!-- tree  -->
    <v-img
      class="auth-tree-3"
      width="377"
      height="289"
      :src="require('@/assets/images/misc/tree-3.png').default"
    ></v-img>
  </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import { mdiChevronLeft, mdiEyeOutline, mdiEyeOffOutline } from '@mdi/js'
import {
  required,
  emailValidator,
  passwordValidator,
  confirmedValidator,
} from '@core/utils/validation'
import { ref } from '@vue/composition-api'
import themeConfig from '@themeConfig'

export default {
  props: {
    token: {
      type: String,
      required: true
    }
  },
  setup(props) {
    // Template Refs
    const resetForm = ref(null)

    const isPasswordVisible = ref(false)
    const isConfirmPasswordVisible = ref(false)
    const email = ref('')
    const password = ref('')
    const confirmPassword = ref('')
    const errorMessages = ref([])

    const handleFormSubmit = () => {
      const isFormValid = resetForm.value.validate()
      if (!isFormValid) return

      axios
        .post('/api/auth/reset-password', {
          token: props.token,
          email: email.value,
          password: password.value,
          password_confirmation: confirmPassword.value,

        })
        .then(response => {
          console.log("Reset password successfully!")
        })
        .catch(error => {
          // TODO: Next Update - Show notification
          console.error('Oops, Unable to send password reset link!')
          console.log('error :>> ', error.response)

          errorMessages.value = error.response.data.message
        })
    }

    return {
      handleFormSubmit,

      isPasswordVisible,
      isConfirmPasswordVisible,
      email,
      password,
      confirmPassword,
      errorMessages,

      // themeConfig
      appName: themeConfig.app.name,
      appLogo: themeConfig.app.logo,

      icons: {
        mdiChevronLeft,
        mdiEyeOutline,
        mdiEyeOffOutline,
      },

      validators: {
        required,
        emailValidator,
        passwordValidator,
        confirmedValidator,
      },

      // Template Refs
      resetForm,
    }
  },
}
</script>

<style lang="scss">
@import '@resources/sass/preset/pages/auth.scss';
</style>

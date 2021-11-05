<template>
  <div class="auth-wrapper auth-v2">
    <div class="auth-inner">
      <!-- brand logo -->
      <router-link to="/" class="brand-logo d-flex align-center">
        <v-img :src="appLogo" max-height="30px" max-width="30px" alt="logo" contain
               class="me-3"></v-img>

        <h2 class="text--primary">
          {{ appName }}
        </h2>
      </router-link>
      <!--/ brand logo -->

      <v-row class="auth-row ma-0">
        <v-col lg="8" class="d-none d-lg-block position-relative overflow-hidden pa-0">
          <div class="auth-illustrator-wrapper">
            <!-- triangle bg -->
            <img
              height="362"
              class="auth-mask-bg"
              :src="require(`@/assets/images/misc/mask-v2-${$vuetify.theme.dark ? 'dark' : 'light'}.png`).default"
            />

            <!-- tree -->
            <v-img
              height="226"
              width="300"
              class="auth-tree"
              :src="require('@/assets/images/misc/tree-2.png').default"
            ></v-img>

            <!-- 3d character -->
            <div class="d-flex align-center h-full pa-16 pe-0">
              <v-img
                contain
                max-width="100%"
                height="710"
                class="auth-3d-group"
                :src="
                  require(`@/assets/images/3d-characters/illustration-register-v2-${
                    $vuetify.theme.dark ? 'dark' : 'light'
                  }.png`).default
                "
              ></v-img>
            </div>
          </div>
        </v-col>

        <v-col lg="4" class="d-flex align-center auth-bg pa-10 pb-0">
          <v-row>
            <v-col cols="12" sm="8" md="6" lg="12" class="mx-auto">
              <v-card flat>
                <v-card-text>
                  <p class="text-2xl font-weight-semibold text--primary my-2">Adventure starts here
                    🚀</p>
                  <p class="mb-2">Make your app management easy and fun!</p>
                </v-card-text>

                <!-- login form -->
                <v-card-text>
                  <v-form ref="registerForm" @submit.prevent="handleFormSubmit">
                    <v-text-field
                      v-model="username"
                      outlined
                      label="Username"
                      :error-messages="errorMessages.username"
                      :rules="[validators.required, validators.alphaValidator]"
                      placeholder="Username"
                      hide-details="auto"
                      class="mb-6"
                    ></v-text-field>

                    <v-text-field
                      v-model="email"
                      outlined
                      :error-messages="errorMessages.email"
                      :rules="[validators.required, validators.emailValidator]"
                      label="Email"
                      placeholder="Email"
                      hide-details="auto"
                      class="mb-6"
                    ></v-text-field>

                    <v-text-field
                      v-model="password"
                      outlined
                      :type="isPasswordVisible ? 'text' : 'password'"
                      label="Password"
                      :error-messages="errorMessages.password"
                      placeholder="Password"
                      :rules="[validators.required, validators.passwordValidator]"
                      :append-icon="isPasswordVisible ? icons.mdiEyeOffOutline : icons.mdiEyeOutline"
                      hide-details="auto"
                      class="mb-2"
                      @click:append="isPasswordVisible = !isPasswordVisible"
                    ></v-text-field>

                    <v-text-field
                      v-model="c_password"
                      outlined
                      :type="isCPasswordVisible ? 'text' : 'password'"
                      label="Password"
                      :error-messages="errorMessages.c_password"
                      placeholder="Password"
                      :rules="[validators.required, validators.confirmedValidator(c_password,password)]"
                      :append-icon="isCPasswordVisible ? icons.mdiEyeOffOutline : icons.mdiEyeOutline"
                      hide-details="auto"
                      class="mb-2"
                      @click:append="isCPasswordVisible = !isCPasswordVisible"
                    ></v-text-field>

                    <v-checkbox hide-details v-model="agreeTerms" :rules="[validators.confirmedValidator(agreeTerms, true)]" class="mt-0">
                      <template #label>
                        <div class="d-flex align-center flex-wrap">
                          <span class="me-2">I agree to</span
                          ><a href="javascript:void(0)">privacy policy &amp; terms</a>
                        </div>
                      </template>
                    </v-checkbox>

                    <v-btn block color="primary" type="submit" class="mt-6"> Sign Up</v-btn>
                  </v-form>
                </v-card-text>

                <!-- create new account  -->
                <v-card-text class="d-flex align-center justify-center flex-wrap mt-2">
                  <p class="mb-0 me-2">Already have an account?</p>
                  <router-link :to="{ name: 'auth-login' }"> Sign in instead</router-link>
                </v-card-text>

              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
  // eslint-disable-next-line object-curly-newline
  import {
    mdiFacebook,
    mdiTwitter,
    mdiGithub,
    mdiGoogle,
    mdiEyeOutline,
    mdiEyeOffOutline,
  } from '@mdi/js'
  // eslint-disable-next-line object-curly-newline
  import {
    required,
    emailValidator,
    passwordValidator,
    confirmedValidator,
    alphaValidator,
  } from '@core/utils/validation'
  import { ref, getCurrentInstance } from '@vue/composition-api'
  import axios from '@axios'
  import store from '@/store'
  import { useRouter, keysToCamel, roleToAbility } from '@core/utils'
  import themeConfig from '@themeConfig'

  export default {
    setup() {
      // Template Refs
      const registerForm = ref(null)

      const vm = getCurrentInstance().proxy
      const { router } = useRouter()

      const isPasswordVisible = ref(false)
      const isCPasswordVisible = ref(false)
      const username = ref('')
      const email = ref('')
      const password = ref('')
      const c_password = ref('')
      const agreeTerms = ref(true)
      const errorMessages = ref([])
      const socialLink = [
        {
          icon: mdiFacebook,
          color: '#4267b2',
          colorInDark: '#4267b2',
        },
        {
          icon: mdiTwitter,
          color: '#1da1f2',
          colorInDark: '#1da1f2',
        },
        {
          icon: mdiGithub,
          color: '#272727',
          colorInDark: '#fff',
        },
        {
          icon: mdiGoogle,
          color: '#db4437',
          colorInDark: '#db4437',
        },
      ]

      const handleFormSubmit = () => {
        const isFormValid = registerForm.value.validate()

        if (!isFormValid) return

        axios
          .post('/api/auth/register', {
            username: username.value,
            email: email.value,
            password: password.value,
            c_password: password.value,
          })
          .then(response => {

            const { accessToken } = response.data

            // ? Set access token in localStorage so axios interceptor can use it
            // Axios Interceptors: https://github.com/axios/axios#interceptors
            localStorage.setItem('accessToken', accessToken)

            return response
          })
          .then(() => {
            axios.get('/api/auth/user').then(response => {

              const userData = keysToCamel(response.data.userData)
              const { role } = userData

              const userAbility = roleToAbility(role.id)

              // Set user ability
              // ? https://casl.js.org/v5/en/guide/intro#update-rules
              vm.$ability.update(userAbility)

              store.commit('auth/setAuthenticated', true)
              store.commit('auth/setUserAbility', userAbility)
              store.commit('auth/setUserData', userData)

              // ? On success redirect to home
              router.push('/')
            })
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            console.error('Oops, Unable to Register!')

            store.commit('auth/setAuthenticated', false)
            store.commit('auth/setUserAbility', null)
            store.commit('auth/setUserData', null)

            let errors = error.response.data.errors
            if (errors.email) {
              if (errors.email.includes('validation.unique')) {
                error.response.data.errors.email = 'The Email already exists'
                errorMessages.value = error.response.data.errors
              }
            }
          })
      }

      return {
        isPasswordVisible,
        isCPasswordVisible,
        username,
        email,
        password,
        c_password,
        agreeTerms,
        errorMessages,
        handleFormSubmit,
        socialLink,
        icons: {
          mdiEyeOutline,
          mdiEyeOffOutline,
        },
        validators: {
          required,
          emailValidator,
          passwordValidator,
          confirmedValidator,
          alphaValidator,
        },

        // themeConfig
        appName: themeConfig.app.name,
        appLogo: themeConfig.app.logo,

        // Template Refs
        registerForm,
      }
    },
  }
</script>

<style lang="scss" scoped>
  @import '@resources/sass/preset/pages/auth.scss';
</style>

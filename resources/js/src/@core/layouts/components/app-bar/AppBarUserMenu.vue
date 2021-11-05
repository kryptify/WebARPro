<template>
  <v-menu offset-y left nudge-bottom="14" min-width="230" content-class="user-profile-menu-content"
          v-if="userData">
    <template v-slot:activator="{ on, attrs }">
      <v-badge bottom color="success" overlap offset-x="12" offset-y="12" class="ms-4" dot>
        <v-avatar size="40px" v-bind="attrs" color="primary" class="v-avatar-light-bg primary--text"
                  v-on="on">
          <v-img v-if="userData.avatar" :src="userData.avatar"></v-img>
          <v-icon v-else color="primary" size="28">
            {{ icons.mdiAccountOutline }}
          </v-icon>
        </v-avatar>
      </v-badge>
    </template>
    <v-list>
      <div class="pb-3 pt-2">
        <v-badge bottom color="success" overlap offset-x="12" offset-y="12" class="ms-4" dot>
          <v-avatar size="40px" color="primary" class="v-avatar-light-bg primary--text">
            <v-img v-if="userData.avatar"
                   :src="require('@/assets/images/avatars/avatar-1.png').default"></v-img>
            <v-icon v-else color="primary" size="28">
              {{ icons.mdiAccountOutline }}
            </v-icon>
          </v-avatar>
        </v-badge>
        <div class="d-inline-flex flex-column justify-center ms-3" style="vertical-align: middle">
          <span class="text--primary font-weight-semibold mb-n1">
            {{ userData.username }}
          </span>
          <small class="text--disabled text-capitalize">{{ userData.username }}</small>
        </div>
      </div>

      <v-divider></v-divider>

      <!-- Profile -->
      <v-list-item :to="{ name: 'profile', params: { id: 21 } }">
        <v-list-item-icon class="me-2">
          <v-icon size="22">
            {{ icons.mdiAccountOutline }}
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>My Profile</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <v-divider class="my-2"></v-divider>

      <!-- Features -->
      <v-list-item :to="{ name: 'features' }">
        <v-list-item-icon class="me-2">
          <v-icon size="22">
            {{ icons.mdiFeatureSearch }}
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Features</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <!-- Pricing -->
      <v-list-item :to="{ name: 'pricing-plans' }">
        <v-list-item-icon class="me-2">
          <v-icon size="22">
            {{ icons.mdiCurrencyUsd }}
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Pricing</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <!-- Help -->
      <v-list-item :to="{ name: 'faq' }">
        <v-list-item-icon class="me-2">
          <v-icon size="22">
            {{ icons.mdiHelpCircleOutline }}
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Help</v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <v-divider class="my-2"></v-divider>

      <!-- Logout -->
      <v-list-item @click="logoutUser">
        <v-list-item-icon class="me-2">
          <v-icon size="22">
            {{ icons.mdiLogoutVariant }}
          </v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Logout</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
  <v-btn v-else depressed class="ml-3" color="primary" to="/login">Sign in</v-btn>
</template>

<script>
  import {
    mdiAccountOutline,
    mdiEmailOutline,
    mdiCheckboxMarkedOutline,
    mdiChatOutline,
    mdiFeatureSearch,
    mdiCurrencyUsd,
    mdiHelpCircleOutline,
    mdiLogoutVariant,
  } from '@mdi/js'
  import { isEmpty, useRouter } from '@core/utils'
  import { initialAbility } from '@/plugins/acl/config'
  import { getCurrentInstance, ref } from '@vue/composition-api'
  import axios from '@axios'
  import store from '@/store'

  export default {
    setup() {
      const vm = getCurrentInstance().proxy
      const { router } = useRouter()
      const userData = store.state.auth.userData
      const errorMessages = ref([])

      const logoutUser = () => {

        axios
          .get('/api/auth/logout')
          .then(response => {
            console.log('Successfully logout!')
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            console.error('Oops, Unable to Logout!')
            console.log('Force log out')
            errorMessages.value = error.response.data.errors
          })
          .finally(() => {

            store.commit('auth/setAuthenticated', false)
            store.commit('auth/setUserAbility', null)
            store.commit('auth/setUserData', null)

            // Reset ability
            vm.$ability.update(initialAbility)

            // Redirect to login page
            router.push({ name: 'auth-login' })
          })
      }

      return {
        logoutUser,
        isEmpty,
        userData,
        errorMessages,
        icons: {
          mdiAccountOutline,
          mdiEmailOutline,
          mdiCheckboxMarkedOutline,
          mdiChatOutline,
          mdiFeatureSearch,
          mdiCurrencyUsd,
          mdiHelpCircleOutline,
          mdiLogoutVariant,
        },
      }
    },
  }
</script>

<style lang="scss">
  .user-profile-menu-content {
    .v-list-item {
      min-height: 2.5rem !important;
    }
  }
</style>

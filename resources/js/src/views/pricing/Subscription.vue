<template>
  <div>
    <v-row class="justify-end my-2">
      <v-btn
        text
        large
        color="primary"
        v-show="paymentOptionStatus === 'paypal'"
        @click="paymentOptionStatus = 'stripe'"
      >
        <v-avatar
          size="40"
          rounded
          color="primary"
          :class="`v-avatar-light-bg primary--text me-3`"
        >
          <v-img max-height="30" max-width="30" contain :src="require('@/assets/images/logos/stripe-account.png').default"></v-img>
        </v-avatar>
        <span>Use Stripe instead</span>
      </v-btn>
      <v-btn
        text
        large
        color="warning"
        v-show="paymentOptionStatus === 'stripe'"
        @click="paymentOptionStatus = 'paypal'"
        disabled
      >
        <v-avatar
          size="40"
          rounded
          color="warning"
          :class="`v-avatar-light-bg warning--text me-3`"
        >
          <v-img max-height="30" max-width="30" contain :src="require('@/assets/images/svg/paypal.svg').default"></v-img>
        </v-avatar>
        <span>Use PayPal instead</span>
      </v-btn>
    </v-row>
    <!-- Stripe -->
    <v-row class="match-height" v-show="paymentOptionStatus === 'stripe'">
      <v-col cols="12" sm="6">
        <v-card outlined>
          <v-card-title>Manage Your Stripe Subscription</v-card-title>
          <v-card-text :class="{ 'error-payment': !paymentMethodSelected && !initUnselected }">
            <div class="payment-details">
              <p class="font-weight-semibold primary--text">Payment details</p>
              <v-progress-linear
                v-if="paymentMethodsLoadStatus !== 2"
                indeterminate
                color="secondary"
                rounded
                height="10"
              ></v-progress-linear>
              <div v-show="paymentMethodsLoadStatus === 2 && paymentMethods.length === 0">
                <v-alert
                  dense
                  text
                  color="warning"
                >
                  No payment method on file, please add a payment method.
                </v-alert>
              </div>
              <div v-show="paymentMethodsLoadStatus === 2 && paymentMethods.length > 0">
                <div v-for="(method, key) in paymentMethods"
                     v-bind:key="'method-'+key"
                     v-on:click="paymentMethodSelected === method.id ? paymentMethodSelected = null : paymentMethodSelected = method.id"
                     class="payment-card d-flex align-center flex-wrap px-2 cursor-pointer rounded-lg"
                     v-bind:class="{'bg-gradient-primary active-payment-card': paymentMethodSelected === method.id}">
                  <v-avatar rounded>
                    <v-img width="42" height="30"
                           :src="require('@/assets/images/logos/credit-card2.jpg').default"></v-img>
                  </v-avatar>

                  <div class="mx-3">
                    <p class="font-weight-medium text--primary mb-0">
                      {{ method.brand.charAt(0).toUpperCase() }}{{ method.brand.slice(1) }}
                    </p>
                    <span class="text-xs">
                      Ending In: {{ method.last_four }} Exp: {{ method.exp_month }} / {{method.exp_year }}
                    </span>
                  </div>

                  <v-spacer></v-spacer>

                  <span
                    class="text-decoration-underline"
                    @click.stop="removePaymentMethod( method.id )"
                  >Remove</span>

                </div>
              </div>

            </div>
          </v-card-text>
          <v-card-text>
            <p class="font-weight-semibold primary--text mt-2">Add Payment method</p>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  id="card-holder-name"
                  label="Name"
                  v-model="paymentMethod"
                  dense
                  value=""
                  placeholder="Type your payment method name"
                  outlined
                  class="mb-3"
                ></v-text-field>
                <div id="card-element">
                  <v-progress-linear
                    indeterminate
                    color="primary"
                  ></v-progress-linear>
                </div>
                <div v-show="!isEmpty(errorMessages)">
                  <v-alert
                    dense
                    text
                    color="error"
                    class="mt-2 mb-0"
                  >
                    {{errorMessages}}
                  </v-alert>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-btn
              id="add-card-button"
              color="primary"
              block
              class="mt-2"
              @click="submitPaymentMethod"
              :loading="processingCard"
            >
              Save Payment Method
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6">
        <v-card outlined>
          <v-card-title class="text-capitalize">Subscribe to {{ selectedPlan.title }} {{
            selectedPlan.interval }}
          </v-card-title>
          <v-card-text>
            <p class="text-sm mb-0">
              Please make the payment to start enjoying all the features of our {{ selectedPlan.title
              }} plan as soon as possible.
            </p>

            <div class="plan-banner d-flex align-center flex-wrap">
              <v-avatar rounded class="banner-avatar" size="43">
                <v-img contain width="23" height="20"
                       :src="require('@/assets/images/pages/briefcase.png').default"></v-img>
              </v-avatar>

              <div class="font-weight-medium ms-3">
                <p class="text--primary text-capitalize mb-0">{{ selectedPlan.title }}</p>
                <span class="text-xs primary--text">Upgrade Plan</span>
              </div>

              <v-spacer></v-spacer>

              <div class="d-flex align-center mt-2">
                <sup>
                  <span class="text-xs font-weight-medium text--primary">$</span>
                </sup>
                <sup><h1 class="text-3xl">{{ selectedPlan.price }}</h1></sup>
                <sub>/{{ selectedPlan.interval }}</sub>
              </div>
            </div>
            <div class="float-end">
              <v-btn
                text
                color="info"
                to="/pricing/plans"
              >
                <span class="me-1">More plans</span>
                <v-icon size="15">
                  {{ icons.mdiArrowRight }}
                </v-icon>
              </v-btn>
            </div>
            <p class="font-weight-semibold primary--text text-capitalize mt-2 mb-0">{{
              selectedPlan.title }} plan benefits</p>
            <v-list dense>
              <v-list-item v-for="benefit in selectedPlanBenefits"
                           :key="benefit.id" class="px-0">
                <v-icon size="20" color="primary">
                  {{ icons.mdiCheckCircle }}
                </v-icon>
                <span class="ms-2">{{ benefit.desc }}</span>
              </v-list-item>
            </v-list>
          </v-card-text>
          <v-card-actions>
            <v-btn block color="success" @click="updateSubscription" :loading="processingSubscription">
              Subscribe Now
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <!-- PayPal -->
    <v-row v-show="paymentOptionStatus === 'paypal'">
      <v-col cols="12">
        <v-card class="py-3">
          <v-card-title class="text-capitalize justify-center">Subscribe to {{ selectedPlan.title }} {{
            selectedPlan.interval }}
          </v-card-title>
          <v-card-text class="text-center" >
            <p class="text-lg">
              Please make the payment to start enjoying all the features of our {{ selectedPlan.title }} plan as soon as possible.
            </p>
            <v-row class="py-sm-15 py-10">
              <v-col sm="6" class="mx-auto">
                <div class="plan-price d-flex align-center justify-center">
                  <sup class="text-sm text-primary">$</sup>
                  <span class="pricing-free-value text-5xl warning--text font-weight-semibold">
                        {{ selectedPlan.price }}
                      </span>
                  <sub class="pricing-duration text-sm mb-n3">/{{ selectedPlan.interval }}</sub>
                </div>
                <p>{{ selectedPlan.subtitle }}</p>
              </v-col>
              <v-col sm="6" class="mx-auto">
                <div id="payment-card">
                  <v-btn outlined x-large color="warning" class="py-15">
                    PayPal
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions class="justify-end">
            <v-btn
              text
              color="info"
              to="/pricing/plans"
            >
              <span class="me-1">More plans</span>
              <v-icon size="15">
                {{ icons.mdiArrowRight }}
              </v-icon>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <!--confirm delete-->
    <confirm-dialog ref="confirm"></confirm-dialog>
  </div>
</template>

<script>
  import { ref, onMounted, watch } from '@vue/composition-api'
  import axios from '@axios'
  import store from '@/store'
  import { isEmpty } from '@core/utils'
  import { mdiArrowRight, mdiCheckCircle, mdiDotsVertical, mdiMenuDown } from '@mdi/js'
  import UpgradePlanCard from './UpgradePlanCard'
  import ConfirmDialog from '@/components/dialog/ConfirmDialog'
  import { getVueNotification } from '@core/utils'

  export default {
    props: {
      plan: {
        type: Object,
        required: true,
      },
      planBenefits: {
        type: Array,
        required: true,
      },
      paymentOption: {
        type:String,
        required: true
      }
    },
    components: {
      UpgradePlanCard,
      ConfirmDialog,
    },
    beforeRouteEnter: (to, from, next) => {
      if (isEmpty(to.params.plan)) next('/pricing/plans')
      next()
    },
    setup(props) {
      const { plan, planBenefits } = props

      var userData = store.state.auth.userData

      // Stripe setup
      const stripeAPIToken = process.env.MIX_STRIPE_KEY
      const paymentMethod = ref('')
      const errorMessages = ref([])
      const stripe = ref('')
      const elements = ref('')
      const card = ref('')
      const intentToken = ref('')
      const addPaymentStatus = ref(0)
      const addPaymentStatusError = ref('')
      const paymentMethods = ref([])
      const paymentMethodsLoadStatus = ref(0)
      const paymentMethodSelected = ref(null)
      const initUnselected = ref(true)
      const confirm = ref(null)
      const processingSubscription = ref(false)
      const processingCard = ref(false)
      const paymentOptionStatus = ref(props.paymentOption)

      const selectedPlan = ref(plan)
      const selectedPlanBenefits = ref(planBenefits)
      const $notify = getVueNotification()

      const includeStripe = (URL, callback) => {
        /*
            Includes Stripe.js dynamically
         */
        let documentTag = document, tag = 'script',
          object = documentTag.createElement(tag),
          scriptTag = documentTag.getElementsByTagName(tag)[0]
        object.src = '//' + URL
        if (callback) {
          object.addEventListener('load', function(e) {
            callback(null, e)
          }, false)
        }
        scriptTag.parentNode.insertBefore(object, scriptTag)
      }

      /*
          Configures Stripe by setting up the elements and
          creating the card element.
      */
      const configureStripe = () => {
        stripe.value = Stripe(stripeAPIToken)

        const appearance = {
          theme: 'stripe',
          variables: {
            colorText: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
          },
        }

        var style = {
          base: {
            color: '#32325d',
            lineHeight: '24px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4',
            },
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a',
          },
        }

        elements.value = stripe.value.elements({ appearance })
        card.value = elements.value.create('card', { style })

        card.value.mount('#card-element')
      }

      /*
          Loads the payment intent key for the user to pay.
      */
      const loadIntent = () => {
        axios.get('/api/user/v1/setup-intent')
          .then(response => {
            intentToken.value = response.data
          })
      }

      const submitPaymentMethod = () => {
        errorMessages.value = 'Payment method name is required'
        if (isEmpty(paymentMethod.value)) return

        addPaymentStatus.value = 1
        processingCard.value = true

        stripe.value.confirmCardSetup(
          intentToken.value.client_secret, {
            payment_method: {
              card: card.value,
              billing_details: {
                name: paymentMethod.value,
              },
            },
          },
        ).then(function(result) {
          if (result.error) {
            errorMessages.value = result.error.message
            processingCard.value = false
            $notify({
              group: 'user',
              type: 'error',
              text: 'Save payment method failed!',
            })
            addPaymentStatus.value = 3
            addPaymentStatusError.value = result.error.message
          } else {
            savePaymentMethod(result.setupIntent.payment_method)
            processingCard.value = false
            addPaymentStatus.value = 2
            card.value.clear()
            paymentMethod.value = ''
            errorMessages.value = ''
            $notify({
              group: 'user',
              type: 'success',
              text: 'A new payment method added!',
            })
          }
        })
      }

      /*
          Saves the payment method for the user and
          re-loads the payment methods.
      */
      const savePaymentMethod = method => {
        axios.post('/api/user/v1/payments', {
          payment_method: method,
        }).then(() => {
          $notify({
            group: 'user',
            type: 'success',
            text: 'Payment method added!',
          })
          loadPaymentMethods()
        })
      }

      /*
          Loads all of the payment methods for the
          user.
      */
      const loadPaymentMethods = () => {
        paymentMethodsLoadStatus.value = 1

        axios.get('/api/user/v1/payment-methods')
          .then(response => {
            paymentMethods.value = response.data

            paymentMethodsLoadStatus.value = 2
          })
      }

      const removePaymentMethod = async paymentID => {
        if (
          await confirm.value.open(
            'Confirm',
            'Are you sure to remove this payment method?',
          )
        ) {
          axios.post('/api/user/v1/remove-payment', {
            id: paymentID,
          }).then((response) => {
            loadPaymentMethods()
            paymentMethodSelected.value = false
          })
        }
      }

      const updateSubscription = () => {
        if (!paymentMethodSelected.value) {
          $notify({
            group: 'user',
            type: 'error',
            text: 'Select your payment method!',
          })
          initUnselected.value = false
          return
        }

        processingSubscription.value = true

        axios.put('/api/user/v1/subscription', {
          plan: plan.stripeId,
          payment: paymentMethodSelected.value,
        }).then(response => {

          if (response.data.subscription_updated) {
            processingSubscription.value = false
            $notify({
              group: 'user',
              type: 'success',
              text: `Your Subscription Update to ${selectedPlan.value.title.toUpperCase()} ${selectedPlan.value.interval}!`,
            })

            store.commit('auth/setUserData', response.data.userData)

          }
        })
      }

      const configurePayPal = () => {
        paypal.Button.render(
          {
            env: 'sandbox', // Optional: specify 'sandbox' environment
            client: {
              sandbox: 'xxxx',
              production: 'xxxx',
            },
            locale: 'en_US',
            style: {
              size: 'large',
              color: 'gold',
              shape: 'pill',
              label: 'checkout',
              tagline: 'true',
            },
            commit: true, // Optional: show a 'Pay Now' button in the checkout flow
            payment: function(resolve, reject) {
              // Set up the payment here, when the buyer clicks on the button

              let returnUrl = '_YOUR_RETURN_URL'
              let amount = 20

              // Here call your own API server
              return new Promise((resolve, reject) => {
                axios.post('/checkout-paypal', {
                  return_url: returnUrl,
                  amount: amount,
                }, {
                  headers: {
                    'Authorization': 'Bearer ' +
                      state.token,
                  },
                })
                  .then(res => {
                    resolve(res.data.id)
                  })
                  .catch(error => {
                    reject(error)
                  })
              })
            },
            onAuthorize: function(data) {
              // Execute the payment here, when the buyer approves the transaction

              return new Promise((resolve, reject) => {
                axios.post('/execute-paypal',  {
                  payer_id: data.payerID,
                  payment_id: data.paymentID,
                }, {
                  headers: { 'Authorization': 'Bearer ' +
                      state.token }
                })
                  .then(res => {
                    resolve(res)
                  })
                  .catch(error => {
                    reject(error)
                  })
              })

            }
          }, '#paypal-button');
      }

      onMounted(() => {
        includeStripe('js.stripe.com/v3/', () => {
          configureStripe()
        })
        loadIntent()
        loadPaymentMethods()

        // configurePayPal()
      })

      watch(paymentMethodSelected, (val) => {
        if (!val)
          $notify({
          group: 'user',
          type: 'error',
          text: 'Select your payment method!',
        })
      })

      return {
        paymentMethod,
        errorMessages,
        stripe,
        elements,
        card,
        stripeAPIToken,
        intentToken,
        addPaymentStatus,
        addPaymentStatusError,
        submitPaymentMethod,
        removePaymentMethod,
        updateSubscription,
        paymentMethods,
        paymentMethodsLoadStatus,
        paymentMethodSelected,
        initUnselected,
        selectedPlan,
        selectedPlanBenefits,
        paymentOptionStatus,
        isEmpty,
        confirm,
        processingSubscription,
        processingCard,
        icons: {
          mdiCheckCircle,
          mdiDotsVertical,
          mdiArrowRight,
          mdiMenuDown,
        },
      }
    },
  }
</script>

<style lang="scss" scoped>
  /* Padding for Stripe Element containers */
  .StripeElement {
    border-radius: 5px;
    width: 100%;
    padding: 8px;
    box-shadow: 0 0 0 1px rgba(94, 86, 105, 0.3);
  }

  .StripeElement--focus {
    outline: 0;
    box-shadow: 0 0 0 2px rgba(145, 85, 253, 1);
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
  }

  .StripeElement--invalid {
    outline: 0;
    box-shadow: 0 0 0 2px rgba(255, 76, 81, 1);
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
  }

  .plan-banner {
    background-color: rgba(145, 85, 253, 0.12);
    padding: 0.563rem 0.688rem;
    border-radius: 4px;
    margin: 0.875rem 0 1.125rem 0;

    .banner-avatar {
      border: solid 1px var(--v-primary-base);
      border-radius: 4px;
    }

    sup {
      h1 {
        margin-top: 0.5rem;
      }
    }
  }

  .payment-details {
    .cvv {
      max-width: 3.5rem;
    }
  }

  //payment card
  .payment-card {
    .chat-meta {
      .v-badge__badge {
        margin-top: 2px;
      }
    }

    &:not(:last-child) {
      margin-bottom: 6px;
    }

    &.active-payment-card {
      p, span {
        color: #fff !important;
      }
    }
  }

  .error-payment {
    border: 1px solid red;
    border-radius: 10px;
    animation: blink 1s;
    animation-iteration-count: 5;
  }

  @keyframes blink {
    50% {
      border-color: #fff;
    }
  }
</style>


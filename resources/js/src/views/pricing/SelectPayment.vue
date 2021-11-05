<template>
  <div class="select-payment py-10">
    <v-card class="text-center py-10">
      <v-card-title class="text-capitalize justify-center mb-4">
        Select your payment method
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col offset-sm="2" sm="8" md="12" offset-lg="2" lg="10" class="mx-auto">
            <v-row >
              <v-col cols="12" md="6">
                <v-card outlined class="py-15 payment-card" @click="selectStripe">
                  <v-card-text class="text-4xl primary--text">
                    Stripe
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" md="6">
                <v-card outlined class="py-15 payment-card" @click="selectPayPal">
                  <v-card-text class="text-4xl warning--text">
                    PayPal
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
  import { isEmpty, useRouter } from '@core/utils'

  export default {
    name: 'SelectPayment',
    props: {
      plan: {
        type: Object,
        required: true,
      },
      planBenefits: {
        type: Array,
        required: true,
      },
    },
    beforeRouteEnter: (to, from, next) => {
      if (isEmpty(to.params.plan)) next('/pricing/plans')
      next()
    },
    setup(props) {

      const { router } = useRouter()

      const selectStripe = () => {
        router.push({
          name: 'pricing-subscription',
          params: { plan: props.plan, planBenefits: props.planBenefits, paymentOption: 'stripe' }
        })
      }
      const selectPayPal = () => {
        // router.push({
        //   name: 'pricing-subscription',
        //   params: { plan: props.plan, planBenefits: props.planBenefits, paymentOption: 'paypal' }
        // })
        router.push({name: 'coming-soon'})
      }

      return {
        selectStripe,
        selectPayPal
      }
    }
  }
</script>

<style scoped>
  .payment-card:hover, .payment-card:focus {
    background: #f6f4f947 !important
  }
</style>

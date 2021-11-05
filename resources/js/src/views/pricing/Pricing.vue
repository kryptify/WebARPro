<template>
  <v-card id="pricing-plan" class="text-center py-sm-15 py-5">
    <v-card-text>
      <v-row>
        <v-col cols="12" md="6" sm="8" class="mx-auto">
          <h1 class="font-weight-medium mb-5">Pricing Plans</h1>
          <p class="mb-3">
            With our transparent pricing you only pay for the features you need, no hidden fees
          </p>

          <!--plan switch -->
          <div class="d-flex align-center justify-center">
            <span class="font-weight-semibold">Monthly</span>
            <v-switch v-model="interval" class="mx-3"></v-switch>
            <span class="font-weight-semibold">Yearly</span>
          </div>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-text class="mt-3 pb-15">
      <v-row>
        <v-col offset-sm="2" sm="8" md="12" offset-lg="2" lg="10" class="mx-auto">
          <v-sheet
            :color="$vuetify.theme.dark ? 'darken-2' : 'lighten-4'"
            class="pa-3"
            v-show="loadingPlans"
          >
            <v-skeleton-loader
              class="mx-auto"
              type="card"
            ></v-skeleton-loader>
          </v-sheet>
          <v-row v-show="!loadingPlans" class="match-height">
            <!-- free -->
            <v-col cols="12" md="4">
              <v-card outlined class="text-center">
                <v-card-text>

                  <!-- image -->
                  <div class="d-flex justify-center">
                      <img :src="require('@/assets/images/misc/pricing-tree-1.png').default" class="mx-auto mt-3"/>
                  </div>

                  <!-- title -->
                  <h1 class="text-2xl font-weight-medium text-uppercase">
                    {{ freePlan.title }}
                  </h1>
                  <p>{{ freePlan.subtitle }}</p>

                  <!-- annual plan -->
                  <div class="annual-plan">
                    <div class="plan-price d-flex align-center justify-center">
                      <sup class="text-sm text-primary">$</sup>
                      <span class="pricing-free-value text-5xl primary--text font-weight-semibold">
                        {{ 0 }}
                      </span>
                      <sub class="pricing-duration text-sm mb-n3">{{ interval ? '/year' : '/month' }}</sub>
                    </div>
                  </div>
                  <!--/ annual plan -->
                </v-card-text>
                <v-card-text class="my-3">
                  <!-- plan button -->
                  <v-btn outlined block color="primary" to="/coming-soon" target="_blank">
                    Free Trial
                  </v-btn>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                  <div class="pb-2">
                    <div
                      v-for="benefit in freePlanBenefits"
                      :key="benefit.id"
                      class="d-flex align-start text-sm px-5 my-4 cursor-pointer text-left"
                    >
                      <v-icon size="14" class="me-2 mt-1">
                        {{ icons.mdiCheckboxBlankCircleOutline }}
                      </v-icon>
                      <span class="text-sm text--secondary">{{ benefit.desc }}</span>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <!-- plus -->
            <v-col cols="12" md="4">
              <v-card outlined :class="plusPlan.popularPlan === 1? 'text-center popular' : 'text-center'">
                <v-card-text>
                  <!-- chip -->
                  <div v-show="plusPlan.popularPlan" class="pricing-badge text-right mt-n3">
                    <v-chip small color="primary" class="v-chip-light-bg primary--text font-weight-semibold">
                      Popular
                    </v-chip>
                  </div>

                  <!-- image -->
                  <div class="d-flex justify-center">
                    <img :src="require('@/assets/images/misc/pricing-tree-2.png').default" class="mx-auto" :class="{'mb-3': !plusPlan.popularPlan}"/>
                  </div>

                  <!-- title -->
                  <h1 class="text-2xl font-weight-medium text-uppercase">
                    {{ plusPlan.title }}
                  </h1>
                  <p>{{ plusPlan.subtitle }}</p>

                  <!-- annual plan -->
                  <div class="annual-plan">
                    <div class="plan-price d-flex align-center justify-center">
                      <sup class="text-sm text-primary">$</sup>
                      <span class="pricing-free-value text-5xl primary--text font-weight-semibold">
                        {{ plusPlan.price }}
                      </span>
                      <sub class="pricing-duration text-sm mb-n3">{{ interval ? '/year' : '/month' }}</sub>
                    </div>
                    <v-chip v-if="interval" class="annual-pricing v-chip-light-bg error--text mt-2" small>
                      {{ plusYearlyDiscount }}% discounted
                    </v-chip>
                  </div>
                  <!--/ annual plan -->
                </v-card-text>
                <v-card-text class="my-3">
                  <!-- plan button -->
                  <v-btn v-if="plusPlan.id === currentPlan" outlined block color="success">
                    Your current plan
                  </v-btn>
                  <v-btn v-else :outlined="plusPlan.popularPlan === 0" block color="primary" :to="{ name: 'pricing-payment-options', params: { plan: plusPlan, planBenefits: plusPlanBenefits } }">
                    Try Free For 14 Days
                  </v-btn>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                  <div class="pb-2">
                    <div class="d-flex align-start text-sm px-5 my-4 cursor-pointer text-left">
                      <span class="text-sm text--secondary">Everything in Free</span>
                    </div>
                    <div
                      v-for="benefit in plusPlanBenefits"
                      :key="benefit.id"
                      class="d-flex align-start text-sm px-5 my-4 cursor-pointer text-left"
                    >
                      <v-icon size="14" class="me-2 mt-1">
                        {{ icons.mdiCheckboxBlankCircleOutline }}
                      </v-icon>
                      <span class="text-sm text--secondary">{{ benefit.desc }}</span>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <!-- premium -->
            <v-col cols="12" md="4">
              <v-card outlined :class="premiumPlan.popularPlan === 1? 'text-center popular' : 'text-center'">
                <v-card-text>
                  <!-- chip -->
                  <div v-show="premiumPlan.popularPlan" class="pricing-badge text-right mt-n3">
                    <v-chip small color="primary"
                            class="v-chip-light-bg primary--text font-weight-semibold">
                      Popular
                    </v-chip>
                  </div>

                  <!-- image -->
                  <div class="d-flex justify-center">
                    <img :src="require('@/assets/images/misc/pricing-tree-3.png').default" class="mx-auto" :class="{'mb-3': !premiumPlan.popularPlan}"/>
                  </div>

                  <!-- title -->
                  <h1 class="text-2xl font-weight-medium text-uppercase">
                    {{ premiumPlan.title }}
                  </h1>
                  <p>{{ premiumPlan.subtitle }}</p>

                  <!-- annual plan -->
                  <div class="annual-plan">
                    <div class="plan-price d-flex align-center justify-center">
                      <sup class="text-sm text-primary">$</sup>
                      <span class="pricing-free-value text-5xl primary--text font-weight-semibold">
                        {{ premiumPlan.price }}
                      </span>
                      <sub class="pricing-duration text-sm mb-n3">{{ interval ? '/year' : '/month' }}</sub>
                    </div>
                    <v-chip v-if="interval" class="annual-pricing v-chip-light-bg error--text mt-2" small>
                      {{ premiumYearlyDiscount }}% discounted
                    </v-chip>
                  </div>
                  <!--/ annual plan -->
                </v-card-text>
                <v-card-text class="my-3">
                  <!-- plan button -->
                  <v-btn v-if="premiumPlan.id === currentPlan" outlined block color="success">
                    Your current plan
                  </v-btn>
                  <v-btn v-else :outlined="premiumPlan.popularPlan === 0" block color="primary" :to="{ name: 'pricing-payment-options', params: { plan: premiumPlan, planBenefits: premiumPlanBenefits } }">
                    Try Free For 14 Days
                  </v-btn>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-text>
                  <div class="pb-2">
                    <div class="d-flex align-start text-sm px-5 my-4 cursor-pointer text-left">
                      <span class="text-sm text--secondary">Everything in Plus</span>
                    </div>
                    <div
                      v-for="benefit in premiumPlanBenefits"
                      :key="benefit.id"
                      class="d-flex align-start text-sm px-5 my-4 cursor-pointer text-left"
                    >
                      <v-icon size="14" class="me-2 mt-1">
                        {{ icons.mdiCheckboxBlankCircleOutline }}
                      </v-icon>
                      <span class="text-sm text--secondary">{{ benefit.desc }}</span>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-card-text>

    <!-- pricing free trial -->
    <v-card-text class="pricing-free-trial my-16">
      <v-row>
        <v-col cols="12" md="10" class="mx-auto">
          <div class="pricing-trial-content d-flex justify-space-between flex-column flex-md-row">
            <div class="text-center text-md-left mt-7">
              <p class="text-2xl font-weight-medium primary--text mb-2">
                Still not convinced? Start with a 3-day FREE trial!
              </p>
              <p class="text-base">You will get full access to with all the features for 3 days.</p>

              <v-btn color="primary" class="mt-4 mt-lg-6" to="/coming-soon" target="_blank"> Start 3-day FREE trial</v-btn>
            </div>

            <!-- images -->
            <v-img
              contain
              height="278"
              max-width="250"
              :src="require('@/assets/images/3d-characters/pose-fs-9.png').default"
              class="pricing-trial-img mx-auto"
              alt="svg img"
            />
            <!--/ images -->
          </div>
        </v-col>
      </v-row>
    </v-card-text>
    <!--/ pricing free trial -->

    <v-card-text>
      <h2 class="text-2xl font-weight-medium mb-2">Frequently Asked Questions</h2>
      <p>The 3D/AR creation & publishing platform for individuals, professionals, and
        organisations.</p>
    </v-card-text>

    <v-card-text class="pricing-accordion mt-1">
      <v-row>
        <v-col cols="12" md="8" class="mx-auto text-left">
          <v-expansion-panels v-model="pricingAccordion">
            <v-expansion-panel v-for="item in faqsList" :key="item.title">
              <v-expansion-panel-header>
                {{ item.title }}
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                {{ item.desc }}
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
  import { ref, onMounted, watch } from '@vue/composition-api'
  import { mdiCheckboxBlankCircleOutline } from '@mdi/js'
  import store from '@/store'
  import { isEmpty, roundToDecimal } from '@core/utils'
  import { getVueNotification } from '@core/utils'

  export default {
    setup() {
      const $notify = getVueNotification()

      var userData, currentPlan;
      userData = store.state.auth.userData
      currentPlan = userData && !isEmpty(userData.subscriptions)?  userData.subscriptions[0].plan.id : null

      const interval = ref(false)   // true -> yearly or false -> monthly
      const pricingAccordion = ref(0)
      const loadingPlans = ref(true)

      const freePlan = ref({})
      const plusPlan = ref({})
      const premiumPlan = ref({})
      const freePlanBenefits = ref([])
      const plusPlanBenefits = ref([])
      const premiumPlanBenefits = ref([])
      const pricingPlans = ref([])
      const plusYearlyDiscount = ref(0)
      const premiumYearlyDiscount = ref(0)

      const setPricingPlans = planData => {
        freePlan.value = planData.filter(item => !item.paymentPlan)[0]
        plusPlan.value = planData.filter(item => item.paymentPlan && item.title === 'plus' && (interval.value ? item.interval === 'yearly' : item.interval === 'monthly'))[0]
        premiumPlan.value = planData.filter(item => item.paymentPlan && item.title === 'premium' && (interval.value ? item.interval === 'yearly' : item.interval === 'monthly'))[0]
      }

      const setPricingPlanBenefits = planBenefitData => {
        freePlanBenefits.value = planBenefitData.filter(item => item.plan === 'free')
        plusPlanBenefits.value = planBenefitData.filter(item => item.plan === 'plus')
        premiumPlanBenefits.value = planBenefitData.filter(item => item.plan === 'premium')
      }

      const calcYearlyPlanDiscount = planData => {
        let plusMonthlyPlan = planData.filter(item => item.paymentPlan && item.title === 'plus' && item.interval === 'monthly')[0]
        let plusYearlyPlan = planData.filter(item => item.paymentPlan && item.title === 'plus' && item.interval === 'yearly')[0]
        let premiumMonthlyPlan = planData.filter(item => item.paymentPlan && item.title === 'premium' && item.interval === 'monthly')[0]
        let premiumYearlyPlan = planData.filter(item => item.paymentPlan && item.title === 'premium' && item.interval === 'yearly')[0]

        plusYearlyDiscount.value = roundToDecimal((plusMonthlyPlan.price * 12 - plusYearlyPlan.price) / (plusMonthlyPlan.price * 12) * 100, 1)
        premiumYearlyDiscount.value = roundToDecimal((premiumMonthlyPlan.price * 12 - premiumYearlyPlan.price) / (premiumMonthlyPlan.price * 12) * 100, 1)
      }

      const fetchPlanData = () => {
        // fetch all plans
        if (isEmpty(store.state.user.planData)) {
          store
            .dispatch('user/fetchPlanData')
            .then(planData => {
              setPricingPlans(planData)
              calcYearlyPlanDiscount(planData)
              fetchPlanBenefitData()
            })
            .catch(error => {
              // TODO: Next Update - Show notification
              $notify({
                group: 'user',
                type: 'error',
                text: 'Oops, Unable to fetch plan data!'
              })
            })
        } else {
          setPricingPlans(store.state.user.planData)
          calcYearlyPlanDiscount(store.state.user.planData)
          fetchPlanBenefitData()
        }
      }

      const fetchPlanBenefitData = () => {
        if (isEmpty(store.state.user.planBenefitData)) {
          store
            .dispatch('user/fetchPlanBenefitData')
            .then(planBenefitData => {
              setPricingPlanBenefits(planBenefitData)
              loadingPlans.value = false
            })
            .catch(error => {
              // TODO: Next Update - Show notification
              $notify({
                group: 'user',
                type: 'error',
                text: 'Oops, Unable to fetch plan benefits data!'
              })
            })
        }
        else {
          setPricingPlanBenefits(store.state.user.planBenefitData)
          loadingPlans.value = false
        }
      }

      onMounted(() => {
        fetchPlanData()
      })

      watch(
        interval,
        () => {
          setPricingPlans(store.state.user.planData)
        }
      )

      const faqsList = [
        {
          title: 'General settings',
          desc: 'Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.',
        },
        {
          title: 'Users',
          desc: 'Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.',
        },
        {
          title: 'Personal data',
          desc: 'Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.',
        },
        {
          title: 'Advanced settings',
          desc: 'Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.',
        },
      ]

      return {
        interval,
        currentPlan,
        loadingPlans,
        freePlan,
        plusPlan,
        premiumPlan,
        freePlanBenefits,
        plusPlanBenefits,
        premiumPlanBenefits,
        pricingPlans,
        faqsList,
        pricingAccordion,
        plusYearlyDiscount,
        premiumYearlyDiscount,
        icons: {
          mdiCheckboxBlankCircleOutline,
        },
      }
    },
  }
</script>

<style lang="scss">
  @import '@resources/sass/preset/mixins.scss';

  #pricing-plan {
    .popular {
      border-color: rgba(145, 85, 253, 0.3);
    }

    .pricing-free-trial {
      background-color: rgba(145, 85, 253, 0.04);
      position: relative;
      height: 14.625rem;

      .pricing-trial-img {
        position: relative;
        top: -3.5rem;
        right: 0;
      }
    }

    .pricing-accordion {
      .v-expansion-panels {
        .v-expansion-panel {
          box-shadow: none !important;
          margin-bottom: -1px;

          &::before {
            box-shadow: none;
          }
        }
      }
    }
  }

  @media (max-width: 960px) {
    #pricing-plan {
      .pricing-free-trial {
        height: auto;

        .pricing-trial-img {
          bottom: -1.2rem;
          top: unset;
        }
      }
    }
  }

  @include theme--child(pricing-accordion) using($material) {
    .v-expansion-panels {
      .v-expansion-panel {
        border: 1px solid map-deep-get($material, 'dividers');
      }
    }
  }
</style>

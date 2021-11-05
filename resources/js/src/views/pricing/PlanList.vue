<template>
  <v-card class="text-center">
    <v-card-title>
      <span>Plan & Benefits</span>
      <v-spacer></v-spacer>

      <v-btn color="primary" class="me-3" @click.stop="onEdit(null)">
        <v-icon>{{ icons.mdiPlus }}</v-icon>
        <span>Add Plan Benefit</span>
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <div>
      <div v-if="!loadingPlans">
        <v-simple-table>
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-center text-uppercase">
                Benefits
              </th>
              <th class="text-center text-uppercase">
                Free Plan
              </th>
              <th class="text-center text-uppercase">
                Plus Plan
              </th>
              <th class="text-center text-uppercase">
                Premium Plan
              </th>
              <th class="text-center text-uppercase">
                Actions
              </th>
            </tr>
            </thead>
            <tbody>
            <tr
              v-for="item in planBenefitTable"
              :key="item.id"
            >
              <td class="text-center">{{ item.desc }}</td>
              <td class="text-center cursor-pointer">
                <v-icon v-if="item.isFree" color="success">{{ icons.mdiCheckOutline }}</v-icon>
                <v-icon v-else>{{ icons.mdiCloseOutline }}</v-icon>
              </td>
              <td class="text-center cursor-pointer">
                <v-icon v-if="item.isPlus" color="success">{{ icons.mdiCheckOutline }}</v-icon>
                <v-icon v-else>{{ icons.mdiCloseOutline }}</v-icon>
              </td>
              <td class="text-center cursor-pointer">
                <v-icon v-if="item.isPro" color="success">{{ icons.mdiCheckOutline }}</v-icon>
                <v-icon v-else>{{ icons.mdiCloseOutline }}</v-icon>
              </td>
              <td class="text-center cursor-pointer">
                <v-icon
                  class="me-3"
                  color="info"
                  @click="onEdit(item)"
                >
                  {{ icons.mdiPencilOutline }}
                </v-icon>
                <v-icon
                  color="error"
                  @click="onDelete(item)"
                >
                  {{ icons.mdiDeleteOutline }}
                </v-icon>
              </td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </div>
      <div v-else>
        <v-sheet
          :color="$vuetify.theme.dark ? 'darken-2' : 'lighten-4'"
          class="pa-3"
        >
          <v-skeleton-loader
            class="mx-auto"
            type="table"
          ></v-skeleton-loader>
        </v-sheet>
      </div>
    </div>

    <!-- edit dialog -->
    <v-dialog
      v-model="isEditDialogOpen"
      max-width="700px"
    >
      <v-card class="user-edit-info pa-sm-10 pa-3">
        <v-card-title class="justify-center text-h5">
          {{ editItem.id? 'Edit' : 'Add' }} Plan Benefit
        </v-card-title>
        <v-card-text class="text-center mt-n2">
          Choose a proper plan for the plan benefit.
        </v-card-text>

        <v-form class="multi-col-validation" ref="planBenefitForm" @submit.prevent="onSave(editItem)">
        <v-card-text class="mt-5">
          <v-select
            v-model="editItem.plan"
            placeholder="Select Plan"
            :items="planBenefitOptions"
            item-text="title"
            item-value="value"
            :rules="[validators.required]"
            outlined
            dense
            hide-details
          ></v-select>
        </v-card-text>
        <v-card-text>
          <v-text-field
            v-model="editItem.desc"
            :rules="[validators.required, validators.betweenTextLengthValidator(editItem.desc, 10, 50)]"
            outlined
            label="Description"
            hide-details="auto"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="secondary"
            outlined
            @click="isEditDialogOpen = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="success"
            type="submit"
            :loading="processingPlanBenefit"
          >
            Save
          </v-btn>
        </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <!--confirm delete-->
    <confirm-dialog ref="confirm"></confirm-dialog>
  </v-card>
</template>

<script>
  import { ref, onMounted } from '@vue/composition-api'
  import { mdiCheckOutline, mdiCloseOutline, mdiPencilOutline, mdiDeleteOutline, mdiPlus } from '@mdi/js'
  import {
    required,
    betweenTextLengthValidator
  } from '@core/utils/validation'
  import ConfirmDialog from '@/components/dialog/ConfirmDialog'
  import axios from '@axios'
  import store from '@/store'
  import { keysToCamel } from '@core/utils'
  import { getVueNotification } from '@core/utils'

  export default {
    components: {
      ConfirmDialog,
    },
    setup() {
      const $notify = getVueNotification()
      const blankPlanBenefitData = {
        plan: '',
        desc: '',
      }

      const loadingPlans = ref(true)
      const planBenefitTable = ref([])
      const planBenefitForm = ref(null)
      const editItem = ref(blankPlanBenefitData)
      const confirm = ref(null)
      const isEditDialogOpen = ref(false)
      const processingPlanBenefit = ref(false)
      const errorMessages = ref([])

      const setPlanBenefitTable = planBenefitData => {
        planBenefitTable.value = planBenefitData.sort((a, b) => {
          if (a.plan < b.plan) {
            return -1
          }
          if (a.plan > b.plan) {
            return 1
          }
          return 0
        }).map(item => {
          return {
            id: item.id,
            desc: item.desc,
            plan: item.plan,
            isFree: item.plan === 'free',
            isPlus: item.plan === 'free' || item.plan === 'plus',
            isPro: item.plan === 'free' || item.plan === 'plus' || item.plan === 'premium',
          }
        })
      }

      const fetchPlanBenefitData = () => {
        loadingPlans.value = true
        store
          .dispatch('user/fetchPlanBenefitData')
          .then(planBenefitData => {
            setPlanBenefitTable(planBenefitData)
            loadingPlans.value = false
            return true
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            $notify({
              group: 'user',
              type: 'error',
              text: 'Oops, Unable to fetch planBenefits data!',
            })
            loadingPlans.value = true
            return false
          })
      }

      const onEdit = (item) => {
        if (item) {
          editItem.value = item
        } else {
          editItem.value = blankPlanBenefitData
        }
        isEditDialogOpen.value = true
      }

      const onSave = item => {
        const isFormValid = planBenefitForm.value.validate()
        if (!isFormValid) return

        processingPlanBenefit.value = true
        isEditDialogOpen.value = false
        loadingPlans.value = true
        axios
          .post('/api/admin/savePlanBenefit', {
            id: item.id,
            plan: item.plan,
            desc: item.desc,
          })
          .then(response => {
            if (response.data.success) {

              processingPlanBenefit.value = false
              loadingPlans.value = false
              editItem.value = {}

              const planBenefitData = keysToCamel(response.data.planBenefitData);
              store.commit('user/setPlanBenefitData', planBenefitData)
              setPlanBenefitTable(planBenefitData)

              editItem.value = blankPlanBenefitData
              planBenefitForm.value.reset()

              $notify({
                group: 'user',
                type: 'success',
                text: 'Plan benefit updated!',
              })
            } else {
              processingPlanBenefit.value = false
              $notify({
                group: 'user',
                type: 'error',
                text: 'Unable to save plan benefit!',
              })
            }
          })
          .catch(error => {
            // TODO: Next Update - Show notification
            processingPlanBenefit.value = false
            $notify({
              group: 'user',
              type: 'error',
              text: 'Unable to save plan benefit!',
            })
          })
      }

      const onDelete = async item => {
        if (
          await confirm.value.open(
            'Confirm',
            'Are you sure to delete this item?',
          )
        ) {
          loadingPlans.value = true
          axios
            .post('/api/admin/deletePlanBenefit', {
              id: item.id,
            }).then(response => {
            if (response.data.success) {
              loadingPlans.value = false
              let newPlanBenefitData = store.state.user.planBenefitData.filter(data => data.id !== item.id)
              store.commit('user/setPlanBenefitData', newPlanBenefitData)
              setPlanBenefitTable(newPlanBenefitData)

              $notify({
                group: 'user',
                type: 'success',
                text: 'Plan benefit item deleted!',
              })
            } else {
              loadingPlans.value = false
              $notify({
                group: 'user',
                type: 'error',
                text: response.data.message,
              })
            }
          })
            .catch(error => {
              // TODO: Next Update - Show notification
              loadingPlans.value = false
              $notify({
                group: 'user',
                type: 'error',
                text: 'Unable to delete the plan benefit item',
              })
            })
        }
      }

      const planBenefitOptions = [
        { id: 1, title: 'Free', value: 'free' },
        { id: 2, title: 'Plus', value: 'plus' },
        { id: 3, title: 'Premium', value: 'premium' },
      ]

      onMounted(async () => {
        const fetchedPlanBenefit = await fetchPlanBenefitData()
        loadingPlans.value = !(fetchedPlanBenefit)
      })

      return {
        loadingPlans,
        planBenefitTable,
        planBenefitForm,
        editItem,
        confirm,
        planBenefitOptions,
        isEditDialogOpen,
        processingPlanBenefit,
        errorMessages,
        onEdit,
        onSave,
        onDelete,
        validators: {
          required,
          betweenTextLengthValidator,
        },
        icons: {
          mdiCheckOutline,
          mdiCloseOutline,
          mdiPencilOutline,
          mdiDeleteOutline,
          mdiPlus,
        },
      }
    },
  }
</script>


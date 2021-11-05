<template>
  <v-row justify="center">
    <v-dialog
      v-model="dialog"
      persistent
      :max-width="options.width"
      @keydown.esc="cancel"
    >
      <v-card>
        <v-card-title>{{ title }}</v-card-title>
        <v-divider></v-divider>
        <v-card-text
          v-show="!!message"
          class="pa-4 black--text"
          v-html="message"
        ></v-card-text>
        <v-card-actions class="pt-3">
          <v-spacer></v-spacer>
          <v-btn
            v-if="!options.noconfirm"
            color="grey"
            outlined
            class="body-2 font-weight-bold"
            @click.native="cancel"
          >Cancel
          </v-btn
          >
          <v-btn
            color="primary"
            class="body-2 font-weight-bold"
            @click.native="agree"
          >OK
          </v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>
<script>
  import { ref } from '@vue/composition-api'

  export default {
    name: 'ConfrimDialog',
    setup() {
      const dialog = ref(false)
      const resolve = ref(null)
      const reject = ref(null)
      const message = ref(null)
      const title = ref(null)
      const options = ref({
        color: '',
        width: '500px',
        zIndex: 200,
        noconfirm: false
      })

      const open = (in_title, in_message, in_options) => {
        dialog.value = true
        title.value = in_title
        message.value = in_message
        if(in_options) options.value = in_options

        return new Promise((res, rej) => {
          resolve.value = res
          reject.value = rej
        })
      }

      const agree = () => {
        resolve.value(true)
        dialog.value = false
      }

      const cancel = () => {
        resolve.value(false)
        dialog.value = false
      }

      return {
        dialog,
        resolve,
        reject,
        message,
        title,
        options,
        open,
        agree,
        cancel,
      }
    },
  }
</script>

<style scoped>

</style>

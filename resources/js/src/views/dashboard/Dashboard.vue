<template>
  <div class="dashboard">
    <!-- seach banner  -->
    <div class="dashboard-block1">
      <p class="title text-h4 font-weight-semibold primary--text mb-8">
        Create Your Own Augmented Reality Experiences
      </p>
      <p class="text-2xl mb-8">
        Add virtual 3D models, photos or videos to the real world objects without any apps or coding experience
      </p>
      <v-btn
        color="primary"
        rounded
        to="/coming-soon"
        target="_blank"
        x-large
      >
        Free Trial
      </v-btn>
    </div>

    <div class="dashboard-block2">
      <v-row>
        <v-col lg="6" class="d-none d-lg-block position-relative overflow-hidden pa-0">
          <div class="dashboard-illustrator-wrapper">
            <!-- 3d character -->
            <div class="d-flex align-center h-full pa-4 pe-0">
              <v-img
                contain
                max-width="100%"
                height="592"
                :src="
                  require(`@/assets/images/3d-characters/illustration-john.png`).default
                "
              ></v-img>
            </div>
          </div>
        </v-col>
        <v-col lg="6" class="d-flex align-center text-center mt-8 mb-0">
          <v-row>
            <v-col cols="12" sm="8" md="6" lg="12" class="mx-auto">
              <p class="text-h4 font-weight-semibold text--primary mb-2">Welcome to WebAR Pro!</p>
              <p class="text-2xl mb-5">Please sign-in to your account and start the adventure</p>
              <v-btn
                color="primary"
                rounded
                to="/register"
                x-large
                v-if="userData == null"
              >
                Sign Up
              </v-btn>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>

    <div class="dashboard-block4">
      <v-parallax :src="imageLink.sub_main" height="450">
        <v-layout column align-center justify-center>
          <div class="text-h4 font-weight-bold primary--text mb-3 text-xs-center">What is WebAR?</div>
          <em>You can access AR experiences directly from their smartphone using the native camera and mobile web browser.</em>
          <v-btn class="primary lighten-2 mt-5" dark large to="/faq">
            Get more info
          </v-btn>
        </v-layout>
      </v-parallax>
    </div>

    <!-- Feature content -->
    <div class="dashboard-block3">
      <v-row class="kb-search-content-info match-height">
        <v-col v-for="item in cardItems" :key="item.character" md="4" sm="6" cols="12" class="kb-search-content">
          <v-card
            color="text-center cursor-pointer"
            @click.native="$router.push({ name: 'features' })"
          >
            <div class="kb-character-wrapper">
              <v-img contain :max-width="item.characterSize" class="mx-auto" :src="item.character"></v-img>
            </div>

            <!-- title -->
            <v-card-title class="justify-center pb-3">
              {{ item.title }}
            </v-card-title>
            <v-card-text>{{ item.desc }}</v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <div class="dashboard-block6">
      <v-parallax :src="imageLink.social_cover" height="380">
        <v-layout column align-center justify-center>
          <div class="headline white--text mb-3 text-xs-center">We are dropping cool news and opportunities on socials</div>
        </v-layout>
        <v-layout justify-center>

          <v-btn v-for="link in socialLink" :key="link.icon" icon class="me-3">
            <v-icon x-large dark>
              {{ link.icon }}
            </v-icon>
          </v-btn>

        </v-layout>
      </v-parallax>
    </div>

    <div class="dashboard-block7">
      <v-layout row wrap>
        <v-flex class="text-center mt-5">
          <div class="headline">Are you amazed? Stay tuned!</div>
          <br>
          <div>We are lunching the beta in a few time. If you want to be one of the first WebAR Pro users we will email you as soon as we're ready. In the beginning only few people will test before the launch. Let us know how WebAR Pro will help you!</div>
        </v-flex>
        <v-flex xs8 offset-xs2>

          <v-card class="elevation-0 transparent">

            <v-card-text>
              <v-form ref="contactForm" @submit.prevent="onContact">
                <v-flex xs12 class="mb-5" v-if="!subscribe">
                  <v-text-field label="Email address" v-model="email" :rules="[validators.required, validators.emailValidator]" hint="Enter your email!" persistent-hint></v-text-field>
                </v-flex>
                <v-flex xs12 v-if="!subscribe">
                  <v-text-field filled label="Contact us..." v-model="contact"></v-text-field>
                </v-flex>
                <v-flex xs12 class="text-sm-center" v-if="!subscribe">
                  <v-btn class="blue lighten-2 mb-5" dark large type="submit">Contact us</v-btn>
                </v-flex>
                <v-flex xs12 class="text-sm-center" v-if="subscribe">
                  <v-btn class="green lighten-2 mb-5" dark large>Welcome on board!</v-btn>
                </v-flex>
              </v-form>
            </v-card-text>
          </v-card>

        </v-flex>
      </v-layout>
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
  import { ref } from '@vue/composition-api'
  import { required, emailValidator } from '@core/utils/validation'
  import store from '@/store'
  import themeConfig from '@themeConfig'

  export default {
    setup() {
      // Template Ref
      const userData = store.state.auth.userData
      const subscribe = ref(false)
      const email = ref(userData? userData.email : '')
      const contact = ref('')
      const contactForm = ref(null)
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
      const imageLink = {
        main:
          "https://firebasestorage.googleapis.com/v0/b/endorfinevue.appspot.com/o/assets%2Fb13f0434-b228-11e6-8e5d-5252025056ab_web_scale_0.4666667_0.4666667__.jpg?alt=media&token=660df23e-599e-434b-9313-ba69c973eeea",
        sub_main:
          "https://firebasestorage.googleapis.com/v0/b/endorfinevue.appspot.com/o/assets%2FNight-Club-Clubbing-Jobs-Abroad2.jpg?alt=media&token=82bbda7d-5df4-430b-9217-adaf1c8485c5",
        logo:
          "https://firebasestorage.googleapis.com/v0/b/endorfinevue.appspot.com/o/assets%2Fandroid-chrome-512x512.png?alt=media&token=8a0a66f6-4741-4ff6-8f28-eb9ec74374df",
        social_cover:
          "https://firebasestorage.googleapis.com/v0/b/endorfinevue.appspot.com/o/assets%2Fo-NIGHTCLUB-facebook.jpg?alt=media&token=cefc5c4c-9714-41da-9c22-f63caf5e89a4"
      }
      const cardItems = [
        {
          character: require('@/assets/images/3d-characters/pose-f-3.png').default,
          category: 'sales-automation',
          characterSize: '139',
          title: 'Completely Web-Based 👩🏻‍💻',
          desc: 'There is perhaps no better demonstration of the folly of image of our tiny world.',
        },
        {
          character: require('@/assets/images/3d-characters/pose-f-28.png').default,
          category: 'marketing-automation',
          characterSize: '188',
          title: 'Easy-to-Use Editor 🚀',
          desc: 'Look again at that dot. That’s here. That’s home. That’s us. On it everyone you love.',
        },
        {
          character: require('@/assets/images/3d-characters/pose-m-34.png').default,
          category: 'api-questions',
          characterSize: '126',
          title: 'API Questions 📱',
          desc: 'Every hero and coward, every creator and destroyer of civilization.',
        },
      ]

      const onContact = () => {
        const isFormValid = contactForm.value.validate()

        if (!isFormValid) return

        subscribe.value = !subscribe.value;
      }

      return {
        userData,
        subscribe,
        email,
        contact,
        contactForm,
        errorMessages,
        socialLink,
        imageLink,
        cardItems,
        icons: {
          mdiEyeOutline,
          mdiEyeOffOutline,
        },
        validators: {
          required,
          emailValidator,
        },
        onContact,
        // themeConfig
        appName: themeConfig.app.name,
        appLogo: themeConfig.app.logo,
      }
    },
  }
</script>

<style lang="scss" scoped>
  @import '@resources/sass/preset/pages/dashboard.scss';
</style>

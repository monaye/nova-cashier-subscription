<template>
  <div>
    <heading v-if="title" class="mb-6">{{ title }}</heading>
    <loading-card :loading="loading" class="px-6 py-4">
      <div v-if="this.getCardLastFour" class="flex">
        <div class="w-1/4 py-4 max-w-sm"></div>
        <div class="w-3/4 py-4">
          {{
            __("Currently register credit card :cardNumber", {
              cardNumber: this.getCardLastFour,
            })
          }}
        </div>
      </div>
      <div class="flex">
        <div class="w-1/4 py-4">
          <h4 class="font-normal text-80">
            {{ __("Credit Card Information") }}
          </h4>
        </div>

        <div class="w-3/4 py-4 max-w-sm">
          <form action="/" method="post" @submit.prevent="onSubmitCreditCard">
            <div id="card-element">
              <!-- A Stripe Element will be inserted here. -->
            </div>
            <!-- Used to display form errors. -->
            <div
              class="mt-6 text-danger"
              v-if="errorMessage"
              id="card-errors"
              role="alert"
            >
              {{ errorMessage }}
            </div>
            <div class="mt-6 pt-6">
              <button
                dusk="submit-update-credit-card"
                type="submit"
                class="btn btn-default btn-primary"
              >
                {{ __("Update Credit Card") }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </loading-card>
  </div>
</template>


<script>
export default {
  props: ["panel"],
  data() {
    return {
      loading: true,
      style: {
        base: {
          color: "#32325d",
          lineHeight: "18px",
          fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
          fontSmoothing: "antialiased",
          fontSize: "16px",
          "::placeholder": {
            color: "#aab7c4",
          },
        },
        invalid: {
          color: "#fa755a",
          iconColor: "#fa755a",
        },
      },
      stripeInstance: null,
      stripeCard: null,
      cardLastFour: null,
      clientSecret: null,
      errorMessage: null,
    };
  },
  computed: {
    getStripeKey() {
      return this.panel.fields[0].stripe_key;
    },
    getCardLastFour() {
      return this.cardLastFour || this.panel.fields[0].card_last_four;
    },
    getClientSecret() {
      return this.clientSecret || this.panel.fields[0].client_secret;
    },
    locale() {
      return this.panel.fields[0].locale;
    },
    title() {
      return this.panel.fields[0].title;
    },
  },
  methods: {
    initializeStripeCard() {
      console.log("stripe key: ", this.getStripeKey);
      console.log("stripe locale: ", this.locale);
      this.stripeInstance = Stripe(this.getStripeKey, {
        locale: this.locale,
      });
      const stripeElements = this.stripeInstance.elements();
      this.stripeCard = stripeElements.create("card", { style: this.style });

      this.stripeCard.mount("#card-element");
      // Handle real-time validation errors from the card Element.
      this.stripeCard.addEventListener("change", function (event) {
        if (event.error) {
          this.errorMessage = event.error.message;
        } else {
          this.errorMessage = null;
        }
      });
    },
    async onSubmitCreditCard() {
      console.log("updating");
      this.errorMessage = null;
      this.loading = true;

      const { setupIntent, error } = await this.stripeInstance.confirmCardSetup(
        this.getClientSecret,
        {
          payment_method: {
            card: this.stripeCard,
          },
        }
      );

      if (error) {
        console.log("got error with setupintent", error);
        // Display "error.message" to the user...
        this.errorMessage = error.message;
        this.loading = false;
        return;
      }

      // The card has been verified successfully...
      // Send the token to your server.
      this.saveToBackend(setupIntent);
    },
    saveToBackend(setupIntent) {
      console.log(setupIntent);
      Nova.request()
        .post(`/nova-vendor/nova-cashier-subscription/update-card`, {
          payment_method: setupIntent.payment_method,
        })
        .then((response) => {
          console.log("success");
          console.log(response);
          this.loading = false;
          this.cardLastFour = response.data.card_last_four;
          this.clientSecret = response.data.client_secret;
          this.$toasted.success(response.data.message);
          this.initializeStripeCard();
          Nova.$emit(
            "nova-cashier-subscription-credit-card-changed",
            response.data.card_last_four
          );
          //   this.cardLastFour =
        })
        .catch((error) => {
          console.log("error on update card", error);
          this.loading = false;
          this.initializeStripeCard();
        });
    },
  },
  mounted() {
    this.initializeStripeCard();
    // let stripeJS = document.createElement("script");
    // stripeJS.setAttribute("src", "https://js.stripe.com/v3/");
    // document.head.appendChild(stripeJS);
    this.loading = false;
  },
};
</script>

<style>
/* Scoped Styles */
</style>

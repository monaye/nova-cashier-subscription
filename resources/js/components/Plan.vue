<template>
  <div>
    <heading v-if="title" class="mb-6">{{ title }}</heading>

    <loading-card
      :loading="loading"
      dusk="nova-subscription-plan-card"
      class="px-6 py-4"
    >
      <div v-if="this.isOnTrial" class="flex">
        <div class="w-1/4 py-4 max-w-sm"></div>
        <div class="w-3/4 py-4">
          {{
            __("You're trial will end at : :trialEndAt", {
              trialEndAt: this.trialEndAt,
            })
          }}
        </div>
      </div>
      <div v-if="this.endedButStillOnTrial" class="flex">
        <div class="w-1/4 py-4 max-w-sm"></div>
        <div class="w-3/4 py-4">
          {{
            __(
              "You have free trial until: :trialEndAt. You can re-subscribe one of the following plan free until end of the freetrial.",
              {
                trialEndAt: this.trialEndAt,
              }
            )
          }}
        </div>
      </div>
      <!-- <card class="card mb-6 py-3 px-6"> -->
      <div class="flex">
        <div class="w-1/4 py-4">
          <h4 class="font-normal text-80">{{ __("Subscription Plan") }}</h4>
        </div>
        <div class="w-3/4 py-4">
          <div
            v-for="plan in this.plans"
            :dusk="plan.value"
            class="mb-6"
            :key="plan.value"
          >
            <label class="inline-flex items-center text-80">
              <input
                type="radio"
                class="form-radio"
                name="plan"
                v-model="currentPlan"
                :value="plan.value"
              />
              <span class="ml-2">{{ plan.label }}</span>
            </label>
          </div>
          <div v-if="!getCardLastFour" class="text-danger">
            {{ __("Please add credit card information to change a plan.") }}
          </div>
          <button
            type="submit"
            dusk="submit-update-subscription-plan"
            class="btn btn-default btn-primary mt-6"
            v-bind:class="[getCardLastFour ? '' : disabledClass]"
            v-on:click="updatePlan"
          >
            {{ __("Update Plan") }}
          </button>
        </div>
      </div>
      <!-- </card> -->
    </loading-card>
  </div>
</template>

<script>
export default {
  props: ["panel"],
  data() {
    return {
      loading: false,
      disabledClass: "opacity-50 cursor-not-allowed",
      cardLastFour: null,
      subscriptionPlan: this.panel.fields[0].subscription_plan,
      currentPlan:
        this.panel.fields[0].subscription_plan &&
        !this.panel.fields[0].subscription_plan.ends_at
          ? this.panel.fields[0].subscription_plan.stripe_plan
          : "free",
    };
  },
  created() {
    Nova.$on(
      "nova-cashier-subscription-credit-card-changed",
      this.updateCardLastFour
    );
    this.currentPlan = this.originalPlan;
  },
  mounted() {
    //
  },
  methods: {
    updateCardLastFour: function (cardLastFour) {
      this.cardLastFour = cardLastFour;
    },
    updatePlan: function (event) {
      // if user selected the same as original plan
      if (this.originalPlan === this.currentPlan) {
        return false;
      }

      // if user try to update to the non-free plan but doesn't have a credit card
      if (!this.getCardLastFour && this.currentPlan !== "free") {
        return false;
      }

      this.loading = true;
      Nova.request()
        .post(`/nova-vendor/nova-cashier-subscription/change-plan`, {
          plan: this.currentPlan,
        })
        .then((response) => {
          console.log("success");
          this.loading = false;
          if (response.data.error) {
            this.$toasted.error(response.data.error);
          }
          if (response.data.message) {
            this.$toasted.success(response.data.message);
          }
          //   this.currentPlan = response.data.subscription_plan;
          this.subscriptionPlan = response.data.subscription_plan;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
  },
  computed: {
    plans() {
      return this.panel.fields[0].plans;
    },
    title() {
      return this.panel.fields[0].title;
    },
    getCardLastFour() {
      return this.cardLastFour || this.panel.fields[0].card_last_four;
    },

    // originalPlan are to track the plan before user make changes
    originalPlan() {
      if (this.subscriptionPlan && !this.subscriptionPlan.ends_at) {
        return this.subscriptionPlan.stripe_plan;
      }
      return "free";
    },
    isOnTrial() {
      return (
        this.subscriptionPlan &&
        !this.subscriptionPlan.ends_at &&
        this.subscriptionPlan.stripe_status === "trialing"
      );
    },
    endedButStillOnTrial() {
      return (
        this.subscriptionPlan &&
        this.subscriptionPlan.ends_at &&
        this.subscriptionPlan.stripe_status === "trialing"
      );
    },
    trialEndAt() {
      return new Date(this.subscriptionPlan.trial_ends_at).toLocaleDateString(
        this.panel.fields[0].locale,
        {
          dateStyle: "long",
        }
      );
    },
  },
};
</script>

<style>
/* Scoped Styles */
</style>

<template>
  <div>
    <heading class="mb-6">{{ name }}</heading>

    <loading-card :loading="loading" class="px-6 py-4">
      <!-- <card class="card mb-6 py-3 px-6"> -->
      <div class="flex">
        <div class="w-1/4 py-4">
          <h4 class="font-normal text-80">{{ planTitle }}</h4>
        </div>
        <div class="w-3/4 py-4">
          <div v-for="plan in this.plans" class="mb-6" :key="plan.value">
            <label class="inline-flex items-center text-80">
              <input
                type="radio"
                class="form-radio"
                name="plan"
                v-model="selectedPlan"
                :value="plan.value"
              />
              <span class="ml-2">{{ plan.label }}</span>
            </label>
          </div>
          <div v-if="!getCardLastFour" class="text-danger">
            Please add credit card information to change a plan.
          </div>
          <button
            type="submit"
            class="btn btn-default btn-primary mt-6"
            v-bind:class="[getCardLastFour ? '' : disabledClass]"
            v-on:click="updatePlan"
          >
            Update Plan
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
      selectedPlan: this.panel.fields[0].subscription_plan
        ? this.panel.fields[0].subscription_plan.stripe_plan
        : "free",
      cardLastFour: null,
    };
  },
  created() {
    Nova.$on(
      "nova-cashier-subscription-credit-card-changed",
      this.updateCardLastFour
    );
  },
  mounted() {
    //
  },
  methods: {
    updateCardLastFour: function (cardLastFour) {
      this.cardLastFour = cardLastFour;
    },
    updatePlan: function (event) {
      if (this.selectedPlan === this.currentPlan) {
        return false;
      }

      if (!this.getCardLastFour && this.selectedPlan !== "free") {
        return false;
      }
      this.loading = true;
      Nova.request()
        .post(`/nova-vendor/nova-cashier-subscription/change-plan`, {
          plan: this.selectedPlan,
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
    name() {
      return this.panel.name;
    },
    planTitle() {
      return this.panel.planTitle || "Plans";
    },
    getCardLastFour() {
      return this.cardLastFour || this.panel.fields[0].card_last_four;
    },
    currentPlan() {
      return this.panel.fields[0].subscription_plan
        ? this.panel.fields[0].subscription_plan.stripe_plan
        : "free";
    },
  },
};
</script>

<style>
/* Scoped Styles */
</style>

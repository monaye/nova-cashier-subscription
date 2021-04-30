Nova.booting((Vue, router, store) => {
  Vue.component('nova-cashier-plan', require('./components/Plan'))
  Vue.component('nova-cashier-credit-card', require('./components/CreditCard'))
})

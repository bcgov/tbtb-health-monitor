/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue/dist/vue';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */


Vue.component('main-component', require('./components/MainComponent.vue').default);
Vue.component('main-group-component', require('./components/MainGroupComponent.vue').default);

Vue.component('logs-component', require('./components/LogsComponent.vue').default);

Vue.component('services-component', require('./components/ServicesComponent.vue').default);
Vue.component('services-group-component', require('./components/ServicesGroupComponent.vue').default);

Vue.component('service-contacts-component', require('./components/ServiceContactsComponent.vue').default);
Vue.component('accounts-component', require('./components/AccountsComponent.vue').default);

Vue.component('add-service-modal', require('./components/AddServiceModal.vue').default);
Vue.component('show-service-modal', require('./components/ShowServiceModal.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = new Vue({
    el: '#app',
});
if (process.env.MIX_APP_ENV === 'production') {
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true;
}
document.refreshTooltips = function(){
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
}

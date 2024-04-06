require('./bootstrap');

//window.Vue = require('vue');
import Vue from 'vue'
import VueRouter from 'vue-router';
import Vuetify from 'vuetify';
import routes from './routes';
import VueProgressBar from 'vue-progressbar';
import {
    Form,
    // HasError,
    // AlertError
} from 'vform';
// import objectToFormData from 'object-to-formdata';
import Vuex from 'vuex';
import i18n from './locales/index';
import Swal from 'sweetalert2';
import Vue2Filters from 'vue2-filters'
import { ValidationProvider, extend } from 'vee-validate';
import Datepicker from 'vuejs-datepicker';
import {dEn, dFr} from 'vuejs-datepicker/dist/locale';
import Datetime from 'vue-datetime';
import { Settings } from 'luxon'
import moment from 'moment';
import 'moment/locale/fr';
import settingsModule from './modules/settings.module';
import VueHtmlToPaper from 'vue-html-to-paper';
// import Gate from './gate'
import vSelect from 'vue-select'
import excel from 'vue-excel-export'
import '@mdi/font/css/materialdesignicons.css'; // Ensure you are using css-loader
import fr from 'vuetify/es5/locale/fr';
import en from 'vuetify/es5/locale/en';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

//Vuex
Vue.use(Vuex);
const store = new Vuex.Store({
    modules:{
        settings: settingsModule
    }
});
const lang = document.head.querySelector("[name~=locale][content]").content;

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

//Vuetify
Vue.use(Vuetify);

//Excel
Vue.use(excel);

//Alerts
window.swal = Swal;

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});
window.toast = toast;

//Router
Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes
});

//Form
window.form = Form;
// window.objectToFormData = objectToFormData;
// Vue.component(HasError.name, HasError)
// Vue.component(AlertError.name, AlertError)

//ProgressBar
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '3px'
});

//Filters
Vue.use(Vue2Filters);

// //Component Communication
window.Fire = new Vue();

//Pagination
Vue.component('pagination', require('laravel-vue-pagination'));

// //Passport
// Vue.component(
//     'passport-clients',
//     require('./components/passport/Clients.vue').default
// );

//Validation
Vue.component('ValidationProvider', ValidationProvider);

//Vue-Select
Vue.component('v-select', vSelect);

//Date
Vue.component('datepicker', Datepicker);
if(lang=="fr"){
    window.dateLanguage=dFr;
}else{
    window.dateLanguage=dEn;
}

//Datetime
if(lang=="fr"){
    Settings.defaultLocale = 'fr';
}else{
    Settings.defaultLocale = 'en';
}
Vue.use(Datetime);

//Today's Date
window.todayDateTime=moment().locale(lang).format('Do MMMM YYYY HH:mm');

//Filters
Vue.filter('formatDate', function(value) {
    if (value) {
      return moment(String(value)).locale(lang).format('Do MMMM YYYY');
    }
});
Vue.filter('formatDateTime', function(value) {
    if (value) {
      return moment(String(value)).locale(lang).format('Do MMMM YYYY HH:mm');
    }
});
Vue.filter('formatDateShort', function(value) {
    if (value) {
      return moment(String(value)).locale(lang).format('DD/MM/YY');
    }
});
Vue.filter('formatDateTimeShort', function(value) {
    if (value) {
      return moment(String(value)).locale(lang).format('D/M/YY HH:mm');
    }
});

//Print
Vue.use(VueHtmlToPaper, {
    name: '_blank',
    specs: [
      'fullscreen=yes',
      'titlebar=yes',
      'scrollbars=yes'
    ],
    styles: [
        // 'http://192.168.88.133:8000/css/app.css'
        '../css/app.css'
    ]
  });

// Vue.component(
//     'passport-authorized-clients',
//     require('./components/passport/AuthorizedClients.vue').default
// );

// Vue.component(
//     'passport-personal-access-tokens',
//     require('./components/passport/PersonalAccessTokens.vue').default
// );

// Vue.component('my-component', {
// methods: {
//     changeLocale () {
//     this.$vuetify.lang.current = lang
//     },
// },
// })

//Spatie Permissions
Vue.prototype.can = function(value){
    return window.Gate.userPermissions.permissions.includes(value);
}
Vue.prototype.is = function(value){
    return window.Gate.userPermissions.roles.includes(value);
}

const app = new Vue({
    el: '#app',
    i18n,
    store,
    vuetify: new Vuetify({
        icons: {
          iconfont: 'mdi', // default - only for display purposes
        },
        lang: {
            locales: {fr, en},
            current: lang
        }
      }),
    router
});
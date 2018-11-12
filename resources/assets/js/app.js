
/* ---------- Load the bootstrapper ---------- */

require('./bootstrap');


/* ---------- Register Vue with the window object ---------- */

window.Vue = require('vue');


/* ---------- Import all sitewide components ---------- */

import bButton from 'bootstrap-vue/es/components/button/button';


/* ---------- Register all sitewide components ---------- */

Vue.component('b-button', bButton);


/* ---------- Initialize Vuex---------- */
   
import Store from './store/Store.js';


/* ---------- Initialize vue-router ---------- */

import router from './router.js';


/* ---------- Initialize Vue ---------- */

const app = new Vue({
    el: '#app',
    store: Store,
    router: router
});

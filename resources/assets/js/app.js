
/* ---------- Load the bootstrapper ---------- */

require('./bootstrap');


/* ---------- Register Vue with the window object ---------- */

window.Vue = require('vue');


/* ---------- Import all sitewide components ---------- */

import bNavbar from 'bootstrap-vue/es/components/navbar/navbar';
import bNavItem from 'bootstrap-vue/es/components/nav/nav-item';
import bNavItemDropdown from 'bootstrap-vue/es/components/nav/nav-item-dropdown';
import bNavbarToggle from 'bootstrap-vue/es/components/navbar/navbar-toggle';
import bNavbarBrand from 'bootstrap-vue/es/components/navbar/navbar-brand';
import bNarbarNav from 'bootstrap-vue/es/components/navbar/navbar-nav';
import bDropdownItem from 'bootstrap-vue/es/components/dropdown/dropdown-item';
import bButton from 'bootstrap-vue/es/components/button/button';
import bCollapse from 'bootstrap-vue/es/components/collapse/collapse';
import bBreadcrumb from 'bootstrap-vue/es/components/breadcrumb/breadcrumb';


/* ---------- Register all sitewide components ---------- */

Vue.component('b-navbar', bNavbar);
Vue.component('b-nav-item', bNavItem);
Vue.component('b-nav-item-dropdown', bNavItemDropdown);
Vue.component('b-navbar-toggle', bNavbarToggle);
Vue.component('b-navbar-brand', bNavbarBrand);
Vue.component('b-navbar-nav', bNarbarNav);
Vue.component('b-dropdown-item', bDropdownItem);
Vue.component('b-button', bButton);
Vue.component('b-collapse', bCollapse);
Vue.component('b-breadcrumb', bBreadcrumb);


/* ---------- Initialize Vuex---------- */

//require('./store.js');

import store from './store.js';

/* ---------- Initialize vue-router ---------- */

//require('./router.js');

import router from './router.js';


/* ---------- Initialize Vue ---------- */

const app = new Vue({
    el: '#app',
    store: store,
    router: router
});

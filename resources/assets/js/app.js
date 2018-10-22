
/* ---------- Load the bootstrapper ---------- */

require('./bootstrap');


/* ---------- Register Vue with the window object ---------- */

window.Vue = require('vue');


/* ---------- Register Vuex with Vue ---------- */

window.Vuex = require('vuex');

Vue.use(Vuex);


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


/* --------- Register all page components ---------- */

Vue.component('players-page', require('./components/pages/PlayersPage.vue'));

Vue.component('power-rankings-page', require('./components/pages/PowerRankingsPage.vue'));
Vue.component('score-rankings-page', require('./components/pages/ScoreRankingsPage.vue'));
Vue.component('speed-rankings-page', require('./components/pages/SpeedRankingsPage.vue'));
Vue.component('deathless-rankings-page', require('./components/pages/DeathlessRankingsPage.vue'));
Vue.component('character-rankings-page', require('./components/pages/CharacterRankingsPage.vue'));
Vue.component('daily-rankings-page', require('./components/pages/DailyRankingsPage.vue'));

Vue.component('score-leaderboards-page', require('./components/pages/ScoreLeaderboardsPage.vue'));
Vue.component('speed-leaderboards-page', require('./components/pages/SpeedLeaderboardsPage.vue'));
Vue.component('deathless-leaderboards-page', require('./components/pages/DeathlessLeaderboardsPage.vue'));

Vue.component('daily-leaderboards-page', require('./components/pages/DailyLeaderboardsPage.vue'));


/* ---------- Initialize Vue ---------- */

const store = new Vuex.Store({
    state: {
        character: {}
    },
    mutations: {
        setCharacter(state, character) {
            state.character = character;
        }
    },
    getters: {
        currentCharacter: state => {
            return state.character;
        }
    }
});

const app = new Vue({
    el: '#app',
    store: store
});

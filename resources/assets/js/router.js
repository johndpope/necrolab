/* ---------- Import and register vue-router ----------*/

import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

/* --------- Register all page components ---------- */

import HomePage from './components/pages/HomePage.vue';

import PlayersPage from './components/pages/PlayersPage.vue';

import PowerRankingsPage from './components/pages/PowerRankingsPage.vue';
import ScoreRankingsPage from './components/pages/ScoreRankingsPage.vue';
import SpeedRankingsPage from './components/pages/SpeedRankingsPage.vue';
import DeathlessRankingsPage from './components/pages/DeathlessRankingsPage.vue';
import CharacterRankingsPage from './components/pages/CharacterRankingsPage.vue';
import DailyRankingsPage from './components/pages/DailyRankingsPage.vue';

import ScoreLeaderboardsPage from './components/pages/ScoreLeaderboardsPage.vue';
import SpeedLeaderboardsPage from './components/pages/SpeedLeaderboardsPage.vue';
import DeathlessLeaderboardsPage from './components/pages/DeathlessLeaderboardsPage.vue';
import DailyLeaderboardsPage from './components/pages/DailyLeaderboardsPage.vue';

/*
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
*/


/* --------- Define routes ---------- */

const routes = [
    {
        path: '/', 
        component: HomePage 
    },
    {
        path: '/players', 
        component: PlayersPage 
    },
    {
        path: '/rankings/power',
        component: PowerRankingsPage
    },
    {
        path: '/rankings/score',
        component: ScoreRankingsPage
    },
    {
        path: '/rankings/speed',
        component: SpeedRankingsPage
    },
    {
        path: '/rankings/deathless',
        component: DeathlessRankingsPage
    },
    {
        path: '/rankings/character',
        component: CharacterRankingsPage
    },
    {
        path: '/rankings/daily',
        component: DailyRankingsPage
    },
    {
        path: '/leaderboards/score',
        component: ScoreLeaderboardsPage
    },
    {
        path: '/leaderboards/speed',
        component: SpeedLeaderboardsPage
    },
    {
        path: '/leaderboards/deathless',
        component: DeathlessLeaderboardsPage
    },
    {
        path: '/leaderboards/daily',
        component: DailyLeaderboardsPage
    }
];



/* --------- Initalize the router ---------- */

const router = new VueRouter({
    routes: routes
});

export default router;

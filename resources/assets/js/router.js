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

import PowerRankingEntriesPage from './components/pages/PowerRankingEntriesPage.vue';
import ScoreRankingEntriesPage from './components/pages/ScoreRankingEntriesPage.vue';
import SpeedRankingEntriesPage from './components/pages/SpeedRankingEntriesPage.vue';
import DeathlessRankingEntriesPage from './components/pages/DeathlessRankingEntriesPage.vue';
import CharacterRankingEntriesPage from './components/pages/CharacterRankingEntriesPage.vue';
import DailyRankingEntriesPage from './components/pages/DailyRankingEntriesPage.vue';

import ScoreLeaderboardsPage from './components/pages/ScoreLeaderboardsPage.vue';
import SpeedLeaderboardsPage from './components/pages/SpeedLeaderboardsPage.vue';
import DeathlessLeaderboardsPage from './components/pages/DeathlessLeaderboardsPage.vue';
import DailyLeaderboardsPage from './components/pages/DailyLeaderboardsPage.vue';

import ScoreLeaderboardSnapshotsPage from './components/pages/ScoreLeaderboardSnapshotsPage.vue';
import SpeedLeaderboardSnapshotsPage from './components/pages/SpeedLeaderboardSnapshotsPage.vue';
import DeathlessLeaderboardSnapshotsPage from './components/pages/DeathlessLeaderboardSnapshotsPage.vue';

import ScoreLeaderboardEntriesPage from './components/pages/ScoreLeaderboardEntriesPage.vue';
import SpeedLeaderboardEntriesPage from './components/pages/SpeedLeaderboardEntriesPage.vue';
import DeathlessLeaderboardEntriesPage from './components/pages/DeathlessLeaderboardEntriesPage.vue';
import DailyLeaderboardEntriesPage from './components/pages/DailyLeaderboardEntriesPage.vue';


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
        path: '/rankings/power/:release/:mode/:seeded_type/:date',
        component: PowerRankingEntriesPage
    },
    {
        path: '/rankings/score',
        component: ScoreRankingsPage
    },
    {
        path: '/rankings/score/:release/:mode/:seeded_type/:date',
        component: ScoreRankingEntriesPage
    },
    {
        path: '/rankings/speed',
        component: SpeedRankingsPage
    },
    {
        path: '/rankings/speed/:release/:mode/:seeded_type/:date',
        component: SpeedRankingEntriesPage
    },
    {
        path: '/rankings/deathless',
        component: DeathlessRankingsPage
    },
    {
        path: '/rankings/deathless/:release/:mode/:seeded_type/:date',
        component: DeathlessRankingEntriesPage
    },
    {
        path: '/rankings/character',
        component: CharacterRankingsPage
    },
    {
        path: '/rankings/character/:character/:release/:mode/:seeded_type/:date',
        component: CharacterRankingEntriesPage
    },
    {
        path: '/rankings/daily',
        component: DailyRankingsPage
    },
    {
        path: '/rankings/daily/:release/:number_of_days/:date',
        component: DailyRankingEntriesPage
    },
    {
        path: '/leaderboards/score',
        component: ScoreLeaderboardsPage,
    },
    {
        path: '/leaderboards/score/:url_name/snapshots',
        component: ScoreLeaderboardSnapshotsPage
    },
    {
        path: '/leaderboards/score/:url_name/snapshots/:date',
        component: ScoreLeaderboardEntriesPage
    },
    {
        path: '/leaderboards/speed',
        component: SpeedLeaderboardsPage
    },
    {
        path: '/leaderboards/speed/:url_name/snapshots',
        component: SpeedLeaderboardSnapshotsPage
    },
    {
        path: '/leaderboards/speed/:url_name/snapshots/:date',
        component: SpeedLeaderboardEntriesPage
    },
    {
        path: '/leaderboards/deathless',
        component: DeathlessLeaderboardsPage
    },
    {
        path: '/leaderboards/deathless/:url_name/snapshots',
        component: DeathlessLeaderboardSnapshotsPage
    },
    {
        path: '/leaderboards/deathless/:url_name/snapshots/:date',
        component: DeathlessLeaderboardEntriesPage
    },
    {
        path: '/leaderboards/daily',
        component: DailyLeaderboardsPage
    },
    {
        path: '/leaderboards/daily/:release/:date',
        component: DailyLeaderboardEntriesPage
    }
];



/* --------- Initalize the router ---------- */

const router = new VueRouter({
    routes: routes
});

export default router;

/* ---------- Import and register vue-router ----------*/

import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

/* --------- Register all page components ---------- */

import HomePage from './components/pages/HomePage.vue';

import LoginPage from  './components/pages/LoginPage.vue';

import PowerRankingsPage from './components/pages/PowerRankingsPage.vue';
import CategoryRankingsPage from './components/pages/CategoryRankingsPage.vue';
import CharacterRankingsPage from './components/pages/CharacterRankingsPage.vue';
import DailyRankingsPage from './components/pages/DailyRankingsPage.vue';

import PowerRankingEntriesPage from './components/pages/PowerRankingEntriesPage.vue';
import CategoryRankingEntriesPage from './components/pages/CategoryRankingEntriesPage.vue';
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

import LeaderboardSourcePlayersPage from './components/player/LeaderboardSourcePlayersPage.vue';
import LeaderboardSourcePlayerProfilePage from './components/player/LeaderboardSourcePlayerProfilePage.vue';
import PlayerProfileLeaderboards from './components/player/PlayerProfileLeaderboards.vue';


/* --------- Define routes ---------- */

const routes = [
    {
        path: '/', 
        component: HomePage 
    },
    {
        path: '/login', 
        component: LoginPage 
    },
    {
        path: '/rankings/power/:leaderboard_source',
        component: PowerRankingsPage
    },
    {
        path: '/rankings/power/:leaderboard_source/:release/:mode/:seeded_type/:date',
        component: PowerRankingEntriesPage
    },
    {
        path: '/rankings/character/:leaderboard_source',
        component: CharacterRankingsPage
    },
    {
        path: '/rankings/character/:leaderboard_source/:character/:release/:mode/:seeded_type/:date',
        component: CharacterRankingEntriesPage
    },
    {
        path: '/rankings/daily/:leaderboard_source',
        component: DailyRankingsPage
    },
    {
        path: '/rankings/daily/:leaderboard_source/:release/:mode/:number_of_days/:date',
        component: DailyRankingEntriesPage
    },
    {
        path: '/rankings/:leaderboard_type/:leaderboard_source',
        component: CategoryRankingsPage
    },
    {
        path: '/rankings/:leaderboard_type/:leaderboard_source/:release/:mode/:seeded_type/:date',
        component: CategoryRankingEntriesPage
    },
    {
        path: '/leaderboards/:leaderboard_type/:leaderboard_source',
        component: LeaderboardsPage,
    },
    {
        path: '/leaderboards/:leaderboard_type/:leaderboard_source/:character/:release/:mode/:seeded_type/:multiplayer_type/:soundtrack/snapshots',
        component: LeaderboardSnapshotsPage
    },
    {
        path: '/leaderboards/:leaderboard_type/:leaderboard_source/:character/:release/:mode/:seeded_type/:multiplayer_type/:soundtrack/snapshots/:date',
        component: LeaderboardEntriesPage
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
    },
    {
        path: '/players/:leaderboard_source', 
        component: LeaderboardSourcePlayersPage
    },
    {
        path: '/players/:leaderboard_source/:player_id', 
        component: LeaderboardSourcePlayerProfilePage,
        children: [
            /*{
                path: 'pbs/:leaderboard_type',
                component: PlayerProfilePbs
            },
            {
                path: 'leaderboards/daily',
                component: PlayerProfileDailyLeaderboards
            },*/
            {
                path: 'leaderboards/:leaderboard_type',
                component: PlayerProfileLeaderboards
            },
            /*{
                path: 'rankings/power',
                component: PlayerProfilePowerRankings
            },
            {
                path: 'rankings/character',
                component: PlayerProfileCharacterRankings
            },
            {
                path: 'rankings/:leaderboard_type',
                component: PlayerProfileRankingCategories
            }*/
        ]
    }
];



/* --------- Initalize the router ---------- */

const router = new VueRouter({
    routes: routes
});

export default router;

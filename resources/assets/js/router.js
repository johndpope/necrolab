/* ---------- Import and register vue-router ----------*/

import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

/* --------- Register all page components ---------- */

import WithNavLayout from './components/layouts/WithNavLayout.vue';

import HomePage from './components/pages/HomePage.vue';

import LoginPage from  './components/pages/LoginPage.vue';

import PowerRankingsPage from './components/pages/rankings/PowerRankingsPage.vue';
import CategoryRankingsPage from './components/pages/rankings/CategoryRankingsPage.vue';
import CharacterRankingsPage from './components/pages/rankings/CharacterRankingsPage.vue';
import DailyRankingsPage from './components/pages/rankings/DailyRankingsPage.vue';

import PowerRankingEntriesPage from './components/pages/rankings/PowerRankingEntriesPage.vue';
import CategoryRankingEntriesPage from './components/pages/rankings/CategoryRankingEntriesPage.vue';
import CharacterRankingEntriesPage from './components/pages/rankings/CharacterRankingEntriesPage.vue';
import DailyRankingEntriesPage from './components/pages/rankings/DailyRankingEntriesPage.vue';

import LeaderboardsPage from './components/pages/leaderboards/LeaderboardsPage.vue';
import DailyLeaderboardsPage from './components/pages/leaderboards/DailyLeaderboardsPage.vue';

import LeaderboardSnapshotsPage from './components/pages/leaderboards/LeaderboardSnapshotsPage.vue';

import LeaderboardEntriesPage from './components/pages/leaderboards/LeaderboardEntriesPage.vue';
import DailyLeaderboardEntriesPage from './components/pages/leaderboards/DailyLeaderboardEntriesPage.vue';

import LeaderboardSourcePlayersPage from './components/pages/players/LeaderboardSourcePlayersPage.vue';
import LeaderboardSourcePlayerProfilePage from './components/pages/players/LeaderboardSourcePlayerProfilePage.vue';
import PlayerProfilePbs from './components/pages/players/PlayerProfilePbs.vue';


/* --------- Define routes ---------- */

const routes = [
    {
        path: '/login', 
        component: LoginPage 
    },
    {
        path: '/',
        component: WithNavLayout,
        children: [
            {
                path: '/', 
                component: HomePage 
            },
            {
                path: '/rankings/power/:leaderboard_source',
                component: PowerRankingsPage
            },
            {
                path: '/rankings/power/:leaderboard_source/:release/:mode/:seeded_type/:multiplayer_type/:soundtrack/:date',
                component: PowerRankingEntriesPage
            },
            {
                path: '/rankings/character/:leaderboard_source',
                component: CharacterRankingsPage
            },
            {
                path: '/rankings/character/:leaderboard_source/:character/:release/:mode/:seeded_type/:multiplayer_type/:soundtrack/:date',
                component: CharacterRankingEntriesPage
            },
            {
                path: '/rankings/daily/:leaderboard_source',
                component: DailyRankingsPage
            },
            {
                path: '/rankings/daily/:leaderboard_source/:character/:release/:mode/:multiplayer_type/:soundtrack/:number_of_days/:date',
                component: DailyRankingEntriesPage
            },
            {
                path: '/rankings/:leaderboard_type/:leaderboard_source',
                component: CategoryRankingsPage
            },
            {
                path: '/rankings/:leaderboard_type/:leaderboard_source/:release/:mode/:seeded_type/:multiplayer_type/:soundtrack/:date',
                component: CategoryRankingEntriesPage
            },
            {
                path: '/leaderboards/daily/:leaderboard_source',
                component: DailyLeaderboardsPage
            },
            {
                path: '/leaderboards/daily/:leaderboard_source/:character/:release/:mode/:multiplayer_type/:soundtrack/:date',
                component: DailyLeaderboardEntriesPage
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
                path: '/players/:leaderboard_source', 
                component: LeaderboardSourcePlayersPage
            },
            {
                path: '/players/:leaderboard_source/:player_id', 
                component: LeaderboardSourcePlayerProfilePage,
                children: [
                    {
                        path: 'pbs/:leaderboard_type',
                        component: PlayerProfilePbs
                    }
                    /*
                    {
                        path: 'leaderboards/daily',
                        component: PlayerProfileDailyLeaderboards
                    },*/
                    /*
                    {
                        path: 'leaderboards/:leaderboard_type',
                        component: PlayerProfileLeaderboards
                    },
                    */
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
        ]
    }
];


/*const routes = [
    
    
];
*/



/* --------- Initalize the router ---------- */

const router = new VueRouter({
    routes: routes
});

export default router;

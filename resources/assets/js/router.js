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
import PlayerProfileInfo from './components/pages/players/PlayerProfileInfo.vue';
//import PlayerProfileConnections from './components/pages/players/PlayerProfileConnections.vue';
//import PlayerProfileSupport from './components/pages/players/PlayerProfileSupport.vue';
import PlayerProfileStats from './components/pages/players/PlayerProfileStats.vue';
import PlayerProfileStatsByRelease from './components/pages/players/PlayerProfileStatsByRelease.vue';
import PlayerProfilePbs from './components/pages/players/PlayerProfilePbs.vue';
import PlayerProfileLeaderboards from './components/pages/players/PlayerProfileLeaderboards.vue';
import PlayerProfilePowerRankings from './components/pages/players/PlayerProfilePowerRankings.vue';
import PlayerProfileCharacterRankings from './components/pages/players/PlayerProfileCharacterRankings.vue';
import PlayerProfileDailyRankings from './components/pages/players/PlayerProfileDailyRankings.vue';
import PlayerProfileCategoryRankings from './components/pages/players/PlayerProfileCategoryRankings.vue';


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
                        path: '/',
                        component: PlayerProfileInfo
                    },
                    /*{
                        path: 'connections',
                        component: PlayerProfileConnections
                    },
                    {
                        path: 'support',
                        component: PlayerProfileSupport
                    },*/
                    {
                        path: 'stats/overall',
                        component: PlayerProfileStats
                    },
                    {
                        path: 'stats/by_release',
                        component: PlayerProfileStatsByRelease
                    },
                    {
                        path: 'pbs/:leaderboard_type',
                        component: PlayerProfilePbs
                    },
                    {
                        path: 'leaderboards/:leaderboard_type',
                        component: PlayerProfileLeaderboards
                    },
                    {
                        path: 'rankings/power',
                        component: PlayerProfilePowerRankings
                    },
                    {
                        path: 'rankings/character',
                        component: PlayerProfileCharacterRankings
                    },
                    {
                        path: 'rankings/daily',
                        component: PlayerProfileDailyRankings
                    },
                    {
                        path: 'rankings/:leaderboard_type',
                        component: PlayerProfileCategoryRankings
                    }
                ]
            }
        ]
    }
];


/* --------- Initalize the router ---------- */

const router = new VueRouter({
    routes: routes
});

export default router;

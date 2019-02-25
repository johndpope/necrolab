/* ---------- Register Vuex with Vue ---------- */

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);


/* ---------- Import modules ---------- */

import PageStore from './modules/PageStore.js';
import BreadcrumbsStore from './modules/BreadcrumbsStore.js';
import AttributesStore from './modules/AttributesStore.js';
import DataTypesStore from './modules/DataTypesStore.js';
import CharactersStore from './modules/CharactersStore.js';
import ModesStore from './modules/ModesStore.js';
import NumberOfDaysStore from './modules/NumberOfDaysStore.js';
import ReleasesStore from './modules/ReleasesStore.js';
import SoundtracksStore from './modules/SoundtracksStore.js';
import MultiplayerTypesStore from './modules/MultiplayerTypesStore.js';
import SeededTypesStore from './modules/SeededTypesStore.js';
import SitesStore from './modules/SitesStore.js';
import LeaderboardsStore from './modules/LeaderboardsStore.js';
import LeaderboardSourcesStore from './modules/LeaderboardSourcesStore.js';
import LeaderboardTypesStore from './modules/LeaderboardTypesStore.js';
import LeaderboardDetailsColumnsStore from './modules/LeaderboardDetailsColumnsStore.js';

const Store = new Vuex.Store({
    modules: {
        page: PageStore,
        breadcrumbs: BreadcrumbsStore,
        attributes: AttributesStore,
        data_types: DataTypesStore,
        characters: CharactersStore,
        modes: ModesStore,
        number_of_days: NumberOfDaysStore,
        releases: ReleasesStore,
        sites: SitesStore,
        soundtracks: SoundtracksStore,
        multiplayer_types: MultiplayerTypesStore,
        seeded_types: SeededTypesStore,
        leaderboards: LeaderboardsStore,
        leaderboard_sources: LeaderboardSourcesStore,
        leaderboard_types: LeaderboardTypesStore,
        details_columns: LeaderboardDetailsColumnsStore
    }
});

export default Store;

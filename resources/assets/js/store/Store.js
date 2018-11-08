/* ---------- Register Vuex with Vue ---------- */

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);


/* ---------- Import modules ---------- */

import CharactersStore from './modules/CharactersStore.js';

import ModesStore from './modules/ModesStore.js';
import NumberOfDaysStore from './modules/NumberOfDaysStore.js';
import ReleasesStore from './modules/ReleasesStore.js';
import SoundtracksStore from './modules/SoundtracksStore.js';
import MultiplayerTypesStore from './modules/MultiplayerTypesStore.js';
import SeededTypesStore from './modules/SeededTypesStore.js';
import SitesStore from './modules/SitesStore.js';
import LeaderboardsStore from './modules/LeaderboardsStore.js';

const Store = new Vuex.Store({
    modules: {
        characters: CharactersStore,
        modes: ModesStore,
        number_of_days: NumberOfDaysStore,
        releases: ReleasesStore,
        sites: SitesStore,
        soundtracks: SoundtracksStore,
        multiplayer_types: MultiplayerTypesStore,
        seeded_types: SeededTypesStore,
        leaderboards: LeaderboardsStore
    }
});

export default Store;

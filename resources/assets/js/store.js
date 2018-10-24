/* ---------- Register Vuex with Vue ---------- */

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        characters: [],
        character: {},
        releases: [],
        modes: [],
        sites: [],
        number_of_days: []
    },
    mutations: {
        setCharacters(state, characters) {
            state.characters = characters;
        },
        setCharacter(state, character) {
            state.character = character;
        },
        setReleases(state, releases) {
            state.releases = releases;
        },
        setModes(state, modes) {
            state.modes = modes;
        },
        setSites(state, sites) {
            state.sites = sites;
        },
        setNumberOfDays(state, number_of_days) {
            state.number_of_days = number_of_days;
        }
    },
    getters: {
        allCharacters: state => {
            return state.characters;
        },
        currentCharacter: state => {
            return state.character;
        },
        allReleases: state => {
            return state.releases;
        },
        allModes: state => {
            return state.modes;
        },
        allSites: state => {
            return state.sites;
        },
        allNumberOfDays: state => {
            return state.number_of_days;
        }
    }
});

export default store;

<template>
    <rankings-overview-page
        v-if="loaded"
        :loaded="loaded"
        category_name="character"
        category_display_name="Character"
        :header_columns="header_columns"
        :filters="filters"
        :url_segment_stores="url_segment_stores"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl }">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ getPlayersProperty(row) }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-summary-details-table
                :leaderboard_types="$store.getters['leaderboard_types/getFiltered']"
                :record="row.characters[currentCharacter.name]"
                :rows="details_table_rows"
            >
            </ranking-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import BasePage from '../BasePage.vue';
import RankingsOverviewPage from './RankingsOverviewPage.vue';
import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import PlayerProfileModal from '../../player/PlayerProfileModal.vue';
import RankingSummaryDetailsTable from '../../table/RankingSummaryDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'character-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-summary-details-table': RankingSummaryDetailsTable
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: 'Character',
                    href: '#/rankings/character'
                }
            ],
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
            ],
            header_columns: [
                'Rank',
                'Players',
                'Points'
            ],
            url_segment_stores: [
                'characters',
                'releases',
                'modes',
                'seeded_types',
                'multiplayer_types',
                'soundtracks'
            ],
            details_table_rows: [
                {
                    name: 'players',
                    display_name: 'Players'
                }
            ]
        }
    },
    computed: {
        currentCharacter() {
            let character = this.$store.getters['characters/getSelected'];

            if(typeof character === "string" && character.length == 0 || character['name'] == null) {
                character = this.$store.getters['characters/getDefault'].name;
            }

            return character;
        }
    },
    methods: {
        getPlayersProperty(record) {
            let players = 0;
            
            if(record['characters'] != null && record['characters'][this.currentCharacter.name] != null) {
                players = record['characters'][this.currentCharacter.name].players;
            }
            
            return players;
        },
        loadState(route_params) {
            this.$store.commit('characters/setFilterStores', [
                'leaderboard_sources',
                'releases',
                'modes'
            ]);
            
            this.$store.commit('releases/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.$store.commit('modes/setFilterStores', [
                'releases'
            ]);
            
            this.$store.commit('leaderboard_types/setFilterStores', [
                'modes'
            ]);
            
            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.loaded = true;
        }
    }
};
</script>

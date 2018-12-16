<template>
    <ranking-entries-page
        v-if="loaded"
        category_name="character"
        category_display_name="Character"
        :api_endpoint_url="api_endpoint_url"
        :filter_records="filter_records"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.characters[currentCharacter].rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.characters[currentCharacter].points"></rounded-decimal>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-summary-details-table 
                :leaderboard_types="$store.getters['leaderboard_types/getFiltered']"
                :record="row"
                :rows="details_table_rows"
            >
            </ranking-summary-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import RankingSummaryDetailsTable from '../table/RankingSummaryDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'character-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'ranking-summary-details-table': RankingSummaryDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/character/entries',
            filter_records: [
                {
                    name: 'leaderboard_source',
                    store_name: 'leaderboard_sources'
                },
                {
                    name: 'character',
                    store_name: 'characters'
                },
                {
                    name: 'release',
                    store_name: 'releases'
                },
                {
                    name: 'mode',
                    store_name: 'modes'
                },
                {
                    name: 'seeded_type',
                    store_name: 'seeded_types'
                }
            ],
            header_columns: [
                'Rank',
                'Player',
                'Points'
            ],
            details_table_rows: [
                {
                    name: 'rank',
                    display_name: 'Rank'
                },
                {
                    name: 'points',
                    display_name: 'Points',
                    rounded: true
                },
            ]
        }
    },
    computed: {
        currentCharacter() {
            return this.$route.params.character;
        }
    },
    methods: {
        loadState(route_params) {
            let promise = this.$store.dispatch('page/loadModules', [
                'leaderboard_sources',
                'characters',
                'releases',
                'modes',
                'seeded_types',
                'leaderboard_types',
                'leaderboard_details_columns',
                'data_types'
            ]);

            promise.then(() => {
                this.$store.commit('leaderboard_types/setFilterStores', [
                    'modes'
                ]);
                
                this.$store.commit('leaderboard_sources/setSelected', route_params.leaderboard_source);
                this.$store.commit('characters/setSelected', route_params.character);
                this.$store.commit('releases/setSelected', route_params.release);
                this.$store.commit('modes/setSelected', route_params.mode);
                this.$store.commit('seeded_types/setSelected', route_params.seeded_type);
                
                this.loaded = true;
            });
        }
    }
};
</script>

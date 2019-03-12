<template>
    <ranking-entries-page
        v-if="loaded"
        :loaded="loaded"
        category_name="character"
        category_display_name="Character"
        :api_endpoint_url="api_endpoint_url"
        :filter_records="filter_records"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.characters[character_name].rank }}
            </td>
            <td>
                <player-profile-modal 
                    :player="row.player"
                    :leaderboard_source="$store.getters['leaderboard_sources/getSelected']"
                ></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.characters[character_name].points"></rounded-decimal>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-summary-details-table 
                :leaderboard_types="$store.getters['leaderboard_types/getFiltered']"
                :record="row.characters[character_name]"
                :rows="details_table_rows"
            >
            </ranking-summary-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import BasePage from '../BasePage.vue';
import RankingEntriesPage from './RankingEntriesPage.vue';
import PlayerProfileModal from '../../player/PlayerProfileModal.vue';
import RoundedDecimal from '../../formatting/RoundedDecimal.vue';
import RankingSummaryDetailsTable from '../../table/RankingSummaryDetailsTable.vue';

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
            character_name: '',
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
                },
                {
                    name: 'multiplayer_type',
                    store_name: 'multiplayer_types'
                },
                {
                    name: 'soundtrack',
                    store_name: 'soundtracks'
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
    methods: {
        loadState(route_params) {
            this.$store.commit('leaderboard_types/setFilterStores', [
                'modes'
            ]);
            
            this.character_name = route_params.character;
            
            this.loaded = true;
        }
    }
};
</script>

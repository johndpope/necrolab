<template>
    <ranking-entries-page
        v-if="loaded"
        category_name="daily"
        category_display_name="Daily"
        :api_endpoint_url="api_endpoint_url"
        :filter_records="filter_records"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.total_points"></rounded-decimal>
            </td>
            <td>
                {{ row.total_score }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <daily-ranking-details-table :record="row">
            </daily-ranking-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import DailyRankingDetailsTable from '../table/DailyRankingDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'daily-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'daily-ranking-details-table': DailyRankingDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/daily/entries',
            filter_records: [
                {
                    name: 'leaderboard_source',
                    store_name: 'leaderboard_sources'
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
                    name: 'number_of_days',
                    store_name: 'number_of_days'
                }
            ],
            header_columns: [
                'Rank',
                'Player',
                'Points',
                'Score'
            ]
        }
    },
    methods: {
        loadState(route_params) {
            let promise = this.$store.dispatch('page/loadModules', [
                'leaderboard_sources',
                'releases',
                'modes',
                'number_of_days',
                'leaderboard_types',
                'leaderboard_details_columns',
                'data_types'
            ]);

            promise.then(() => {
                this.$store.commit('leaderboard_types/setFilterStores', [
                    'modes'
                ]);
                
                this.$store.commit('leaderboard_sources/setSelected', route_params.leaderboard_source);
                this.$store.commit('releases/setSelected', route_params.release);
                this.$store.commit('modes/setSelected', route_params.mode);
                this.$store.commit('number_of_days/setSelected', route_params.number_of_days);
                
                this.loaded = true;
            });
        }
    }
};
</script>

<template>
    <ranking-entries-page
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
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import DailyRankingDetailsTable from '../table/DailyRankingDetailsTable.vue';

export default {
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
                    name: 'release',
                    store_name: 'releases'
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
    }
};
</script>

<template>
    <ranking-entries-page
        category_name="power"
        category_display_name="Power"
        :api_endpoint_url="api_endpoint_url"
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
                <rounded-decimal :original_number="row.points"></rounded-decimal>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-details-table :record="row"></ranking-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import RankingDetailsTable from '../table/RankingDetailsTable.vue';

export default {
    name: 'power-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'toggle-details': ToggleDetails,
        'ranking-details-table': RankingDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/power/entries',
            header_columns: [
                'Rank',
                'Player',
                'Points'
            ]
        }
    }
};
</script>

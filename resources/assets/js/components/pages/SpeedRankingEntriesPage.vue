<template>
    <ranking-entries-page
        category_name="speed"
        category_display_name="Speed"
        :header_columns="header_columns"
        :api_endpoint_url="api_endpoint_url"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.speed.rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.speed.points"></rounded-decimal>
            </td>
            <td>
                <seconds-to-time :unformatted="row.speed.time" :include_hours="true" :zero_pad_hours="false"></seconds-to-time>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-details-table
                :record="row.characters"
                category="speed"
                category_display_name="Time"
                details_property="time"
            >
            </ranking-category-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';
import RankingCategoryDetailsTable from '../table/RankingCategoryDetailsTable.vue';

export default {
    name: 'speed-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'seconds-to-time': SecondsToTime,
        'ranking-category-details-table': RankingCategoryDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/speed/entries',
            header_columns: [
                'Rank',
                'Player',
                'Points',
                'Win Count'
            ]
        }
    }
};
</script>

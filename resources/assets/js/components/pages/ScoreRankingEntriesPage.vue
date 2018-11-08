<template>
    <ranking-entries-page
        category_name="score"
        category_display_name="Score"
        :header_columns="header_columns"
        :api_endpoint_url="api_endpoint_url"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.score.rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.score.points"></rounded-decimal>
            </td>
            <td>
                {{ row.score.score }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-details-table
                :record="row.characters"
                category="score"
                category_display_name="Score"
                details_property="score"
            >
            </ranking-category-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import RankingCategoryDetailsTable from '../table/RankingCategoryDetailsTable.vue';

export default {
    name: 'score-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'ranking-category-details-table': RankingCategoryDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/score/entries',
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

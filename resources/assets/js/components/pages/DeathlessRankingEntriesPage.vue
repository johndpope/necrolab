<template>
    <ranking-entries-page
        category_name="deathless"
        category_display_name="Deathless"
        :header_columns="header_columns"
        :api_endpoint_url="api_endpoint_url"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.deathless.rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.deathless.points"></rounded-decimal>
            </td>
            <td>
                {{ row.deathless.win_count }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-details-table
                :record="row.characters"
                category="deathless"
                category_display_name="Win Count"
                details_property="win_count"
                :show_multi_characters="false"
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
    name: 'deathless-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'ranking-category-details-table': RankingCategoryDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/deathless/entries',
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

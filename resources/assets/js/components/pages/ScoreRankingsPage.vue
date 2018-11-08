<template>
    <rankings-overview-page
        category_name="score"
        category_display_name="Score"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl, getCategoryField }">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ getCategoryField(row, 'score', 'players') }}
            </td>
            <td>
                {{ getCategoryField(row, 'score', 'score') }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-summary-details-table
                :record="row.characters"
                category="score"
                category_display_name="Score"
                details_property="score"
            >
            </ranking-category-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import RankingCategorySummaryDetailsTable from '../table/RankingCategorySummaryDetailsTable.vue'

export default {
    name: 'score-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable
    },
    data() {
        return {
            header_columns: [
                'Date',
                'Players',
                'Score'
            ]
        }
    }
};
</script>

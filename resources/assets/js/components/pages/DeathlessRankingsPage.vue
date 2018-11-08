<template>
    <rankings-overview-page
        category_name="deathless"
        category_display_name="Deathless"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl, getCategoryField }">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ getCategoryField(row, 'deathless', 'players') }}
            </td>
            <td>
                {{ getCategoryField(row, 'deathless', 'win_count') }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-summary-details-table
                :record="row.characters"
                category="deathless"
                category_display_name="Deathless"
                details_property="win_count"
                :show_multi_characters="false"
            >
            </ranking-category-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import RankingCategorySummaryDetailsTable from '../table/RankingCategorySummaryDetailsTable.vue'

export default {
    name: 'deathless-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable
    },
    data() {
        return {
            header_columns: [
                'Date',
                'Players',
                'Win Count'
            ]
        }
    }
};
</script>

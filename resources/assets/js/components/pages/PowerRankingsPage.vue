<template>
    <rankings-overview-page
        category_name="power"
        category_display_name="Power"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl }">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ row.players }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-summary-details-table :record="row">
            </ranking-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import RankingSummaryDetailsTable from '../table/RankingSummaryDetailsTable.vue'

export default {
    name: 'power-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-summary-details-table': RankingSummaryDetailsTable
    },
    data() {
        return {
            header_columns: [
                'Date',
                'Players'
            ]
        }
    }
};
</script>

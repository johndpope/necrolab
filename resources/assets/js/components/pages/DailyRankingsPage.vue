<template>
    <rankings-overview-page
        category_name="daily"
        category_display_name="Daily"
        :api_endpoint_url="api_endpoint_url"
        :header_columns="header_columns"
        :filters="filters"
        :filter_records="filter_records"
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
            <td>
                {{ row.total_score }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <daily-ranking-summary-details-table :record="row" :show_calculated="false">
            </daily-ranking-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import DailyRankingSummaryDetailsTable from '../table/DailyRankingSummaryDetailsTable.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import NumberOfDaysDropdownFilter from '../table/filters/NumberOfDaysDropdownFilter.vue';

export default {
    name: 'daily-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'daily-ranking-summary-details-table': DailyRankingSummaryDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/daily',
            header_columns: [
                'Rank',
                'Players',
                'Score'
            ],
            filters: [
                ReleaseDropdownFilter,
                NumberOfDaysDropdownFilter
            ],
            filter_records: [
                {
                    name: 'release',
                    store_name: 'releases'
                },
                {
                    name: 'number_of_days',
                    store_name: 'number_of_days'
                }
            ]
        }
    }
};
</script>

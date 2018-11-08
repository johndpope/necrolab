<template>
    <rankings-overview-page
        category_name="speed"
        category_display_name="Speed"
        :api_endpoint_url="api_endpoint_url"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl, getCategoryField }">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ getCategoryField(row, 'speed', 'players') }}
            </td>
            <td>
                <seconds-to-time 
                    :unformatted="getCategoryField(row, 'speed', 'time')" 
                    :include_hours="true" 
                    :zero_pad_hours="false"
                >
                </seconds-to-time>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-summary-details-table
                :record="row.characters"
                category="speed"
                category_display_name="Speed"
                details_property="time"
                :details_formatter_component="details_formatter_component"
            >
            </ranking-category-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';
import RankingCategorySummaryDetailsTable from '../table/RankingCategorySummaryDetailsTable.vue'

export default {
    name: 'speed-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable,
        'seconds-to-time': SecondsToTime
    },
    data() {
        return {
            header_columns: [
                'Date',
                'Players',
                'Time'
            ],
            details_formatter_component: SecondsToTime
        }
    }
};
</script>

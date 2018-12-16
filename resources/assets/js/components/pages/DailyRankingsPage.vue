<template>
    <rankings-overview-page
        v-if="loaded"
        category_name="daily"
        category_display_name="Daily"
        :api_endpoint_url="api_endpoint_url"
        :header_columns="header_columns"
        :filters="filters"
        :url_segment_stores="url_segment_stores"
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
import BasePage from './BasePage.vue';
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import DailyRankingSummaryDetailsTable from '../table/DailyRankingSummaryDetailsTable.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import NumberOfDaysDropdownFilter from '../table/filters/NumberOfDaysDropdownFilter.vue';

export default {
    extends: BasePage,
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
                ModeDropdownFilter,
                NumberOfDaysDropdownFilter
            ],
            url_segment_stores: [
                'releases',
                'modes',
                'number_of_days'
            ]
        }
    },
    methods: {
        loadState() {
            let promise = this.$store.dispatch('page/loadModules', [
                'leaderboard_sources',
                'leaderboard_types',
                'releases',
                'modes',
                'number_of_days'
            ]);

            promise.then(() => {
                this.$store.commit('releases/setFilterStores', [
                    'leaderboard_sources'
                ]);
                
                this.$store.commit('modes/setFilterStores', [
                    'leaderboard_types',
                    'releases'
                ]);
                
                this.$store.commit('leaderboard_sources/setSelected', this.$route.params.leaderboard_source);
                
                this.$store.commit('leaderboard_types/setSelected', 'daily');
                
                this.loaded = true;
            });
        }
    }
};
</script>

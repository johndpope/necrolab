<template>
    <rankings-overview-page
        :loaded="loaded"
        key="daily"        
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
            <daily-ranking-summary-details-table 
                :record="row" 
                :details_columns="details_columns"
            >
            </daily-ranking-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import DailyRankingSummaryDetailsTable from '../table/DailyRankingSummaryDetailsTable.vue';
import CharacterDropdownFilter from '../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../table/filters/SoundtrackDropdownFilter.vue';
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
            details_columns: [],
            api_endpoint_url: '/api/1/rankings/daily',
            header_columns: [
                'Rank',
                'Players',
                'Score'
            ],
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter,
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
        loadState(route_params) {
            this.$store.commit('releases/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.$store.commit('characters/setFilterStores', [
                'leaderboard_sources',
                'releases',
                'leaderboard_types',
                'modes'
            ]);
            
            this.$store.commit('modes/setFilterStores', [
                'leaderboard_types',
                'releases'
            ]);
            
            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.$store.commit('leaderboard_types/setSelected', 'daily');
            
            let leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];
            
            this.details_columns = this.$store.getters['details_columns/getAllByNames'](leaderboard_type.details_columns);
            
            this.loaded = true;
        }
    }
};
</script>

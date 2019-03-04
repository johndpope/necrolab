<template>
    <rankings-overview-page
        v-if="loaded"
        :loaded="loaded"
        key="daily"        
        category_name="daily"
        category_display_name="Daily"
        :api_endpoint_url="api_endpoint_url"
        :header_columns="headerColumns"
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
            <td
                v-for="details_column in details_columns"
            >
                <details-column
                    :details_name="details_column.name"
                    :details_value="getDetailsValue(row, details_column.name)"
                >
                </details-column>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <daily-ranking-summary-details-table 
                :record="row" 
            >
            </daily-ranking-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import BasePage from '../BasePage.vue';
import RankingsOverviewPage from './RankingsOverviewPage.vue';
import DailyRankingSummaryDetailsTable from '../../table/DailyRankingSummaryDetailsTable.vue';
import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import NumberOfDaysDropdownFilter from '../../table/filters/NumberOfDaysDropdownFilter.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';

export default {
    extends: BasePage,
    name: 'daily-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'daily-ranking-summary-details-table': DailyRankingSummaryDetailsTable,
        'details-column': DetailsColumn
    },
    data() {
        return {
            details_columns: [],
            api_endpoint_url: '/api/1/rankings/daily',
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter,
                NumberOfDaysDropdownFilter
            ],
            url_segment_stores: [
                'characters',
                'releases',
                'modes',
                'multiplayer_types',
                'soundtracks',
                'number_of_days'
            ]
        }
    },
    computed: {
        headerColumns() {
            const header_columns = [
                'Rank',
                'Players'
            ];
            
            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });
            
            return header_columns;
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
        },
        getDetailsValue(row, details_name) {            
            let details_value = '';
            
            if(
                row['details'] != null &&
                row['details'][details_name] != null
            ) {
                details_value = row['details'][details_name];
            }
            
            return details_value;
        },
    }
};
</script>

<template>
    <ranking-entries-page
        v-if="loaded"
        :loaded="loaded"
        category_name="daily"
        category_display_name="Daily"
        :api_endpoint_url="api_endpoint_url"
        :filter_records="filter_records"
        :header_columns="headerColumns"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.rank }}
            </td>
            <td>
                <player-profile-modal 
                    :player="row.player"
                    :leaderboard_source="$store.getters['leaderboard_sources/getSelected']"
                ></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.points"></rounded-decimal>
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
            <daily-ranking-details-table :record="row">
            </daily-ranking-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import DetailsColumn from '../formatting/DetailsColumn.vue';
import DailyRankingDetailsTable from '../table/DailyRankingDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'daily-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'details-column': DetailsColumn,
        'daily-ranking-details-table': DailyRankingDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/daily/entries',
            filter_records: [
                {
                    name: 'leaderboard_source',
                    store_name: 'leaderboard_sources'
                },
                {
                    name: 'character',
                    store_name: 'characters'
                },
                {
                    name: 'release',
                    store_name: 'releases'
                },
                {
                    name: 'mode',
                    store_name: 'modes'
                },
                {
                    name: 'multiplayer_type',
                    store_name: 'multiplayer_types'
                },
                {
                    name: 'soundtrack',
                    store_name: 'soundtracks'
                },
                {
                    name: 'number_of_days',
                    store_name: 'number_of_days'
                }
            ],
            details_columns: []
        }
    },
    computed: {
        headerColumns() {
            const header_columns = [
                'Rank',
                'Player',
                'Points'
            ];
            
            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });
            
            return header_columns;
        }
    },
    methods: {
        loadState(route_params) {
            this.$store.commit('leaderboard_types/setFilterStores', [
                'modes'
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

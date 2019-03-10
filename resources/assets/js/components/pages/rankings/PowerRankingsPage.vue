<template>
    <rankings-overview-page
        v-if="loaded"
        :loaded="loaded"
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
            <ranking-summary-details-table
                :leaderboard_types="$store.getters['leaderboard_types/getFiltered']"
                :record="row"
                :rows="details_table_rows"
            >
            </ranking-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import BasePage from '../BasePage.vue';
import RankingsOverviewPage from './RankingsOverviewPage.vue';
import RankingSummaryDetailsTable from '../../table/RankingSummaryDetailsTable.vue'

export default {
    extends: BasePage,
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
            ],
            details_table_rows: [
                {
                    name: 'players',
                    display_name: 'Players'
                }
            ]
        }
    },
    methods: {
        loadState(route_params) {   
            this.$store.commit('leaderboard_types/setFilterStores', [
                'modes'
            ]);
            
            this.$store.commit('releases/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.$store.commit('modes/setFilterStores', [
                'releases'
            ]);
            
            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.loaded = true;
        }
    }
};
</script>
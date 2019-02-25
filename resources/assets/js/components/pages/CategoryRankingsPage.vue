<template>
    <rankings-overview-page
        :loaded="loaded"
        :key="leaderboard_type.name"
        :category_name="leaderboard_type.name"
        :category_display_name="leaderboard_type.display_name"
        :header_columns="headerColumns"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl}">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ getPlayers(row, leaderboard_type.name) }}
            </td>
            <td
                v-for="details_column in details_columns"
                :key="details_column.name"
            >
                <details-column
                    :details_name="details_column.name"
                    :details_value="getDetailsValue(row, leaderboard_type.name, details_column.name)"
                >
                </details-column>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-summary-details-table
                :characters="characters"
                :record="row.characters"
                :leaderboard_type="leaderboard_type"
                :details_columns="details_columns"
                :rows="details_table_rows"
            >
            </ranking-category-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import RankingCategorySummaryDetailsTable from '../table/RankingCategorySummaryDetailsTable.vue';
import DetailsColumn from '../formatting/DetailsColumn.vue';

export default {
    extends: BasePage,
    name: 'category-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable,
        'details-column': DetailsColumn
    },
    data() {
        return {
            leaderboard_type: {},
            details_columns: [],
            details_table_rows: [
                {
                    name: 'players',
                    display_name: 'Players'
                }
            ]
        }
    },
    computed: {
        headerColumns() {
            let header_columns = [
                'Date',
                'Players'
            ];
            
            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });
            
            return header_columns;
        },
        characters() {
            return this.$store.getters['characters/getFiltered'];
        }
    },
    methods: {
        getPlayers(row, category) {
            let players = '';
            
            if(
                row['categories'] != null &&
                row['categories'][category] != null &&
                row['categories'][category]['players'] != null
            ) {
                players = row['categories'][category]['players'];
            }
            
            return players;
        },
        getDetailsValue(row, category, details_name) {            
            let details_value = '';
            
            if(
                row['categories'] != null &&
                row['categories'][category] != null &&
                row['categories'][category]['details'] != null &&
                row['categories'][category]['details'][details_name] != null
            ) {
                details_value = row['categories'][category]['details'][details_name];
            }
            
            return details_value;
        },
        loadState(route_params) {
            this.leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];

            if(this.leaderboard_type['name'] != null) {
                this.$store.commit('characters/setFilterStores', [
                    'leaderboard_sources',
                    'releases',
                    'leaderboard_types',
                    'modes'
                ]);
                
                this.$store.commit('releases/setFilterStores', [
                    'leaderboard_sources'
                ]);
                
                this.$store.commit('modes/setFilterStores', [
                    'releases',
                    'leaderboard_types'
                ]);
                
                this.$store.commit('multiplayer_types/setFilterStores', [
                    'leaderboard_sources'
                ]);
            }
            
            this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);

            this.loaded = true;
        }
    }
};
</script>

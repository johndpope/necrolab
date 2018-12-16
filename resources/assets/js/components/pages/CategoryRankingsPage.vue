<template>
    <rankings-overview-page
        v-if="loaded"
        :category_name="leaderboard_type.name"
        :category_display_name="leaderboard_type.display_name"
        :header_columns="headerColumns"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl, getCategoryField }">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ getCategoryField(row, leaderboard_type.name, 'players') }}
            </td>
            <td>
                <template v-if="details_column.data_type == 'seconds'">
                    <seconds-to-time 
                        :unformatted="getCategoryField(row, leaderboard_type.name, details_column.name)" 
                        :include_hours="true" 
                        :zero_pad_hours="true"
                    >
                    </seconds-to-time>
                </template>
                <template v-else>
                    {{ getCategoryField(row, leaderboard_type.name, details_column.name) }}
                </template>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-category-summary-details-table
                :characters="characters"
                :record="row.characters"
                :category="leaderboard_type.name"
                :category_display_name="details_column.display_name"
                :details_property="details_column.name"
                :details_data_type="details_column.data_type"
                :rows="details_table_rows"
            >
            </ranking-category-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import RankingCategorySummaryDetailsTable from '../table/RankingCategorySummaryDetailsTable.vue'
import SecondsToTime from '../formatting/SecondsToTime.vue';

export default {
    extends: BasePage,
    name: 'score-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable,
        'seconds-to-time': SecondsToTime,
    },
    data() {
        return {
            leaderboard_type: {},
            details_column: {},
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
            return [
                'Date',
                'Players',
                this.details_column.display_name
            ]
        },
        characters() {
            return this.$store.getters['characters/getFiltered'];
        }
    },
    methods: {
        loadState(route_params) {
            let promise = this.$store.dispatch('page/loadModules', [
                'leaderboard_sources',
                'leaderboard_types',
                'characters',
                'releases',
                'modes',
                'seeded_types',
                'leaderboard_details_columns',
                'data_types'
            ]);

            promise.then(() => {
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
                
                this.$store.commit('leaderboard_sources/setSelected', route_params.leaderboard_source);
                this.$store.commit('leaderboard_types/setSelected', route_params.leaderboard_type);
                
                this.leaderboard_type = this.$store.getters['leaderboard_types/getByName'](route_params.leaderboard_type);
                this.details_column = this.$store.getters['leaderboard_details_columns/getByName'](this.leaderboard_type.details_column_name);

                this.loaded = true;
            });
        }
    }
};
</script>

<template>
    <ranking-entries-page
        v-if="loaded"
        :category_name="leaderboard_type.name"
        :category_display_name="leaderboard_type.display_name"
        :header_columns="headerColumns"
        :api_endpoint_url="apiEndpointUrl"
        :default_api_request_parameters="apiRequestParameters"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row[leaderboard_type.name].rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row[leaderboard_type.name].points"></rounded-decimal>
            </td>
            <td>
                <template v-if="details_column.data_type == 'seconds'">
                    <seconds-to-time 
                        :unformatted="row[leaderboard_type.name][details_column.name]" 
                        :include_hours="true" 
                        :zero_pad_hours="true"
                    >
                    </seconds-to-time>
                </template>
                <template v-else>
                    {{ row[leaderboard_type.name][details_column.name] }}
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
    </ranking-entries-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';
import RankingCategorySummaryDetailsTable from '../table/RankingCategorySummaryDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'score-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'seconds-to-time': SecondsToTime,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable
    },
    data() {
        return {
            leaderboard_type: {},
            details_column: {},
            details_table_rows: [
                {
                    name: 'rank',
                    display_name: 'Rank'
                },
                {
                    name: 'points',
                    display_name: 'Points',
                    rounded: true
                },
            ]
        }
    },
    computed: {
        apiEndpointUrl() {
            return '/api/1/rankings/category/entries';
        },
        apiRequestParameters() {
            return {
                leaderboard_type: this.leaderboard_type.name
            };
        },
        headerColumns() {
            return [
                'Rank',
                'Player',
                'Points',
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
                'characters',
                'releases',
                'modes',
                'seeded_types',
                'leaderboard_types',
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
                
                this.$store.commit('leaderboard_sources/setSelected', route_params.leaderboard_source);
                this.$store.commit('leaderboard_types/setSelected', route_params.leaderboard_type);
                this.$store.commit('releases/setSelected', route_params.release);
                this.$store.commit('modes/setSelected', route_params.mode);
                this.$store.commit('seeded_types/setSelected', route_params.seeded_type);
                
                this.leaderboard_type = this.$store.getters['leaderboard_types/getByName'](route_params.leaderboard_type);
                this.details_column = this.$store.getters['leaderboard_details_columns/getByName'](this.leaderboard_type.details_column_name);
                
                this.loaded = true;
            });
        }
    }
};
</script>

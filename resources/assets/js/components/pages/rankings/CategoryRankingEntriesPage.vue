<template>
    <ranking-entries-page
        v-if="loaded"
        :loaded="loaded"
        :key="leaderboard_type.name"
        :category_name="leaderboard_type.name"
        :category_display_name="leaderboard_type.display_name"
        :header_columns="headerColumns"
        :dataset="dataset"
        :filter_records="filter_records"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row['categories'][leaderboard_type.name].rank }}
            </td>
            <td>
                <player-profile-modal
                    :player="row.player"
                    :leaderboard_source="$store.getters['leaderboard_sources/getSelected']"
                ></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row['categories'][leaderboard_type.name].points"></rounded-decimal>
            </td>
            <td
                v-for="details_column in details_columns"
                :key="details_column.name"
            >
                <details-column
                    :details_name="details_column.name"
                    :details_value="row['categories'][leaderboard_type.name]['details'][details_column.name]"
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
    </ranking-entries-page>
</template>

<script>
import BasePage from '../BasePage.vue';
import RankingEntriesPage from './RankingEntriesPage.vue';
import Dataset from '../../../datasets/Dataset.js';
import PlayerProfileModal from '../../player/PlayerProfileModal.vue';
import RoundedDecimal from '../../formatting/RoundedDecimal.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';
import RankingCategorySummaryDetailsTable from '../../table/RankingCategorySummaryDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'score-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'details-column': DetailsColumn,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable
    },
    data() {
        return {
            leaderboard_type: {},
            dataset: {},
            filter_records: [
                {
                    name: 'leaderboard_source',
                    store_name: 'leaderboard_sources'
                },
                {
                    name: 'leaderboard_type',
                    store_name: 'leaderboard_types'
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
                    name: 'seeded_type',
                    store_name: 'seeded_types'
                },
                {
                    name: 'multiplayer_type',
                    store_name: 'multiplayer_types'
                },
                {
                    name: 'soundtrack',
                    store_name: 'soundtracks'
                }
            ],
            details_columns: [],
            details_table_rows: [
                {
                    name: 'rank',
                    display_name: 'Rank'
                },
                {
                    name: 'points',
                    display_name: 'Points',
                    rounded: true
                }
            ]
        }
    },
    computed: {
        apiRequestParameters() {
            return {
                leaderboard_type: this.leaderboard_type.name
            };
        },
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
        },
        characters() {
            return this.$store.getters['characters/getFiltered'];
        }
    },
    methods: {
        loadState(route_params) {
            this.$store.commit('characters/setFilterStores', [
                'leaderboard_sources',
                'releases',
                'leaderboard_types',
                'modes'
            ]);

            this.leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];

            this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);

            this.dataset = new Dataset('category_ranking_entries', '/api/1/rankings/category/entries');

            this.loaded = true;
        }
    }
};
</script>

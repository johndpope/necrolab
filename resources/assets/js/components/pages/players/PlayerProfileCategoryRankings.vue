<template>
    <with-nav-body
        :loaded="loaded"
        :sub_title="leaderboard_type.display_name + ' Rankings'"
        :show_breadcrumbs="false"
    >
        <player-category-ranking-stats-chart
                :dataset="dataset"
                :leaderboard_type="leaderboard_type"
                :characters="$store.getters['characters/getFiltered']"
                :details_columns_by_name="$store.getters['details_columns/getAllByName']"
        >
        </player-category-ranking-stats-chart>
        <br />
        <necrotable
            :key="$route.fullPath"
            :dataset="dataset"
            :header_columns="headerColumns"
            :has_action_column="true"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.date }}
                </td>
                <td>
                    {{ row.categories[leaderboard_type.name].rank }}
                </td>
                <td>
                    <rounded-decimal :original_number="row.categories[leaderboard_type.name].points"></rounded-decimal>
                </td>
                <td
                    v-for="details_column in details_columns"
                    :key="details_column.name"
                >
                    <details-column
                        :details_name="details_column.name"
                        :details_value="row.categories[leaderboard_type.name].details[details_column.name]"
                    >
                    </details-column>
            </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <ranking-category-summary-details-table
                    :characters="$store.getters['characters/getFiltered']"
                    :record="row.characters"
                    :leaderboard_type="leaderboard_type"
                    :details_columns="details_columns"
                    :rows="details_table_rows"
                >
                </ranking-category-summary-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import Dataset from '../../../datasets/Dataset.js';
import PlayerCategoryRankingStatsChart from '../../charts/PlayerCategoryRankingStatsChart.vue';
import NecroTable from '../../table/NecroTable.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import RoundedDecimal from '../../formatting/RoundedDecimal.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
import RankingCategorySummaryDetailsTable from '../../table/RankingCategorySummaryDetailsTable.vue';

const PlayerProfileCategoryRankings = {
    extends: BasePage,
    name: 'player-profile-category-rankings',
    components: {
        'with-nav-body': WithNavBody,
        'player-category-ranking-stats-chart': PlayerCategoryRankingStatsChart,
        'necrotable': NecroTable,
        'rounded-decimal': RoundedDecimal,
        'details-column': DetailsColumn,
        'toggle-details': ToggleDetails,
        'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable
    },
    data() {
        return {
            dataset: {},
            player_id: '',
            leaderboard_source: {},
            leaderboard_type: {},
            details_columns: [],
            filters: [
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
            ],
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
        breadcrumbs() {
            return [
                {
                    text: 'Leaderboards'
                },
                {
                    text: this.leaderboard_type.display_name,
                    href: '/leaderboards/' + this.leaderboard_type.name
                }
            ];
        },
        headerColumns() {
            const header_columns = [
                'Date',
                'Rank',
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
                'releases'
            ]);

            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.player_id = route_params.player_id,
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            this.leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];

            this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);

            this.dataset = new Dataset('player_category_rankings', '/api/1/player/rankings/category/entries');

            this.dataset.disableServerPagination();
            this.dataset.setRequestParameter('player_id', this.player_id);
            this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);
            this.dataset.setRequestParameter('leaderboard_type', this.leaderboard_type.name);

            this.loaded = true;
        }
    }
};

export default PlayerProfileCategoryRankings;
</script>

<template>
    <with-nav-body
        :loaded="loaded"
        sub_title="Daily Rankings"
        :show_breadcrumbs="false"
    >
        <player-daily-ranking-stats-chart
            :dataset="dataset"
            :leaderboard_type="leaderboard_type"
            :details_columns_by_name="$store.getters['details_columns/getAllByName']"
        >
        </player-daily-ranking-stats-chart>
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
                    {{ row.rank }}
                </td>
                <td>
                    <rounded-decimal :original_number="row.points"></rounded-decimal>
                </td>
                <td
                    v-for="details_column in details_columns"
                    :key="details_column.name"
                >
                    <details-column
                        :details_name="details_column.name"
                        :details_value="getDetailsValue(row, details_column.name)"
                    >
                    </details-column>
            </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <daily-ranking-details-table :record="row">
                </daily-ranking-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import Dataset from '../../../datasets/Dataset.js';
import PlayerDailyRankingStatsChart from '../../charts/PlayerDailyRankingStatsChart.vue';
import NecroTable from '../../table/NecroTable.vue';
import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import NumberOfDaysDropdownFilter from '../../table/filters/NumberOfDaysDropdownFilter.vue';
import RoundedDecimal from '../../formatting/RoundedDecimal.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
import DailyRankingDetailsTable from '../../table/DailyRankingDetailsTable.vue';

const PlayerProfileDailyRankings = {
    extends: BasePage,
    name: 'player-profile-daily-rankings',
    components: {
        'with-nav-body': WithNavBody,
        'player-daily-ranking-stats-chart': PlayerDailyRankingStatsChart,
        'necrotable': NecroTable,
        'rounded-decimal': RoundedDecimal,
        'details-column': DetailsColumn,
        'toggle-details': ToggleDetails,
        'daily-ranking-details-table': DailyRankingDetailsTable
    },
    data() {
        return {
            dataset: {},
            player_id: '',
            leaderboard_source: {},
            leaderboard_type: {},
            details_columns: [],
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter,
                NumberOfDaysDropdownFilter
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
                'leaderboard_types',
                'releases'
            ]);

            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.player_id = route_params.player_id,
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];

            this.$store.commit('leaderboard_types/setSelected', 'daily');

            this.leaderboard_type = this.$store.getters['leaderboard_types/getByName']('daily');

            this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);

            this.dataset = new Dataset('player_daily_rankings', '/api/1/player/rankings/daily/entries');

            this.dataset.disableServerPagination();
            this.dataset.setRequestParameter('player_id', this.player_id);
            this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);

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
        }
    }
};

export default PlayerProfileDailyRankings;
</script>

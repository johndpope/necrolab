<template>
    <with-nav-body
        :loaded="loaded"
        :show_breadcrumbs="false"
        sub_title="Stats By Release"
    >
        <player-stats-chart
            :dataset="dataset"
            :details_columns="details_columns"
        >
        </player-stats-chart>
        <br />
        <necrotable
            :dataset="dataset"
            :header_columns="headerColumns"
            :has_pagination="true"
            :has_action_column="true"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.date }}
                </td>
                <td>
                    {{ row.pbs }}
                </td>
                <td>
                    {{ row.leaderboards }}
                </td>
                <td>
                    {{ row.first_place_ranks }}
                </td>
                <td>
                    {{ row.dailies }}
                </td>
                <td>
                    {{ row.seeded_pbs }}
                </td>
                <td>
                    {{ row.unseeded_pbs }}
                </td>
                <td v-for="details_column in details_columns">
                    <details-column
                        :details_name="details_column.name"
                        :details_value="row.details[details_column.name] != null ? row.details[details_column.name] : ''"
                    >
                    </details-column>
                </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <player-stats-details-table :record="row">
                </player-stats-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import PlayerLinkedSites from '../../player/PlayerLinkedSites.vue';
import Dataset from '../../../datasets/Dataset.js';
import PlayerStatsChart from '../../charts/PlayerStatsChart.vue';
import NecroTable from '../../table/NecroTable.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
import PlayerStatsDetailsTable from '../../table/PlayerStatsDetailsTable.vue';

const PlayerProfileStatsByRelease = {
    extends: BasePage,
    name: 'player-profile-info',
    components: {
        'with-nav-body': WithNavBody,
        'player-linked-sites': PlayerLinkedSites,
        'player-stats-chart': PlayerStatsChart,
        'necrotable': NecroTable,
        'details-column': DetailsColumn,
        'toggle-details': ToggleDetails,
        'player-stats-details-table': PlayerStatsDetailsTable
    },
    data() {
        return {
            player_id: '',
            leaderboard_source: {},
            player: {},
            details_columns: [],
            dataset: {},
            filters: [
                ReleaseDropdownFilter
            ]
        }
    },
    computed: {
        headerColumns() {
            const header_columns = [
                'Date',
                'PBs',
                'Leaderboards',
                'WRs',
                'Dailies',
                'Seeded PBs',
                'Unseeded PBs'
            ];

            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });

            return header_columns;
        }
    },
    methods: {
        loadState(route_params) {
            this.player_id = route_params.player_id;
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];

            this.player = this.$store.getters['players/get'](this.leaderboard_source.name, this.player_id);

            this.details_columns = this.$store.getters['details_columns/getAll'];

            this.$store.commit('releases/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.dataset = new Dataset('player_stats', '/api/1/player/stats/by_release');

            this.dataset.disableServerPagination();
            this.dataset.setRequestParameter('player_id', this.player_id);
            this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);

            this.loaded = true;
        }
    }
};

export default PlayerProfileStatsByRelease;
</script>

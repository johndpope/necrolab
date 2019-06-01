<template>
    <with-nav-body
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Power Rankings"
    >
        <power-ranking-stats-chart
            :dataset="dataset"
            :leaderboard_types="$store.getters['leaderboard_types/getAll']"
            :details_columns_by_name="$store.getters['details_columns/getAllByName']"
        >
        </power-ranking-stats-chart>
        <br />
        <necrotable
            :dataset="dataset"
            :header_columns="header_columns"
            :has_action_column="true"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    <router-link :to="getEntriesUrl(row.date)">
                        {{ row.date }}
                    </router-link>
                </td>
                <td>
                    {{ row.players }}
                </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <ranking-summary-details-table
                    :leaderboard_types="$store.getters['leaderboard_types/getFiltered']"
                    :record="row"
                    :rows="details_table_rows"
                >
                </ranking-summary-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import Dataset from '../../../datasets/Dataset.js';
import PowerRankingStatsChart from '../../charts/PowerRankingStatsChart.vue';
import NecroTable from '../../table/NecroTable.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
import RankingSummaryDetailsTable from '../../table/RankingSummaryDetailsTable.vue'

export default {
    extends: BasePage,
    name: 'power-rankings-page',
    components: {
        'with-nav-body': WithNavBody,
        'power-ranking-stats-chart': PowerRankingStatsChart,
        'necrotable': NecroTable,
        'toggle-details': ToggleDetails,
        'ranking-summary-details-table': RankingSummaryDetailsTable
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: 'Power',
                    href: '#/rankings/power'
                }
            ],
            dataset: {},
            header_columns: [
                'Date',
                'Players'
            ],
            details_table_rows: [
                {
                    name: 'players',
                    display_name: 'Players'
                }
            ],
            filters: [
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
            ],
            url_segment_stores: [
                'releases',
                'modes',
                'seeded_types',
                'multiplayer_types',
                'soundtracks'
            ]
        }
    },
    methods: {
        getEntriesUrl(date) {
            let url_segments = [];

            this.url_segment_stores.forEach((url_segment_store) => {
                let selected = this.$store.getters[`${url_segment_store}/getSelected`];

                url_segments.push(selected.name);
            });

            return `/rankings/power/${this.$route.params.leaderboard_source}/${url_segments.join('/')}/${date}`;
        },
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

            this.dataset = new Dataset('power_rankings', '/api/1/rankings/power');

            this.dataset.disablePagination();
            this.dataset.setRequestParameter('leaderboard_source', route_params.leaderboard_source);

            this.loaded = true;
        }
    }
};
</script>

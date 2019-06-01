<template>
    <with-nav-body
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Daily Rankings"
    >
        <daily-ranking-stats-chart
            :dataset="dataset"
            :leaderboard_type="leaderboard_type"
            :details_columns_by_name="$store.getters['details_columns/getAllByName']"
        >
        </daily-ranking-stats-chart>
        <br />
        <necrotable
            :dataset="dataset"
            :header_columns="headerColumns"
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
                <td v-for="details_column in details_columns">
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
                <daily-ranking-summary-details-table :record="row">
                </daily-ranking-summary-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
    import BasePage from '../BasePage.vue';
    import WithNavBody from '../../layouts/WithNavBody.vue';
    import Dataset from '../../../datasets/Dataset.js';
    import DailyRankingStatsChart from '../../charts/DailyRankingStatsChart.vue';
    import NecroTable from '../../table/NecroTable.vue';
    import DailyRankingSummaryDetailsTable from '../../table/DailyRankingSummaryDetailsTable.vue';
    import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
    import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
    import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
    import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
    import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
    import NumberOfDaysDropdownFilter from '../../table/filters/NumberOfDaysDropdownFilter.vue';
    import DetailsColumn from '../../formatting/DetailsColumn.vue';
    import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';

    export default {
        extends: BasePage,
        name: 'daily-rankings-page',
        components: {
            'with-nav-body': WithNavBody,
            'daily-ranking-stats-chart': DailyRankingStatsChart,
            'necrotable': NecroTable,
            'toggle-details': ToggleDetails,
            'daily-ranking-summary-details-table': DailyRankingSummaryDetailsTable,
            'details-column': DetailsColumn
        },
        data() {
            return {
                breadcrumbs: [
                    {
                        text: 'Rankings'
                    },
                    {
                        text: 'Daily',
                        href: '#/rankings/daily'
                    }
                ],
                dataset: {},
                leaderboard_type: {},
                details_columns: [],
                details_table_rows: [
                    {
                        name: 'players',
                        display_name: 'Players'
                    }
                ],
                filters: [
                    CharacterDropdownFilter,
                    ReleaseDropdownFilter,
                    ModeDropdownFilter,
                    MultiplayerTypeDropdownFilter,
                    SoundtrackDropdownFilter,
                    NumberOfDaysDropdownFilter
                ],
                url_segment_stores: [
                    'characters',
                    'releases',
                    'modes',
                    'multiplayer_types',
                    'soundtracks',
                    'number_of_days'
                ]
            }
        },
        computed: {
            headerColumns() {
                const header_columns = [
                    'Rank',
                    'Players'
                ];

                this.details_columns.forEach((details_column) => {
                    header_columns.push(details_column.display_name);
                });

                return header_columns;
            }
        },
        methods: {
            getEntriesUrl(date) {
                let url_segments = [];

                this.url_segment_stores.forEach((url_segment_store) => {
                    let selected = this.$store.getters[`${url_segment_store}/getSelected`];

                    url_segments.push(selected.name);
                });

                return `/rankings/daily/${this.$route.params.leaderboard_source}/${url_segments.join('/')}/${date}`;
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
            loadState(route_params) {
                this.$store.commit('releases/setFilterStores', [
                    'leaderboard_sources'
                ]);

                this.$store.commit('characters/setFilterStores', [
                    'leaderboard_sources',
                    'releases',
                    'leaderboard_types',
                    'modes'
                ]);

                this.$store.commit('modes/setFilterStores', [
                    'leaderboard_types',
                    'releases'
                ]);

                this.$store.commit('multiplayer_types/setFilterStores', [
                    'leaderboard_sources'
                ]);

                this.$store.commit('leaderboard_types/setSelected', 'daily');

                this.leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];

                this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);

                this.dataset = new Dataset('daily_rankings', '/api/1/rankings/daily');

                this.dataset.disablePagination();
                this.dataset.setRequestParameter('leaderboard_source', route_params.leaderboard_source);

                this.loaded = true;
            }
        }
    };
</script>
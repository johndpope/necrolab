<template>
    <with-nav-body
        :loaded="loaded"
        :key="leaderboard_type.name"
        :breadcrumbs="breadcrumbs"
        :title="`${leaderboard_type.display_name} Rankings`"
    >
        <category-ranking-stats-chart
            :dataset="dataset"
            :leaderboard_type="leaderboard_type"
            :characters="$store.getters['characters/getFiltered']"
            :details_columns_by_name="$store.getters['details_columns/getAllByName']"
        >
        </category-ranking-stats-chart>
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
                    {{ getPlayers(row, leaderboard_type.name) }}
                </td>
                <td v-for="details_column in details_columns">
                    <details-column
                        :details_name="details_column.name"
                        :details_value="getDetailsValue(row, leaderboard_type.name, details_column.name)"
                    >
                    </details-column>
                </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
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
        </necrotable>
    </with-nav-body>
</template>

<script>
    import BasePage from '../BasePage.vue';
    import WithNavBody from '../../layouts/WithNavBody.vue';
    import Dataset from '../../../datasets/Dataset.js';
    import CategoryRankingStatsChart from '../../charts/CategoryRankingStatsChart.vue';
    import NecroTable from '../../table/NecroTable.vue';
    import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
    import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
    import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
    import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
    import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
    import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
    import RankingCategorySummaryDetailsTable from '../../table/RankingCategorySummaryDetailsTable.vue';
    import DetailsColumn from '../../formatting/DetailsColumn.vue';

    export default {
        extends: BasePage,
        name: 'power-rankings-page',
        components: {
            'with-nav-body': WithNavBody,
            'category-ranking-stats-chart': CategoryRankingStatsChart,
            'necrotable': NecroTable,
            'toggle-details': ToggleDetails,
            'ranking-category-summary-details-table': RankingCategorySummaryDetailsTable,
            'details-column': DetailsColumn
        },
        data() {
            return {
                leaderboard_type: {},
                details_columns: [],
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
        computed: {
            headerColumns() {
                const header_columns = [
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
            getEntriesUrl(date) {
                let url_segments = [];

                this.url_segment_stores.forEach((url_segment_store) => {
                    let selected = this.$store.getters[`${url_segment_store}/getSelected`];

                    url_segments.push(selected.name);
                });

                return `/rankings/${this.$route.params.leaderboard_type}/${this.$route.params.leaderboard_source}/${url_segments.join('/')}/${date}`;
            },
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

                this.dataset = new Dataset('power_rankings', '/api/1/rankings/power');


                this.dataset.disablePagination();
                this.dataset.setRequestParameter('leaderboard_source', route_params.leaderboard_source);

                this.loaded = true;
            }
        }
    };
</script>
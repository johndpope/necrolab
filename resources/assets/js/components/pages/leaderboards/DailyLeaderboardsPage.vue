<template>
    <with-nav-body 
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Daily Leaderboards"
    >
        <stats-chart 
            :dataset="dataset"
            :details_columns="details_columns"
        >
        </stats-chart>
        <necrotable 
            :dataset="dataset"
            :header_columns="headerColumns" 
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
                        :details_value="row.details[details_column.name] != null ? row.details[details_column.name] : ''"
                    >
                    </details-column>
                </td>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import Dataset from '../../../datasets/Dataset.js';
import LeaderboardSnapshotStatsChart from '../../charts/LeaderboardSnapshotStatsChart.vue';
import NecroTable from '../../table/NecroTable.vue';
import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';

const LeaderboardSnapshotsPage = {
    extends: BasePage,
    name: 'daily-leaderboards-page',
    components: {
        'with-nav-body': WithNavBody,
        'stats-chart': LeaderboardSnapshotStatsChart,
        'necrotable': NecroTable,
        'details-column': DetailsColumn
    },
    data() {
        return {
            dataset: {},
            leaderboard_source: {},
            leaderboard_type: {},
            details_columns: [],
            breadcrumbs: [
                {
                    text: 'Leaderboards'
                },
                {
                    text: 'Daily',
                    href: '#/leaderboards/daily'
                }   
            ],
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
            ]
        }
    },
    computed: {
        apiRequestParameters() {
            return {
                leaderboard_source: this.leaderboard_source.name
            };
        },
        headerColumns() {
            const header_columns = [
                'Date',
                'Player'
            ];
            
            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });
            
            return header_columns;
        }
    },
    methods: {
        getEntriesUrl(date) {            
            return '/leaderboards/daily/' + 
                this.leaderboard_source.name + '/' + 
                this.$store.getters['characters/getSelected'].name + '/' + 
                this.$store.getters['releases/getSelected'].name + '/' + 
                this.$store.getters['modes/getSelected'].name + '/' + 
                this.$store.getters['multiplayer_types/getSelected'].name + '/' +
                this.$store.getters['soundtracks/getSelected'].name + '/' +
                date;
        },
        loadState(route_params) {
            this.$store.commit('characters/setFilterStores', [
                'leaderboard_sources',
                'leaderboard_types',
                'releases',
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

            this.$store.commit('leaderboard_types/setSelected', 'daily');
            
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            this.leaderboard_type = this.$store.getters['leaderboard_types/getByName']('daily');
            this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);
            
            this.dataset = new Dataset('leaderboard_snapshots', '/api/1/leaderboards/daily');
                
            this.dataset.disablePagination();
            this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);
            
            this.loaded = true;
        }
    }
};

export default LeaderboardSnapshotsPage;
</script> 

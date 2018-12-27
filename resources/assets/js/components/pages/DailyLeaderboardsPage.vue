<template>
    <with-nav-layout 
        v-if="loaded"
        :breadcrumbs="breadcrumbs"
        title="Daily Leaderboards"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url"
            :default_request_parameters="apiRequestParameters"
            :header_columns="header_columns" 
            :filters="filters"
            :has_server_pagination="false"
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
                <td>
                    <template v-if="leaderboard_details_column.data_type == 'seconds'">
                        <seconds-to-time 
                            :unformatted="row[leaderboard_details_column.name]" 
                            :include_hours="true" 
                            :zero_pad_hours="true"
                        >
                        </seconds-to-time>
                    </template>
                    <template v-else>
                        {{ row[leaderboard_details_column.name] }}
                    </template>
                </td>  
            </template>
        </necrotable>
    </with-nav-layout>
</template>

<script>
import BasePage from './BasePage.vue';
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import CharacterDropdownFilter from '../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../table/filters/MultiplayerTypeDropdownFilter.vue';
import SecondsToTime from '../formatting/SecondsToTime';

const LeaderboardSnapshotsPage = {
    extends: BasePage,
    name: 'daily-leaderboards-page',
    components: {
        'with-nav-layout': WithNavLayout,
        'necrotable': NecroTable,
        'seconds-to-time': SecondsToTime
    },
    data() {
        return {
            leaderboard_source: {},
            leaderboard_type: {},
            leaderboard_details_column: {},
            breadcrumbs: [
                {
                    text: 'Leaderboards'
                },
                {
                    text: 'Daily',
                    href: '#/leaderboards/daily'
                }   
            ],
            api_endpoint_url: '/api/1/leaderboards/daily',
            header_columns: [
                'Date',
                'Players',
                'Score'
            ],
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                MultiplayerTypeDropdownFilter
            ]
        }
    },
    computed: {
        apiRequestParameters() {
            return {
                leaderboard_source: this.leaderboard_source.name
            };
        }
    },
    methods: {
        getEntriesUrl(date) {
            return '/leaderboards/daily/' + this.$store.getters['releases/getSelected'] + '/' + date;
        },
        loadState(route_params) {
            let promise = this.$store.dispatch('page/loadModules', [
                'leaderboard_sources',
                'leaderboard_types',
                'characters',
                'releases',
                'modes',
                'multiplayer_types',
                'leaderboard_details_columns'
            ]);

            promise.then(() => {
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

                this.$store.commit('leaderboard_sources/setSelected', route_params.leaderboard_source);
                this.$store.commit('leaderboard_types/setSelected', 'daily');
                
                this.leaderboard_source = this.$store.getters['leaderboard_sources/getByName'](route_params.leaderboard_source);
                this.leaderboard_type = this.$store.getters['leaderboard_types/getByName']('daily');
                this.leaderboard_details_column = this.$store.getters['leaderboard_details_columns/getByName'](this.leaderboard_type.details_column_name);
                
                this.loaded = true;
            });
        }
    }
};

export default LeaderboardSnapshotsPage;
</script> 

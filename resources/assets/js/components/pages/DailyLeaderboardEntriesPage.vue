<template>
    <with-nav-layout 
        v-if="loaded"
        :breadcrumbs="breadcrumbs"
        title="Daily Leaderboard Entries"
        :sub_title="sub_title"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url"
            :header_columns="headerColumns" 
            :has_search="true" 
            :has_action_column="true" 
            :filters="filters"
            :default_request_parameters="apiRequestParameters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.rank }}
                </td>
                <td>
                    <player-profile-modal :player="row.player"></player-profile-modal>
                </td>
                <td>
                    <template v-if="leaderboard_details_column.data_type == 'seconds'">
                        <seconds-to-time 
                            :unformatted="row.pb[leaderboard_details_column.name]" 
                            :include_hours="true"
                        >
                        </seconds-to-time>
                    </template>
                    <template v-else>
                        {{ row.pb[leaderboard_details_column.name] }}
                    </template>
                </td>
                <td v-if="leaderboard_type.show_seed === 1">
                    <seed :record="row"></seed>
                </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <leaderboard-entry-details-table :record="row">
                </leaderboard-entry-details-table>
            </template>
        </necrotable>
    </with-nav-layout>
</template>

<script>
import BasePage from './BasePage.vue';
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import Seed from '../leaderboards/Seed.vue';
import SecondsToTime from '../formatting/SecondsToTime';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import LeaderboardEntryDetailsTable from '../table/LeaderboardEntryDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'daily-leaderboard-entries-page',
    components: {
        'with-nav-layout': WithNavLayout,
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'seed': Seed,
        'seconds-to-time': SecondsToTime,
        'toggle-details': ToggleDetails,
        'leaderboard-entry-details-table': LeaderboardEntryDetailsTable
    },
    data() {
        return {
            date: '',
            leaderboard_source: {},
            character: {},
            release: {},
            mode: {},
            multiplayer_type: {},
            leaderboard_type: {},
            leaderboard_details_column: {},
            api_endpoint_url: '/api/1/leaderboards/daily/entries',
            filters: [
                SiteDropdownFilter
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
                    text: 'Daily',
                    href: '#/leaderboards/daily'
                },
                {
                    text: 'Entries - ' + this.sub_title
                }
            ];
        },
        apiRequestParameters() {
            return {
                leaderboard_source: this.leaderboard_source.name,
                character: this.character.name,
                release: this.release.name,
                mode: this.mode.name,
                multiplayer_type: this.multiplayer_type.name,
                date: this.date
            };
        },
        sub_title() {
            return this.leaderboard_source.display_name + ' ' + 
                this.character.display_name + ' ' + 
                this.release.display_name + ' ' + 
                this.mode.display_name + ' ' + 
                this.multiplayer_type.display_name + ' ' + 
                this.$route.params.date;
        },
        headerColumns() {
            let header_columns = [
                'Rank',
                'Player',
                this.leaderboard_details_column.display_name
            ];
            
            if(this.leaderboard_type.show_seed === 1) {
                header_columns.push('Seed');
            }
            
            return header_columns;
        }
    },
    methods: {
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
                this.character = this.$store.getters['characters/getByName'](route_params.character);
                this.release = this.$store.getters['releases/getByName'](route_params.release);
                this.mode = this.$store.getters['modes/getByName'](route_params.mode);
                this.multiplayer_type = this.$store.getters['multiplayer_types/getByName'](route_params.multiplayer_type);
                this.leaderboard_details_column = this.$store.getters['leaderboard_details_columns/getByName'](this.leaderboard_type.details_column_name);
                
                this.date = route_params.date;
                
                this.loaded = true;
            });
        }
    }
};
</script>

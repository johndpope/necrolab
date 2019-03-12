<template>
    <with-nav-body 
        :loaded="loaded"
        :key="leaderboard_type.name"
        :sub_title="leaderboard_type.display_name + ' Leaderboards'"
        :show_breadcrumbs="false"
    >
        <necrotable 
            :api_endpoint_url="apiEndpointUrl"
            :header_columns="headerColumns" 
            :default_request_parameters="apiRequestParameters"
            :has_pagination="false"
            :has_action_column="leaderboard_type.show_zone_level === 1"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td v-if="leaderboard_type.name != 'daily'">
                    <character-icon-selector :name="row.pb.character" :display_name="getCharacterDisplayName(row.pb.character)">
                    </character-icon-selector>
                </td>
                <td v-if="leaderboard_type.name == 'daily'">
                    {{ row.pb.date }}
                </td>
                <td>
                    {{ row.rank }}
                </td>
                <td v-for="details_column in details_columns">
                    <details-column
                        :details_name="details_column.name"
                        :details_value="row.pb.details[details_column.name] != null ? row.pb.details[details_column.name] : ''"
                    >
                    </details-column>
                </td>
                <td v-if="leaderboard_type.show_seed === 1">
                    <seed :record="row"></seed>
                </td>
                <td v-if="leaderboard_type.show_replay === 1">
                    <replay-download-link :record="row"></replay-download-link>
                </td>
            </template>
            <template v-if="leaderboard_type.show_zone_level === 1" slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template v-if="leaderboard_type.show_zone_level === 1" slot="row-details" slot-scope="{ row }">
                <leaderboard-entry-details-table :record="row">
                </leaderboard-entry-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import NecroTable from '../../table/NecroTable.vue';
import Datepicker from '../../date/Datepicker.vue';
import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import CharacterIconSelector from '../../characters/CharacterIconSelector.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';
import Seed from '../../leaderboards/Seed.vue';
import ReplayDownloadLink from '../../leaderboards/ReplayDownloadLink.vue';
import LeaderboardEntryDetailsTable from '../../table/LeaderboardEntryDetailsTable.vue';

const PlayerProfileLeaderboards = {
    extends: BasePage,
    name: 'player-profile-leaderboards',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'character-icon-selector': CharacterIconSelector,
        'details-column': DetailsColumn,
        'seed': Seed,
        'replay-download-link': ReplayDownloadLink,
        'toggle-details': ToggleDetails,
        'leaderboard-entry-details-table': LeaderboardEntryDetailsTable
    },
    props: {
        name: {
            type: String,
            default: ''
        },
        display_name: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            player_id: '',
            leaderboard_source: {},
            leaderboard_type: {},
            details_columns: []
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
        apiEndpointUrl() {
            let api_endpoint_url = '';
            
            if(this.leaderboard_type.name == 'daily') {
                api_endpoint_url = '/api/1/player/leaderboards/daily/entries';
            }
            else {
                api_endpoint_url = '/api/1/player/leaderboards/category/entries';
            }
            
            return api_endpoint_url;
        },
        apiRequestParameters() {
            return {
                player_id: this.player_id,
                leaderboard_source: this.leaderboard_source.name,
                leaderboard_type: this.leaderboard_type.name
            }
        },
        headerColumns() {
            const header_columns = [];
            
            if(this.leaderboard_type.name != 'daily') {
                header_columns.push('Character');
            }
            else {
                header_columns.push('Date');
            }
            
            header_columns.push('Rank');
            
            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });
            
            if(this.leaderboard_type.show_seed === 1) {
                header_columns.push('Seed');
            }
            
            if(this.leaderboard_type.show_replay === 1) {
                header_columns.push('Replay');
            }
            
            return header_columns;
        },
        filters() {
            const filters = [];
            
            if(this.leaderboard_type.name == 'daily') {
                filters.push(CharacterDropdownFilter);
            }
            else {
                filters.push(Datepicker);
            }
            
            filters.push(
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
            );
            
            return filters;
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
                'releases',
                'leaderboard_types'
            ]);
            
            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.player_id = route_params.player_id,
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            this.leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];
            
            this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);

            this.loaded = true;
        },
        getCharacterDisplayName(character_name) {
            return this.$store.getters['characters/getByName'](character_name).display_name;
        }
    }
};

export default PlayerProfileLeaderboards;
</script> 

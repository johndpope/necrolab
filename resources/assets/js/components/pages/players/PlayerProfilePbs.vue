<template>
    <with-nav-body 
        :loaded="loaded"
        :key="leaderboard_type.name"
        :sub_title="leaderboard_type.display_name + ' PBs'"
        :show_breadcrumbs="false"
    >
        <necrotable 
            api_endpoint_url="/api/1/player/pbs"
            :header_columns="headerColumns" 
            :default_request_parameters="apiRequestParameters"
            :has_pagination="false"
            :has_action_column="leaderboard_type.show_zone_level === 1"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.date }}
                </td>
                <td v-for="details_column in details_columns">
                    <details-column
                        :details_name="details_column.name"
                        :details_value="row.details[details_column.name] != null ? row.details[details_column.name] : ''"
                    >
                    </details-column>
                </td>
                <td v-if="leaderboard_type.show_seed === 1">
                    <seed :record="{ pb: row }"></seed>
                </td>
                <td v-if="leaderboard_type.show_replay === 1">
                    <replay-download-link :record="{ pb: row }"></replay-download-link>
                </td>
            </template>
            <template v-if="leaderboard_type.show_zone_level === 1" slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template v-if="leaderboard_type.show_zone_level === 1" slot="row-details" slot-scope="{ row }">
                <leaderboard-entry-details-table :record="{ pb: row } ">
                </leaderboard-entry-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import NecroTable from '../../table/NecroTable.vue';
import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';
import DetailsColumn from '../../formatting/DetailsColumn.vue';
import Seed from '../../leaderboards/Seed.vue';
import ReplayDownloadLink from '../../leaderboards/ReplayDownloadLink.vue';
import LeaderboardEntryDetailsTable from '../../table/LeaderboardEntryDetailsTable.vue';

const LeaderboardsPage = {
    extends: BasePage,
    name: 'leaderboards-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
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
            details_columns: [],
            filters: [
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                CharacterDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
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
        apiRequestParameters() {
            return {
                player_id: this.player_id,
                leaderboard_source: this.leaderboard_source.name,
                leaderboard_type: this.leaderboard_type.name
            }
        },
        headerColumns() {
            const header_columns = [
                'Date'
            ];
            
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
        }
    }
};

export default LeaderboardsPage;
</script> 

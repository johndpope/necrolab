<template>
    <with-nav-body 
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Leaderboard Entries"
        :sub_title="subTitle"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url"
            :header_columns="headerColumns" 
            :has_search="true" 
            :has_action_column="leaderboard.show_zone_level === 1"
            :default_request_parameters="apiRequestParameters"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.rank }}
                </td>
                <td>
                    <player-profile-modal 
                        :leaderboard_source="leaderboard_source"
                        :player="row.player"
                    >
                    </player-profile-modal>
                </td>
                <td v-for="details_column in details_columns">
                    <details-column
                        :details_name="details_column.name"
                        :details_value="row.pb.details[details_column.name] != null ? row.pb.details[details_column.name] : ''"
                    >
                    </details-column>
                </td>
                <td v-if="leaderboard.show_seed === 1">
                    <seed :record="row"></seed>
                </td>
                <td v-if="leaderboard.show_replay === 1">
                    <replay-download-link :record="row"></replay-download-link>
                </td>
            </template>
            <template v-if="leaderboard.show_zone_level === 1" slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template v-if="leaderboard.show_zone_level === 1" slot="row-details" slot-scope="{ row }">
                <leaderboard-entry-details-table :record="row">
                </leaderboard-entry-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import LeaderboardBasePage from './LeaderboardBasePage.vue';
import WithNavBody from '../layouts/WithNavBody.vue';
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import DetailsColumn from '../formatting/DetailsColumn.vue';
import Seed from '../leaderboards/Seed.vue';
import ReplayDownloadLink from '../leaderboards/ReplayDownloadLink.vue';
import LeaderboardEntryDetailsTable from '../table/LeaderboardEntryDetailsTable.vue';

const LeaderboardEntriesPage = {
    extends: LeaderboardBasePage,
    name: 'leaderboard-entries-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'details-column': DetailsColumn,
        'seed': Seed,
        'replay-download-link': ReplayDownloadLink,
        'toggle-details': ToggleDetails,
        'leaderboard-entry-details-table': LeaderboardEntryDetailsTable
    },
    data() {
        return {
            date: '',
            api_endpoint_url: '/api/1/leaderboard/entries',
            filters: [
                SiteDropdownFilter
            ]
        }
    },
    computed: {
        apiRequestParameters() {
            return {
                leaderboard_source: this.leaderboard_source.name,
                leaderboard_id: this.leaderboard.id,
                date: this.date
            };
        },
        breadcrumbs() {
            const snapshots_url = '#/leaderboards/' + this.name + '/' + this.$route.params.url_name + '/snapshots';
            
            return [
                {
                    text: 'Leaderboards'
                },
                {
                    text: 'Snapshots'
                },
                {
                    text: this.date,
                },
                {
                    text: this.subTitle
                }
            ]
        },
        headerColumns() {
            const header_columns = [
                'Rank',
                'Player'
            ];
            
            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });
            
            if(this.leaderboard.show_seed === 1) {
                header_columns.push('Seed');
            }
            
            if(this.leaderboard.show_replay === 1) {
                header_columns.push('Replay');
            }
            
            return header_columns;
        }
    },
    methods: {
        loadState(route_params) {
            this.loadRecords(route_params).then(() => {
                this.date = route_params.date;

                this.loaded = true;
            });
        }
    }
};

export default LeaderboardEntriesPage;
</script> 

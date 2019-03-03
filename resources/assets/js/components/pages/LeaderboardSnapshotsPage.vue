<template>
    <with-nav-body 
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Leaderboard Snapshots"
        :sub_title="subTitle"
    >
        <necrotable
            :api_endpoint_url="apiEndpointUrl" 
            :default_request_parameters="apiRequestParameters"
            :header_columns="headerColumns" 
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
import LeaderboardBasePage from './LeaderboardBasePage.vue';
import WithNavBody from '../layouts/WithNavBody.vue';
import NecroTable from '../table/NecroTable.vue';
import DetailsColumn from '../formatting/DetailsColumn.vue';

const LeaderboardSnapshotsPage = {
    extends: LeaderboardBasePage,
    name: 'leaderboard-snapshots-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'details-column': DetailsColumn
    },
    data() {
        return {};
    },
    computed: {
        apiEndpointUrl() {
            return '/api/1/leaderboard/snapshots';
        },
        apiRequestParameters() {
            return {
                leaderboard_source: this.leaderboard_source.name,
                leaderboard_id: this.leaderboard.id
            };
        },
        breadcrumbs() {
            return [
                {
                    text: 'Leaderboards'
                },
                {
                    text: 'Snapshots'
                },
                {
                    text: this.subTitle
                }
            ]
        },
        headerColumns() {
            const leaderboard_columns = [
                'Date',
                'Players'
            ];
            
            this.details_columns.forEach((details_column) => {
                leaderboard_columns.push(details_column.display_name);
            });
            
            return leaderboard_columns;
        }
    },
    methods: {
        loadState(route_params) {
            this.loadRecords(route_params).then(() => {
                this.loaded = true;
            });
        },
        getEntriesUrl(date) {
            return this.$route.path + '/' + date;
        }
    }
};

export default LeaderboardSnapshotsPage;
</script> 

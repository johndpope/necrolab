<template>
    <with-nav-body 
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Leaderboard Snapshots"
        :sub_title="subTitle"
    >
        <necrotable
            :api_endpoint_url="apiEndpointUrl" 
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
    </with-nav-body>
</template>

<script>
import LeaderboardBasePage from './LeaderboardBasePage.vue';
import WithNavBody from '../layouts/WithNavBody.vue';
import NecroTable from '../table/NecroTable.vue';
import SecondsToTime from '../formatting/SecondsToTime';

const LeaderboardSnapshotsPage = {
    extends: LeaderboardBasePage,
    name: 'leaderboard-snapshots-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'seconds-to-time': SecondsToTime
    },
    data() {
        return {};
    },
    computed: {
        apiEndpointUrl() {
            return '/api/1/leaderboards/' + this.leaderboard.id + '/snapshots';
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
            return [
                'Date',
                'Players',
                this.leaderboard_details_column.display_name
            ];
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

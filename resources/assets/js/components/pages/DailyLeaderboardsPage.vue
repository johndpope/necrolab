<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>
                    Daily Leaderboards
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable 
                    :api_endpoint_url="api_endpoint_url" 
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
                            {{ row.score }}
                        </td>  
                    </template>
                </necrotable>
            </div>
        </div>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';
import ntDateTimeFilter from '../table/filters/DateTimeFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';

const LeaderboardSnapshotsPage = {
    name: 'daily-leaderboards-page',
    components: {
        'necrotable': NecroTable
    },
    data() {
        return {
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
                ReleaseDropdownFilter,
                ntDateTimeFilter
            ]
        }
    },
    computed: {
        
    },
    methods: {
        getEntriesUrl(date) {
            return '/leaderboards/daily/' + this.$store.getters['releases/getSelected'] + '/' + date;
        }
    }
};

export default LeaderboardSnapshotsPage;
</script> 

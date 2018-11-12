<template>
    <with-nav-layout 
        :breadcrumbs="breadcrumbs"
        title="Daily Leaderboards"
    >
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
    </with-nav-layout>
</template>

<script>
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';

const LeaderboardSnapshotsPage = {
    name: 'daily-leaderboards-page',
    components: {
        'with-nav-layout': WithNavLayout,
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
                ReleaseDropdownFilter
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

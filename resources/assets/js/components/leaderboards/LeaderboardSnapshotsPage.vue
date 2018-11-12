<template>
    <with-nav-layout 
        :breadcrumbs="breadcrumbs"
        :title="leaderboard_display_name"
        :show_body="leaderboard['id'] != null"
    >
        <necrotable
            :api_endpoint_url="apiEndpointUrl" 
            :header_columns="header_columns" 
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
                    <slot name="details-column" :row="row">
                        {{ row[details_column_name] }}
                    </slot>
                </td>  
            </template>
        </necrotable>
    </with-nav-layout>
</template>

<script>
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';

const LeaderboardSnapshotsPage = {
    name: 'leaderboard-snapshots-page',
    components: {
        'with-nav-layout': WithNavLayout,
        'necrotable': NecroTable
    },
    props: {
        name: {
            type: String,
            default: ''
        },
        display_name: {
            type: String,
            default: ''
        },
        details_column_name: {
            type: String,
            default: ''
        },
        details_column_display_name: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            leaderboard: {},
            leaderboard_display_name: '',
            header_columns: [
                'Date',
                'Players',
                this.details_column_display_name
            ]
        }
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
                    text: this.display_name,
                    href: '#/leaderboards/' + this.name
                },
                {
                    text: this.leaderboard.display_name
                },
                {
                    text: 'Snapshots',
                    href: '#/leaderboards/' + this.name + '/' + this.$route.params.url_name + '/snapshots'
                }
            ]
        }
    },
    methods: {
        getEntriesUrl(date) {
            return '/leaderboards/' + this.name + '/' + this.leaderboard.url_name + '/snapshots/' + date;
        }
    },
    created() {
        let url_name = this.$route.params.url_name;
        
        this.$store.dispatch('leaderboards/load', url_name)
            .then(() => {                        
                this.leaderboard = this.$store.getters['leaderboards/getRecord'](url_name);
                
                this.leaderboard_display_name = this.leaderboard.display_name;
            });
    }
};

export default LeaderboardSnapshotsPage;
</script> 

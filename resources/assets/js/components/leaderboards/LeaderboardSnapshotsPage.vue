<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb v-if="leaderboard['id'] != null" :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1 v-if="leaderboard['id'] != null">
                    {{ leaderboard.display_name }}
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable 
                    v-if="leaderboard['id'] != null"
                    :api_endpoint_url="apiEndpointUrl" 
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
                            <slot name="details-column" :row="row">
                                {{ row[details_column_name] }}
                            </slot>
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

const LeaderboardSnapshotsPage = {
    name: 'leaderboard-snapshots-page',
    components: {
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
            header_columns: [
                'Date',
                'Players',
                this.details_column_display_name
            ],
            filters: [
                ntDateTimeFilter
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
            });
    }
};

export default LeaderboardSnapshotsPage;
</script> 

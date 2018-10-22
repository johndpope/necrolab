<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>{{ display_name }} Leaderboards</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable 
                    :api_endpoint_url="api_endpoint_url" 
                    :header_columns="header_columns" 
                    :has_pagination="false" 
                    :filters="filters" 
                    :data_processor="data_processor"
                >
                    <template slot="table-row" slot-scope="{ row_index, row }">
                        <td>
                            {{ row.name }}
                        </td>
                        <td>
                            <a v-if="row['leaderboard'] != null" :href="'/leaderboards/' + row.leaderboard.id + '/snapshots'" class="h3">
                                <right-arrow></right-arrow>
                            </a>
                        </td>
                    </template>
                </necrotable>
            </div>
        </div>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';

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
        has_seeded: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Leaderboards'
                },
                {
                    text: this.display_name,
                    href: '/leaderboards/' + this.name
                }
            ],
            api_endpoint_url: '/api/1/leaderboards/' + this.name,
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter
            ]
        }
    }
};

export default LeaderboardSnapshotsPage;
</script> 

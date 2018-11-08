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
                    :has_pagination="false" 
                    :filters="filters" 
                    :data_processor="dataProcessor"
                >
                    <template slot="table-row" slot-scope="{ row_index, row }">
                        <td>
                            {{ row.name }}
                        </td>
                        <td>
                            <a 
                                v-if="row['leaderboard'] != null" 
                                :href="getSnapshotsUrl(row.leaderboard)" 
                                class="h3" 
                                @click="$store.commit('leaderboards/setRecord', row.leaderboard)"
                            >
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
import CharacterDropdownFilter from '../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import RightArrow from '../formatting/RightArrow.vue';

const LeaderboardsListingPage = {
    name: 'leaderboards-listing-page',
    components: {
        'necrotable': NecroTable,
        'right-arrow': RightArrow
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
    },
    methods: {
        getSnapshotsUrl(leaderboard) {
            return '#/leaderboards/' + this.name + '/' + leaderboard.url_name + '/snapshots';
        },
        dataProcessor: function(leaderboards) {
            let all_zones_row = {
                name: 'Standard'
            };
            
            let seeded_row = {
                name: 'Seeded'
            };
            
            let custom_music_row = {
                name: 'Custom Music'
            };
            
            let seeded_custom_music_row = {
                name: 'Seeded Custom Music'
            };
            
            let co_op_row = {
                name: 'Co-Op'
            };
            
            let seeded_co_op_row = {
                name: 'Seeded Co-Op'
            };
            
            let co_op_custom_music_row = {
                name: 'Co-Op Custom Music'
            };
            
            let seeded_co_op_custom_music_row = {
                name: 'Seeded Co-Op Custom Music'
            };
            
            let leaderboards_length = leaderboards.length;
            
            for(let index = 0; index < leaderboards_length; index++) {
                let leaderboard = leaderboards[index];
                
                if(leaderboard.multiplayer_type == 'single') {
                    if(leaderboard.seeded_type == 'unseeded') {
                        // All zones
                        if(leaderboard.soundtrack == 'default') {
                            all_zones_row['leaderboard'] = leaderboard;
                        }
                        // Custom Music
                        else {
                            custom_music_row['leaderboard'] = leaderboard;
                        }
                    }
                    else {
                        // All zones seeded
                        if(leaderboard.soundtrack == 'default') {
                            seeded_row['leaderboard'] = leaderboard;
                        }
                        // Seeded custom music
                        else {
                            seeded_custom_music_row['leaderboard'] = leaderboard;
                        }
                    }
                }
                else {
                    if(leaderboard.seeded_type == 'unseeded') {
                        // Co-op all zopnes
                        if(leaderboard.soundtrack == 'default') {
                            co_op_row['leaderboard'] = leaderboard;
                        }
                        // Co-op custom music
                        else {
                            co_op_custom_music_row['leaderboard'] = leaderboard;
                        }
                    }
                    else {
                        // Seeded co-op
                        if(leaderboard.soundtrack == 'default') {
                            seeded_co_op_row['leaderboard'] = leaderboard;
                        }
                        // Seeded co-op custom music
                        else {
                            seeded_co_op_custom_music_row['leaderboard'] = leaderboard;
                        }
                    }
                }
            }
            
            let ordered_leaderboards = [];
            
            if(!this.has_seeded) {
                ordered_leaderboards = [
                    all_zones_row,
                    custom_music_row,
                    co_op_row,
                    co_op_custom_music_row,
                ];
            }
            else {
                ordered_leaderboards = [
                    all_zones_row,
                    seeded_row,
                    custom_music_row,
                    seeded_custom_music_row,
                    co_op_row,
                    seeded_co_op_row,
                    co_op_custom_music_row,
                    seeded_co_op_custom_music_row
                ];
            }
            
            return ordered_leaderboards;
        }
    }
};

export default LeaderboardsListingPage;
</script> 

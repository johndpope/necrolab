<template>
    <with-nav-layout 
        v-if="loaded"
        :breadcrumbs="breadcrumbs"
        :title="leaderboard_type.display_name + ' Leaderboards'"
    >
        <necrotable 
            api_endpoint_url="/api/1/leaderboards/category"
            :default_request_parameters="apiRequestParameters"
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
    </with-nav-layout>
</template>

<script>
import BasePage from './BasePage.vue';
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import CharacterDropdownFilter from '../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import RightArrow from '../formatting/RightArrow.vue';

const LeaderboardsPage = {
    extends: BasePage,
    name: 'leaderboards-page',
    components: {
        'with-nav-layout': WithNavLayout,
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
            leaderboard_type: {},
            leaderboard_source: {},
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter
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
                leaderboard_source: this.leaderboard_source.name,
                leaderboard_type: this.leaderboard_type.name
            }
        }
    },
    methods: {
        getSnapshotsUrl(leaderboard) { 
            return '#/leaderboards/' + 
                leaderboard.leaderboard_type + '/' +
                //TODO: update this to leaderboard.leaderboard_source when leaderboard sources are implemented for leaderboards
                this.leaderboard_source.name + '/' + 
                leaderboard.character + '/' +
                leaderboard.release + '/' +
                leaderboard.mode + '/' +
                leaderboard.seeded_type + '/' +
                leaderboard.multiplayer_type + '/' +
                leaderboard.soundtrack + '/' +
                'snapshots';
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
        },
        loadState(route_params) {
            let promise = this.$store.dispatch('page/loadModules', [
                'leaderboard_sources',
                'leaderboard_types',
                'characters',
                'releases',
                'modes',
                'seeded_types'
            ]);

            promise.then(() => {
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
                
                this.$store.commit('leaderboard_sources/setSelected', route_params.leaderboard_source);
                this.$store.commit('leaderboard_types/setSelected', route_params.leaderboard_type);

                this.leaderboard_source = this.$store.getters['leaderboard_sources/getByName'](route_params.leaderboard_source);
                this.leaderboard_type = this.$store.getters['leaderboard_types/getByName'](route_params.leaderboard_type);

                this.loaded = true;
            });
        }
    }
};

export default LeaderboardsPage;
</script> 

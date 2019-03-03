<template>
    <with-nav-body 
        :loaded="loaded"
        :key="leaderboard_type.name"
        :breadcrumbs="breadcrumbs"
        :title="leaderboard_type.display_name + ' Leaderboards'"
    >
        <necrotable 
            api_endpoint_url="/api/1/leaderboards/characters"
            :default_request_parameters="apiRequestParameters"
            :has_pagination="false" 
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    <character-icon-selector :name="row.character">
                    </character-icon-selector>
                </td>
                <td class="align-middle">
                    <a 
                        :href="getSnapshotsUrl(row)" 
                        @click="$store.commit('leaderboards/setRecord', row)"
                    >
                        View Snapshots
                        <!-- <right-arrow></right-arrow> -->
                    </a>
                </td>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from './BasePage.vue';
import WithNavBody from '../layouts/WithNavBody.vue';
import NecroTable from '../table/NecroTable.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../table/filters/SoundtrackDropdownFilter.vue';
import CharacterIconSelector from '../characters/CharacterIconSelector.vue';
import RightArrow from '../formatting/RightArrow.vue';

const LeaderboardsPage = {
    extends: BasePage,
    name: 'leaderboards-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'character-icon-selector': CharacterIconSelector
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
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
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
                this.leaderboard_source.name + '/' + 
                leaderboard.character + '/' +
                leaderboard.release + '/' +
                leaderboard.mode + '/' +
                leaderboard.seeded_type + '/' +
                leaderboard.multiplayer_type + '/' +
                leaderboard.soundtrack + '/' +
                'snapshots';
        },
        loadState(route_params) {
            this.$store.commit('releases/setFilterStores', [
                'leaderboard_sources'
            ]);
            
            this.$store.commit('modes/setFilterStores', [
                'releases',
                'leaderboard_types'
            ]);
            
            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            this.leaderboard_type = this.$store.getters['leaderboard_types/getSelected'];

            this.loaded = true;
        }
    }
};

export default LeaderboardsPage;
</script> 

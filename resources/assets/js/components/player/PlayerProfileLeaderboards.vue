<template>
    <div v-if="properties_loaded">
        <h2>{{ leaderboard_type.display_name }} Leaderboard Entries</h2>
        <necrotable
            :api_endpoint_url="apiEndpointUrl"
            :header_columns="headerColumns"
            :has_action_column="has_details_row"
            :default_request_parameters="apiRequestParameters"
            :filters="filters"
            :pagination="false"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    <character-icon-selector 
                        :name="row.pb.character"
                        :display_name="getCharacterByName(row.pb.character).display_name"
                    >
                    </character-icon-selector>
                </td>
                <td>
                    {{ row.rank }}
                </td>
                <td>
                    {{ row.pb[leaderboard_type.details_field_name] }}
                </td>
                <td v-if="has_seed">
                    <seed :record="row"></seed>
                </td>
                <td v-if="has_replay">
                    <replay-download-link :record="row"></replay-download-link>
                </td>
            </template>
            <template v-if="has_details_row" slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template v-if="has_details_row" slot="row-details" slot-scope="{ row }">
                <slot name="row-details" :row="row">
                    Override this slot to specify a details row.
                </slot>
            </template>
        </necrotable>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';
import DateTimeFilter from '../table/filters/DateTimeFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../table/filters/SoundtrackDropdownFilter.vue';
import CharacterIconSelector from '../characters/CharacterIconSelector.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import Seed from '../leaderboards/Seed.vue';
import ReplayDownloadLink from '../leaderboards/ReplayDownloadLink.vue';

const PlayerProfileLeaderboardsPage = {
    name: 'player-profile-leaderboards-page',
    components: {
        'necrotable': NecroTable,
        'seed': Seed,
        'replay-download-link': ReplayDownloadLink,
        'character-icon-selector': CharacterIconSelector,
        'toggle-details': ToggleDetails
    },
    props: {
        has_seed: {
            type: Boolean,
            default: true
        },
        has_replay: {
            type: Boolean,
            default: true
        },
        has_details_row: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            properties_loaded: false,
            leaderboard_type: {},
            leaderboard_source: {},
            filters: [
                DateTimeFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
            ]
        }
    },
    computed: {
        apiEndpointUrl() {
            let url = '/api/1/players';
            
            if(this.hasLeaderboardSource()) {
                url += '/' + this.$route.params.leaderboard_source;
            }
            
            url += '/' + this.$route.params.player_id + '/leaderboards/' + this.$route.params.leaderboard_type + '/entries';
            
            return url;
        },
        apiRequestParameters() {
            let parameters = {};
            
            if(this.hasLeaderboardSource()) {
                //parameters['leaderboard_source'] = this.$route.params.leaderboard_source;
            }
            
            return parameters;
        },
        /*breadcrumbs() {
            let snapshots_url = '#/leaderboards/' + this.name + '/' + this.$route.params.url_name + '/snapshots';
            
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
                    href: snapshots_url
                },
                {
                    text: this.date,
                    href: snapshots_url + '/' + this.date
                }
            ]
        },*/
        headerColumns() {
            let header_columns = [
                'Character',
                'Rank',
                this.leaderboard_type.display_name
            ];
            
            if(this.has_seed) {
                header_columns.push('Seed');
            }
            
            if(this.has_replay) {
                header_columns.push('Replay');
            }
            
            return header_columns;
        }
    },
    methods: {
        initializeProperties() {
            this.leaderboard_type = this.$store.getters['leaderboard_types/getByField']('name', this.$route.params.leaderboard_type);
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getByField']('name', this.$route.params.leaderboard_source);
            
            //Set if has seed
        
            //Set if has replay
            
            //Set if has details row
        },
        hasLeaderboardSource() {
            return this.$route.params['leaderboard_source'] != null;
        },
        getCharacterByName(character_name) {
            return this.$store.getters['characters/getByField']('name', character_name);
        }
    },
    created() {
        let promises = [
            this.$store.dispatch('leaderboard_sources/loadAll'),
            this.$store.dispatch('leaderboard_types/loadAll'),
            this.$store.dispatch('characters/loadAll')
        ];
        
        Promise.all(promises).then(() => {            
            this.initializeProperties();
            
            this.properties_loaded = true;
        });
    },
    beforeRouteUpdate(to, from, next) {
        this.initializeProperties();
        
        next();
    }
};

export default PlayerProfileLeaderboardsPage;
</script> 

<template>
    <with-nav-layout 
        :breadcrumbs="breadcrumbs"
        :title="leaderboard_display_name"
        :show_body="leaderboard['id'] != null"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url"
            :header_columns="headerColumns" 
            :has_search="true" 
            :has_action_column="has_details_row"
            :default_request_parameters="apiRequestParameters"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.rank }}
                </td>
                <td>
                    <player-profile-modal :player="row.player"></player-profile-modal>
                </td>
                <td>
                    <slot name="entry-details" :row="row">
                        Override this slot to specify entry details.
                    </slot>
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
    </with-nav-layout>
</template>

<script>
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import Seed from '../leaderboards/Seed.vue';
import ReplayDownloadLink from '../leaderboards/ReplayDownloadLink.vue';

const LeaderboardEntriesPage = {
    name: 'leaderboard-snapshots-page',
    components: {
        'with-nav-layout': WithNavLayout,
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'seed': Seed,
        'replay-download-link': ReplayDownloadLink,
        'toggle-details': ToggleDetails
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
        details_column_display_name: {
            type: String,
            default: ''
        },
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
            leaderboard: {},
            leaderboard_display_name: '',
            date: '',
            api_endpoint_url: '/api/1/leaderboards/entries',
            filters: [
                SiteDropdownFilter
            ]
        }
    },
    computed: {
        apiRequestParameters() {
            return {
                'lbid': this.leaderboard.id,
                'date': this.date
            };
        },
        breadcrumbs() {
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
        },
        headerColumns() {
            let header_columns = [
                'Rank',
                'Player',
                this.details_column_display_name
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
    created() {
        this.date = this.$route.params.date;
        
        let url_name = this.$route.params.url_name;
        
        this.$store.dispatch('leaderboards/load', url_name)
            .then(() => {                        
                this.leaderboard = this.$store.getters['leaderboards/getRecord'](url_name);
                
                this.leaderboard_display_name = this.date + ' - ' + leaderboard.display_name;
            });
    }
};

export default LeaderboardEntriesPage;
</script> 

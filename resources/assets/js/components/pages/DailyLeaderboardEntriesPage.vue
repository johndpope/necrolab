<template>
    <with-nav-layout 
        :breadcrumbs="breadcrumbs"
        :title="title"
        :show_body="release['id'] != null"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url" 
            :header_columns="header_columns" 
            :has_search="true" 
            :has_action_column="true" 
            :filters="filters"
            :default_request_parameters="apiRequestParameters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.rank }}
                </td>
                <td>
                    <player-profile-modal :player="row.player"></player-profile-modal>
                </td>
                <td>
                    {{ row.pb.score }}
                </td>
                <td>
                    <seed :record="row"></seed>
                </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <leaderboard-score-entry-details-table :record="row">
                </leaderboard-score-entry-details-table>
            </template>
        </necrotable>
    </with-nav-layout>
</template>

<script>
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import Seed from '../leaderboards/Seed.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import LeaderboardScoreEntryDetailsTable from '../table/LeaderboardScoreEntryDetailsTable.vue';

export default {
    name: 'daily-leaderboard-entries-page',
    components: {
        'with-nav-layout': WithNavLayout,
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'seed': Seed,
        'toggle-details': ToggleDetails,
        'leaderboard-score-entry-details-table': LeaderboardScoreEntryDetailsTable
    },
    data() {
        return {
            release: {},
            title: '',
            api_endpoint_url: '/api/1/leaderboards/daily/entries',
            filters: [
                SiteDropdownFilter
            ],
            header_columns: [
                'Rank',
                'Player',
                'Score',
                'Seed'
            ]
        }
    },
    computed: {
        breadcrumbs() {
            let breadcrumbs = [];
            
            if(this.release['id'] != null) {
                breadcrumbs = [
                    {
                        text: 'Leaderboards'
                    },
                    {
                        text: 'Daily',
                        href: '#/leaderboards/daily'
                    },
                    {
                        text: this.release.display_name
                    },
                    {
                        text: this.$route.params.date,
                        href: '#/leaderboards/daily/' + this.$route.params.release + '/' + this.$route.params.date
                    }
                ]
            }
            
            return breadcrumbs;
        },
        apiRequestParameters() {
            return {
                'release': this.$route.params.release,
                'date': this.$route.params.date
            };
        }
    },
    created() {
        this.$store.dispatch('releases/loadAll')
            .then(() => {                        
                this.release = this.$store.getters['releases/getByField']('name', this.$route.params.release);
                
                this.title = this.release.display_name + ' Daily Leaderboard - ' + this.$route.params.date;
            });
    }
};
</script>

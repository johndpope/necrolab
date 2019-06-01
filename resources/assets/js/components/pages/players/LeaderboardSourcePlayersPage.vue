<template>
    <with-nav-body 
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        title="Players"
        :sub_title="leaderboard_source.display_name"
    >
        <necrotable 
            :dataset="dataset"
            :header_columns="header_columns" 
            :has_search="true" 
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    <player-profile-modal 
                        :leaderboard_source="leaderboard_source"
                        :player="row"
                    >
                    </player-profile-modal>
                </td>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import NecroTable from '../../table/NecroTable.vue';
import SiteDropdownFilter from '../../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../../player/PlayerProfileModal.vue';
import Dataset from '../../../datasets/Dataset.js';

export default {
    extends: BasePage,
    name: 'LeaderboardSourcePlayersPage',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal
    },
    data() {
        return {
            dataset: {},
            leaderboard_source: {},
            api_endpoint_url: '/api/1/players',
            filters: [
                SiteDropdownFilter
            ],
            header_columns: [
                'Player'
            ]
        }
    },
    computed: {
        breadcrumbs() {
            return [
                {
                    text: 'Players',
                    href: '#'
                },
                {
                    text: this.leaderboard_source.display_name,
                    href: '#/players/' + this.leaderboard_source.url_name
                }
            ];
        }
    },
    methods: {
        loadState(route_params) {
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            
            this.dataset = new Dataset('players', '/api/1/players');
            this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);

            this.loaded = true;
        }
    }
};
</script>

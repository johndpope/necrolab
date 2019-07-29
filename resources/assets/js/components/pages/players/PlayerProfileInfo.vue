<template>
    <with-nav-body
        :loaded="loaded"
        :show_breadcrumbs="false"
    >
        <player-linked-sites :player="player"></player-linked-sites>
        <div v-if="dataset.data" class="container-fluid pr-0 pl-0">
            <div class="row">
                <div class="col-md-6 pt-md-4 table-responsive">
                    <h4>Stats</h4>
                    <table class="table">
                        <tr>
                            <th>PBs</th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                {{ dataset.data.pbs }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Leaderboards</th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ dataset.data.leaderboards }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>WRs</th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ dataset.data.first_place_ranks }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Dailies</th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ dataset.data.dailies }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 pt-md-4 table-responsive">
                    <h4>Totals</h4>
                    <table class="table">
                        <tr v-for="details_column in details_columns">
                            <th>
                                {{ details_column.display_name }}
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    <details-column
                                        :details_name="details_column.name"
                                        :details_value="dataset.data.details[details_column.name] != null ? dataset.data.details[details_column.name] : ''"
                                    >
                                    </details-column>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import PlayerLinkedSites from '../../player/PlayerLinkedSites.vue';
import Dataset from '../../../datasets/Dataset.js';
import DetailsColumn from '../../formatting/DetailsColumn.vue';

const PlayerProfileInfo = {
    extends: BasePage,
    name: 'player-profile-info',
    components: {
        'with-nav-body': WithNavBody,
        'player-linked-sites': PlayerLinkedSites,
        'details-column': DetailsColumn
    },
    data() {
        return {
            player_id: '',
            leaderboard_source: {},
            player: {},
            details_columns: [],
            dataset: {}
        }
    },
    computed: {
        headerColumns() {
            const header_columns = [
                'PBs',
                'Leaderboards',
                'WRs',
                'Dailies',
                'Categories',
                'Characters',
                'Modes',
                'Seeded/Unseeded',
                'Single/Multiplayer',
                'Soundtracks'
            ];

            this.details_columns.forEach((details_column) => {
                header_columns.push(details_column.display_name);
            });

            return header_columns;
        }
    },
    methods: {
        loadState(route_params) {
            this.player_id = route_params.player_id;
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];

            this.player = this.$store.getters['players/get'](this.leaderboard_source.name, this.player_id);

            this.details_columns = this.$store.getters['details_columns/getAll'];

            this.dataset = new Dataset('player_stats', '/api/1/player/stats/latest');

            this.dataset.disablePagination();
            this.dataset.setRequestParameter('player_id', this.player_id);
            this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);

            this.dataset.fetch().then(() => {
                this.loaded = true;
            });
        }
    }
};

export default PlayerProfileInfo;
</script>

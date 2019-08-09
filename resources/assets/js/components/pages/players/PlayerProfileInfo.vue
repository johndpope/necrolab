<template>
    <with-nav-body
        v-if="loaded"
        :loaded="loaded"
        :show_breadcrumbs="false"
    >
        <player-linked-sites :player="player">
        </player-linked-sites>
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
                        <tr>
                            <th>Seeded PBs</th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ dataset.data.seeded_pbs }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Unseeded PBs</th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ dataset.data.unseeded_pbs }}
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
                <div class="col-md-6 pt-md-4 table-responsive">
                    <h4>Bests</h4>
                    <table class="table">
                        <tr>
                            <th>
                                Category
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ $store.getters['leaderboard_types/getByName'](getBest('leaderboard_type')).display_name }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Character
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ $store.getters['characters/getByName'](getBest('character')).display_name }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Release
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ $store.getters['releases/getByName'](getBest('release')).display_name }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Mode
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ $store.getters['modes/getByName'](getBest('mode')).display_name }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Seeded Type
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ $store.getters['seeded_types/getByName'](getBest('seeded_type')).display_name }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Multiplayer Type
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ $store.getters['multiplayer_types/getByName'](getBest('multiplayer_type')).display_name }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Soundtrack
                            </th>
                            <td>
                                <div class="text-sm-right text-lg-left">
                                    {{ $store.getters['soundtracks/getByName'](getBest('soundtrack')).display_name }}
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
        getBest(best_name) {
            let value = '';

            if(this.dataset.data.bests[best_name] != null) {
                value = this.dataset.data.bests[best_name];
            }

            return value;
        },
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

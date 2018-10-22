<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>Daily Leaderboards</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable :api_endpoint_url="api_endpoint_url" :header_columns="header_columns" :has_search="true" :has_action_column="true" :filters="filters">
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
                        <template>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Zone</th>
                                            <th scope="col">Level</th>
                                            <th scope="col">Win</th>
                                            <th scope="col">Killed By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ row.pb.zone }}
                                            </td>
                                            <td>
                                                {{ row.pb.level }}
                                            </td>
                                            <td>
                                                <win :record="row"></win>
                                            </td>
                                            <td>
                                                <killed-by :record="row"></killed-by>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </template>
                </necrotable>
            </div>
        </div>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';
import ntDateTimeFilter from '../table/filters/DateTimeFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import Seed from '../leaderboards/Seed.vue';
import Win from '../leaderboards/Win.vue';
import KilledBy from '../leaderboards/KilledBy';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';

export default {
    name: 'daily-leaderboards-page',
    components: {
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'seed': Seed,
        'win': Win,
        'killed-by': KilledBy,
        'toggle-details': ToggleDetails
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Leaderboards'
                },
                {
                    text: 'Daily',
                    href: '/leaderboards/daily'
                }
            ],
            api_endpoint_url: '/api/1/leaderboards/daily/entries',
            filters: [
                ntDateTimeFilter,
                ReleaseDropdownFilter,
                SiteDropdownFilter
            ],
            header_columns: [
                'Rank',
                'Player',
                'Score',
                'Seed'
            ]
        }
    }
};
</script>

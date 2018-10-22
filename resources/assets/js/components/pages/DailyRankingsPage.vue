<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>Daily Rankings</h1>
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
                            <rounded-decimal :original_number="row.total_points"></rounded-decimal>
                        </td>
                        <td>
                            {{ row.total_score }}
                        </td>
                    </template>
                    <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                        <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
                    </template>
                    <template slot="row-details" slot-scope="{ row }">
                        <template>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">1st Place</th>
                                            <th scope="col">Top 5</th>
                                            <th scope="col">Top 10</th>
                                            <th scope="col">Top 20</th>
                                            <th scope="col">Top 50</th>
                                            <th scope="col">Top 100</th>
                                            <th scope="col">Points Per Day</th>
                                            <th scope="col">Attempts</th>
                                            <th scope="col">Wins</th>
                                            <th scope="col">Average Rank</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ row.first_place_ranks }}
                                            </td>
                                            <td>
                                                {{ row.top_5_ranks }}
                                            </td>
                                            <td>
                                                {{ row.top_10_ranks }}
                                            </td>
                                            <td>
                                                {{ row.top_20_ranks }}
                                            </td>
                                            <td>
                                                {{ row.top_50_ranks }}
                                            </td>
                                            <td>
                                                {{ row.top_100_ranks }}
                                            </td>
                                            <td>
                                                <rounded-decimal :original_number="row.points_per_day"></rounded-decimal>
                                            </td>
                                            <td>
                                                {{ row.total_dailies }}
                                            </td>
                                            <td>
                                                {{ row.total_wins }}
                                            </td>
                                            <td>
                                                <rounded-decimal :original_number="row.average_rank"></rounded-decimal>
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
import NumberOfDaysDropdownFilter from '../table/filters/NumberOfDaysDropdownFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';

export default {
    name: 'daily-rankings-page',
    components: {
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'toggle-details': ToggleDetails
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: 'Daily',
                    href: '/rankings/daily'
                }
            ],
            api_endpoint_url: '/api/1/rankings/daily/entries',
            filters: [
                ntDateTimeFilter,
                NumberOfDaysDropdownFilter,
                ReleaseDropdownFilter,
                SiteDropdownFilter
            ],
            header_columns: [
                'Rank',
                'Player',
                'Points',
                'Score'
            ]
        }
    }
};
</script>

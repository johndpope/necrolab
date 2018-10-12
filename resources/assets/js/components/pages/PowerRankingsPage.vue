<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>Power Rankings</h1>
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
                            <player-profile-link :id="row.player.id" :username="row.player.username"></player-profile-link>
                        </td>
                        <td>
                            <linked-sites></linked-sites>
                        </td>
                        <td>
                            <rounded-decimal :original_number="row.points"></rounded-decimal>
                        </td>
                    </template>
                    <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                        <b-button @click.stop="toggleDetailsRow(row_index)">
                            {{ detailsRowVisible(row_index) ? 'Hide' : 'Show' }} Details
                        </b-button>
                    </template>
                    <template slot="row-details" slot-scope="{ row }">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Speed</th>
                                    <th scope="col">Deathless</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Ranks</th>
                                    <td>
                                        {{ getCategoryField(row, 'score', 'rank') }}
                                    </td>
                                    <td>
                                        {{ getCategoryField(row, 'speed', 'rank') }}
                                    </td>
                                    <td>
                                        {{ getCategoryField(row, 'deathless', 'rank') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Points</th>
                                    <td>
                                        <rounded-decimal :original_number="getCategoryField(row, 'score', 'points')"></rounded-decimal>
                                    </td>
                                    <td>
                                        <rounded-decimal :original_number="getCategoryField(row, 'speed', 'points')"></rounded-decimal>
                                    </td>
                                    <td>
                                        <rounded-decimal :original_number="getCategoryField(row, 'deathless', 'points')"></rounded-decimal>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Score/Time/Wins</th>
                                    <td>
                                        {{ getCategoryField(row, 'score', 'score') }}
                                    </td>
                                    <td>
                                        <seconds-to-time :seconds="getCategoryField(row, 'speed', 'time')"></seconds-to-time>
                                    </td>
                                    <td>
                                        {{ getCategoryField(row, 'deathless', 'win_count') }}
                                    </td>
                                </tr> 
                            </tbody>
                        </table>
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
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import SeededDropdownFilter from '../table/filters/SeededDropdownFilter.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileLink from '../formatting/PlayerProfileLink.vue';
import LinkedSites from '../formatting/LinkedSites.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';

export default {
    name: 'PlayersPageTable',
    components: {
        'necrotable': NecroTable,
        'player-profile-link': PlayerProfileLink,
        'linked-sites': LinkedSites,
        'rounded-decimal': RoundedDecimal,
        'seconds-to-time': SecondsToTime
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: 'Power',
                    href: '/rankings/power'
                }
            ],
            api_endpoint_url: '/api/1/rankings/power/entries',
            filters: [
                ntDateTimeFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededDropdownFilter,
                SiteDropdownFilter
            ],
            header_columns: [
                'Rank',
                'Player',
                '',
                'Points'
            ]
        }
    },
    methods: {
        getCategoryField(row, category_name, field_name) {
            let field_value = '';
            
            if(row[category_name] != null) {
                field_value = row[category_name][field_name];
            }
            
            return field_value;
        }
    }
};
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>Speed Rankings</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable :api_endpoint_url="api_endpoint_url" :header_columns="header_columns" :has_search="true" :has_action_column="true" :filters="filters">
                    <template slot="table-row" slot-scope="{ row_index, row }">
                        <td>
                            {{ row.speed.rank }}
                        </td>
                        <td>
                            <player-profile-modal :player="row.player"></player-profile-modal>
                        </td>
                        <td>
                            <rounded-decimal :original_number="row.speed.points"></rounded-decimal>
                        </td>
                        <td>
                            <seconds-to-time :unformatted="row.speed.time" :include_hours="true" :zero_pad_hours="true"></seconds-to-time>
                        </td>
                    </template>
                    <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                        <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
                    </template>
                    <template slot="row-details" slot-scope="{ row }">
                        <ranking-category-details-table
                            :key="row.player.id"
                            :record="row.characters"
                            category="speed"
                            category_display_name="Time"
                            details_property="time"
                            :details_formatter_component="details_formatter_component"
                        >
                        </ranking-category-details-table>
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
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import RankingCategoryDetailsTable from '../table/RankingCategoryDetailsTable.vue'

export default {
    name: 'speed-rankings-page',
    components: {
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'seconds-to-time': SecondsToTime,
        'toggle-details': ToggleDetails,
        'ranking-category-details-table': RankingCategoryDetailsTable
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: 'Speed',
                    href: '/rankings/speed'
                }
            ],
            api_endpoint_url: '/api/1/rankings/speed/entries',
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
                'Points',
                'Time'
            ],
            details_formatter_component: SecondsToTime
        }
    }
};
</script>

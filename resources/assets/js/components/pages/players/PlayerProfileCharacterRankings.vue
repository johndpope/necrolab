<template>
    <with-nav-body
        :loaded="loaded"
        sub_title="Character Rankings"
        :show_breadcrumbs="false"
    >
        <player-character-ranking-stats-chart
            :dataset="dataset"
            :character="$store.getters['characters/getSelected']"
            :leaderboard_types="$store.getters['leaderboard_types/getAll']"
            :details_columns_by_name="$store.getters['details_columns/getAllByName']"
        >
        </player-character-ranking-stats-chart>
        <br />
        <necrotable
            :dataset="dataset"
            :header_columns="header_columns"
            :has_action_column="true"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    {{ row.date }}
                </td>
                <td>
                    {{ row.characters[characterName].rank }}
                </td>
                <td>
                    <rounded-decimal :original_number="row.characters[characterName].points"></rounded-decimal>
                </td>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <ranking-summary-details-table
                    :leaderboard_types="$store.getters['leaderboard_types/getFiltered']"
                    :record="row.characters[characterName]"
                    :rows="details_table_rows"
                ></ranking-summary-details-table>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import Dataset from '../../../datasets/Dataset.js';
import PlayerCharacterRankingStatsChart from '../../charts/PlayerCharacterRankingStatsChart.vue';
import NecroTable from '../../table/NecroTable.vue';
import CharacterDropdownFilter from '../../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import RoundedDecimal from '../../formatting/RoundedDecimal.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';

import RankingSummaryDetailsTable from '../../table/RankingSummaryDetailsTable.vue';

const PlayerProfileCharacterRankings = {
    extends: BasePage,
    name: 'player-profile-character-rankings',
    components: {
        'with-nav-body': WithNavBody,
        'player-character-ranking-stats-chart': PlayerCharacterRankingStatsChart,
        'necrotable': NecroTable,
        'rounded-decimal': RoundedDecimal,
        'toggle-details': ToggleDetails,
        'ranking-summary-details-table': RankingSummaryDetailsTable
    },
    data() {
        return {
            dataset: {},
            player_id: '',
            leaderboard_source: {},
            header_columns: [
                'Date',
                'Rank',
                'Points'
            ],
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter,
                MultiplayerTypeDropdownFilter,
                SoundtrackDropdownFilter
            ],
            details_table_rows: [
                {
                    name: 'rank',
                    display_name: 'Rank'
                },
                {
                    name: 'points',
                    display_name: 'Points',
                    rounded: true
                }
            ]
        }
    },
    computed: {
        breadcrumbs() {
            return [
                {
                    text: 'Leaderboards'
                },
                {
                    text: this.leaderboard_type.display_name,
                    href: '/leaderboards/' + this.leaderboard_type.name
                }
            ];
        },
        characterName() {
            return this.$store.getters['characters/getSelected'].name;
        }
    },
    methods: {
        loadState(route_params) {
            this.$store.commit('leaderboard_types/setFilterStores', [
                'modes'
            ]);

            this.$store.commit('characters/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.$store.commit('releases/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.$store.commit('modes/setFilterStores', [
                'characters',
                'releases'
            ]);

            this.$store.commit('multiplayer_types/setFilterStores', [
                'leaderboard_sources'
            ]);

            this.player_id = route_params.player_id,
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];

            this.dataset = new Dataset('player_character_rankings', '/api/1/player/rankings/character/entries');

            this.dataset.disableServerPagination();
            this.dataset.setRequestParameter('player_id', this.player_id);
            this.dataset.setRequestParameter('leaderboard_source', this.leaderboard_source.name);

            this.loaded = true;
        }
    }
};

export default PlayerProfileCharacterRankings;
</script>

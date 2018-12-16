<template>
    <ranking-entries-page
        v-if="loaded"
        category_name="power"
        category_display_name="Power"
        :api_endpoint_url="api_endpoint_url"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.points"></rounded-decimal>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-summary-details-table 
                :leaderboard_types="$store.getters['leaderboard_types/getFiltered']"
                :record="row"
                :rows="details_table_rows"
            ></ranking-summary-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import BasePage from './BasePage.vue';
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';
import RankingSummaryDetailsTable from '../table/RankingSummaryDetailsTable.vue';

export default {
    extends: BasePage,
    name: 'power-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'toggle-details': ToggleDetails,
        'ranking-summary-details-table': RankingSummaryDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/power/entries',
            header_columns: [
                'Rank',
                'Player',
                'Points'
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
                },
            ]
        }
    },
    methods: {
        loadState(route_params) {
            let promise = this.$store.dispatch('page/loadModules', [
                'leaderboard_sources',
                'releases',
                'modes',
                'seeded_types',
                'leaderboard_types',
                'leaderboard_details_columns',
                'data_types'
            ]);

            promise.then(() => {
                this.$store.commit('leaderboard_types/setFilterStores', [
                    'modes'
                ]);
                
                this.$store.commit('leaderboard_sources/setSelected', route_params.leaderboard_source);
                this.$store.commit('releases/setSelected', route_params.release);
                this.$store.commit('modes/setSelected', route_params.mode);
                this.$store.commit('seeded_types/setSelected', route_params.seeded_type);
                
                this.loaded = true;
            });
        }
    }
};
</script>

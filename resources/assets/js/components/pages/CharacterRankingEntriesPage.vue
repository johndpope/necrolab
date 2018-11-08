<template>
    <ranking-entries-page
        category_name="character"
        category_display_name="Character"
        :api_endpoint_url="api_endpoint_url"
        :filter_records="filter_records"
        :header_columns="header_columns"
    >
        <template slot="table-row" slot-scope="{ row_index, row }">
            <td>
                {{ row.characters[currentCharacter].rank }}
            </td>
            <td>
                <player-profile-modal :player="row.player"></player-profile-modal>
            </td>
            <td>
                <rounded-decimal :original_number="row.characters[currentCharacter].points"></rounded-decimal>
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <daily-ranking-details-table :record="row">
            </daily-ranking-details-table>
        </template>
    </ranking-entries-page>
</template>

<script>
import RankingEntriesPage from '../rankings/RankingEntriesPage.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import RankingDetailsTable from '../table/RankingDetailsTable.vue';

export default {
    name: 'character-ranking-entries-page',
    components: {
        'ranking-entries-page': RankingEntriesPage,
        'player-profile-modal': PlayerProfileModal,
        'rounded-decimal': RoundedDecimal,
        'daily-ranking-details-table': RankingDetailsTable
    },
    data() {
        return {
            api_endpoint_url: '/api/1/rankings/character/entries',
            filter_records: [
                {
                    name: 'character',
                    store_name: 'characters'
                },
                {
                    name: 'release',
                    store_name: 'releases'
                },
                {
                    name: 'mode',
                    store_name: 'modes'
                },
                {
                    name: 'seeded_type',
                    store_name: 'seeded_types'
                }
            ],
            header_columns: [
                'Rank',
                'Player',
                'Points'
            ]
        }
    },
    computed: {
        currentCharacter() {
            return this.$route.params.character;
        }
    }
};
</script>

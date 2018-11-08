<template>
    <rankings-overview-page
        category_name="character"
        category_display_name="Character"
        :header_columns="header_columns"
        :filters="filters"
        :filter_records="filter_records"
    >
        <template slot="table-row" slot-scope="{ row_index, row, getEntriesUrl }">
            <td>
                <router-link :to="getEntriesUrl(row.date)">
                    {{ row.date }}
                </router-link>
            </td>
            <td>
                {{ getPlayersProperty(row) }}
            </td>
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <ranking-summary-details-table :record="row.characters[currentCharacter]">
            </ranking-summary-details-table>
        </template>
    </rankings-overview-page>
</template>

<script>
import RankingsOverviewPage from '../rankings/RankingsOverviewPage.vue';
import CharacterDropdownFilter from '../table/filters/CharacterDropdownFilter.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../table/filters/SeededTypeDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';
import RankingSummaryDetailsTable from '../table/RankingSummaryDetailsTable.vue';

export default {
    name: 'character-rankings-page',
    components: {
        'rankings-overview-page': RankingsOverviewPage,
        'ranking-summary-details-table': RankingSummaryDetailsTable
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: 'Character',
                    href: '#/rankings/character'
                }
            ],
            filters: [
                CharacterDropdownFilter,
                ReleaseDropdownFilter,
                ModeDropdownFilter,
                SeededTypeDropdownFilter
            ],
            header_columns: [
                'Rank',
                'Players',
                'Points'
            ],
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
            ]
        }
    },
    computed: {
        currentCharacter() {
            let character_name = 'cadence';
            
            let character = this.$store.getters['characters/getSelected'];

            if(character != null) {
                character_name = character;
            }

            return character_name;
        }
    },
    methods: {
        getPlayersProperty(record) {
            let players = 0;
            
            if(record['characters'] != null && record['characters'][this.currentCharacter] != null) {
                players = record['characters'][this.currentCharacter].players;
            }
            
            return players;
        }
    }
};
</script>

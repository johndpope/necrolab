<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th 
                        v-for="(character, character_index) in characters"
                        :key="character_index"
                        scope="col"
                    >
                        <character-icon-selector :name="character.name"></character-icon-selector>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(row, row_index) in rows"
                >
                    <th scope="row">
                        {{ row.display_name }}
                    </th>
                    <td
                        v-for="(character, character_index) in characters"
                        :key="character_index"
                    >
                        <template v-if="row['rounded'] != null && row.rounded">
                            <rounded-decimal :original_number="getCharacterCategoryProperty(character.name, row.name)"></rounded-decimal>
                        </template>
                        <template v-else>
                            {{ getCharacterCategoryProperty(character.name, row.name) }}
                        </template>
                    </td>
                </tr>
                <tr
                    v-for="details_column in details_columns"
                    :key="details_column.name"
                >
                    <th scope="row">{{ details_column.display_name }}</th>
                    <td
                        v-for="(character, character_index) in characters"
                        :key="character_index"
                        scope="col"
                    >
                        <details-column
                            :details_name="details_column.name" 
                            :details_value="getCharacterCategoryDetailsProperty(character.name, details_column.name)"
                        >
                        </details-column>
                    </td>
                </tr> 
            </tbody>
        </table>
    </div>
</template>

<script>
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import DetailsColumn from '../formatting/DetailsColumn.vue';
import CharacterIconSelector from '../characters/CharacterIconSelector.vue';

const RankingCategorySummaryDetailsTable = {
    name: 'ranking-category-details-table',
    components: {
        'rounded-decimal': RoundedDecimal,
        'details-column': DetailsColumn,
        'character-icon-selector': CharacterIconSelector
    },
    props: {
        leaderboard_type: {
            type: Object
        },
        characters: {
            type: Array,
            default: () => []
        },
        record: {
            type: Object,
            default: () => {}
        },
        category: {
            type: String,
            default: ''
        },
        category_display_name: {
            type: String,
            default: ''
        },
        details_columns: {
            type: Array,
            default: () => []
        },
        rows: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        getCharacterCategoryProperty(character_name, property_name) {
            let property_value = '';

            if(
                this.record[character_name] != null && 
                this.record[character_name]['categories'] != null &&
                this.record[character_name]['categories'][this.leaderboard_type.name] != null &&
                this.record[character_name]['categories'][this.leaderboard_type.name][property_name] != null
            ) {
                property_value = this.record[character_name]['categories'][this.leaderboard_type.name][property_name];
            }
            
            return property_value;
        },
        getCharacterCategoryDetailsProperty(character_name, details_name) {
            let character_category_property = this.getCharacterCategoryProperty(character_name, 'details');
            
            let property_value = '';

            if(
                typeof character_category_property !== 'string' && 
                character_category_property[details_name] != null
            ) {
                property_value = character_category_property[details_name];
            }
            
            return property_value;
        }
    }
};

export default RankingCategorySummaryDetailsTable;
</script>

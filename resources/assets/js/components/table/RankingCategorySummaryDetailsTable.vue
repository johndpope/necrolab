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
                            <rounded-decimal :original_number="getCharacterProperty(character.name, row.name)"></rounded-decimal>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty(character.name, row.name) }}
                        </template>
                    </td>
                </tr>
                <tr>
                    <th scope="row">{{ category_display_name }}</th>
                    <td
                        v-for="(character, character_index) in characters"
                        :key="character_index"
                        scope="col"
                    >
                        <template v-if="details_data_type == 'seconds'">
                            <seconds-to-time 
                                :unformatted="getCharacterProperty(character.name, details_property)" 
                                :include_hours="true" 
                                :zero_pad_hours="true"
                            >
                            </seconds-to-time>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty(character.name, details_property) }}
                        </template>
                    </td>
                </tr> 
            </tbody>
        </table>
    </div>
</template>

<script>
import SecondsToTime from '../formatting/SecondsToTime.vue';
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import CharacterIconSelector from '../characters/CharacterIconSelector.vue';

const RankingCategorySummaryDetailsTable = {
    name: 'ranking-category-details-table',
    components: {
        'seconds-to-time': SecondsToTime,
        'rounded-decimal': RoundedDecimal,
        'character-icon-selector': CharacterIconSelector
    },
    props: {
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
        details_property: {
            type: String,
            default: ''
        },
        details_data_type: {
            type: String,
            default: ''
        },
        rows: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        getCharacterProperty(character_name, property_name) {
            let property_value = '';

            if(
                this.record[character_name] != null && 
                this.record[character_name][this.category] != null &&
                this.record[character_name][this.category][property_name] != null
            ) {
                property_value = this.record[character_name][this.category][property_name];
            }
            
            return property_value;
        }
    }
};

export default RankingCategorySummaryDetailsTable;
</script>

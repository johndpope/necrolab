<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">
                        <cadence-icon></cadence-icon>
                    </th>
                    <th scope="col">
                        <bard-icon></bard-icon>
                    </th>
                    <th scope="col">
                        <monk-icon></monk-icon>
                    </th>
                    <th scope="col">
                        <aria-icon></aria-icon>
                    </th>
                    <th scope="col">
                        <bolt-icon></bolt-icon>
                    </th>
                    <th scope="col">
                        <dove-icon></dove-icon>
                    </th>
                    <th scope="col">
                        <eli-icon></eli-icon>
                    </th>
                    <th scope="col">
                        <melody-icon></melody-icon>
                    </th>
                    <th scope="col">
                        <dorian-icon></dorian-icon>
                    </th>
                    <th scope="col">
                        <coda-icon></coda-icon>
                    </th>
                    <template v-if="show_amplified_characters">
                        <th scope="col">
                            <nocturna-icon></nocturna-icon>
                        </th>
                        <th scope="col">
                            <diamond-icon></diamond-icon>
                        </th>
                        <th scope="col">
                            <mary-icon></mary-icon>
                        </th>
                        <th scope="col">
                            <tempo-icon></tempo-icon>
                        </th>
                    </template>
                    <template v-if="show_multi_characters">
                        <th scope="col">
                            <story-icon></story-icon>
                        </th>
                        <th scope="col">
                            <all-icon></all-icon>
                        </th>
                        <template v-if="show_amplified_characters">
                            <th scope="col">
                                <all-dlc-icon></all-dlc-icon>
                            </th>
                        </template>
                    </template>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Players</th>
                    <td>
                        {{ getCharacterProperty('cadence', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('bard', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('monk', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('aria', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('bolt', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('dove', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('eli', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('melody', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('dorian', 'players') }}
                    </td>
                    <td>
                        {{ getCharacterProperty('coda', 'players') }}
                    </td>
                    <template v-if="show_amplified_characters">
                        <td>
                            {{ getCharacterProperty('nocturna', 'players') }}
                        </td>
                        <td>
                            {{ getCharacterProperty('diamond', 'players') }}
                        </td>
                        <td>
                            {{ getCharacterProperty('mary', 'players') }}
                        </td>
                        <td>
                            {{ getCharacterProperty('tempo', 'players') }}
                        </td>
                    </template>
                    <template v-if="show_multi_characters">
                        <td>
                            {{ getCharacterProperty('story', 'players') }}
                        </td>
                        <td>
                            {{ getCharacterProperty('all', 'players') }}
                        </td>
                        <template v-if="show_amplified_characters">
                            <td>
                                {{ getCharacterProperty('all_dlc', 'players') }}
                            </td>
                        </template>
                    </template>
                </tr>
                <tr>
                    <th scope="row">{{ category_display_name }}</th>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('cadence', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('cadence', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('bard', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('bard', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('monk', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('monk', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('aria', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('aria', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('bolt', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('bolt', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('dove', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('dove', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('eli', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('eli', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('melody', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('melody', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('dorian', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('dorian', details_property) }}
                        </template>
                    </td>
                    <td>
                        <template v-if="details_formatter_component != null && hasDetailsFormatter()">
                            <component :is="details_formatter_component" :unformatted="getCharacterProperty('coda', details_property)" :include_hours="true">
                            </component>
                        </template>
                        <template v-else>
                            {{ getCharacterProperty('coda', details_property) }}
                        </template>
                    </td>
                    <template v-if="show_amplified_characters">
                        <td>
                            <template v-if="hasDetailsFormatter()">
                                <component :is="details_formatter_component" :unformatted="getCharacterProperty('nocturna', details_property)" :include_hours="true">
                                </component>
                            </template>
                            <template v-else>
                                {{ getCharacterProperty('nocturna', details_property) }}
                            </template>
                        </td>
                        <td>
                            <template v-if="hasDetailsFormatter()">
                                <component :is="details_formatter_component" :unformatted="getCharacterProperty('diamond', details_property)" :include_hours="true">
                                </component>
                            </template>
                            <template v-else>
                                {{ getCharacterProperty('diamond', details_property) }}
                            </template>
                        </td>
                        <td>
                            <template v-if="hasDetailsFormatter()">
                                <component :is="details_formatter_component" :unformatted="getCharacterProperty('mary', details_property)" :include_hours="true">
                                </component>
                            </template>
                            <template v-else>
                                {{ getCharacterProperty('mary', details_property) }}
                            </template>
                        </td>
                        <td>
                            <template v-if="hasDetailsFormatter()">
                                <component :is="details_formatter_component" :unformatted="getCharacterProperty('tempo', details_property)" :include_hours="true">
                                </component>
                            </template>
                            <template v-else>
                                {{ getCharacterProperty('tempo', details_property) }}
                            </template>
                        </td>
                    </template>
                    <template v-if="show_multi_characters">
                        <td>
                            <template v-if="hasDetailsFormatter()">
                                <component :is="details_formatter_component" :unformatted="getCharacterProperty('story', details_property)" :include_hours="true">
                                </component>
                            </template>
                            <template v-else>
                                {{ getCharacterProperty('story', details_property) }}
                            </template>
                        </td>
                        <td>
                            <template v-if="hasDetailsFormatter()">
                                <component :is="details_formatter_component" :unformatted="getCharacterProperty('all', details_property)" :include_hours="true">
                                </component>
                            </template>
                            <template v-else>
                                {{ getCharacterProperty('all', details_property) }}
                            </template>
                        </td>
                        <template v-if="show_amplified_characters">
                            <td>
                                <template v-if="hasDetailsFormatter()">
                                    <component :is="details_formatter_component" :unformatted="getCharacterProperty('all_dlc', details_property)" :include_hours="true">
                                    </component>
                                </template>
                                <template v-else>
                                    {{ getCharacterProperty('all_dlc', details_property) }}
                                </template>
                            </td>
                        </template>
                    </template>
                </tr> 
            </tbody>
        </table>
    </div>
</template>

<script>
import RoundedDecimal from '../formatting/RoundedDecimal.vue';

import CadenceIcon from '../characters/CadenceIcon.vue';
import BardIcon from '../characters/BardIcon.vue';
import MonkIcon from '../characters/MonkIcon.vue';
import AriaIcon from '../characters/AriaIcon.vue';
import BoltIcon from '../characters/BoltIcon.vue';
import DoveIcon from '../characters/DoveIcon.vue';
import EliIcon from '../characters/EliIcon.vue';
import MelodyIcon from '../characters/MelodyIcon.vue';
import DorianIcon from '../characters/DorianIcon.vue';
import CodaIcon from '../characters/CodaIcon.vue';
import NocturnaIcon from '../characters/NocturnaIcon.vue';
import DiamondIcon from '../characters/DiamondIcon.vue';
import MaryIcon from '../characters/MaryIcon.vue';
import TempoIcon from '../characters/TempoIcon.vue';
import StoryIcon from '../characters/StoryIcon.vue';
import AllIcon from '../characters/AllIcon.vue';
import AllDlcIcon from '../characters/AllDlcIcon.vue';

const RankingCategoryDetailsTable = {
    name: 'ranking-category-details-table',
    components: {
        'rounded-decimal': RoundedDecimal,
        'cadence-icon': CadenceIcon,
        'bard-icon': BardIcon,
        'monk-icon': MonkIcon,
        'aria-icon': AriaIcon,
        'bolt-icon': BoltIcon,
        'dove-icon': DoveIcon,
        'eli-icon': EliIcon,
        'melody-icon': MelodyIcon,
        'dorian-icon': DorianIcon,
        'coda-icon': CodaIcon,
        'nocturna-icon': NocturnaIcon,
        'diamond-icon': DiamondIcon,
        'mary-icon': MaryIcon,
        'tempo-icon': TempoIcon,
        'story-icon': StoryIcon,
        'all-icon': AllIcon,
        'all-dlc-icon': AllDlcIcon
    },
    props: {
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
        details_formatter_component: {
            type: Object,
            default: () => {}
        },
        show_amplified_characters: {
            type: Boolean,
            default: true
        },
        show_multi_characters: {
            type: Boolean,
            default: true
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
        },
        hasDetailsFormatter() {
            return this.details_formatter_component != null && this.details_formatter_component['props'] != null;
        }
    }
};

export default RankingCategoryDetailsTable;
</script>

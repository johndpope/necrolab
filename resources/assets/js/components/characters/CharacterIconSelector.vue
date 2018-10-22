<template>
    <div class="character-icon-selector h-100">
        <cadence-icon v-if="name == 'cadence'"></cadence-icon>
        <bard-icon v-else-if="name == 'bard'"></bard-icon>
        <monk-icon v-else-if="name == 'monk'"></monk-icon>
        <aria-icon v-else-if="name == 'aria'"></aria-icon>
        <bolt-icon v-else-if="name == 'bolt'"></bolt-icon>
        <dove-icon v-else-if="name == 'dove'"></dove-icon>
        <eli-icon v-else-if="name == 'eli'"></eli-icon>
        <melody-icon v-else-if="name == 'melody'"></melody-icon>
        <dorian-icon v-else-if="name == 'dorian'"></dorian-icon>
        <coda-icon v-else-if="name == 'coda'"></coda-icon>
        <nocturna-icon v-else-if="name == 'nocturna'"></nocturna-icon>
        <diamond-icon v-else-if="name == 'diamond'"></diamond-icon>
        <mary-icon v-else-if="name == 'mary'"></mary-icon>
        <tempo-icon v-else-if="name == 'tempo'"></tempo-icon>
        <story-icon v-else-if="name == 'story'"></story-icon>
        <all-icon v-else-if="name == 'all'"></all-icon>
        <all-dlc-icon v-else-if="name == 'all_dlc'"></all-dlc-icon>

        <span v-if="display_name != ''" class="pl-4 align-middle">
            {{ display_name }}
        </span>
    </div>
</template>

<script>
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

const ChraacterIconSelector = {
    name: 'site-icon-display',
    components: {
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
        name: {
            type: String,
            default: ''
        },
        display_name: {
            type: String,
            default: ''
        }
    }
};

export default ChraacterIconSelector;
</script>

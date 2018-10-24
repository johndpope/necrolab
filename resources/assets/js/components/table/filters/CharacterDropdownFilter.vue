<script>
import DropdownFilter from './DropdownFilter.vue';
import CharacterIconSelector from '../../characters/CharacterIconSelector.vue';

const CharacterDropdownFilter = {
    extends: DropdownFilter,
    name: 'character-dropdown-filter',
    props: {
        api_endpoint_url: {
            type: String,
            default: '/api/1/characters'
        },
        storage_mutation_name: {
            type: String,
            default: 'setCharacters'
        },
        storage_getter_name: {
            type: String,
            default: 'allCharacters'
        },
        field_name: {
            type: String,
            default: 'character'
        },
        label: {
            type: String,
            default: 'Character'
        },
        default_selected_value: {
            type: String,
            default: 'cadence'
        },
        option_formatter: {
            type: Object,
            default: () => CharacterIconSelector
        }
    },
    methods: {
        setSelectedState(selected) {
            this.$store.commit('setCharacter', selected);
        }
    }
};

export default CharacterDropdownFilter;
</script>

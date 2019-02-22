<script>
import DropdownFilter from './DropdownFilter.vue';
import CharacterIconSelector from '../../characters/CharacterIconSelector.vue';

const CharacterDropdownFilter = {
    extends: DropdownFilter,
    name: 'character-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'character'
        },
        label: {
            type: String,
            default: 'Character'
        },
        option_formatter: {
            type: Object,
            default: () => CharacterIconSelector
        }
    },
    methods: {
        getDefaultOptions() {            
            return this.$store.getters['attributes/getFiltered']('characters');
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('attributes/setSelected', {
                attribute: 'characters',
                record: selected
            });
        }
    }
};

export default CharacterDropdownFilter;
</script>

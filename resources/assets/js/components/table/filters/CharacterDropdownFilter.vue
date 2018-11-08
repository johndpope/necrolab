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
        loadOptions(resolve, reject) {
            this.$store.dispatch('characters/loadAll')
                .then(() => {                        
                    this.options = this.$store.getters['characters/getAll'];
                    
                    resolve();
                });
        },
        setSelectedState(selected) {
            this.$store.commit('characters/setSelected', selected);
        }
    }
};

export default CharacterDropdownFilter;
</script>

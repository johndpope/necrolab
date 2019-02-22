<script>
import DropdownFilter from './DropdownFilter.vue';

const MultiplayerTypeDropdownFilter = {
    extends: DropdownFilter,
    name: 'multiplayer-type-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'multiplayer_type'
        },
        label: {
            type: String,
            default: 'Multiplayer Type'
        }
    },
    methods: {
        getDefaultOptions() {            
            return this.$store.getters['attributes/getFiltered']('multiplayer_types');
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('attributes/setSelected', {
                attribute: 'multiplayer_types',
                record: selected
            });
        }
    }
};

export default MultiplayerTypeDropdownFilter;
</script>

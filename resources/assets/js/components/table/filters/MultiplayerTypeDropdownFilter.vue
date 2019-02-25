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
            return this.$store.getters['multiplayer_types/getFiltered'];
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('multiplayer_types/setSelected', selected);
        }
    }
};

export default MultiplayerTypeDropdownFilter;
</script>

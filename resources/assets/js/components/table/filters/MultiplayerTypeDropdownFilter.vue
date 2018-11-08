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
        },
        default_selected_value: {
            type: String,
            default: 'single'
        }
    },
    methods: {
        loadOptions(resolve, reject) {
            this.$store.dispatch('multiplayer_types/loadAll')
                .then(() => {                        
                    this.options = this.$store.getters['multiplayer_types/getAll'];
                    
                    resolve();
                });
        },
        setSelectedState(selected) {
            this.$store.commit('multiplayer_types/setSelected', selected);
        }
    }
};

export default MultiplayerTypeDropdownFilter;
</script>

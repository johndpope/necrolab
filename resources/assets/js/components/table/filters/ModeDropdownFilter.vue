<script>
import DropdownFilter from './DropdownFilter.vue';

const ModeDropdownFilter = {
    extends: DropdownFilter,
    name: 'mode-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'mode'
        },
        label: {
            type: String,
            default: 'Mode'
        },
        default_selected_value: {
            type: String,
            default: 'normal'
        }
    },
    methods: {
        loadOptions(resolve, reject) {
            this.$store.dispatch('modes/loadAll')
                .then(() => {                        
                    this.options = this.$store.getters['modes/getAll'];
                    
                    resolve();
                });
        },
        setSelectedState(selected) {
            this.$store.commit('modes/setSelected', selected);
        }
    }
};

export default ModeDropdownFilter;
</script>

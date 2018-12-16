<script>
import DropdownFilter from './DropdownFilter.vue';

const SeededTypeDropdownFilter = {
    extends: DropdownFilter,
    name: 'seeded-type-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'seeded_type'
        },
        default_selected_value: {
            type: String,
            default: 'unseeded'
        }
    },
    methods: {
        getDefaultOptions() {
            return this.$store.getters['seeded_types/getAll'];
        },
        loadOptions(resolve, reject) {            
            this.$store.dispatch('seeded_types/loadAll').then(() => {                
                resolve();
            });
        },
        setSelectedState(selected) {
            this.$store.commit('seeded_types/setSelected', selected);
        }
    }
};

export default SeededTypeDropdownFilter;
</script>

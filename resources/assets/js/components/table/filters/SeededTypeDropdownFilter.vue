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
        label: {
            type: String,
            default: 'Seeded Type'
        }
    },
    methods: {
        getDefaultOptions() {            
            return this.$store.getters['seeded_types/getAll'];
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('seeded_types/setSelected', selected);
        }
    }
};

export default SeededTypeDropdownFilter;
</script>

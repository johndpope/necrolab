<script>
import DropdownFilter from './DropdownFilter.vue';

const NumberOfDaysDropdownFilter = {
    extends: DropdownFilter,
    name: 'number-of-days-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'number_of_days'
        },
        label: {
            type: String,
            default: 'Number of Days'
        }
    },
    methods: {
        getDefaultOptions() {            
            return this.$store.getters['number_of_days/getFiltered'];
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('number_of_days/setSelected', selected);
        }
    }
};

export default NumberOfDaysDropdownFilter;
</script>

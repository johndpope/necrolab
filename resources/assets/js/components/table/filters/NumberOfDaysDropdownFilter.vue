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
        },
        default_selected_value: {
            type: String,
            default: '30'
        }
    },
    methods: {
        getDefaultOptions() {
            return this.$store.getters['number_of_days/getAll'];
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

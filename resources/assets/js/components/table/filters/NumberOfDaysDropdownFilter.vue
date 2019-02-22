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
            return this.$store.getters['attributes/getFiltered']('number_of_days');
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('attributes/setSelected', {
                attribute: 'number_of_days',
                record: selected
            });
        }
    }
};

export default NumberOfDaysDropdownFilter;
</script>

<script>
import DropdownFilter from './DropdownFilter.vue';

const NumberOfDaysDropdownFilter = {
    extends: DropdownFilter,
    name: 'number-of-days-dropdown-filter',
    props: {
        api_endpoint_url: {
            type: String,
            default: '/api/1/rankings/daily/day_types'
        },
        storage_mutation_name: {
            type: String,
            default: 'setNumberOfDays'
        },
        storage_getter_name: {
            type: String,
            default: 'allNumberOfDays'
        },
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
        },
        option_display_name: {
            type: String,
            default: 'name'
        }
    }
};

export default NumberOfDaysDropdownFilter;
</script>

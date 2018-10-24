<script>
import DropdownFilter from './DropdownFilter.vue';

const ModeDropdownFilter = {
    extends: DropdownFilter,
    name: 'mode-dropdown-filter',
    props: {
        api_endpoint_url: {
            type: String,
            default: '/api/1/modes'
        },
        storage_mutation_name: {
            type: String,
            default: 'setModes'
        },
        storage_getter_name: {
            type: String,
            default: 'allModes'
        },
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
    }
};

export default ModeDropdownFilter;
</script>

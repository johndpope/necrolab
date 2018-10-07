<script>
import DropdownFilter from './DropdownFilter.vue';

const ReleaseDropdownFilter = {
    extends: DropdownFilter,
    name: 'release-dropdown-filter',
    props: {
        api_endpoint_url: {
            type: String,
            default: '/api/1/releases'
        },
        field_name: {
            type: String,
            default: 'release'
        },
        default_selected_value: {
            type: String,
            default: 'amplified_dlc'
        },
        option_value_name: {
            type: String,
            default: 'name'
        },
        option_display_name: {
            type: String,
            default: 'display_name'
        }
    }
};

export default ReleaseDropdownFilter;
</script>

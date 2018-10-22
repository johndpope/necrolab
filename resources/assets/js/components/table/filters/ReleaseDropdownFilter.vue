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
        label: {
            type: String,
            default: 'Release'
        },
        default_selected_value: {
            type: String,
            default: 'amplified_dlc'
        }
    }
};

export default ReleaseDropdownFilter;
</script>

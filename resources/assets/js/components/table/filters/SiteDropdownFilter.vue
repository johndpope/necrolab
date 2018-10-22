<script>
import DropdownFilter from './DropdownFilter.vue';
import SiteIconDisplay from '../../sites/SiteIconDisplay.vue';

const SiteDropdownFilter = {
    extends: DropdownFilter,
    name: 'site-dropdown-filter',
    props: {
        api_endpoint_url: {
            type: String,
            default: '/api/1/external_sites'
        },
        field_name: {
            type: String,
            default: 'site'
        },
        label: {
            type: String,
            default: 'Site'
        },
        has_blank_option: {
            type: Boolean,
            default: true
        },
        blank_option_display: {
            type: String,
            default: 'Steam'
        },
        option_formatter: {
            type: Object,
            default: () => SiteIconDisplay
        }
    } 
};

export default SiteDropdownFilter;
</script>

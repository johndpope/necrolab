<script>
import DropdownFilter from './DropdownFilter.vue';
import SiteIconDisplay from '../../sites/SiteIconDisplay.vue';

const SiteDropdownFilter = {
    extends: DropdownFilter,
    name: 'site-dropdown-filter',
    props: {
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
            default: 'All'
        },
        option_formatter: {
            type: Object,
            default: () => SiteIconDisplay
        }
    },
    methods: {
        getDefaultOptions() {            
            return this.$store.getters['attributes/getAll']('sites');
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('attributes/setSelected', {
                attribute: 'sites',
                record: selected
            });
        }
    }
};

export default SiteDropdownFilter;
</script>

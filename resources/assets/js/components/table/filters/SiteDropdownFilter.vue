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
            return this.$store.getters['sites/getAll'];
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('sites/setSelected', selected);
        }
    }
};

export default SiteDropdownFilter;
</script>

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
            default: 'Steam'
        },
        option_formatter: {
            type: Object,
            default: () => SiteIconDisplay
        }
    },
    methods: {
        loadOptions(resolve, reject) {
            this.$store.dispatch('sites/loadAll')
                .then(() => {                        
                    this.options = this.$store.getters['sites/getAll'];
                    
                    resolve();
                });
        },
        setSelectedState(selected) {
            this.$store.commit('sites/setSelected', selected);
        }
    }
};

export default SiteDropdownFilter;
</script>

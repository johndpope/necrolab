<script>
import DropdownFilter from './DropdownFilter.vue';

const ReleaseDropdownFilter = {
    extends: DropdownFilter,
    name: 'release-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'release'
        },
        label: {
            type: String,
            default: 'Release'
        }
    },
    methods: {
        getDefaultOptions() {            
            return this.$store.getters['attributes/getFiltered']('releases');
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('attributes/setSelected', {
                attribute: 'releases',
                record: selected
            });
        }
    }
};

export default ReleaseDropdownFilter;
</script>

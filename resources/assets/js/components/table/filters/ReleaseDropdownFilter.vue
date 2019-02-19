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
            return this.$store.getters['releases/getFiltered'];
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('releases/setSelected', selected);
        }
    }
};

export default ReleaseDropdownFilter;
</script>

<script>
import DropdownFilter from './DropdownFilter.vue';

const ModeDropdownFilter = {
    extends: DropdownFilter,
    name: 'mode-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'mode'
        },
        label: {
            type: String,
            default: 'Mode'
        }
    },
    methods: {
        getDefaultOptions() {
            return this.$store.getters['attributes/getFiltered']('modes');
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('attributes/setSelected', {
                attribute: 'modes',
                record: selected
            });
        }
    }
};

export default ModeDropdownFilter;
</script>

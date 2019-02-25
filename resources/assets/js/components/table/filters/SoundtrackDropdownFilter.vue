<script>
import DropdownFilter from './DropdownFilter.vue';

const SoundtrackDropdownFilter = {
    extends: DropdownFilter,
    name: 'soundtrack-dropdown-filter',
    props: {
        field_name: {
            type: String,
            default: 'soundtrack'
        },
        label: {
            type: String,
            default: 'Soundtrack'
        }
    },
    methods: {
        getDefaultOptions() {            
            return this.$store.getters['soundtracks/getAll'];
        },
        loadOptions(resolve, reject) {
            resolve();
        },
        setSelectedState(selected) {
            this.$store.commit('soundtracks/setSelected', selected);
        }
    }
};

export default SoundtrackDropdownFilter;
</script>

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
        },
        default_selected_value: {
            type: String,
            default: 'amplified_dlc'
        }
    },
    methods: {
        loadOptions(resolve, reject) {
            this.$store.dispatch('releases/loadAll')
                .then(() => {                        
                    this.options = this.$store.getters['releases/getAll'];
                    
                    resolve();
                });
        },
        setSelectedState(selected) {
            this.$store.commit('releases/setSelected', selected);
        }
    }
};

export default ReleaseDropdownFilter;
</script>

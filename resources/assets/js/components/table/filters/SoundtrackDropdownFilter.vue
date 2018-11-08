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
        },
        default_selected_value: {
            type: String,
            default: 'default'
        }
    },
    methods: {
        loadOptions(resolve, reject) {
            this.$store.dispatch('soundtracks/loadAll')
                .then(() => {                        
                    this.options = this.$store.getters['soundtracks/getAll'];
                    
                    resolve();
                });
        },
        setSelectedState(selected) {
            this.$store.commit('soundtracks/setSelected', selected);
        }
    }
};

export default SoundtrackDropdownFilter;
</script>

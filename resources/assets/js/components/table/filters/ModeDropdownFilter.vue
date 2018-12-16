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
        },
        default_selected_value: {
            type: String,
            default: 'normal'
        }
    },
    methods: {
        getDefaultOptions() {
            return this.$store.getters['modes/getFiltered'];
        },
        loadOptions(resolve, reject) {
            resolve();
        
            /*let filter_stores = [
                'releases',
            ];
        
            let promises = [
                this.$store.dispatch('modes/loadDependencies', {
                    filter_stores: filter_stores
                }),
                this.$store.dispatch('modes/loadAll')
            ];
            
            Promise.all(promises).then(() => {                
                resolve();
            });*/
        },
        setSelectedState(selected) {
            this.$store.commit('modes/setSelected', selected);
        }
    }
};

export default ModeDropdownFilter;
</script>

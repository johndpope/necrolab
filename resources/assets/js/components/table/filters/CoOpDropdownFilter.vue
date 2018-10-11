<script>
import DropdownFilter from './DropdownFilter.vue';

const CoOpDropdownFilter = {
    extends: DropdownFilter,
    name: 'co-op-dropdown-filter',
    props: {
        default_options: {
            type: Array,
            default: () => [
                {
                    name: '0',
                    display_name: 'Single Player'
                },
                {
                    name: '1',
                    display_name: 'Co-Op'
                },
            ]
        },
        field_name: {
            type: String,
            default: 'co_op'
        },
        default_selected_value: {
            type: String,
            default: '0'
        }
    }
};

export default CoOpDropdownFilter;
</script>

<script>
import DropdownFilter from './DropdownFilter.vue';

const SeededDropdownFilter = {
    extends: DropdownFilter,
    name: 'seeded-dropdown-filter',
    props: {
        default_options: {
            type: Array,
            default: () => [
                {
                    name: '0',
                    display_name: 'Unseeded'
                },
                {
                    name: '1',
                    display_name: 'Seeded'
                },
            ]
        },
        field_name: {
            type: String,
            default: 'seeded'
        },
        default_selected_value: {
            type: String,
            default: '0'
        }
    }
};

export default SeededDropdownFilter;
</script>

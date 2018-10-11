<script>
import DropdownFilter from './DropdownFilter.vue';

const CustomMusicDropdownFilter = {
    extends: DropdownFilter,
    name: 'custom-music-dropdown-filter',
    props: {
        default_options: {
            type: Array,
            default: () => [
                {
                    name: '0',
                    display_name: 'Default Music'
                },
                {
                    name: '1',
                    display_name: 'Custom Music'
                },
            ]
        },
        field_name: {
            type: String,
            default: 'custom'
        },
        default_selected_value: {
            type: String,
            default: '0'
        }
    }
};

export default CustomMusicDropdownFilter;
</script>

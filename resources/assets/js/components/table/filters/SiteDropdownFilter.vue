<template>
    <v-select label="display_name" v-model="selected" :options="all_options" :searchable="false" :filterable="false" :clearable="false" class="nt-site-filter">
        <template slot="selected-option" slot-scope="option">
            <site-icon-display :name="selected.name" :display_name="selected.display_name"></site-icon-display>
        </template>
        <template slot="option" slot-scope="option">
            <site-icon-display :name="option.name" :display_name="option.display_name"></site-icon-display>
        </template>
    </v-select>
</template>

<script>
import DropdownFilter from './DropdownFilter.vue';
import SiteIconDisplay from '../../formatting/SiteIconDisplay.vue';
import vSelect from 'vue-select/src';

const SiteDropdownFilter = {
    extends: DropdownFilter,
    name: 'site-dropdown-filter',
    components: {
        'v-select': vSelect,
        'site-icon-display': SiteIconDisplay
    },
    props: {
        api_endpoint_url: {
            type: String,
            default: '/api/1/external_sites'
        },
        field_name: {
            type: String,
            default: 'site'
        },
        has_blank_option: {
            type: Boolean,
            default: true
        },
        blank_option_display: {
            type: String,
            default: 'Steam'
        }
    } 
};

export default SiteDropdownFilter;
</script>

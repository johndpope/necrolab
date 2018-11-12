<template>
    <with-nav-layout 
        :breadcrumbs="breadcrumbs"
        :title="title"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url" 
            :header_columns="header_columns" 
            :has_action_column="true" 
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <slot 
                    name="table-row" 
                    :row="row" 
                    :getEntriesUrl="getEntriesUrl"
                    :getCategoryField="getCategoryField"
                >
                    Override the "table-row" slot to replace this text.
                </slot>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <slot name="row-details" :row="row">
                    Override the "row-details" slot to replace this text.
                </slot>
            </template>
        </necrotable>
    </with-nav-layout>
</template>

<script>
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import ReleaseDropdownFilter from '../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../table/filters/SeededTypeDropdownFilter.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';

const RankingsOverviewPage = {
    name: 'rankings-overview-page',
    components: {
        'with-nav-layout': WithNavLayout,
        'necrotable': NecroTable,
        'toggle-details': ToggleDetails,
    },
    props: {
        category_name: {
            type: String,
            default: ''
        },
        category_display_name: {
            type: String,
            default: ''
        },
        api_endpoint_url: {
            type: String,
            default: '/api/1/rankings/power'
        },
        header_columns: {
            type: Array,
            default: () => {
                return [];
            }
        },
        filters: {
            type: Array,
            default: () => {
                return [
                    ReleaseDropdownFilter,
                    ModeDropdownFilter,
                    SeededTypeDropdownFilter
                ];
            }
        },
        filter_records: {
            type: Array,
            default: () => {
                return [
                    {
                        name: 'release',
                        store_name: 'releases'
                    },
                    {
                        name: 'mode',
                        store_name: 'modes'
                    },
                    {
                        name: 'seeded_type',
                        store_name: 'seeded_types'
                    }
                ];
            }
        }
    },
    data() {
        return {
            title: this.category_display_name + ' Rankings',
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: this.category_display_name,
                    href: '#/rankings/' + this.category_name
                }
            ]
        }
    },
    methods: {
        getEntriesUrl(date) {
            let filter_records_length = this.filter_records.length;
            
            let url_segments = [];
            
            for(let index = 0; index < filter_records_length; index++) {
                let filter_record = this.filter_records[index];
                
                let getter_name = filter_record.store_name + '/getSelected';
                
                url_segments.push(this.$store.getters[getter_name]);
            }
            
            return '/rankings/' + this.category_name + '/' + url_segments.join('/') + '/' + date;
        },
        getCategoryField(record, category_name, field_name) {
            let field_value = '';
            
            if(record != null && record[category_name] != null) {
                field_value = record[category_name][field_name];
            }
            
            return field_value;
        }
    }
};

export default RankingsOverviewPage;
</script>

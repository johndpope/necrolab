<template>
    <div class="container-fluid">
        <div class="row">
            <div v-if="properties_loaded" class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div v-if="properties_loaded" class="col-12 pb-3">
                <h1>{{ category_display_name }} Rankings {{ getDisplayName() }}</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable 
                v-if="properties_loaded"
                :api_endpoint_url="api_endpoint_url"
                :default_request_parameters="api_request_parameters"
                :header_columns="header_columns" 
                :has_search="true" 
                :has_action_column="true" 
                :filters="filters"
            >
                    <template slot="table-row" slot-scope="{ row_index, row }">
                        <slot name="table-row" :row="row" :row_index="row_index">
                            Override the "table-row" slot to customize the table row.
                        </slot>
                    </template>
                    <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                        <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
                    </template>
                    <template slot="row-details" slot-scope="{ row }">
                        <slot name="row-details"  :row="row">
                            Override the "row-details" slot to customize row details.
                        </slot>
                    </template>
                </necrotable>
            </div>
        </div>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import ToggleDetails from '../table/action_columns/ToggleDetails.vue';

const RankingEntriesPage = {
    name: 'ranking-entries-page',
    components: {
        'necrotable': NecroTable,
        'toggle-details': ToggleDetails
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
            default: ''
        },
        header_columns: {
            type: Array,
            default: () => {
                return [];
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
            release: {},
            mode: {},
            seeded_type: {},
            filter_record_values: {},
            properties_loaded: false,
            api_request_parameters: {},
            filters: [
                SiteDropdownFilter
            ]
        }
    },
    computed: {
        breadcrumbs() {            
            let breadcrumbs = [];

            if(this.properties_loaded) {
                breadcrumbs = [
                    {
                        text: 'Rankings'
                    },
                    {
                        text: this.category_display_name,
                        href: '#/rankings/' + this.category_name
                    },
                    {
                        text: this.getDisplayName(),
                        href: this.$route.path
                    }
                ]
            }
            
            return breadcrumbs;
        }
    },
    methods: {
        getDisplayName() {
            let filter_records_length = this.filter_records.length;
            
            let display_name_segments = [];
            
            for(let index = 0; index < filter_records_length; index++) {
                let filter_record = this.filter_records[index];
                
                display_name_segments.push(this.filter_record_values[filter_record.name].display_name);
            }
            
            return '(' + display_name_segments.join(' ') + ') - ' + this.$route.params.date;
        }
    },
    created() {
        let filter_records_length = this.filter_records.length;
        
        let promises = [];
        
        for(let index = 0; index < filter_records_length; index++) {
            let dispatch_name = this.filter_records[index].store_name + '/loadAll';
            
            let promise = this.$store.dispatch(dispatch_name);
            
            promises.push(promise);
        }
        
        Promise.all(promises)
            .then(() => {   
                for(let index = 0; index < filter_records_length; index++) {
                    let filter_record = this.filter_records[index];
                    
                    let getter_name = filter_record.store_name + '/getByField';
                    let route_parameter_value = this.$route.params[filter_record.name];
                    
                    this.filter_record_values[filter_record.name] = this.$store.getters[getter_name]('name', route_parameter_value);
                    
                    this.api_request_parameters[filter_record.name] = route_parameter_value;
                }
                
                this.api_request_parameters['date'] = this.$route.params.date;
                
                this.properties_loaded = true;
            });
    }
};

export default RankingEntriesPage;
</script>

<template>
    <div class="container-fluid mr-0 ml-0 pl-0 pr-0">
        <div v-if="filters.length > 0" class="row pb-2">
            <component 
                v-for="(filter, filter_index) in filters" 
                :is="filter" 
                :key="filter.name" 
                @loaded="addLoadedFilter" 
                @selectedValueChanged="updateFromRequestParameter" 
            >
            </component>
        </div>
        <div class="row">
            <div v-if="has_search" class="col-sm-12 col-md-6 pb-2">
                <table-search v-if="has_search" @searchSubmitted="updateFromRequestParameter"></table-search>
            </div>
            <div v-if="showPagination" class="col-sm-12 col-md-6 col-lg-6 pb-2">
                <b-pagination v-if="pagination" size="lg" align="right" :total-rows="total_records" v-model="currentPage" :per-page="limit"></b-pagination>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table :id="id" class="table necrotable">
                    <thead>
                        <tr v-if="header_columns != null && number_of_columns > 0">
                            <th 
                                v-for="(header_column, header_column_index) in header_columns" 
                                :key="header_column_index" 
                                scope="col"
                            >
                                <template v-if="header_column['props'] != null">
                                    <component :is="header_column"></component>
                                </template>
                                <template v-else>
                                    {{ header_column }}
                                </template>
                                
                            </th>
                            <th v-if="hasActionsColumn()"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(row, row_index) in display_data">
                            <tr v-bind:class="{ 'nt-details-row-expanded': hasDetailsRow && detailsRowVisible(row_index) }">
                                <slot name="table-row" :row="row" :row_index="row_index">
                                    <td :colspan="number_of_columns">
                                        Override the "table-row" slot to replace this text.
                                    </td>
                                </slot>
                                <td v-if="hasActionsColumn()">
                                    <slot 
                                        name="actions-column" 
                                        :detailsRowVisible="detailsRowVisible" 
                                        :toggleDetailsRow="toggleDetailsRow"
                                        :row_index="row_index"
                                        :row="row"
                                    >
                                        Override the "row-details" slot to customize row details.
                                    </slot>
                                </td>
                            </tr>
                            <tr v-if="hasDetailsRow && detailsRowVisible(row_index)" class="nt-details-row">
                                <td :colspan="number_of_columns">
                                    <div class="border p-3 m-2">
                                        <slot name="row-details" :row="row">
                                            Override the "row-details" slot to customize row details.
                                        </slot>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="!display_data.length">
                            <td :colspan="number_of_columns">No matching records found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import bPagination from 'bootstrap-vue/es/components/pagination/pagination';
import TableSearch from './filters/TableSearch.vue';

const NecroTable = {
    name: 'necrotable',
    components: {
        'b-pagination': bPagination,
        'table-search': TableSearch
    },
    props: {
        id: {
            type: String,
            default: 'necrotable'
        },
        api_endpoint_url: {
            type: String,
            default: ''
        },
        default_request_parameters: {
            type: Object,
            default: () => {}
        },
        header_columns: {
            type: Array,
            default: () => []
        },
        has_search: {
            type: Boolean,
            default: false
        },
        filters: {
            type: Array,
            default: () => []
        },
        pagination: {
            type: Boolean,
            default: true
        },
        server_pagination: {
            type: Boolean,
            default: true
        },
        data_processor: {
            type: Function
        },
        has_details_row: {
            type: Boolean,
            default: false
        },
        has_action_column: {
            type: Boolean,
            default: false
        },
        page: {
            type: Number,
            default: 1
        },
        limit: {
            type: Number,
            default: 25
        }
    },
    data() {
        return {
            server_page: this.page || 1,
            internal_page: 1,
            request_parameters: this.default_request_parameters || {},
            response: {},
            number_of_columns: 0,
            total_records: 0,
            display_data: [],
            loaded_filters: [],
            hidden_filters: {},
            opened_details_rows: []
        };
    },
    computed: {
        currentPage: {
            get: function() {
                let current_page = null;
            
                if(this.server_pagination) {
                    current_page = this.server_page;
                }
                else {
                    current_page = this.internal_page;
                }

                return current_page;
            },
            set: function(current_page) {
                if(this.server_pagination) {

                    this.server_page = current_page;
                    
                    this.updateFromRequestParameter('page', this.server_page);
                }
                else {
                    this.internal_page = current_page;
                }
            }
        },
        showPagination() {
            return (this.pagination && this.total_records > this.limit);
        }
    },
    methods: {
        resetState() {
            this.server_page = this.page || 1;
            this.internal_page = 1;
            this.response = {};
            this.total_records = 0;
            this.display_data = [];
            this.opened_details_rows = [];
        },
        addLoadedFilter(name) {
            if(this.loaded_filters.indexOf(name) == -1) {
                this.loaded_filters.push(name);
            }
        },
        setRequestParameter(name, value) {
            if(value == null || value.length == 0) {
                if(this.request_parameters[name] != null) {
                    delete this.request_parameters[name];
                }
            }
            else {
                this.request_parameters[name] = value;
            }
        },
        updateFromRequestParameter(name, value) {
            if(this.request_parameters[name] != value) {
                this.addLoadedFilter(name);
                
                this.setRequestParameter(name, value);

                if(this.loaded_filters.length >= this.filters.length) {
                    this.updateFromServer();
                }
            }
        },
        updateFromServer() {
            axios.get(this.api_endpoint_url, {
                params: this.request_parameters
            })
            .then(response =>{
                let response_data = response.data;
                
                if(this.data_processor != null) {
                    response_data.data = this.data_processor(response_data.data);
                }
                
                this.response = response_data;
                
                this.$emit('serverResponseReceived', this.response);
            })
            .catch(error =>{
                alert('There was a problem trying to retrieve data. Please wait a moment and try again.');
                
                this.$emit('serverResponseFailed', error);
            })
            .then(function () {
                
            });
        },
        getStartOffset() {
            return (this.internal_page - 1) * this.limit;
        },
        getEndOffset() {
            return this.limit - 1;
        },
        setDisplayData() {
            if(this.response['data'] != null) {
                this.opened_details_rows = [];
                
                this.display_data = this.response.data.slice(this.getStartOffset(), this.getEndOffset());
            }
        },
        hasDetailsRow() {
            return (this.has_details_row && this.$scopedSlots['row-details'] != null);
        },
        detailsRowVisible(row_index) {
            return (this.opened_details_rows.indexOf(row_index) != -1);
        },
        toggleDetailsRow(row_index) {
            let index = this.opened_details_rows.indexOf(row_index);
            
            if(index > -1) {
                this.opened_details_rows.splice(index, 1);
            }
            else {
                this.opened_details_rows.push(row_index);
            }
        },
        hasActionsColumn() {
            return (this.has_action_column && this.$scopedSlots['actions-column'] != null);
        }
    },
    mounted() {
        this.number_of_columns = this.header_columns.length;
        
        if(this.number_of_columns > 0 && this.hasActionsColumn()) {
            this.number_of_columns += 1;
        }
        
        if(this.api_endpoint_url.length > 0) {
            if(this.has_pagination && this.server_pagination) {
                this.setRequestParameter('page', this.server_page);
                this.setRequestParameter('limit', this.limit);
            }
        }
        
        if(this.filters.length == 0) {
            this.updateFromServer();
        }
    },
    watch: {
        api_endpoint_url() {
            this.resetState();
            
            this.updateFromServer();
        },
        response() {
            /* ---------- Set response metadata ---------- */
            
            if(this.response['meta'] != null) {
                let response_meta = this.response.meta;
                
                if(response_meta['total'] != null) {
                    this.total_records = response_meta['total'];
                }
            }
            
            /* ---------- Initialize display_data ---------- */
            
            this.setDisplayData();
        },
        internal_page() {            
            this.setDisplayData();
        }
    }
};

export default NecroTable;
</script>

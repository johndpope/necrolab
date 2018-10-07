<template>
    <div class="container-fluid">
        <div v-if="filters.length > 0" class="row">
            <div class="col-sm-12 col-md-6 col-lg-4">
                <component v-for="filter in filters" :is="filter" :key="filter.name" @initialValueSet="setRequestParameter" @selectedValueChanged="updateFromRequestParameter">
                </component>
            </div>
        </div>
        <div class="row">
            <div v-if="has_search" class="col-sm-12 col-md-6 pt-2 pb-2">
                <table-search v-if="has_search" @searchSubmitted="updateFromRequestParameter"></table-search>
            </div>
            <!-- ml-auto -->
            <div v-if="showPagination" class="col-sm-12 col-md-6 col-lg-6 pt-2 pb-2">
                <b-pagination v-if="pagination" size="lg" align="right" :total-rows="total_records" v-model="currentPage" :per-page="limit"></b-pagination>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table :id="id" class="table table-striped">
                    <thead>
                        <tr v-if="columns.length > 0">
                            <th 
                                v-for="column in columns" 
                                :key="column.field" 
                                scope="col"
                            >
                                {{column.label}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in display_data" :key="row.id">
                            <template v-for="(column, key) in columns">
                                <table-cell>
                                    <template slot="cell-value" slot-scope="value">            
                                        <template v-if="column['component'] != null">
                                            <component :is="column['component']" :row_data="row"></component>
                                        </template>
                                        <template v-else>
                                            {{ row[column.field] }}
                                        </template>
                                    </template>
                                </table-cell>
                            </template>
                        </tr>
                        <tr v-if="!display_data.length">
                            <td :colspan="columns.length">No matching records found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row pt-2">
            <div v-if="showPagination" class="col-12">
                <b-pagination v-if="pagination" size="lg" align="right" :total-rows="total_records" v-model="currentPage" :per-page="limit"></b-pagination>
            </div>
        </div>
    </div>
</template>

<script>
import TableSearch from './filters/TableSearch.vue';
import bPagination from 'bootstrap-vue/es/components/pagination/pagination';
import TableCell from './fields/TableCell.vue';

const NecroTable = {
    name: 'necrotable',
    components: {
        'table-search': TableSearch,
        'b-pagination': bPagination,
        'table-cell': TableCell
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
        columns: {
            type: Array,
            default: () => []
        },
        request_pagination: {
            type: Boolean,
            default: true
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
            search: '',
            server_page: this.page || 1,
            internal_page: 1,
            request_parameters: {},
            response: {},
            total_records: 0,
            display_data: [],
            paginated_data: {},
            paginated_display_data: {}
        };
    },
    computed: {
        currentPage: {
            get: function() {
                let current_page = null;
            
                if(this.server_pagination) {
                    current_page = this.sever_page;
                }
                else {
                    current_page = this.internal_page;
                }
                
                return current_page;
            },
            set: function(current_page) {
                if(this.server_pagination) {
                    this.sever_page = current_page;
                    
                    this.updateFromRequestParameter('page', this.sever_page);
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
            this.setRequestParameter(name, value);
            
            this.updateFromServer();
        },
        updateFromServer() {            
            axios.get(this.api_endpoint_url, {
                params: this.request_parameters
            })
            .then(response =>{
                this.response = response.data;
                
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
                this.display_data = this.response.data.slice(this.getStartOffset(), this.getEndOffset());
            }
        }
    },
    mounted() {
        if(this.api_endpoint_url.length > 0) {
            if(this.server_pagination) {
                this.setRequestParameter('page', this.server_page);
                this.setRequestParameter('limit', this.limit);
            }
        }
    },
    watch: {
        response() {
            /* ---------- Set response metadata ---------- */
            
            if(this.response['meta'] != null) {
                let response_meta = this.response.meta;
                
                if(response_meta['current_page'] != null) {
                    this.server_page = response_meta['current_page'];
                }
                
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

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
            <div v-if="hasPagination" class="col-sm-12 col-md-6 col-lg-6 pb-2">
                <b-pagination v-if="showPagination" size="lg" align="right" :total-rows="totalRecords" v-model="currentPage" :per-page="recordsPerPage">
                </b-pagination>
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
                        <template v-if="!dataset.loading && totalRecords > 0" v-for="(record, row_index) in dataset.data">
                            <tr v-bind:class="{ 'nt-details-row-expanded': hasDetailsRow && detailsRowVisible(row_index) }">
                                <slot name="table-row" :row="record" :row_index="row_index">
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
                                        :row="record"
                                    >
                                        Override the "row-details" slot to customize row details.
                                    </slot>
                                </td>
                            </tr>
                            <tr v-if="hasDetailsRow && detailsRowVisible(row_index)" class="nt-details-row">
                                <td :colspan="number_of_columns">
                                    <div class="border p-3 m-2">
                                        <slot name="row-details" :row="record">
                                            Override the "row-details" slot to customize row details.
                                        </slot>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="!dataset.loading && totalRecords === 0">
                            <td :colspan="number_of_columns">No matching records found</td>
                        </tr>
                        <tr v-if="dataset.loading">
                            <td :colspan="number_of_columns">Loading...</td>
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
        dataset: {
            type: Object
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
        has_details_row: {
            type: Boolean,
            default: false
        },
        has_action_column: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            number_of_columns: 0,
            loaded_filters: [],
            opened_details_rows: []
        };
    },
    computed: {
        currentPage: {
            get: function() {
                return this.dataset.getPage();
            },
            set: function(current_page) {
                if(current_page != this.dataset.getPage()) {
                    this.dataset.setPage(current_page);
                }
            }
        },
        hasPagination() {
            return this.dataset.hasPagination();
        },
        showPagination() {
            return (this.dataset.total_records > this.dataset.getRecordsPerPage());
        },
        recordsPerPage() {
            return this.dataset.getRecordsPerPage();
        },
        totalRecords() {
            return this.dataset.total_records;
        }
    },
    methods: {
        resetState() {
            this.opened_details_rows = [];
        },
        addLoadedFilter(name) {
            if(this.loaded_filters.indexOf(name) == -1) {
                this.loaded_filters.push(name);
            }
        },
        updateFromRequestParameter(name, value) {
            if(this.dataset.getRequestParameter(name) != value) {
                this.addLoadedFilter(name);
                
                this.dataset.setRequestParameter(name, value);

                if(this.loaded_filters.length >= this.filters.length) {
                    this.dataset.fetch();
                }
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
    watch: {
        'dataset.data'() {
            this.opened_details_rows = [];
        }
    },
    mounted() {
        this.number_of_columns = this.header_columns.length;
        
        if(this.number_of_columns > 0 && this.hasActionsColumn()) {
            this.number_of_columns += 1;
        }
        
        if(this.filters.length == 0) {
            this.dataset.fetch();
        }
    }
};

export default NecroTable;
</script>

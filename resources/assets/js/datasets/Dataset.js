import Vue from 'vue';

export default class Dataset {
    constructor(name, fetch_url) {        
        this.name = name;
        this.fetch_url = fetch_url;
        this.enablePagination();
        this.enableServerPagination();
        this.records_per_page = 100;
        
        this.setPage(1, false);
        this.setTotalRecords(0);
        this.setNotLoading();
        this.setNotError();
        
        this.request_parameters = {};
        this.loading = false;
        
        this.setFetchedData([]);
        this.setData([]);
    }
    
    getName() {
        return this.name;
    }
    
    getFetchUrl() {
        return this.fetch_url;
    }
    
    enablePagination() {
        this.has_pagination = true;
    }
    
    disablePagination() {
        this.has_pagination = false;
    }
    
    hasPagination() {
        return this.has_pagination;
    }
    
    enableServerPagination() {
        this.server_side_pagination = true;
    }
    
    disableServerPagination() {
        this.server_side_pagination = false;
    }
    
    enableInternalPagination() {
        this.server_side_pagination = false;
    }
    
    disableInternalPagination() {
        this.server_side_pagination = true;
    }
    
    setPage(page, trigger_pagination = true) {
        if(page != this.page) {
            Vue.set(this, 'page', parseInt(page, 10));
            
            if(this.has_pagination && trigger_pagination) {
                if(this.server_side_pagination) {
                    this.fetch();
                }
                else {
                    this.setInternalPageData();
                }
            }
        }
    }
    
    getPage() {
        return this.page;
    }
    
    setRecordsPerPage(records_per_page) {
        this.records_per_page = parseInt(records_per_page, 10);
    }
    
    getRecordsPerPage() {
        return this.records_per_page;
    }
    
    setTotalRecords(total_records) {
        Vue.set(this, 'total_records', parseInt(total_records, 10));
    }
    
    getTotalRecords() {
        return this.total_records;
    }
    
    setRequestParameter(name, value) {
        if(value == null || value.length == 0) {
            if(this.request_parameters.hasOwnProperty(name)) {
                delete this.request_parameters[name];
            }
        }
        else {
            this.request_parameters[name] = value;
        }
    }
    
    getRequestParameter(name) {
        let value = null;
        
        if(this.request_parameters[name] != null) {
            value = this.request_parameters[name];
        }

        return value;
    }
    
    setLoading() {
        Vue.set(this, 'loading', true);
    }
    
    setNotLoading() {
        Vue.set(this, 'loading', false);
    }
    
    setError() {
        Vue.set(this, 'error', true);
    }
    
    setNotError() {
        Vue.set(this, 'error', false);
    }
    
    setFetchedData(data) {
        Vue.set(this, 'fetched_data', data);
    }
    
    setData(data) {
        Vue.set(this, 'data', data);
    }
    
    setInternalPageData() {
        const start_offset = (this.page - 1) * this.records_per_page;
        
        this.setData(this.fetched_data.slice(start_offset, (start_offset + this.records_per_page)));
    }
    
    fetch() {
        return new Promise((resolve, reject) => {
            const self = this;
            
            if(self.loading === false) {
                self.setLoading();
                self.setNotError();
                
                const request_parameters = self.request_parameters;
                
                if(self.has_pagination && self.server_side_pagination) {
                    request_parameters.page = self.page;
                    request_parameters.limit = self.records_per_page;
                }

                axios.get(self.fetch_url, {
                    params: request_parameters
                })
                .then(response => {
                    this.setFetchedData(response.data.data);
                    
                    if(self.has_pagination) {
                        if(self.server_side_pagination) {
                            if(response.data['meta'] != null) {
                                const response_meta = response.data.meta;
                                
                                if(response_meta['total'] != null) {
                                    self.setTotalRecords(response_meta.total);
                                }
                            }
                            
                            self.setData(self.fetched_data);
                        }
                        else {
                            self.setTotalRecords(self.fetched_data.length);
                            
                            self.setInternalPageData();
                        }
                    }
                    else {
                        self.setTotalRecords(self.fetched_data.length);
                        
                        self.setData(self.fetched_data);
                    }
                    
                    resolve();
                })
                .catch(error => {
                    self.setError();
                    
                    alert('There was a problem trying to retrieve data. Please wait a moment and try again.');
                    
                    reject();
                })
                .then(function() {
                    self.setNotLoading();
                });
            }
        });
    }
}

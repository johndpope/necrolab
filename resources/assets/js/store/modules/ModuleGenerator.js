const ModuleGenerator = {
    getNew(api_endpoint_url, filter_property_name) {
        return {
            namespaced: true,
            state: {
                loading_promise: null,
                records: [],
                records_by_name: {},
                selected: {},
                filter_stores: []
            },
            mutations: {
                setAll(state, records) {
                    state.records = records;
                    
                    let records_length = records.length;
                    
                    for(let index = 0; index < records_length; index++) {
                        state.records_by_name[records[index].name] = records[index];
                    }
                },
                setSelected(state, record) {
                    state.selected = record;
                },
                setFilterStores(state, filter_stores) {
                    state.filter_stores = filter_stores;
                }
            },
            getters: {
                getAll: state => {
                    return state.records;
                },
                getAllByNames: (state) => (names) => {
                    let records = [];
                    
                    let names_length = names.length;
                    
                    for(let index = 0; index < names_length; index++) {
                        let name = names[index];
                        
                        if(state.records_by_name[name] != null) {
                            records.push(state.records_by_name[name]);
                        }
                    }

                    return records;
                },
                getByName: (state) => (name) => {
                    let record = {};
                    
                    if(state.records_by_name[name] != null) {
                        record = state.records_by_name[name];
                    }

                    return record;
                },
                getFiltered: (state, getters, root_state, root_getters) => {
                    let filtered_names = [];
                    let first_pass_completed = false;
                    
                    let filter_stores_length = state.filter_stores.length;

                    if(filter_stores_length > 0) {                        
                        let records_length = state.records.length;

                        for(let index = 0; index < filter_stores_length; index++) {
                            let filter_store_name = state.filter_stores[index];
                            
                            let filter_option = root_getters[filter_store_name + '/getSelected'];
                            
                            if(typeof filter_option == 'string') {
                                filter_option = root_getters[filter_store_name + '/getByField']('name', filter_option);
                            }
                            else if(filter_option['name'] == null) {
                                filter_option = {};
                            }

                            if(filter_option[filter_property_name] != null) {
                                if(!first_pass_completed || filter_option[filter_property_name].length < filtered_names.length) {
                                    filtered_names = filter_option[filter_property_name];
                                    
                                    first_pass_completed = true;
                                }
                            }
                        }
                    }
                    
                    return getters.getAllByNames(filtered_names);
                },
                getByField: (state) => (field, value) => {
                    return state.records.find(record => record[field] === value);
                },
                getSelected: state => {
                    return state.selected;
                }
            },
            actions: {
                loadAll({ commit, state }) {
                    if(state.loading_promise == null) {
                        state.loading_promise = new Promise((resolve, reject) => {
                            if(state.records.length == 0) {
                                axios.get(api_endpoint_url)
                                .then(response => {                    
                                    commit('setAll', response.data.data);
                                    
                                    resolve();
                                })
                                .catch(error => {                    
                                    reject();
                                });
                            }
                            else {
                                resolve();
                            }
                        });
                    }
                    
                    return state.loading_promise;
                },
                loadDependencies(context) {
                    return new Promise((resolve, reject) => {
                        let filter_stores_length = context.state.filter_stores.length;
                        
                        let promises = [];
                        
                        for(let index = 0; index < filter_stores_length; index++) {
                            let promise = this.dispatch[context.state.filter_stores[index] + '/loadAll'];
                            
                            promises.push(promise);
                        }
                        
                        Promise.all(promises).then(() => {
                            resolve();
                        });
                    });
                }
            }
        };
    }
};

export default ModuleGenerator;

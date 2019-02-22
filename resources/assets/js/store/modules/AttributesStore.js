const AttributesStore = {
    namespaced: true,
    state: {
        names: [
            'leaderboard_sources',
            'leaderboard_types',
            'characters',
            'releases',
            'modes',
            'seeded_types',
            'multiplayer_types',
            'soundtracks',
            'leaderboard_details_columns',
            'data_types',
            'sites'
        ],
        loading_promise: null,
        loaded: false,
        records: {},
        records_by_name: {},
        default_records: {},
        filter_stores: {},
        selected: {}
    },
    mutations: {
        setAll(state, records) {
            state.names.forEach((attribute_name) => {
                if(records[attribute_name] != null) {
                    state.records[attribute_name] = records[attribute_name];
                    
                    state.filter_stores[attribute_name] = [];
                    
                    state.selected[attribute_name] = '';
                    
                    state.records_by_name[attribute_name] = {};
                    
                    records[attribute_name].forEach((record) => {
                        state.records_by_name[attribute_name][record.name] = record;
                        
                        if(record['is_default'] != null && record.is_default == 1) {
                            state.default_records[attribute_name] = record;
                        }
                    });
                }
            });
        },
        setSelected(state, payload) {
            // In order to make state.selected reactive it needs to be cleared and reset from a local variable.
            let all_selected = state.selected;
            
            state.selected = {};
            
            all_selected[payload.attribute] = payload.record;
            
            state.selected = all_selected;
        },
        setFilterStores(state, payload) {
            state.filter_stores[payload.attribute] = payload.filter_stores;
        }
    },
    getters: {
        getAll: state => (attribute) => {
            return state.records[attribute];
        },
        getAllByNames: (state) => (attribute, names) => {
            let records = [];
            
            names.forEach((name) => {
                if(state.records_by_name[attribute][name] != null) {
                    records.push(state.records_by_name[attribute][name]);
                }
            });

            return records;
        },
        getByName: (state) => (attribute, name) => {
            let record = {};
            
            if(state.records_by_name[attribute][name] != null) {
                record = state.records_by_name[attribute][name];
            }

            return record;
        },
        getFiltered: (state, getters, root_state, root_getters) => (attribute) => {
            let filtered_names = [];

            /* 
             * 1. Loop through each filter store
             * 2. Get the selected value of each filter store (if string then grab record by name)
             * 3. Check if filter store record contains the name of the current attribute
             * 4. If filtered_names has not been set then set the filtered record names from the filter store property.
             * 5. If filtered_names has been set then only set filtered record names if the count is less than the current count.
             */

            state.filter_stores[attribute].forEach((filter_store_name) => {
                let filter_option = state.selected[filter_store_name];

                if(typeof filter_option == 'string') {
                    filter_option = state.records_by_name[filter_store_name][filter_option];
                }
                else if(filter_option['name'] == null) {
                    filter_option = {};
                }

                if(filter_option[attribute] != null) {
                    if(filtered_names.length == 0 || filter_option[attribute].length < filtered_names.length) {
                        filtered_names = filter_option[attribute];
                    }
                }
            });
            
            return getters.getAllByNames(attribute, filtered_names);
        },
        getByField: (state) => (attribute, field, value) => {
            return state.records[attribute].find(record => record[field] === value);
        },
        getSelected: state => (attribute) => {
            let selected = state.selected[attribute];
            
            if(typeof filter_option == 'string' && selected.length == 0) {
                selected = null;
            }
            else if(typeof filter_option == 'object' && filter_option['name'] == null) {
                selected = null;
            }
            
            if(selected == null) {
                if(state.default_records[attribute] != null) {
                    selected = state.default_records[attribute];
                }
                else if(state.records[attribute][0] != null) {
                    selected = state.records[attribute][0];
                }
            }
            
            return selected;
        }
    },
    actions: {
        load({ commit, getters, state }) {
            if(state.loading_promise == null) {
                state.loading_promise = new Promise((resolve, reject) => {                    
                    if(!state.loaded) {
                        axios.get('/storage/attributes.json')
                        .then(response => {
                            commit('setAll', response.data);
                            
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
        }
    }
}

export default AttributesStore;

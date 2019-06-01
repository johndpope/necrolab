const ModuleGenerator = {
    getNew(attribute_name) {
        return {
            namespaced: true,
            state: {
                attribute_name: attribute_name,
                records: [],
                records_by_name: {},
                selected: {},
                default_record: {},
                filter_stores: []
            },
            mutations: {
                setAll(state, records) {
                    state.records = records;

                    records.forEach((record) => {
                        state.records_by_name[record.name] = record;

                        if(record['is_default'] != null && record.is_default === 1) {
                            state.default_record = record;
                        }
                    });
                },
                setSelected(state, record) {
                    if(record != null) {
                        if(record['name'] != null) {
                            if (state.records_by_name[record.name] == null) {
                                record = {};
                            }
                        }
                        else {
                            if(state.records_by_name[record] != null) {
                                record = state.records_by_name[record];
                            }
                            else {
                                record = {};
                            }
                        }
                    }
                    else {
                        record = {};
                    }

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

                    names.forEach((name) => {
                        if(state.records_by_name[name] != null) {
                            records.push(state.records_by_name[name]);
                        }
                    })

                    return records;
                },
                getAllByName: (state) => {
                    return state.records_by_name;
                },
                getByName: (state) => (name) => {
                    let record = {};

                    if(state.records_by_name[name] != null) {
                        record = state.records_by_name[name];
                    }

                    return record;
                },
                getFiltered: (state, getters, root_state, root_getters) => {
                    /*
                    * 1. Loop through filter_stores
                    * 2. Get the selected value of each filter store
                    * 3. Check if filter store record contains the name of the current attribute
                    * 4. If filtered_names has not been set then set the filtered record names from the filter store property.
                    * 5. If filtered_names has been set then only set filtered record names if the count is less than the current count.
                    */
                    let filtered_names = [];

                    state.filter_stores.forEach((filter_store_name) => {
                        let filter_option = root_getters[`${filter_store_name}/getSelected`];

                        if(filter_option['name'] == null) {
                            filter_option = {};
                        }

                        if(filter_option[state.attribute_name] != null) {
                            if(filtered_names.length == 0 || filter_option[state.attribute_name].length < filtered_names.length) {
                                filtered_names = filter_option[state.attribute_name];
                            }
                        }
                    });

                    return getters.getAllByNames(filtered_names);
                },
                getByField: (state) => (field, value) => {
                    return state.records.find(record => record[field] === value);
                },
                getSelected: state => {
                    return state.selected;
                },
                getDefault: state => {
                    return state.default_record;
                },
            }
        };
    }
};

export default ModuleGenerator;

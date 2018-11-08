const ModuleGenerator = {
    getNew(api_endpoint_url) {
        return {
            namespaced: true,
            state: {
                records: [],
                selected: {},
            },
            mutations: {
                setAll(state, records) {
                    state.records = records;
                },
                setSelected(state, record) {
                    state.selected = record;
                }
            },
            getters: {
                getAll: state => {
                    return state.records;
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
                    return new Promise((resolve, reject) => {
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
            }
        };
    }
};

export default ModuleGenerator;

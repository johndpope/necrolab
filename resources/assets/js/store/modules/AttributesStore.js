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
            'details_columns',
            'data_types',
            'sites'
        ],
        loading_promise: null,
        loaded: false
    },
    actions: {
        load({ commit, getters, state }) {
            if(state.loading_promise == null) {
                state.loading_promise = new Promise((resolve, reject) => {                    
                    if(!state.loaded) {
                        axios.get('/storage/attributes.json')
                        .then(response => {
                            state.names.forEach((attribute_name) => { 
                                if(response.data[attribute_name] != null) {
                                    commit(`${attribute_name}/setAll`, response.data[attribute_name], { root: true });
                                }
                            });
                            
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
 

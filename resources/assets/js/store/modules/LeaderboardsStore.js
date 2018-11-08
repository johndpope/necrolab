const LeaderboardsStore = {
    namespaced: true,
    state: {
        records: {},
    },
    mutations: {
        setRecord(state, record) {
            state.records[record.url_name] = record;
        }
    },
    getters: {
        getRecord: (state) => (url_name) => {
            return state.records[url_name];
        }
    },
    actions: {
        load({ commit, state }, url_name) {
            return new Promise((resolve, reject) => {
                if(state.records[url_name] == null) {
                    axios.get('/api/1/leaderboards/by_url_name/' + url_name)
                    .then(response => {                    
                        commit('setRecord', response.data.data);
                        
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

export default LeaderboardsStore;

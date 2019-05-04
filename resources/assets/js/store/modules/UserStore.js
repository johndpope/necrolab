const UserStore = {
    namespaced: true,
    state: {
        record: {}
    },
    mutations: {
        setRecord(state, record) {
            state.record = record;
        }
    },
    getters: {
        get: (state) => {
            state.record = record;
        },
        isAuthenticated: (state) => {
            return record != null && record[id] != null;
        }
    },
    actions: {
        load({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                if(
                    state.records[payload.leaderboard_source] == null ||
                    (state.records[payload.leaderboard_source] != null && state.records[payload.leaderboard_source][payload.player_id] == null)
                ) {
                    axios.get('/api/1/player', {
                        params: {
                            leaderboard_source: payload.leaderboard_source,
                            player_id: payload.player_id
                        }
                    })
                    .then(response => {       
                        commit('setRecord', {
                            leaderboard_source: payload.leaderboard_source,
                            record: response.data.data
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
    }
};

export default UserStore;

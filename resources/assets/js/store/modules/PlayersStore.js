const PlayersStore = {
    namespaced: true,
    state: {
        records: {}
    },
    mutations: {
        setRecord(state, { leaderboard_source, record }) {
            if(state.records[leaderboard_source] == null) {
                state.records[leaderboard_source] = {};
            }
            
            state.records[leaderboard_source][record.player_id] = record;
        }
    },
    getters: {
        get: (state) => (leaderboard_source, player_id) => {
            let record = {};
            
            if(state.records[leaderboard_source] != null && state.records[leaderboard_source][player_id] != null) {
                record = state.records[leaderboard_source][player_id];
            }
            
            return record;
        }
    },
    actions: {
        load({ commit, state }, { leaderboard_source, player_id }) {
            return new Promise((resolve, reject) => {
                if(
                    state.records[leaderboard_source] == null ||
                    (state.records[leaderboard_source] != null && state.records[leaderboard_source][player_id] == null)
                ) {
                    axios.get('/api/1/player', {
                        params: {
                            leaderboard_source: leaderboard_source,
                            player_id: player_id
                        }
                    })
                    .then(response => {                    
                        commit('setRecord', {
                            leaderboard_source: leaderboard_source,
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

export default PlayersStore;

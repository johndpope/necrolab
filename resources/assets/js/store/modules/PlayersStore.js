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
            
            state.records[leaderboard_source][record.player.id] = record;
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

export default PlayersStore;

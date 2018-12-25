function get_record_hash(leaderboard_source, leaderboard_type, character, release, mode, seeded_type, multiplayer_type, soundtrack) {
    return leaderboard_source + leaderboard_type + character + release + mode + seeded_type + multiplayer_type + soundtrack;
};

const LeaderboardsStore = {
    namespaced: true,
    state: {
        records: {},
    },
    mutations: {
        //TODO: Add leaderboard_source to the response retrieval of leaderboards since record.leaderboard_source comes back as undefined right now
        setRecord(state, record) {
            let hash = get_record_hash(
                record.leaderboard_source,
                record.leaderboard_type,
                record.character,
                record.release,
                record.mode,
                record.seeded_type,
                record.multiplayer_type,
                record.soundtrack
            );
            
            state.records[hash] = record;
        }
    },
    getters: {
        getByAttributes: (state) => (leaderboard_source, leaderboard_type, character, release, mode, seeded_type, multiplayer_type, soundtrack) => {            
            let hash = get_record_hash(
                leaderboard_source,
                leaderboard_type,
                character,
                release,
                mode,
                seeded_type,
                multiplayer_type,
                soundtrack
            );
            
            let record = {};
            
            if(state.records[hash] != null) {
                record = state.records[hash];
            }
            
            return record;
        }
    },
    actions: {
        loadByAttributes({ commit, state }, { leaderboard_source, leaderboard_type, character, release, mode, seeded_type, multiplayer_type, soundtrack }) {
            return new Promise((resolve, reject) => {
                let hash = get_record_hash(
                    leaderboard_source,
                    leaderboard_type,
                    character,
                    release,
                    mode,
                    seeded_type,
                    multiplayer_type,
                    soundtrack
                );
                
                if(state.records[hash] == null) {
                    axios.get('/api/1/leaderboard/by_attributes', {
                        params: {
                            leaderboard_source: leaderboard_source,
                            leaderboard_type: leaderboard_type,
                            character: character,
                            release: release,
                            mode: mode,
                            seeded_type: seeded_type,
                            multiplayer_type: multiplayer_type,
                            soundtrack: soundtrack
                        }
                    })
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

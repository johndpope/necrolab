function get_record_hash(leaderboard_source, leaderboard_type, character, release, mode, seeded_type, multiplayer_type, soundtrack) {
    return leaderboard_source + leaderboard_type + character + release + mode + seeded_type + multiplayer_type + soundtrack;
};

const LeaderboardsStore = {
    namespaced: true,
    state: {
        records: {}
    },
    mutations: {
        setRecord(state, { leaderboard_source, record }) {
            const hash = get_record_hash(
                leaderboard_source,
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
            const hash = get_record_hash(
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
                const hash = get_record_hash(
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

export default LeaderboardsStore;

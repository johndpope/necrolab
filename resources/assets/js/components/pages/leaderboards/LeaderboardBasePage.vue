<script>
import BasePage from '../BasePage.vue';

const LeaderboardBasePage = {
    extends: BasePage,
    name: 'leaderboard-base-page',
    data() {
        return {
            leaderboard: {},
            leaderboard_source: {},
            leaderboard_type: {},
            character: {},
            release: {},
            mode: {},
            seeded_type: {},
            multiplayer_type: {},
            soundtrack: {},
            details_columns: []
        }
    },
    computed: {
        subTitle() {
            return this.leaderboard_source.display_name + ' ' + 
                this.character.display_name + ' ' + 
                this.leaderboard_type.display_name + ' ' + 
                this.release.display_name + ' ' + 
                this.mode.display_name + ' ' + 
                this.seeded_type.display_name + ' ' +
                this.multiplayer_type.display_name + ' ' +
                this.soundtrack.display_name;
        }
    },
    methods: {
        loadRecords(route_params) {
            return new Promise((resolve, reject) => {
                let promises = [];
                
                promises.push(this.$store.dispatch('leaderboards/loadByAttributes', {
                    leaderboard_source: route_params.leaderboard_source,
                    leaderboard_type: route_params.leaderboard_type,
                    character: route_params.character,
                    release: route_params.release,
                    mode: route_params.mode,
                    seeded_type: route_params.seeded_type,
                    multiplayer_type: route_params.multiplayer_type,
                    soundtrack: route_params.soundtrack
                }));

                Promise.all(promises).then(() => {
                    this.leaderboard_source = this.$store.getters['leaderboard_sources/getByName'](route_params.leaderboard_source);
                    this.leaderboard_type = this.$store.getters['leaderboard_types/getByName'](route_params.leaderboard_type);
                    this.character = this.$store.getters['characters/getByName'](route_params.character);
                    this.release = this.$store.getters['releases/getByName'](route_params.release);
                    this.mode = this.$store.getters['modes/getByName'](route_params.mode);
                    this.seeded_type = this.$store.getters['seeded_types/getByName'](route_params.seeded_type);
                    this.multiplayer_type = this.$store.getters['multiplayer_types/getByName'](route_params.multiplayer_type);
                    this.soundtrack = this.$store.getters['soundtracks/getByName'](route_params.soundtrack);
                    this.details_columns = this.$store.getters['details_columns/getAllByNames'](this.leaderboard_type.details_columns);
                    
                    this.leaderboard = this.$store.getters['leaderboards/getByAttributes'](
                        route_params.leaderboard_source,
                        route_params.leaderboard_type,
                        route_params.character,
                        route_params.release,
                        route_params.mode,
                        route_params.seeded_type,
                        route_params.multiplayer_type,
                        route_params.soundtrack
                    );

                    if(this.leaderboard['id'] != null) {
                        resolve();
                    }
                });
            });
        }
    }
};

export default LeaderboardBasePage;
</script> 

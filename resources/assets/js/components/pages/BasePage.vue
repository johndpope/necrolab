<script>
const BasePage = {
    name: 'base-page',
    data() {
        return {
            loaded: false
        }
    },
    methods: {
        loadState() {},
        initialize(route_parameters) {
            this.loaded = false;

            const route_segment_attributes = {
                leaderboard_source: 'leaderboard_sources',
                leaderboard_type: 'leaderboard_types',
                character: 'characters',
                release: 'releases',
                mode: 'modes',
                seeded_type: 'seeded_types',
                multiplayer_type: 'multiplayer_types',
                soundtrack: 'soundtracks'
            };
            
            Object.keys(route_segment_attributes).forEach((route_segment) => {
                let route_segment_value = null;
                
                if(route_parameters[route_segment] != null) {
                    route_segment_value = route_parameters[route_segment];
                }

                this.$store.commit(`${route_segment_attributes[route_segment]}/setSelected`, route_segment_value);
            });
            
            this.loadState(route_parameters);
        }
    },
    created() {
        let promise = this.$store.dispatch('attributes/load');
        
        promise.then(() => {
            this.initialize(this.$route.params);
        });
    },
    beforeRouteUpdate(to, from, next) {
        this.initialize(to.params);
        
        next();
    }
};

export default BasePage;
</script>

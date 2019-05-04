<template>
    <with-nav-body 
        :loaded="loaded"
        :show_breadcrumbs="false"
    >
        <h3>Connections!</h3>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';

const PlayerProfileConnections = {
    extends: BasePage,
    name: 'player-profile-connections',
    components: {
        'with-nav-body': WithNavBody
    },
    data() {
        return {
            player_id: '',
            leaderboard_source: {}
        }
    },
    methods: {
        loadState(route_params) {
            this.player_id = route_params.player_id,
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];

            this.loaded = true;
        }
    }
};

export default PlayerProfileConnections;
</script> 

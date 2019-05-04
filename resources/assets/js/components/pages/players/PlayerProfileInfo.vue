<template>
    <with-nav-body 
        :loaded="loaded"
        :show_breadcrumbs="false"
    >
        <h4>Connections</h4>
        <hr />
        <player-linked-sites :player="player"></player-linked-sites>
    </with-nav-body>
</template>

<script>
import BasePage from '../BasePage.vue';
import WithNavBody from '../../layouts/WithNavBody.vue';
import PlayerLinkedSites from '../../player/PlayerLinkedSites.vue';

const PlayerProfileInfo = {
    extends: BasePage,
    name: 'player-profile-info',
    components: {
        'with-nav-body': WithNavBody,
        'player-linked-sites': PlayerLinkedSites
    },
    data() {
        return {
            player_id: '',
            leaderboard_source: {},
            player: {}
        }
    },
    methods: {
        loadState(route_params) {
            this.player_id = route_params.player_id,
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getSelected'];
            
            this.player = this.$store.getters['players/get'](this.leaderboard_source.name, this.player_id);

            this.loaded = true;
        }
    }
};

export default PlayerProfileInfo;
</script> 

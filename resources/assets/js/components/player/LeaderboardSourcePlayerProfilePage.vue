<template>
    <player-profile-page>
    </player-profile-page>
</template>

<script>
import PlayerProfilePage from './PlayerProfilePage.vue';

export default {
    name: 'leaderboard-source-player-profile-page',
    components: {
        'player-profile-page': PlayerProfilePage,
    },
    methods: {
        setLeaderboardSource(leaderboard_source_name) {
            this.leaderboard_source = this.$store.getters['leaderboard_sources/getByField']('url_name', leaderboard_source_name);
        }
    },
    created() {        
        let promise = this.$store.dispatch('leaderboard_sources/loadAll');

        promise.then(() => {
            this.setLeaderboardSource(this.$route.params.leaderboard_source);
        });
    },
    beforeRouteUpdate(to, from, next) {
        this.setLeaderboardSource(to.params.leaderboard_source);
        
        next();
    }
};
</script>

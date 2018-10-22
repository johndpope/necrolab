<template>
    <leaderboards-listing-page name="deathless" display_name="Deathless" :has_seeded="false">
    </leaderboards-listing-page>
</template>

<script>
import LeaderboardsListingPage from '../leaderboards/LeaderboardsListingPage.vue';

export default {
    name: 'deathless-leaderboards-page',
    components: {
        'leaderboards-listing-page': LeaderboardsListingPage
    }
};
</script>

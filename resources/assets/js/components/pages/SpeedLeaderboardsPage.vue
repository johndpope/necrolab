<template>
    <leaderboards-listing-page name="speed" display_name="Speed">
    </leaderboards-listing-page>
</template>

<script>
import LeaderboardsListingPage from '../leaderboards/LeaderboardsListingPage.vue';

export default {
    name: 'speed-leaderboards-page',
    components: {
        'leaderboards-listing-page': LeaderboardsListingPage
    }
};
</script>

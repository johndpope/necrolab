<template>
    <leaderboards-listing-page name="score" display_name="Score">
    </leaderboards-listing-page>
</template>

<script>
import LeaderboardsListingPage from '../leaderboards/LeaderboardsListingPage.vue';

export default {
    name: 'score-leaderboards-page',
    components: {
        'leaderboards-listing-page': LeaderboardsListingPage
    }
};
</script>

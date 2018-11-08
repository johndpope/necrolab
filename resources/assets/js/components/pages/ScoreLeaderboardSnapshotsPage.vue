<template>
    <leaderboard-snapshots-page
        name="score"
        display_name="Score"
        details_column_name="score"
        details_column_display_name="Score"
    >
    </leaderboard-snapshots-page>
</template>

<script>
import LeaderboardSnapshotsPage from '../leaderboards/LeaderboardSnapshotsPage.vue';

const ScoreLeaderboardSnapshotsPage = {
    name: 'score-leaderboard-snapshots-page',
    components: {
        'leaderboard-snapshots-page': LeaderboardSnapshotsPage
    }
};

export default ScoreLeaderboardSnapshotsPage;
</script> 

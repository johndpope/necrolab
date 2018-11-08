<template>
    <leaderboard-snapshots-page
        name="deathless"
        display_name="Deathless"
        details_column_name="win_count"
        details_column_display_name="Win Count"
    >
    <template slot="details-column" slot-scope="{ row }">
        {{ row.win_count }}
    </template>
    </leaderboard-snapshots-page>
</template>

<script>
import LeaderboardSnapshotsPage from '../leaderboards/LeaderboardSnapshotsPage.vue';

const DeathlessLeaderboardSnapshotsPage = {
    name: 'deathless-leaderboard-snapshots-page',
    components: {
        'leaderboard-snapshots-page': LeaderboardSnapshotsPage
    }
};

export default DeathlessLeaderboardSnapshotsPage;
</script> 

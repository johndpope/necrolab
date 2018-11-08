<template>
    <leaderboard-snapshots-page
        name="speed"
        display_name="Speed"
        details_column_name="time"
        details_column_display_name="Time"
    >
    <template slot="details-column" slot-scope="{ row }">
        <seconds-to-time :unformatted="row.time" :include_hours="true" :zero_pad_hours="true"></seconds-to-time>
    </template>
    </leaderboard-snapshots-page>
</template>

<script>
import LeaderboardSnapshotsPage from '../leaderboards/LeaderboardSnapshotsPage.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';

const SpeedLeaderboardSnapshotsPage = {
    name: 'speed-leaderboard-snapshots-page',
    components: {
        'leaderboard-snapshots-page': LeaderboardSnapshotsPage,
        'seconds-to-time': SecondsToTime
    }
};

export default SpeedLeaderboardSnapshotsPage;
</script> 

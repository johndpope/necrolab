<template>
    <leaderboard-entries-page
        name="score"
        display_name="Score"
        details_column_display_name="Score"
        :has_details_row="false"
    >
        <template slot="entry-details" slot-scope="{ row }">
            <seconds-to-time :unformatted="row.pb.time" :include_hours="true" :zero_pad_hours="false"></seconds-to-time>
        </template>
    </leaderboard-entries-page>
</template>

<script>
import LeaderboardEntriesPage from '../leaderboards/LeaderboardEntriesPage.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';

const SpeedLeaderboardEntriesPage = {
    name: 'speed-leaderboard-entries-page',
    components: {
        'leaderboard-entries-page': LeaderboardEntriesPage,
        'seconds-to-time': SecondsToTime
    }
};

export default SpeedLeaderboardEntriesPage;
</script> 

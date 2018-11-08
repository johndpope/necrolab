<template>
    <leaderboard-entries-page
        name="score"
        display_name="Score"
        details_column_display_name="Score"
    >
        <template slot="entry-details" slot-scope="{ row }">
            {{ row.pb.score }}
        </template>
        <template slot="row-details" slot-scope="{ row }">
            <leaderboard-score-entry-details-table :record="row">
            </leaderboard-score-entry-details-table>
        </template>
    </leaderboard-entries-page>
</template>

<script>
import LeaderboardEntriesPage from '../leaderboards/LeaderboardEntriesPage.vue';
import LeaderboardScoreEntryDetailsTable from '../table/LeaderboardScoreEntryDetailsTable.vue';

const ScoreLeaderboardEntriesPage = {
    name: 'score-leaderboard-entries-page',
    components: {
        'leaderboard-entries-page': LeaderboardEntriesPage,
        'leaderboard-score-entry-details-table': LeaderboardScoreEntryDetailsTable
    }
};

export default ScoreLeaderboardEntriesPage;
</script>

<template>
    <leaderboard-entries-page
        name="deathless"
        display_name="Deathless"
        details_column_display_name="Wins"
        :has_seed="false"
        :has_replay="false"
    >
        <template slot="entry-details" slot-scope="{ row }">
            {{ row.pb.win_count }}
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

const DeathlessLeaderboardEntriesPage = {
    name: 'deathless-leaderboard-entries-page',
    components: {
        'leaderboard-entries-page': LeaderboardEntriesPage,
        'leaderboard-score-entry-details-table': LeaderboardScoreEntryDetailsTable
    }
};

export default DeathlessLeaderboardEntriesPage;
</script>

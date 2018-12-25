<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th scope="col">Zone</th>
                    <th scope="col">Level</th>
                    <th scope="col">Win</th>
                    <th scope="col">Killed By</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ record.pb.zone }}
                    </td>
                    <td>
                        {{ record.pb.level }}
                    </td>
                    <td>
                        <win :record="record"></win>
                    </td>
                    <td>
                        <killed-by :record="record"></killed-by>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import Win from '../leaderboards/Win.vue';
import KilledBy from '../leaderboards/KilledBy';

const LeaderboardEntryDetailsTable = {
    name: 'leaderboard-score-entry-details-table',
    components: {
        'win': Win,
        'killed-by': KilledBy
    },
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    }
};

export default LeaderboardEntryDetailsTable;
</script>

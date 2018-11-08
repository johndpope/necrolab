<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Score</th>
                    <th scope="col">Speed</th>
                    <th scope="col">Deathless</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Ranks</th>
                    <td>
                        {{ getCategoryField('score', 'rank') }}
                    </td>
                    <td>
                        {{ getCategoryField('speed', 'rank') }}
                    </td>
                    <td>
                        {{ getCategoryField('deathless', 'rank') }}
                    </td>
                </tr>
                <tr>
                    <th scope="row">Points</th>
                    <td>
                        <rounded-decimal :original_number="getCategoryField('score', 'points')"></rounded-decimal>
                    </td>
                    <td>
                        <rounded-decimal :original_number="getCategoryField('speed', 'points')"></rounded-decimal>
                    </td>
                    <td>
                        <rounded-decimal :original_number="getCategoryField('deathless', 'points')"></rounded-decimal>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Score/Time/Wins</th>
                    <td>
                        {{ getCategoryField('score', 'score') }}
                    </td>
                    <td>
                        <seconds-to-time :unformatted="getCategoryField('speed', 'time')" :include_hours="true" :zero_pad_hours="true">
                        </seconds-to-time>
                    </td>
                    <td>
                        {{ getCategoryField('deathless', 'win_count') }}
                    </td>
                </tr> 
            </tbody>
        </table>
    </div>
</template>

<script>
import RoundedDecimal from '../formatting/RoundedDecimal.vue';
import SecondsToTime from '../formatting/SecondsToTime.vue';

const RankingDetailsTable = {
    name: 'ranking-details-table',
    components: {
        'rounded-decimal': RoundedDecimal,
        'seconds-to-time': SecondsToTime
    },
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    },
    methods: {
        getCategoryField(category_name, field_name) {
            let field_value = '';
            
            if(this.record[category_name] != null) {
                field_value = this.record[category_name][field_name];
            }
            
            return field_value;
        }
    }
};

export default RankingDetailsTable;
</script>

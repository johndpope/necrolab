<template>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
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
                    <th scope="row">Players</th>
                    <td>
                        {{ getCategoryField('score', 'players') }}
                    </td>
                    <td>
                        {{ getCategoryField('speed', 'players') }}
                    </td>
                    <td>
                        {{ getCategoryField('deathless', 'players') }}
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
import SecondsToTime from '../formatting/SecondsToTime.vue';

const RankingSummaryDetailsTable = {
    name: 'ranking-details-table',
    components: {
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

export default RankingSummaryDetailsTable;
</script>

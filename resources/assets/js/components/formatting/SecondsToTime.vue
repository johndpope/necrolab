<template>
    <span>
        {{ time }}
    </span>
</template>

<script>
import TimeUtility from '../../utilities/TimeUtility.js';

const SecondsToTime = {
    name: 'seconds-to-time',
    props: {
        unformatted: {
            type: [
                Number,
                String
            ]
        },
        include_hours: {
            type: Boolean,
            default: false
        },
        zero_pad_hours: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        time() {
            return TimeUtility.secondsToTime(this.unformatted, this.include_hours, this.zero_pad_hours);
        }
    }
};

export default SecondsToTime;
</script>

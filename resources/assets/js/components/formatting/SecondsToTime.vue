<template>
    <span>
        {{ time }}
    </span>
</template>

<script>
import format from 'date-fns/format';
import addSeconds from 'date-fns/add_seconds';

const SecondsToTime = {
    name: 'seconds-to-time',
    props: {
        seconds: {
            type: Number
        },
        include_hours: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        time() {
            let parsed_time = '';

            if(this.seconds != null) {
                let seconds = parseFloat(this.seconds);
                
                if(seconds > 0) {
                    //The following solution found at: https://stackoverflow.com/a/49190901
                    let format_string = 'mm:ss.SS';
                    
                    if(this.include_hours) {
                        if(seconds >= 3600) {
                            format_string = 'HH:' + format_string;
                        }
                        else {
                            format_string = '00:' + format_string;
                        }
                    }
                    
                    let timestamp = addSeconds(new Date(0), seconds);
                    
                    parsed_time = format(timestamp, format_string);
                }
            }
            
            return parsed_time;
        }
    }
};

export default SecondsToTime;
</script>

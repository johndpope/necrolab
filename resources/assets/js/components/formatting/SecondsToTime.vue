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
            let parsed_time = '';

            if(this.unformatted != null) {
                let seconds = parseFloat(this.unformatted);
                
                if(seconds > 0) {
                    //The following solution found at: https://stackoverflow.com/a/49190901
                    let format_string = 'mm:ss.SS';
                    
                    if(this.include_hours) {
                        if(seconds >= 3600) {
                            format_string = 'hh:' + format_string;
                        }
                        else if(this.zero_pad_hours) {
                            format_string = '00:' + format_string;
                        }
                    }
                    
                    let start_of_day = new Date();
                    
                    start_of_day.setHours(0, 0, 0, 0);
                    
                    let timestamp = addSeconds(start_of_day, seconds);
                    
                    parsed_time = format(timestamp, format_string);
                }
            }
            
            return parsed_time;
        }
    }
};

export default SecondsToTime;
</script>

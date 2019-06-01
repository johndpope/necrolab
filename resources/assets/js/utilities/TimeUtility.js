import format from 'date-fns/format';
import addSeconds from 'date-fns/add_seconds';

export default class TimeUtility {
    static secondsToTime(unformatted, include_hours = false, zero_pad_hours = false) {
        let parsed_time = '';

        if(unformatted != null) {
            let seconds = parseFloat(unformatted);
            
            if(seconds > 0) {
                //The following solution found at: https://stackoverflow.com/a/49190901
                let format_string = 'mm:ss.SS';
                
                if(include_hours) {
                    if(seconds >= 3600) {
                        format_string = 'hh:' + format_string;
                    }
                    else if(zero_pad_hours) {
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

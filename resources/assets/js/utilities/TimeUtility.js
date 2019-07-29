import subHours from 'date-fns/sub_hours';
import subMinutes from 'date-fns/sub_minutes';
import addSeconds from 'date-fns/add_seconds';
import subSeconds from 'date-fns/sub_seconds';
import differenceInHours from 'date-fns/difference_in_hours';
import differenceInMinutes from 'date-fns/difference_in_minutes';
import differenceInSeconds from 'date-fns/difference_in_seconds';
import differenceInMilliseconds from 'date-fns/difference_in_milliseconds';

export default class TimeUtility {
    static secondsToTime(unformatted, include_hours = false, zero_pad_hours = false) {
        let parsed_time = '';

        if(unformatted != null) {
            let seconds = parseFloat(unformatted);

            if(seconds > 0) {
                const start_of_day = new Date(0);

                start_of_day.setHours(0, 0, 0, 0);

                let timestamp = addSeconds(start_of_day, seconds);

                // Hours
                let hours_difference = differenceInHours(timestamp, start_of_day);

                timestamp = subHours(timestamp, hours_difference);

                hours_difference = String(hours_difference).padStart(2, '0');

                // Minutes
                let minutes_difference = differenceInMinutes(timestamp, start_of_day);

                timestamp = subMinutes(timestamp, minutes_difference);

                minutes_difference = String(minutes_difference).padStart(2, '0');

                // Seconds
                let seconds_difference = differenceInSeconds(timestamp, start_of_day);

                timestamp = subSeconds(timestamp, seconds_difference);

                seconds_difference = String(seconds_difference).padStart(2, '0');

                // Milliseconds
                let milliseconds_difference = String(differenceInMilliseconds(timestamp, start_of_day));

                if(milliseconds_difference.length > 2) {
                    const milliseconds_digits_to_trim = (milliseconds_difference.length - 2);

                    if(milliseconds_digits_to_trim > 0) {
                        milliseconds_difference = milliseconds_difference.substr(0, milliseconds_difference.length - milliseconds_digits_to_trim);

                        milliseconds_difference = milliseconds_difference.padEnd(2, '0');
                    }
                }
                else {
                    milliseconds_difference = milliseconds_difference.padStart(2, '0');
                }

                // Format the parsed time
                const time_segments = [];

                if(include_hours) {
                    time_segments.push(hours_difference);
                }

                time_segments.push(minutes_difference);
                time_segments.push(seconds_difference);

                parsed_time = `${time_segments.join(':')}.${milliseconds_difference}`
            }
        }

        return parsed_time;
    }
}

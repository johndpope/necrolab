<template>
    <datepicker 
        v-model="date" 
        :bootstrap-styling="true" 
        :format="formatter" 
        input-class="form-control form-control-lg" 
        :calendar-button="true"
        calendar-button-icon="fa fa-calendar"
        :disabledDates="disabled_dates"
    ></datepicker>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import format from 'date-fns/format';

const DateTimeFilter = {
    name: 'nt-datetime',
    components: {
        'datepicker': Datepicker
    },
    data() {
        return {
            date: '',
            disabled_dates: {
                to: new Date(2014, 3, 22), // Disable all dates up to specific date
                from: new Date(), // Disable all dates after specific date
            }
        }
    },
    methods: {
        formatter(date) {
            return format(date, 'YYYY-MM-DD');
        },
        emitChanged() {
            
        }
    },
    mounted() {    
        this.$emit('loaded', 'date');
        
        this.date = new Date();
    },
    watch: {
        date() {
            this.$emit('selectedValueChanged', 'date', this.formatter(this.date));
        }
    }
};

export default DateTimeFilter;
</script>

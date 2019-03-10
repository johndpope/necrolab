<template>
    <div class="container-fluid" v-if="visible">
        <div class="row">
            <div class="col-2 text-left pr-0 pl-0" @click="decrementMonth">
                <div>
                    <left-arrow></left-arrow>
                </div>
            </div>
            <div class="col-8 pl-0 pr-0 pb-3 text-center align-self-center">
                <div>
                    {{ title }}
                </div>
            </div>
            <div class="col-2 text-right pl-0 pr-0" @click="incrementMonth">
                <div>
                    <right-arrow></right-arrow>
                </div>
            </div>
        </div>
        <div class="row pb-3">
            <div v-for="weekday_name in weekdayNames" class="weekday-title text-center font-weight-bold">
                {{ weekday_name }}
            </div>
            <div 
                v-for="visible_date in visibleMonthDates" 
                class="calendar-day d-inline-block text-center" 
                :class="{'bg-secondary text-white': dateSelected(visible_date), 'day-disabled': !dateWithinVisibleMonth(visible_date), 'bg-light': dateHovered(visible_date)}" 
                @click="selectDate(visible_date)"
                @mouseover="setHoveredDate(visible_date)"
                @mouseleave="hovered_date = ''"
            >
                {{ getVisibleDayDate(visible_date) }}
            </div>
        </div>
    </div>
</template>

<script>
import parse from 'date-fns/parse';
import format from 'date-fns/format';
import addMonths from 'date-fns/add_months';
import subMonths from 'date-fns/sub_months';
import getYear from 'date-fns/get_year';
import getMonth from 'date-fns/get_month';
import getDate from 'date-fns/get_date';
import getDay from 'date-fns/get_day';
import eachDay from 'date-fns/each_day';
import startOfMonth from 'date-fns/start_of_month';
import startOfWeek from 'date-fns/start_of_week';
import endOfMonth from 'date-fns/end_of_month';
import endOfWeek from 'date-fns/end_of_week';
import isSameMonth from 'date-fns/is_same_month';
import isSameDay from 'date-fns/is_same_day';
import isWithinRange from 'date-fns/is_within_range';
import LeftArrow from '../formatting/LeftArrow.vue';
import RightArrow from '../formatting/RightArrow.vue';

const Calendar = {
    name: 'calendar',
    components: {
        'left-arrow': LeftArrow,
        'right-arrow': RightArrow,
    },
    props: {
        visible: {
            type: Boolean,
            default: true
        },
        initial_date: {
            type: Date,
            default: () => {
                return new Date(); 
            }
        },
        min_date: {
            type: Date,
            default: () => {
                return parse('2015-04-23');
            }
        },
        max_date: {
            type: Date,
            default: () => new Date()
        }
    },
    data() {
        return {
            selected_date: this.initial_date,
            visible_month_date: this.selected_date,
            hovered_date: ''
        };
    },
    computed: {
        date: {
            get() {
                return this.selected_date;
            },
            set(date) {
                if(typeof date == 'string') {
                    date = parse(date);
                }
                
                this.selected_date = date;
                this.visible_month_date = date;
            }
        },
        visibleMonthDate() {
            if(this.visible_month_date == null) {
                this.visible_month_date = this.selected_date;
            }
            
            return this.visible_month_date;
        },
        title() {
            return format(this.visibleMonthDate, 'MMMM YYYY');
        },
        weekdayNames() {
            const now = new Date();
            
            const each_day = eachDay(startOfWeek(now), endOfWeek(now));
            
            const weekday_names = [];
            
            each_day.forEach((date) => {
                weekday_names.push(format(date, 'ddd'));
            });
            
            return weekday_names;
        },
        visibleMonthDates() {
            const visible_dates = eachDay(
                this.getVisibleMonthStartDate(this.visible_month_date),
                this.getVisibleMonthEndDate(this.visible_month_date)
            );
            
            return visible_dates;
        }
    },
    methods: {
        incrementMonth() {
            this.visible_month_date = addMonths(this.visible_month_date, 1);
        },
        decrementMonth() {
            this.visible_month_date = subMonths(this.visible_month_date, 1);
        },
        getVisibleMonthStartDate(date) {
            const month_start_date = startOfMonth(date);
            
            return startOfWeek(month_start_date);
        },
        getVisibleMonthEndDate(date) {
            const month_end_date = endOfMonth(date);
            
            return endOfWeek(month_end_date);
        },
        getVisibleDayDate(date) {
            return getDate(date);
        },
        dateWithinVisibleMonth(date) {
            return isSameMonth(this.visible_month_date, date) && isWithinRange(date, this.min_date, this.max_date);
        },
        selectDate(date) {
            if(this.dateWithinVisibleMonth(date)) {
                this.selected_date = date;
                
                this.hovered_date = '';
                
                this.$emit("dateSelected", format(date, 'YYYY-MM-DD'));
            }
        },
        dateSelected(date) {
            return isSameDay(date, this.selected_date);
        },
        dateHovered(date) {
            let hovered = false;
            
            if(isSameDay(date, this.hovered_date)) {
                hovered = true;
            }

            return hovered;
        },
        setHoveredDate(date) {
            if(this.dateWithinVisibleMonth(date) && !this.dateSelected(date)) {
                this.hovered_date = date;
            }
        }
    }
};
export default Calendar;
</script>
<style>
    .weekday-title,
    .calendar-day {
        width: calc(100% / 7);
        line-height: 48px;
    }
    
    .weekday-title {
        cursor: default;
    }
    
    .calendar-day {
        cursor: pointer;
    }
    
    .day-disabled {
        opacity: 0.4;
        cursor: default;
    }
</style>

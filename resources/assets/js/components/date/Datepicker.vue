<template>
    <div class="col-sm-12 col-md-6 col-lg-4 pt-2">
        <div class="dropdown-filter">
            <div class="form-control form-control-lg" :class="{ 'pt-1 pb-1 pl-3 pr-3': date != '' }">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-2 text-left pr-0 pl-0" @click="decrementDate">
                            <div class="">
                                <left-arrow></left-arrow>
                            </div>
                        </div>
                        <div class="col-8 pl-0 pr-0 text-center align-self-center" @click="modalShow = true">
                            <calendar-icon></calendar-icon>
                            <span class="ml-2">{{ displayDate }}</span>
                        </div>
                        <div class="col-2 text-right pl-0 pr-0" @click="incrementDate">
                            <div>
                                <right-arrow></right-arrow>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <b-modal v-model="modalShow" :centered="true" :hide-footer="true">
                <div slot="modal-header" class="w-100">
                    <span class="h4">
                        {{ label }}
                    </span>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <calendar :visible_month_date="modalDate" :selected_date="date" @dateSelected="setModalDate">
                            </calendar>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3 mr-auto">
                            <button type="button" class="btn btn-primary" @click="modalShow = false">
                                Cancel
                            </button>
                        </div>
                        <div class="col-3 text-right">
                            <button type="button" class="btn btn-primary" @click="saveModalDate">
                                Ok
                            </button>
                        </div>
                    </div>
                </div>
            </b-modal>
        </div>
    </div>
</template>

<script>
import parse from 'date-fns/parse';
import format from 'date-fns/format';
import addDays from 'date-fns/add_days';
import subDays from 'date-fns/sub_days';
import bModal from 'bootstrap-vue/es/components/modal/modal';
import Calendar from './Calendar.vue';
import LeftArrow from '../formatting/LeftArrow.vue';
import RightArrow from '../formatting/RightArrow.vue';
import CalendarIcon from '../formatting/CalendarIcon.vue';

const Datepicker = {
    name: 'nt-dropdown-filter',
    components: {
        'b-modal': bModal,
        'calendar': Calendar,
        'left-arrow': LeftArrow,
        'right-arrow': RightArrow,
        'calendar-icon': CalendarIcon
    },
    props: {
        initialize: {
            type: Boolean,
            default: false
        },
        field_name: {
            type: String,
            default: 'date'
        },
        label: {
            type: String,
            default: 'Date'
        },
        format: {
            type: String,
            default: 'YYYY-MM-DD'
        }
    },
    data() {
        return {
            modal_show: false,
            selected_date: '',
            modal_date: ''
        };
    },
    computed: {
        displayDate() {
            return format(this.date, this.format);
        },
        date: {
            get() {
                let selected_date = this.selected_date;
                
                if(selected_date === '') {
                    selected_date = new Date();
                }
                
                return selected_date;
            },
            set(date) {
                if(typeof date == 'string') {
                    date = parse(date);
                }
                
                this.selected_date = date;
                this.modal_date = date;
                
                this.$emit("selectedValueChanged", this.field_name, format(date, 'YYYY-MM-DD'));
            }
        },
        modalDate: {
            get() {
                let modal_date = this.modal_date;
            
                if(modal_date === '') {
                    modal_date = this.date;
                }
                
                return modal_date;
            },
            set(date) {
                this.modal_date = date;
            }
        },
        modalShow: {
            get() {
                return this.modal_show;
            },
            set(modal_show) {
                this.modal_date = this.date;
                
                this.modal_show = modal_show;
            }
        }
    },
    methods: {
        incrementDate() {
            this.date = addDays(this.date, 1);
        },
        decrementDate() {
            this.date = subDays(this.date, 1);
        },
        setModalDate(date) {
            if(typeof date == 'string') {
                date = parse(date);
            }
            
            this.modal_date = date;
        },
        saveModalDate() {
            this.date = this.modal_date;
            
            this.modalShow = false;
        },
        loadDates(resolve, reject) {            
            resolve();
        }
    },
    mounted() {
        let load_dates_promise = new Promise((resolve, reject) => {
            this.loadDates(resolve, reject);
        });
    
        load_dates_promise.then((success_message) => {            
            this.$emit("loaded", this.field_name);
            
            this.date = new Date();
        });
    }
};
export default Datepicker;
</script>

<template>
    <highcharts
        v-if="!loading"
        :constructor-type="'stockChart'"
        :options="chart_options"
        :updateArgs="update_arguments"
    >
    </highcharts>
</template>

<script>
import Highcharts from 'highcharts';
import stockInit from 'highcharts/modules/stock';
import { Chart } from 'highcharts-vue';
import TimeUtility from '../../utilities/TimeUtility.js';

stockInit(Highcharts);

const StatsChart = {
    name: 'stats-chart',
    components: {
        'highcharts': Chart
    },
    props: {
        dataset: {
            type: Object
        }
    },
    data() {
        return {
            loading: true,
            chart_options: {
                rangeSelector: {
                    selected: 1
                },
                tooltip: {
                    shared: true,
                    split: false
                },
                legend: {
                    enabled: true,
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                },
                series: []
            },
            update_arguments: [
                true,
                true,
                true
            ],
            current_dash_style_index: 0,
            dash_styles: [
                'Solid',
                'ShortDash',
                'ShortDot',
                'ShortDashDot',
                'ShortDashDotDot',
                'Dot',
                'Dash',
                'LongDash',
                'DashDot',
                'LongDashDot',
                'LongDashDotDot'
            ],
            current_color_index: 0,
            colors: [
                "#7cb5ec",
                "#434348",
                "#90ed7d",
                "#f7a35c",
                "#8085e9",
                "#f15c80",
                "#e4d354",
                "#2b908f",
                "#f45b5b",
                "#91e8e1"
            ]
        }
    },
    methods: {
        resetDashStyleIndex() {
            this.current_dash_style_index = 0;
        },
        getDashStyle() {
            if(this.current_dash_style_index > (this.dash_styles.length - 1)) {
                this.current_dash_style_index = 0;
            }

            const dash_style = this.dash_styles[this.current_dash_style_index];

            this.current_dash_style_index += 1;

            return dash_style;
        },
        resetColorIndex() {
            this.current_color_index = 0;
        },
        getColor() {
            if(this.current_color_index > (this.colors.length - 1)) {
                this.current_color_index = 0;
            }

            const color = this.colors[this.current_color_index];

            this.current_color_index += 1;

            return color;
        },
        getFormattedTime(seconds) {
            return TimeUtility.secondsToTime(seconds, true, false);
        },
        getYAxis() {
            return [];
        },
        getSeries() {
            return [];
        },
        getSeriesData() {
            const series_data = {};

            if(this.dataset.total_records > 0) {
                this.dataset.data.slice().reverse().forEach((row) => {
                    this.processSeriesDataRow(series_data, row);
                });
            }

            return Object.values(series_data);
        },
        processSeriesDataRow(series_data, row) {}
    },
    watch: {
        'dataset.total_records'() {
            this.loading = true;

            if(this.dataset.total_records > 0) {
                const series_data = this.getSeriesData();

                series_data.forEach((series_rows, index) => {
                    //console.log(this.chart_options);
                    this.chart_options.series[index].data = series_rows;
                });
            }

            this.loading = false;
        }
    },
    created() {
        this.chart_options.yAxis = this.getYAxis();
        this.chart_options.series = this.getSeries();
    }
};

export default StatsChart;
</script>


<script>
import StatsChart from './StatsChart.vue';
import parse from 'date-fns/parse';

const PlayerPbStatsChart = {
    name: 'player-pb-stats-chart',
    extends: StatsChart,
    props: {
        details_columns: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        getYAxis() {
            this.resetColorIndex();

            const y_axis = [];

            y_axis.push(this.getSingleYAxis('Rank'));

            this.details_columns.forEach((details_column) => {
                y_axis.push(this.getSingleYAxis(details_column.display_name));
            });

            return y_axis;
        },
        getSeries() {
            this.resetColorIndex();

            const self = this;

            const series = [];

            series.push(self.getSingleSeries('Rank', 0));

            self.details_columns.forEach((details_column, index) => {
                series.push(self.getSingleSeries(details_column.display_name, index + 1, {
                    tooltip: {
                        // NOTE: "function()" is needed here otherwise the context of "this" is in Vue and not Highcharts.
                        pointFormatter: function() {
                            let value = this.y;

                            if(details_column.name == 'time') {
                                value = self.getFormattedTime(value);
                            }

                            return `<span style="color:${this.color}">●</span> ${this.series.name}: <b>${value}</b><br/>`;
                        }
                    }
                }));
            });

            return Object.values(series);
        },
        processSeriesDataRow(series_data, row) {
            const date = parse(row.date, 'YYYY-MM-DD');
            const utc_date = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());

            this.addToSeriesData(series_data, 'rank', [
                utc_date,
                row.rank
            ]);

            this.details_columns.forEach((details_column) => {
                this.addToSeriesData(series_data, details_column.name, [
                    utc_date,
                    row.details[details_column.name]
                ]);
            });
        }
    }
};

export default PlayerPbStatsChart;
</script>
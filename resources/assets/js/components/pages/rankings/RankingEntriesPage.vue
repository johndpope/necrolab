<template>
    <with-nav-body
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        :title="title"
        :sub_title="sub_title"
    >
        <necrotable
            :dataset="dataset"
            :header_columns="header_columns"
            :has_search="true"
            :has_action_column="true"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <slot name="table-row" :row="row" :row_index="row_index">
                    Override the "table-row" slot to customize the table row.
                </slot>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <slot name="row-details"  :row="row">
                    Override the "row-details" slot to customize row details.
                </slot>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import WithNavBody from '../../layouts/WithNavBody.vue';
import NecroTable from '../../table/NecroTable.vue';
import SiteDropdownFilter from '../../table/filters/SiteDropdownFilter.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';

const RankingEntriesPage = {
    name: 'ranking-entries-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'toggle-details': ToggleDetails
    },
    props: {
        category_name: {
            type: String,
            default: ''
        },
        category_display_name: {
            type: String,
            default: ''
        },
        dataset: {
            type: Object,
            default: () => {}
        },
        api_endpoint_url: {
            type: String,
            default: ''
        },
        default_api_request_parameters: {
            type: Object,
            default: () => {
                return {};
            }
        },
        header_columns: {
            type: Array,
            default: () => {
                return [];
            }
        },
        filter_records: {
            type: Array,
            default: () => {
                return [
                    {
                        name: 'leaderboard_source',
                        store_name: 'leaderboard_sources'
                    },
                    {
                        name: 'release',
                        store_name: 'releases'
                    },
                    {
                        name: 'mode',
                        store_name: 'modes'
                    },
                    {
                        name: 'seeded_type',
                        store_name: 'seeded_types'
                    },
                    {
                        name: 'multiplayer_type',
                        store_name: 'multiplayer_types'
                    },
                    {
                        name: 'soundtrack',
                        store_name: 'soundtracks'
                    }
                ];
            }
        }
    },
    data() {
        return {
            loaded: false,
            release: {},
            mode: {},
            filter_record_values: {},
            properties_loaded: false,
            title: '',
            sub_title: '',
            api_request_parameters: this.default_api_request_parameters,
            filters: [
                SiteDropdownFilter
            ]
        }
    },
    computed: {
        breadcrumbs() {
            let breadcrumbs = [];

            if(this.properties_loaded) {
                breadcrumbs = [
                    {
                        text: 'Rankings'
                    },
                    {
                        text: this.category_display_name,
                        href: '#/rankings/' + this.category_name
                    },
                    {
                        text: 'Entries, ' + this.getDisplayName(),
                        href: this.$route.path
                    }
                ]
            }

            return breadcrumbs;
        }
    },
    methods: {
        getDisplayName() {
            let filter_records_length = this.filter_records.length;

            let display_name_segments = [];

            for(let index = 0; index < filter_records_length; index++) {
                let filter_record = this.filter_records[index];

                display_name_segments.push(this.filter_record_values[filter_record.name].display_name);
            }

            return display_name_segments.join(' ') + ', ' + this.$route.params.date;
        }
    },
    created() {
        this.filter_records.forEach((filter_record) => {
            let getter_name = `${filter_record.store_name}/getByName`;
            let route_parameter_value = this.$route.params[filter_record.name];

            this.filter_record_values[filter_record.name] = this.$store.getters[getter_name](route_parameter_value);

            this.dataset.setRequestParameter(filter_record.name, route_parameter_value);
        });

        this.dataset.setRequestParameter('date', this.$route.params.date);

        this.title = this.category_display_name + ' Ranking Entries';

        this.sub_title = this.getDisplayName();

        this.loaded = true;
    }
};

export default RankingEntriesPage;
</script>

<template>
    <with-nav-body 
        :loaded="loaded"
        :breadcrumbs="breadcrumbs"
        :title="title"
    >
        <necrotable 
            :api_endpoint_url="api_endpoint_url" 
            :header_columns="header_columns" 
            :has_action_column="true" 
            :default_request_parameters="defaultRequestParameters"
            :filters="filters"
        >
            <template slot="table-row" slot-scope="{ row_index, row }">
                <slot 
                    name="table-row" 
                    :row="row" 
                    :getEntriesUrl="getEntriesUrl"
                    :getCategoryField="getCategoryField"
                >
                    Override the "table-row" slot to replace this text.
                </slot>
            </template>
            <template slot="actions-column" slot-scope="{ row_index, row, detailsRowVisible, toggleDetailsRow }">
                <toggle-details :row_index="row_index" :detailsRowVisible="detailsRowVisible" @detailsRowToggled="toggleDetailsRow"></toggle-details>
            </template>
            <template slot="row-details" slot-scope="{ row }">
                <slot name="row-details" :row="row">
                    Override the "row-details" slot to replace this text.
                </slot>
            </template>
        </necrotable>
    </with-nav-body>
</template>

<script>
import WithNavBody from '../../layouts/WithNavBody.vue';
import NecroTable from '../../table/NecroTable.vue';
import ReleaseDropdownFilter from '../../table/filters/ReleaseDropdownFilter.vue';
import ModeDropdownFilter from '../../table/filters/ModeDropdownFilter.vue';
import SeededTypeDropdownFilter from '../../table/filters/SeededTypeDropdownFilter.vue';
import MultiplayerTypeDropdownFilter from '../../table/filters/MultiplayerTypeDropdownFilter.vue';
import SoundtrackDropdownFilter from '../../table/filters/SoundtrackDropdownFilter.vue';
import ToggleDetails from '../../table/action_columns/ToggleDetails.vue';

const RankingsOverviewPage = {
    name: 'rankings-overview-page',
    components: {
        'with-nav-body': WithNavBody,
        'necrotable': NecroTable,
        'toggle-details': ToggleDetails,
    },
    props: {
        loaded: {
            type: Boolean,
            default: false
        },
        category_name: {
            type: String,
            default: ''
        },
        category_display_name: {
            type: String,
            default: ''
        },
        api_endpoint_url: {
            type: String,
            default: '/api/1/rankings/power'
        },
        header_columns: {
            type: Array,
            default: () => {
                return [];
            }
        },
        filters: {
            type: Array,
            default: () => {
                return [
                    ReleaseDropdownFilter,
                    ModeDropdownFilter,
                    SeededTypeDropdownFilter,
                    MultiplayerTypeDropdownFilter,
                    SoundtrackDropdownFilter
                ];
            }
        },
        url_segment_stores: {
            type: Array,
            default: () => {
                return [
                    'releases',
                    'modes',
                    'seeded_types',
                    'multiplayer_types',
                    'soundtracks'
                ];
            }
        }
    },
    data() {
        return {
            title: this.category_display_name + ' Rankings',
            breadcrumbs: [
                {
                    text: 'Rankings'
                },
                {
                    text: this.category_display_name,
                    href: '#/rankings/' + this.category_name
                }
            ]
        }
    },
    computed: {
        defaultRequestParameters() {
            return {
                leaderboard_source: this.$route.params.leaderboard_source
            };
        }
    },
    methods: {
        getEntriesUrl(date) {            
            let url_segments = [];

            this.url_segment_stores.forEach((url_segment_store) => {                
                let selected = this.$store.getters[`${url_segment_store}/getSelected`];
                
                url_segments.push(selected.name);
            });
            
            return '/rankings/' + this.category_name + '/' + this.$route.params.leaderboard_source + '/' + url_segments.join('/') + '/' + date;
        },
        getCategoryField(record, category_name, field_name) {
            let field_value = '';
            
            if(record != null && record[category_name] != null) {
                field_value = record[category_name][field_name];
            }
            
            return field_value;
        }
    }
};

export default RankingsOverviewPage;
</script>

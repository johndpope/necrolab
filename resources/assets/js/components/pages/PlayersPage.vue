<template>
    <with-nav-layout 
        :breadcrumbs="breadcrumbs"
        title="Players"
    >
        <necrotable :api_endpoint_url="api_endpoint_url" :header_columns="header_columns" :has_search="true" :filters="filters">
            <template slot="table-row" slot-scope="{ row_index, row }">
                <td>
                    <player-profile-modal :player="row"></player-profile-modal>
                </td>
            </template>
        </necrotable>
    </with-nav-layout>
</template>

<script>
import WithNavLayout from '../layouts/WithNavLayout.vue';
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';

export default {
    name: 'PlayersPageTable',
    components: {
        'with-nav-layout': WithNavLayout,
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal  
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Players',
                    href: '#/players'
                }
            ],
            api_endpoint_url: '/api/1/players/steam',
            filters: [
                SiteDropdownFilter
            ],
            header_columns: [
                'Player'
            ]
        }
    }
};
</script>

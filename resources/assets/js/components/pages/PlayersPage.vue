<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <b-breadcrumb :items="breadcrumbs"></b-breadcrumb>
            </div>
        </div>
        <div class="row">
            <div class="col-12 pb-3">
                <h1>Players</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <necrotable :api_endpoint_url="api_endpoint_url" :header_columns="header_columns" :has_search="true" :filters="filters">
                    <template slot="table-row" slot-scope="{ row_index, row }">
                        <td>
                            <player-profile-modal :player="row"></player-profile-modal>
                        </td>
                    </template>
                </necrotable>
            </div>
        </div>
    </div>
</template>

<script>
import NecroTable from '../table/NecroTable.vue';
import SiteDropdownFilter from '../table/filters/SiteDropdownFilter.vue';
import PlayerProfileModal from '../player/PlayerProfileModal.vue';

export default {
    name: 'PlayersPageTable',
    components: {
        'necrotable': NecroTable,
        'player-profile-modal': PlayerProfileModal  
    },
    data() {
        return {
            breadcrumbs: [
                {
                    text: 'Players',
                    href: '/players'
                }
            ],
            api_endpoint_url: '/api/1/players/steam',
            filters: [
                SiteDropdownFilter
            ],
            header_columns: [
                'Player',
                /*'Steam',
                'Discord',
                'Mixer',
                'Reddit',
                'Twitch',
                'Twitter',
                'YouTube'*/
            ]
        }
    }
};
</script>

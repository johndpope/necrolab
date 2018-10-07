<template>
    <player-profile-link :id="row_data.id" :username="row_data.username"></player-profile-link>
</template>

<script>
import PlayerProfileLink from '../../formatting/PlayerProfileLink.vue';

const PlayerProfileField = {
    name: 'necrotable-search',
    components: {
        'player-profile-link': PlayerProfileLink
    },
    props: {
        row_data: {
            type: Object,
            default: () => {}
        }
    }
};

export default PlayerProfileField;
</script>

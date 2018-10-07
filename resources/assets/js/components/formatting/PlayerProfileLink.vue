<template>
    <a v-bind:href="'/players/' + id">
        {{ username }}
    </a>
</template>

<script>
const PlayerProfileLink = {
    name: 'player-profile-link',
    props: {
        id: {
            type: String
        },
        username: {
            type: String,
            default: ''
        }
    }
};

export default PlayerProfileLink;
</script>

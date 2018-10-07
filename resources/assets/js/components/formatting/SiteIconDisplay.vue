<template>
    <div class="container-fluid site-icon-display">
        <div class="row">
            <div class="col-1">
                <i v-if="name == ''" class="fab fa-steam-square"></i>
                <i v-else-if="name == 'steam'" class="fab fa-steam-square"></i>
                <i v-else-if="name == 'discord'" class="fab fa-discord"></i>
                <i v-else-if="name == 'mixer'" class="fas fa-gamepad"></i>
                <i v-else-if="name == 'patreon'" class="fab fa-patreon"></i>
                <i v-else-if="name == 'reddit'" class="fab fa-reddit-square"></i>
                <i v-else-if="name == 'twitch'" class="fab fa-twitch"></i>
                <i v-else-if="name == 'twitter'" class="fab fa-twitter-square"></i>
                <i v-else-if="name == 'youtube'" class="fab fa-youtube"></i>
            </div>
            <div class="col-auto">
                <template v-if="display_name != ''">
                    {{ display_name }}
                </template>
                <template v-else>
                    Steam
                </template>
            </div>
        </div>
    </div>
</template>

<script>
const SiteIconDisplay = {
    name: 'site-icon-display',
    props: {
        name: {
            type: String,
            default: ''
        },
        display_name: {
            type: String,
            default: ''
        }
    }
};

export default SiteIconDisplay;
</script>

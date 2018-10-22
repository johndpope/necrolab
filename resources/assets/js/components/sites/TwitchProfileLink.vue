<template>
    <site-profile-link :site_icon="icon" :username="profile_data.username" :profile_url="'https://www.twitch.tv/' + profile_data.username">
    </site-profile-link>
</template>

<script>
import TwitchIcon from './TwitchIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const TwitchProfileLink = {
    name: 'twitch-profile-link',
    components: {
        'site-profile-link': SiteProfileLink
    },
    props: {
        profile_data: {
            type: Object,
            default: () => {}
        }
    },
    data() {
        return {
            icon: TwitchIcon
        }
    }
};

export default TwitchProfileLink;
</script>

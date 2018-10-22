<template>
    <site-profile-link :site_icon="icon" :username="profile_data.username" :profile_url="profile_data.profile_url">
    </site-profile-link>
</template>

<script>
import SteamIcon from './SteamIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const SteamProfileLink = {
    name: 'steam-profile-link',
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
            icon: SteamIcon
        }
    }
};

export default SteamProfileLink;
</script>

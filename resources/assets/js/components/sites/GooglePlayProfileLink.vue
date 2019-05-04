<template>
    <site-profile-link :site_icon="icon" :username="profile_data.username">
    </site-profile-link>
</template>

<script>
import GooglePlayIcon from './GooglePlayIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const GooglePlayProfileLink = {
    name: 'google-play-profile-link',
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
            icon: GooglePlayIcon
        }
    }
};

export default GooglePlayProfileLink;
</script>

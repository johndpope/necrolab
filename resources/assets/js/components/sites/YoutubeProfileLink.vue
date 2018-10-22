<template>
    <site-profile-link :site_icon="icon" :username="profile_data.username" :profile_url="'https://www.youtube.com/' + profile_data.id">
    </site-profile-link>
</template>

<script>
import YoutubeIcon from './YoutubeIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const YoutubeProfileLink = {
    name: 'youtube-profile-link',
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
            icon: YoutubeIcon
        }
    }
};

export default YoutubeProfileLink;
</script>

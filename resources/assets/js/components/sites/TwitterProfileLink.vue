<template>
    <site-profile-link :site_icon="icon" :username="profile_data.name" :profile_url="'https://www.twitter.com/' + profile_data.name">
    </site-profile-link>
</template>

<script>
import TwitterIcon from './TwitterIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const TwitterProfileLink = {
    name: 'twitter-profile-link',
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
            icon: TwitterIcon
        }
    }
};

export default TwitterProfileLink;
</script>

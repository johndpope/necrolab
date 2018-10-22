<template>
    <site-profile-link :site_icon="icon" :username="profile_data.username" :profile_url="'https://www.reddit.com/u/' + profile_data.username">
    </site-profile-link>
</template>

<script>
import RedditIcon from './RedditIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const RedditProfileLink = {
    name: 'reddit-profile-link',
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
            icon: RedditIcon
        }
    }
};

export default RedditProfileLink;
</script>

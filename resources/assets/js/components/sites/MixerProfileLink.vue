<template>
    <site-profile-link :site_icon="icon" :username="profile_data.username" :profile_url="'https://mixer.com/' + profile_data.username">
    </site-profile-link>
</template>

<script>
import MixerIcon from './MixerIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const MixerProfileLink = {
    name: 'mixer-profile-link',
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
            icon: MixerIcon
        }
    }
};

export default MixerProfileLink;
</script>

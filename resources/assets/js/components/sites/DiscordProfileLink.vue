<template>
    <site-profile-link :site_icon="icon">
        <template slot="username">
            {{ fullUserName }}
        </template>
    </site-profile-link>
</template>

<script>
import DiscordIcon from './DiscordIcon.vue';
import SiteProfileLink from './SiteProfileLink.vue';

const DiscordProfileLink = {
    name: 'discord-profile-link',
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
            icon: DiscordIcon
        }
    },
    computed: {
        fullUserName() {
            return this.profile_data.username + '#' + this.profile_data.discriminator;
        }
    }
};

export default DiscordProfileLink;
</script>

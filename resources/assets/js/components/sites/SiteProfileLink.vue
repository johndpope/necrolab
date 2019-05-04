<template>
    <div class="container-fluid border rounded ml-0 mr-0 pt-1 pb-0">
        <div class="row">
            <div class="col-2 pr-0">
                <span class="site_profile_link">
                    <component :is="site_icon"></component>
                </span>
            </div>
            <div :class="'align-middle col-' + username_cols + ' pl-0 pl-sm-1 pt-2'">
                <slot name="username">
                    {{ username }}
                </slot>
            </div>
            <div v-if="profile_url.length > 0" class="col-3 text-right pt-2">
                <site-link :url="profile_url"></site-link>
            </div>
        </div>
    </div>
</template>

<script>
import SiteLink from './SiteLink.vue';

const SiteProfileLink = {
    name: 'site-profile-link',
    components: {
        'site-link': SiteLink
    },
    props: {
        site_icon: {
            type: Object,
            default: () => {}
        },
        username: {
            type: String,
            default: ''
        },
        profile_url: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            username_cols: 7
        }
    },
    mounted() {
        if(this.profile_url.length == 0) {
            this.username_cols = 9;
        }
    }
};

export default SiteProfileLink;
</script>

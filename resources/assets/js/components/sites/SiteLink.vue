<template>
    <a :href="url" target="_blank">
        <link-icon></link-icon>
    </a>
</template>

<script>
import LinkIcon from './LinkIcon.vue';

const SiteLink = {
    name: 'site-link',
    components: {
        'link-icon': LinkIcon
    },
    props: {
        url: {
            type: String,
            default: ''
        }
    }
};

export default SiteLink;
</script>

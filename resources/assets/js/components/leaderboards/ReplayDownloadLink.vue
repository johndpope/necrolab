<template>
    <a v-if="hasUploadedReplay" href="record.pb.replay.file_url">
        <replay-download-icon></replay-download-icon>
    </a>
</template>

<script>
import ReplayDownloadIcon from './ReplayDownloadIcon.vue';

const ReplayDownloadLink = {
    name: 'replay-download-link',
    components: {
        'replay-download-icon': ReplayDownloadIcon
    },
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    },
    computed: {
        hasUploadedReplay() {        
            return 
                this.record.pb['replay'] != null && 
                this.record.pb.replay['file_url'] != null &&
                this.record.pb.replay.file_url.length > 0;
        }
    }
};

export default ReplayDownloadLink;
</script>

<template>
    <span v-if="record.pb['replay'] != null">
        {{ killedBy }}
    </span>
</template>

<script>
const KilledBy = {
    name: 'killed-by',
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    },
    computed: {
        killedBy() {
            let killed_by = '';
            
            if(this.record.win == 0 && this.record.pb['replay'] != null) {
                killed_by = this.record.pb.replay.killed_by;
            }
            
            return killed_by;
        }
    }
};

export default KilledBy;
</script>

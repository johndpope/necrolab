<template>
    <span v-if="record['pb'] != null">
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

            if(this.record.pb.win === 0 && this.record.pb.run_result != null) {
                killed_by = this.record.pb.run_result;
            }
            
            return killed_by;
        }
    }
};

export default KilledBy;
</script>

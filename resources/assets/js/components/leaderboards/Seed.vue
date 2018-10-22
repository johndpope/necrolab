<template>
    <span v-if="record.pb['replay'] != null">
        {{ record.pb.replay.seed }}
    </span>
</template>

<script>
const Seed = {
    name: 'seed',
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    }
};

export default Seed;
</script>

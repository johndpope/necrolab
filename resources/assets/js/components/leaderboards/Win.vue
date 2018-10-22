<template>
    <span>
        <template v-if="record.pb.win == 1">
            Yes
        </template>
        <template v-else>
            No
        </template>
    </span>
</template>

<script>
const Win = {
    name: 'win',
    props: {
        record: {
            type: Object,
            default: () => {}
        }
    }
};

export default Win;
</script>

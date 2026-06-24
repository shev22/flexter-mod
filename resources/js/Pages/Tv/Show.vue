<script setup>
import MediaDetail from '../../Components/media/MediaDetail.vue';

defineProps({
    media: { type: Object, required: true },
    watchProgress: { type: Number, default: null },
    watchContext: { type: Object, default: null },
    comments: {
        type: Object,
        default: () => ({ threads: [], total: 0 }),
    },
});
</script>

<template>
    <MediaDetail
        :key="`${media.type}-${media.id}`"
        :media="media"
        :watch-progress="watchProgress"
        :watch-context="watchContext"
        :comments="comments"
    />
</template>

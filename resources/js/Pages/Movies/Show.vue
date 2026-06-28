<script setup>
import MediaDetail from '../../Components/media/MediaDetail.vue';

defineProps({
    media: { type: Object, required: true },
    watchProgress: { type: Number, default: null },
    comments: {
        type: Object,
        default: () => ({ threads: [], total: 0 }),
    },
    review: { type: Object, default: null },
});
</script>

<template>
    <MediaDetail
        :key="`${media.type}-${media.id}`"
        :media="media"
        :watch-progress="watchProgress"
        :comments="comments"
        :review="review"
    />
</template>

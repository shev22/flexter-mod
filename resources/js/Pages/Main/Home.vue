<template>
    <HomeCarousel :movies="movies" />
</template>
<script setup lang="ts">
import HomeCarousel from "../../Components/HomeCarousel.vue";

const { movies } = defineProps<{ movies: any }>();
</script>

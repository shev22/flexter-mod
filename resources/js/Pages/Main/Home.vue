<template>
    <HomeCarousel :movies="movies" />
</template>
<script setup lang="ts">
import HomeCarousel from "../../Components/Home/HomeCarousel.vue";
import {Head} from "@inertiajs/vue3";

const { movies } = defineProps<{ movies: any }>();
</script>

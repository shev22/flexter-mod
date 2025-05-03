<template>
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6 pb-2 border-b border-gray-700">Biography</h2>
        <div class="space-y-4 text-justify">
            <p v-for="(paragraph, index) in paragraphs" :key="index">
                {{ paragraph }}
            </p>
        </div>
    </section>
</template>

<script setup>
import {computed} from "vue";

const props = defineProps({
    biography: {
        type: String,
        required: true
    }
});

const paragraphs = computed(() => {
    return props.biography
        ? props.biography.split(/\r?\n/).filter(p => p.trim() !== '')
        : [];
});
</script>

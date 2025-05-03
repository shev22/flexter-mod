<template>
    <Link
        :href="ActorUrl"
        class="block "
    >
        <div class="actor-card shadow hover:scale-105 transition-transform duration-300 ">
            <img :src="actor.image" :alt="actor.name" class="actor-image" />
            <p class="actor-name ">{{ actor.name }}</p>
        </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {computed} from "vue";
import {route} from "ziggy-js";

const props = defineProps({
    actor: {
        type: Object,
        required: true,
    },
})

const ActorUrl = computed(() => {
    return route('actor.show', {
        id: props.actor.id,
        slug: generateSlug(props.actor.name)
    })
})

function generateSlug(title) {
    return title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim()
}
</script>

<style scoped>
.actor-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #1f2937; /* Tailwind gray-800 */
    color: white;
    border-radius: 10px;
    width: 100%;
    text-align: center;
    overflow: hidden;
}

.actor-image {
    width: 100%;
    height: auto;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    aspect-ratio: 2/3;
    object-fit: cover;
}

.actor-name {
    padding: 0.5rem;
    font-weight: bold;
    font-size: 0.9rem;
}
</style>

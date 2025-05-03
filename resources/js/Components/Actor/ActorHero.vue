<template>
    <div class="relative">
        <div class="h-64 bg-gray-800 w-full"></div>
        <div class="container mx-auto px-4 relative -mt-16">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Actor Photo -->
                <div class="w-48 h-64 bg-gray-700 rounded-lg shadow-lg overflow-hidden">
                    <div class="w-full h-full bg-gray-600 flex items-center justify-center">
                        <img
                            :src="'https://image.tmdb.org/t/p/w185/' + actor.profile_path"
                            :alt="actor.name"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>

                <!-- Actor Info -->
                <div class="flex-1 py-4">
                    <h1 class="text-4xl font-bold mb-2">{{ actor.name }}</h1>
                    <div class="flex items-center gap-4 mb-4">
                        <span class="text-yellow-400">★ {{ actor.popularity }}</span>
                        <span>Born: {{ actor.birthday }}</span>
                        <span>{{ actor.place_of_birth }}</span>
                    </div>

                    <SocialMediaLinks :social-media="actor.social_media" class="mb-6" />

                    <button class="px-6 py-2 bg-yellow-500 text-gray-900 font-semibold rounded-lg hover:bg-yellow-400 transition">
                        Add to Favorites
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>


import SocialMediaLinks from "./SocialMediaLinks.vue";

defineProps({
    actor: {
        type: Object,
        required: true
    }
});
</script>

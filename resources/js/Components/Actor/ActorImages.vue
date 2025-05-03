<template>
    <section class="bg-gray-800 rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Images</h3>
        <div class="grid grid-cols-2 gap-4 max-h-[68rem] overflow-y-auto">
            <div v-for="image in images" :key="image" class="images">
                <div class="relative pb-[150%] bg-gray-700 rounded overflow-hidden mb-2">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img
                            :src="image ? 'https://image.tmdb.org/t/p/w185/' + image : 'https://placehold.co/300x400'"
                            :alt="name"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    images: {
        type: Array,
        required: true
    },
    name: {
        type: String,
        required: true
    }
});
</script>

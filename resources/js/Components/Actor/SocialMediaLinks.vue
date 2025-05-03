<template>
    <div class="flex gap-4" :class="class">
        <a
            v-if="socialMedia.instagram_id"
            :href="'https://www.instagram.com/' + socialMedia.instagram_id"
            target="_blank"
            class="p-2 bg-gray-700 rounded-full hover:bg-pink-600 transition"
        >
            <i class="fab fa-instagram text-xl"></i>
        </a>

        <a
            v-if="socialMedia.facebook_id"
            :href="'https://www.facebook.com/' + socialMedia.facebook_id"
            target="_blank"
            class="p-2 bg-gray-700 rounded-full hover:bg-blue-600 transition"
        >
            <i class="fab fa-facebook text-xl"></i>
        </a>

        <a
            v-if="socialMedia.twitter_id"
            :href="'https://twitter.com/' + socialMedia.twitter_id"
            target="_blank"
            class="p-2 bg-gray-700 rounded-full hover:bg-blue-400 transition"
        >
            <i class="fab fa-twitter text-xl"></i>
        </a>

        <a
            v-if="socialMedia.youtube_id"
            :href="'https://www.youtube.com/' + socialMedia.youtube_id"
            target="_blank"
            class="p-2 bg-gray-700 rounded-full hover:bg-red-600 transition"
        >
            <i class="fab fa-youtube text-xl"></i>
        </a>

        <a
            v-if="socialMedia.imdb_id"
            :href="'https://www.imdb.com/name/' + socialMedia.imdb_id"
            target="_blank"
            class="p-2 bg-gray-700 rounded-full hover:bg-yellow-500 transition"
        >
            <i class="fab fa-imdb text-xl"></i>
        </a>

        <a
            v-if="socialMedia.tiktok_id"
            :href="'https://www.tiktok.com/@' + socialMedia.tiktok_id"
            target="_blank"
            class="p-2 bg-gray-700 rounded-full hover:bg-black transition"
        >
            <i class="fab fa-tiktok text-xl"></i>
        </a>
    </div>
</template>

<script setup>
defineProps({
    socialMedia: {
        type: Object,
        required: true
    },
    class: {
        type: String,
        default: ''
    }
});
</script>

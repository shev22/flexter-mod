<template>
<!--    <nav class="vertical-nav  bg-zinc-100 text-black dark:bg-black dark:text-zinc-100" >-->
    <nav class="vertical-nav  bg-transparent text-[5rem] text-black dark:text-zinc-100" >

    <ul >
            <Link :href="route('home')">
                <span class="material-symbols-outlined my-7 hover:text-green-500 " :class="{'text-green-500 rounded' : $page.component === 'Main/Home'}"> home </span>
            </Link>

            <Link :href="route('search.show')">
                <span class="material-symbols-outlined hover:text-green-500 my-7" :class="{'text-green-500 rounded' : $page.component === 'Main/Search/Show'}">search</span>
            </Link>

            <Link :href="route('actors')">
                <span class="material-symbols-outlined hover:text-green-500 my-7 " :class="{'text-green-500 rounded' : $page.component === 'Main/Actors/Actors'}">people</span>
            </Link>

            <Link :href="route('watchlist')">
                <span class="material-symbols-outlined hover:text-green-500 my-7" :class="{'text-green-500 rounded' : $page.component === 'Main/Watchlist'}">bookmarks</span>
            </Link>

            <Link :href="route('feedback')">
                <span class="material-symbols-outlined hover:text-green-500 my-7" :class="{'text-green-500 rounded' : $page.component === 'Main/Feedback'}">feedback</span>
            </Link>

            <Link :href="route('admin')">
                <span class="material-symbols-outlined hover:text-green-500 my-7" :class="{'text-green-500 rounded' : $page.component === 'Main/Admin'}">security</span>
            </Link>
        </ul>
<!--        <div>-->
<!--            <div class="py-2 mx-10 my-6 border-b-4 border-b-red-500">-->
<!--                <Link :href="route('home')">-->
<!--                <HomeOutline fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>-->
<!--                </Link>-->
<!--            </div>-->

<!--            <div class="py-2 mx-10 my-6">-->
<!--                <Link :href="route('search')">-->
<!--                <Magnify fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>-->
<!--                </Link>-->
<!--            </div>-->

<!--            <div class="py-2 mx-10 my-6">-->
<!--                <Link :href="route('actors')">-->
<!--                <PeopleRounded fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>-->
<!--                </Link>-->
<!--            </div>-->
<!--            <div class="py-2 mx-10 my-6">-->
<!--                <Link :href="route('watchlist')">-->
<!--                <Plus fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>-->
<!--                </Link>-->
<!--            </div>-->
<!--            <div class="py-2 mx-10 my-6">-->
<!--                <Link :href="route('feedback')">-->
<!--                <Reply fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>-->
<!--                </Link>-->
<!--            </div>-->
<!--            <div class="py-2 mx-10 my-6">-->
<!--                <Link :href="route('admin')">-->
<!--                <Shield fillColor="#FFFFFF" :size="40" class="cursor-pointer"/>-->
<!--                </Link>-->
<!--            </div>-->
<!--        </div>-->
    </nav>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import Magnify from "vue-material-design-icons/Magnify.vue";
import HomeOutline from "vue-material-design-icons/HomeOutline.vue";
import Reply from "vue-material-design-icons/Reply.vue";
import Shield from "vue-material-design-icons/Shield.vue";
import Plus from "vue-material-design-icons/Plus.vue";

</script>

<style scoped>
.vertical-nav {
    height: 100vh; /* Full height */
    width: 56px; /* Width of the nav bar */
    position: fixed; /* Fixed position */
    left: 0;
    overflow-x: hidden; /* Disable horizontal scroll */

    display: flex;
    flex-direction: column;
    align-items: center; /* Center the icons horizontally */
    justify-content: center;

}

.vertical-nav ul {
    list-style-type: none; /* Remove bullet points */
    padding: 0; /* Remove default padding */
    margin: 0; /* Remove default margin */
    width: 100%; /* Full width of the nav bar */
    display: flex;
    flex-direction: column;
    align-items: center; /* Center the icons horizontally */
}

</style>

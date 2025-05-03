<template>
    <div
        class="bg-transparent p-5 flex justify-between transition-transform duration-300"
        :class="{'transform -translate-y-full': hideNavbar}"
    >
        <div class="flex bg-green-900 rounded-md px-4 space-x-6 font-bold p-1 ml-10">
            <h1 class="text-xl font-extra bold text-green-400 mr-40">flexter</h1>

            <Link :href="route('home')" class="px-2" :class="{'bg-teal-800 rounded' : $page.component === 'Main/Home'}">Home</Link>
            <Link :href="route('movies')" class="px-2" :class="{'bg-teal-800 rounded' : $page.component === 'Main/Movies/Movies'}">Movies</Link>
            <Link :href="route('tv')" class="px-2" :class="{'bg-teal-800 rounded' : $page.component === 'Main/Tv/Tv'}">TV Show</Link>

        </div>
                <Search/>



        <div class="flex bg-green-900 p-1 px-4 rounded-md font-bold">
            <div v-if="$page.props.auth.user" class="space-x-6">
                <Link :href="route('dashboard')" :class="{'bg-teal-800 px-3 rounded' : $page.component === 'User/Dashboard'}">Dashboard</Link>
                <Link :href="route('logout')" method="post" type="button" as="button">Logout</Link>
            </div>

            <div v-else>
                <Link :href="route('register')" class="px-2" :class="{'bg-zinc-900 rounded' : $page.component === 'Auth/Register'}">Register</Link>
                <Link :href="route('login')" class="px-2" :class="{'bg-zinc-900 rounded' : $page.component === 'Auth/Login'}">Login</Link>
            </div>
        </div>

        <div>
            <button @click="switchTheme">
                <span class="material-symbols-outlined hover:cursor-pointer text-zinc-900 dark:text-white">dark_mode</span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { switchTheme } from "../../theme"
import Search from "../Search/Search.vue";

const hideNavbar = ref(false)
let lastScrollTop = 0
let debounceTimeout: NodeJS.Timeout

// Handle the scroll event with debounce
const handleScroll = () => {
    clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(() => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop
        if (scrollTop > lastScrollTop) {
            // Scrolling down
            hideNavbar.value = true
        } else {
            // Scrolling up
            hideNavbar.value = false
        }
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop
    }, 50) // debounce delay of 50ms
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
nav {
    transition: transform 0.3s ease-in-out;
}
</style>

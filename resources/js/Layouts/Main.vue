
<template>
    <div class="relative w-full min-h-[100vh] h-auto dark:bg-zinc-900 text-zinc-100">
        <NavBar class="fixed top-0 left-0 w-full z-50" />
        <SideBar class="z-50"/>
        <main class="overflow-x-hidden">
            <div class="">
                <slot />
            </div>

        </main>
    </div>
</template>
<script setup lang="ts">

import NavBar from "../Components/Layout/NavBar.vue";
import SideBar from "../Components/Layout/SideBar.vue";
</script>

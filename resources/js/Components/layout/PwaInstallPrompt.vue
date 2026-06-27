<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const visible = ref(false);
const deferredPrompt = ref(null);

function onBeforeInstall(event) {
    event.preventDefault();
    deferredPrompt.value = event;
    visible.value = true;
}

async function install() {
    if (!deferredPrompt.value) return;

    deferredPrompt.value.prompt();
    await deferredPrompt.value.userChoice;
    deferredPrompt.value = null;
    visible.value = false;
}

function dismiss() {
    visible.value = false;
    sessionStorage.setItem('flexter.pwa.dismissed', '1');
}

onMounted(() => {
    if (sessionStorage.getItem('flexter.pwa.dismissed')) return;
    if (window.matchMedia('(display-mode: standalone)').matches) return;

    window.addEventListener('beforeinstallprompt', onBeforeInstall);
});

onBeforeUnmount(() => {
    window.removeEventListener('beforeinstallprompt', onBeforeInstall);
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-300"
        enter-from-class="opacity-0 translate-y-3"
        leave-active-class="transition duration-200"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible"
            class="fixed bottom-20 left-4 right-4 z-50 mx-auto flex max-w-lg items-center justify-between gap-4 rounded-2xl border border-hair/15 bg-surface/95 p-4 shadow-glow backdrop-blur-md lg:bottom-6"
        >
            <div class="min-w-0">
                <p class="text-sm font-semibold text-ink">Install Flexter</p>
                <p class="text-xs text-muted">Add to your home screen for quick access.</p>
            </div>
            <div class="flex shrink-0 gap-2">
                <button type="button" class="rounded-xl px-3 py-2 text-xs font-medium text-muted hover:text-ink" @click="dismiss">Not now</button>
                <button type="button" class="rounded-xl bg-aurora px-3 py-2 text-xs font-semibold text-white" @click="install">Install</button>
            </div>
        </div>
    </Transition>
</template>

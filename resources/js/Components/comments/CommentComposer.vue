<script setup>
import { computed, ref, watch } from 'vue';
import { ChatBubbleLeftEllipsisIcon, PaperAirplaneIcon } from '@heroicons/vue/24/outline';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    placeholder: { type: String, default: 'Share your thoughts…' },
    submitLabel: { type: String, default: 'Post' },
    initialBody: { type: String, default: '' },
    initialSpoiler: { type: Boolean, default: false },
    maxLength: { type: Number, default: 2000 },
    compact: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    pending: { type: Boolean, default: false },
    showCancel: { type: Boolean, default: false },
});

const emit = defineEmits(['submit', 'cancel']);

const body = ref(props.initialBody);
const isSpoiler = ref(props.initialSpoiler);

watch(
    () => props.initialBody,
    (value) => {
        body.value = value;
    },
);

const remaining = computed(() => props.maxLength - body.value.length);
const canSubmit = computed(() => body.value.trim().length > 0 && remaining.value >= 0 && !props.pending);

function submit() {
    if (!canSubmit.value) return;
    emit('submit', { body: body.value.trim(), isSpoiler: isSpoiler.value });
    body.value = '';
    isSpoiler.value = false;
}
</script>

<template>
    <div class="rounded-2xl glass p-4" :class="compact ? 'bg-surface2/50' : ''">
        <div class="flex gap-3">
            <div
                class="hidden h-9 w-9 shrink-0 place-items-center rounded-full bg-aurora-soft sm:grid"
                aria-hidden="true"
            >
                <ChatBubbleLeftEllipsisIcon class="h-4 w-4 text-accent" />
            </div>
            <div class="min-w-0 flex-1">
                <textarea
                    v-model="body"
                    rows="3"
                    :maxlength="maxLength"
                    :placeholder="placeholder"
                    :autofocus="autofocus"
                    class="w-full resize-none rounded-xl border border-hair/10 bg-surface/60 px-4 py-3 text-sm text-ink placeholder:text-muted focus:border-accent/40 focus:outline-none focus:ring-2 focus:ring-accent/20"
                />
                <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-xs text-muted">
                        <input
                            v-model="isSpoiler"
                            type="checkbox"
                            class="rounded border-hair/20 text-accent focus:ring-accent/30"
                        />
                        <ExclamationTriangleIcon class="h-3.5 w-3.5 text-amber-400" />
                        Contains spoilers
                    </label>
                    <span class="text-xs tabular-nums" :class="remaining < 50 ? 'text-amber-500' : 'text-muted'">
                        {{ remaining }}
                    </span>
                </div>
                <div class="mt-3 flex items-center justify-end gap-2">
                    <button
                        v-if="showCancel"
                        type="button"
                        class="rounded-full px-3 py-1.5 text-xs font-medium text-muted transition hover:text-ink"
                        @click="emit('cancel')"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-full bg-aurora px-4 py-2 text-xs font-semibold text-white shadow-glow transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!canSubmit"
                        @click="submit"
                    >
                        <PaperAirplaneIcon class="h-3.5 w-3.5" />
                        {{ submitLabel }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

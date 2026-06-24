<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ChatBubbleBottomCenterTextIcon, FireIcon, SparklesIcon } from '@heroicons/vue/24/outline';
import { useComments } from '../../lib/useComments.js';
import CommentComposer from './CommentComposer.vue';
import CommentItem from './CommentItem.vue';

const props = defineProps({
    media: { type: Object, required: true },
    comments: {
        type: Object,
        default: () => ({ threads: [], total: 0 }),
    },
});

const page = usePage();
const sort = ref('newest');
const { pending, maxBody, postComment, updateComment, deleteComment, toggleLike } = useComments(
    props.media.type,
    props.media.id,
);

const isLoggedIn = computed(() => !!page.props.auth?.user);
const total = computed(() => props.comments?.total ?? 0);

const sortedThreads = computed(() => {
    const list = [...(props.comments?.threads ?? [])];

    if (sort.value === 'top') {
        return list.sort(
            (a, b) =>
                b.likes_count - a.likes_count ||
                new Date(b.created_at).getTime() - new Date(a.created_at).getTime(),
        );
    }

    return list.sort(
        (a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime(),
    );
});

const hasComments = computed(() => sortedThreads.value.length > 0);

function onPost({ body, isSpoiler }) {
    postComment(body, { isSpoiler });
}

function onReply({ parentId, body, isSpoiler }) {
    postComment(body, { parentId, isSpoiler });
}

function onEdit({ commentId, body, isSpoiler }) {
    updateComment(commentId, body, isSpoiler);
}
</script>

<template>
    <section id="discussion" class="scroll-mt-24">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-1 text-xs font-semibold uppercase tracking-[0.25em] text-accent">Discussion</p>
                <h2 class="font-display text-2xl font-bold text-ink">
                    {{ total === 1 ? '1 take' : `${total} takes` }}
                </h2>
                <p class="mt-1 text-sm text-muted">Hot takes, deep dives, and spoiler-tagged theories welcome.</p>
            </div>
            <div class="flex rounded-full glass p-1 text-xs font-semibold">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 transition"
                    :class="sort === 'newest' ? 'bg-aurora text-white shadow-glow' : 'text-muted hover:text-ink'"
                    @click="sort = 'newest'"
                >
                    <SparklesIcon class="h-3.5 w-3.5" />
                    Newest
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 transition"
                    :class="sort === 'top' ? 'bg-aurora text-white shadow-glow' : 'text-muted hover:text-ink'"
                    @click="sort = 'top'"
                >
                    <FireIcon class="h-3.5 w-3.5" />
                    Top
                </button>
            </div>
        </div>

        <div class="rounded-3xl glass-strong p-5 sm:p-6">
            <CommentComposer
                v-if="isLoggedIn"
                :max-length="maxBody"
                :pending="pending"
                placeholder="What's your verdict on this one?"
                @submit="onPost"
            />
            <div
                v-else
                class="rounded-2xl border border-dashed border-hair/20 bg-surface2/40 px-5 py-6 text-center"
            >
                <ChatBubbleBottomCenterTextIcon class="mx-auto h-8 w-8 text-muted" />
                <p class="mt-2 text-sm text-muted">
                    <Link :href="route('login')" class="font-semibold text-accent hover:underline">Sign in</Link>
                    to join the conversation.
                </p>
            </div>

            <div v-if="hasComments" class="mt-6 divide-y divide-hair/10">
                <CommentItem
                    v-for="thread in sortedThreads"
                    :key="thread.id"
                    :comment="thread"
                    :pending="pending"
                    @reply="onReply"
                    @edit="onEdit"
                    @delete="deleteComment"
                    @like="toggleLike"
                />
            </div>

            <div v-else class="mt-8 py-10 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-aurora-soft">
                    <ChatBubbleBottomCenterTextIcon class="h-7 w-7 text-accent" />
                </div>
                <p class="mt-4 font-display text-lg font-semibold text-ink">Be the first voice in the room</p>
                <p class="mt-1 text-sm text-muted">No comments yet — start the thread with your hot take.</p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import {
    ChatBubbleLeftIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    HandThumbUpIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { HandThumbUpIcon as HandThumbUpSolidIcon } from '@heroicons/vue/24/solid';
import CommentComposer from './CommentComposer.vue';

const props = defineProps({
    comment: { type: Object, required: true },
    depth: { type: Number, default: 0 },
    maxDepth: { type: Number, default: 3 },
    pending: { type: Boolean, default: false },
});

const emit = defineEmits(['reply', 'edit', 'delete', 'like']);

const showReply = ref(false);
const showEdit = ref(false);
const collapsed = ref(false);
const spoilerRevealed = ref(false);

const indent = computed(() => Math.min(props.depth, props.maxDepth) * 1.25);
const hasReplies = computed(() => (props.comment.replies?.length ?? 0) > 0);
const replyCount = computed(() => countReplies(props.comment));
const avatarHue = computed(() => (props.comment.user.id * 47) % 360);

function countReplies(node) {
    return (node.replies ?? []).reduce((sum, r) => sum + 1 + countReplies(r), 0);
}

function onReply({ body, isSpoiler }) {
    emit('reply', { parentId: props.comment.id, body, isSpoiler });
    showReply.value = false;
}

function onEdit({ body, isSpoiler }) {
    emit('edit', { commentId: props.comment.id, body, isSpoiler });
    showEdit.value = false;
}

function confirmDelete() {
    if (window.confirm('Remove this comment and all its replies?')) {
        emit('delete', props.comment.id);
    }
}
</script>

<template>
    <article class="group relative" :style="{ marginLeft: depth > 0 ? `${indent}rem` : undefined }">
        <div
            v-if="depth > 0"
            class="absolute -left-3 top-0 bottom-0 w-px bg-gradient-to-b from-accent/30 via-hair/20 to-transparent"
            aria-hidden="true"
        />

        <div class="flex gap-3 py-4">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white ring-2 ring-hair/10"
                :style="{ background: `linear-gradient(135deg, hsl(${avatarHue} 70% 45%), hsl(${(avatarHue + 40) % 360} 65% 35%))` }"
                :title="comment.user.name"
            >
                {{ comment.user.initials }}
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                    <span class="text-sm font-semibold text-ink">{{ comment.user.name }}</span>
                    <span v-if="comment.reply_to && depth >= maxDepth" class="text-xs text-muted">
                        replying to
                        <span class="font-medium text-accent2">@{{ comment.reply_to.name }}</span>
                    </span>
                    <time class="text-xs text-muted" :datetime="comment.created_at">{{ comment.created_at_human }}</time>
                    <span v-if="comment.edited_at" class="text-xs italic text-muted">(edited)</span>
                    <span
                        v-if="comment.is_spoiler"
                        class="rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-500"
                    >
                        Spoiler
                    </span>
                </div>

                <div v-if="!showEdit" class="mt-1.5">
                    <p
                        class="whitespace-pre-wrap text-sm leading-relaxed text-ink/90 transition"
                        :class="comment.is_spoiler && !spoilerRevealed ? 'select-none blur-sm' : ''"
                    >
                        {{ comment.body }}
                    </p>
                    <button
                        v-if="comment.is_spoiler && !spoilerRevealed"
                        type="button"
                        class="mt-1 text-xs font-semibold text-amber-500 underline-offset-2 hover:underline"
                        @click="spoilerRevealed = true"
                    >
                        Reveal spoiler
                    </button>
                </div>

                <CommentComposer
                    v-else
                    compact
                    autofocus
                    show-cancel
                    :initial-body="comment.body"
                    :initial-spoiler="comment.is_spoiler"
                    submit-label="Save"
                    :pending="pending"
                    @submit="onEdit"
                    @cancel="showEdit = false"
                />

                <div v-if="!showEdit" class="mt-2 flex flex-wrap items-center gap-1">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium transition"
                        :class="comment.liked_by_me ? 'bg-accent/15 text-accent' : 'text-muted hover:bg-surface2 hover:text-ink'"
                        @click="emit('like', comment.id)"
                    >
                        <HandThumbUpSolidIcon v-if="comment.liked_by_me" class="h-3.5 w-3.5" />
                        <HandThumbUpIcon v-else class="h-3.5 w-3.5" />
                        {{ comment.likes_count || 'Like' }}
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium text-muted transition hover:bg-surface2 hover:text-ink"
                        @click="showReply = !showReply"
                    >
                        <ChatBubbleLeftIcon class="h-3.5 w-3.5" />
                        Reply
                    </button>
                    <button
                        v-if="comment.can_edit"
                        type="button"
                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium text-muted opacity-0 transition group-hover:opacity-100 hover:bg-surface2 hover:text-ink"
                        @click="showEdit = true"
                    >
                        <PencilSquareIcon class="h-3.5 w-3.5" />
                        Edit
                    </button>
                    <button
                        v-if="comment.can_delete"
                        type="button"
                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium text-muted opacity-0 transition group-hover:opacity-100 hover:bg-rose-500/10 hover:text-rose-400"
                        @click="confirmDelete"
                    >
                        <TrashIcon class="h-3.5 w-3.5" />
                        Delete
                    </button>
                    <button
                        v-if="hasReplies"
                        type="button"
                        class="ml-auto inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium text-muted transition hover:text-ink"
                        @click="collapsed = !collapsed"
                    >
                        <ChevronDownIcon v-if="collapsed" class="h-3.5 w-3.5" />
                        <ChevronUpIcon v-else class="h-3.5 w-3.5" />
                        {{ collapsed ? `Show ${replyCount} replies` : 'Hide replies' }}
                    </button>
                </div>

                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-1"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 -translate-y-1"
                >
                    <div v-if="showReply" class="mt-3">
                        <CommentComposer
                            compact
                            autofocus
                            show-cancel
                            :placeholder="`Reply to ${comment.user.name}…`"
                            submit-label="Reply"
                            :pending="pending"
                            @submit="onReply"
                            @cancel="showReply = false"
                        />
                    </div>
                </Transition>

                <div v-if="hasReplies && !collapsed" class="mt-1 border-l border-hair/10 pl-1">
                    <CommentItem
                        v-for="reply in comment.replies"
                        :key="reply.id"
                        :comment="reply"
                        :depth="depth + 1"
                        :max-depth="maxDepth"
                        :pending="pending"
                        @reply="emit('reply', $event)"
                        @edit="emit('edit', $event)"
                        @delete="emit('delete', $event)"
                        @like="emit('like', $event)"
                    />
                </div>
            </div>
        </div>
    </article>
</template>

<script>
export default {
    name: 'CommentItem',
};
</script>

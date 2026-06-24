import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const MAX_BODY = 2000;

export function useComments(mediaType, mediaId) {
    const page = usePage();
    const pending = ref(false);

    function isGuest() {
        if (!page.props.auth?.user) {
            router.visit(route('login'));
            return true;
        }
        return false;
    }

    function postComment(body, { parentId = null, isSpoiler = false } = {}) {
        if (isGuest()) return;

        pending.value = true;
        router.post(
            route('comments.store'),
            {
                media_type: mediaType,
                media_id: mediaId,
                body,
                is_spoiler: isSpoiler,
                parent_id: parentId,
            },
            {
                preserveScroll: true,
                only: ['comments'],
                onFinish: () => {
                    pending.value = false;
                },
            },
        );
    }

    function updateComment(commentId, body, isSpoiler = false) {
        if (isGuest()) return;

        pending.value = true;
        router.patch(
            route('comments.update', commentId),
            {
                body,
                is_spoiler: isSpoiler,
            },
            {
                preserveScroll: true,
                only: ['comments'],
                onFinish: () => {
                    pending.value = false;
                },
            },
        );
    }

    function deleteComment(commentId) {
        if (isGuest()) return;

        pending.value = true;
        router.delete(route('comments.destroy', commentId), {
            preserveScroll: true,
            only: ['comments'],
            onFinish: () => {
                pending.value = false;
            },
        });
    }

    function toggleLike(commentId) {
        if (isGuest()) return;

        router.post(
            route('comments.like', commentId),
            {},
            {
                preserveScroll: true,
                only: ['comments'],
            },
        );
    }

    return {
        pending,
        maxBody: MAX_BODY,
        postComment,
        updateComment,
        deleteComment,
        toggleLike,
        isGuest,
    };
}

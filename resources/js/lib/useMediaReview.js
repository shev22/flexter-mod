import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export function useMediaReview(initialReview, media) {
    const page = usePage();
    const review = ref(initialReview ?? null);

    const form = useForm({
        media_type: media.type,
        media_id: media.id,
        rating: initialReview?.rating ?? null,
        body: initialReview?.body ?? '',
        watched_on: initialReview?.watched_on ?? new Date().toISOString().slice(0, 10),
    });

    const isAuthenticated = computed(() => !!page.props.auth?.user);

    function save() {
        form.post(route('reviews.store'), {
            preserveScroll: true,
            onSuccess: () => {
                review.value = {
                    id: review.value?.id ?? null,
                    rating: form.rating,
                    body: form.body,
                    watched_on: form.watched_on,
                };
            },
        });
    }

    function setRating(value) {
        form.rating = value;
    }

    return {
        review,
        form,
        isAuthenticated,
        save,
        setRating,
    };
}

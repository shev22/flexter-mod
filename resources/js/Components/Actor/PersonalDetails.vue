<template>
    <section class="bg-gray-800 rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Personal Details</h3>
        <div class="space-y-4">
            <div>
                <h4 class="text-gray-400 text-sm">Birth Name</h4>
                <p>{{ details.place_of_birth }}</p>
            </div>
            <div>
                <h4 class="text-gray-400 text-sm">Birthday</h4>
                <p>{{ details.birthday }} (Age {{ details.age }})</p>
            </div>
            <div>
                <h4 class="text-gray-400 text-sm">Place of Birth</h4>
                <p>{{ details.place_of_birth }}</p>
            </div>
            <div>
                <h4 class="text-gray-400 text-sm">Known For</h4>
                <p>{{ details.known_for_department }}</p>
            </div>
            <div>
                <h4 class="text-gray-400 text-sm">Gender</h4>
                <p>{{ details.gender }}</p>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    details: {
        type: Object,
        required: true
    }
});
</script>

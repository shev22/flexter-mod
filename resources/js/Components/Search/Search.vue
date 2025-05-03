
<template>
    <div class="relative max-w-md mx-auto w-full">
        <SearchInput
            v-model="searchQuery"
            @submit="handleSearch"
            @update:modelValue="debouncedSearch"
        />

        <SearchResultsDropdown
            :show="showResults"
            :results="searchResults"
            :isLoading="isLoading"
            :searchQuery="searchQuery"
            @close="closeDropdown"
            @navigate="navigateToResult"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { route } from 'ziggy-js';
import { debounce } from 'lodash';
import SearchInput from './SearchInput.vue';
import SearchResultsDropdown from './SearchResultsDropdown.vue';

const searchQuery = ref('');
const searchResults = ref([]);
const showResults = ref(false);
const isLoading = ref(false);

// Debounced search function
const debouncedSearch = debounce(() => {
    if (searchQuery.value.trim().length > 1) {
        performSearch();
    } else {
        searchResults.value = [];
        showResults.value = false;
    }
}, 300);

// Perform the actual search
const performSearch = async () => {
    try {
        isLoading.value = true;
        showResults.value = true;

        const response = await axios.get(route('api.search'), {
            params: { query: searchQuery.value.trim() }
        });

        searchResults.value = response.data.results || [];
    } catch (error) {
        console.error('Search failed:', error);
        searchResults.value = [];
    } finally {
        isLoading.value = false;
    }
};

// Handle form submission
const handleSearch = () => {
    if (searchQuery.value.trim()) {
        window.location.href = route('search.show', { query: searchQuery.value.trim() });
    }
};

// Close dropdown
const closeDropdown = () => {
    showResults.value = false;
};

// Navigate to individual result
const navigateToResult = (result) => {
    window.location.href = route(result.route, {
        id: result.id,
        slug: generateSlug(result.title)
    });
};

function generateSlug(title) {
    return title
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim();
}
</script>

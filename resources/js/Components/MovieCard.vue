<template>
    <Link class="overflow-hidden" :href="`/show/${movie.id}`">
    <div ref="target" :style="{
        transform: cardTransform,
        transition: 'transform 0.25s ease-out, box-shadow 0.3s ease-in-out'
    }"
        class="relative bg-[#141414] max-w-[220px] rounded-xl cursor-pointer overflow-hidden shadow-lg hover:shadow-2xl hover:scale-105 transform transition-all">

        <!-- Movie Poster -->
        <img :src="`https://image.tmdb.org/t/p/w220_and_h330_face/${movie.poster_path}`" alt="Movie Poster"
            class="w-full overflow-hidden h-auto object-cover rounded-t-xl transition-transform duration-500 ease-in-out" />

        <!-- Overlay for more details on hover -->
        <div
            class="absolute inset-0 bg-black overflow-hidden bg-opacity-60 opacity-0 hover:opacity-100 transition-all flex flex-col justify-between p-3">
            <div class="text-white">
                <!-- Movie Title -->
                <h1 class="font-semibold text-xl mb-2 overflow-hidden">{{ movie.title }}</h1>

                <!-- Movie Overview -->
                <p class="text-sm line-clamp-3 mb-2 overflow-hidden">{{ movie.overview }}</p>

                <!-- Rating -->
                <div class="flex items-center space-x-2 text-green-500 overflow-hidden">
                    <span class="font-semibold overflow-hidden">{{ (movie.vote_average).toFixed() }}/10</span>
                    <span class="text-sm text-gray-400 overflow-hidden">({{ movie.vote_count }} votes)</span>
                </div>
            </div>

            <!-- Movie Release Date and Genre -->
            <div class="text-white mt-2 text-sm overflow-hidden">
                <div><strong>Release Date:</strong> {{ moment(movie.release_date).format('DD/MM/YYYY') }}</div>
            </div>
        </div>
    </div>
    </Link>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useMouseInElement } from '@vueuse/core';
import { Link } from '@inertiajs/vue3';
import moment from 'moment';

defineProps({
    movie: Object,
});

const target = ref(null);

const { elementX, elementY, isOutside, elementHeight, elementWidth } = useMouseInElement(target);

const cardTransform = computed(() => {
    const MAX_ROTATION = 6;
    const normalizedX = (elementX.value / elementWidth.value) - 0.5;
    const normalizedY = (elementY.value / elementHeight.value) - 0.5;

    const rX = (normalizedY * MAX_ROTATION).toFixed(2);
    const rY = (normalizedX * MAX_ROTATION).toFixed(2);

    return isOutside.value ? '' : `perspective(${elementWidth.value}px) rotateX(${rX}deg) rotateY(${rY}deg)`;
});

// Function to get genres from genre_ids
const getGenres = (genreIds: number[]) => {
    const genres = {
        99: "Documentary",
        28: "Action",
        12: "Adventure",
        35: "Comedy",
        18: "Drama",
        // Add more genres here as needed
    };

    return genreIds.map(id => genres[id] || 'Unknown').join(', ');
};
</script>

<style scoped>
/* Styling for the card */
.card {
    transform: v-bind(cardTransform);
    transition: transform 0.25s ease-out;
}

/* Hover effect */
.card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    scale: 1.05;
}

/* Image scaling on hover */
img {
    transition: transform 0.5s ease-in-out;
}

img:hover {
    transform: scale(1.1);
}

/* Overlay effect on hover */
.card:hover .absolute {
    opacity: 1;
}

/* Title and description styling */
h1 {
    font-size: 1.125rem;
    line-height: 1.3;
    font-weight: 600;
    color: #fff;
}

p {
    font-size: 0.875rem;
    color: #ccc;
}

/* Genre and release date styling */
.text-sm {
    font-size: 0.875rem;
}

.text-yellow-500 {
    color: #fbbf24;
}

.text-gray-400 {
    color: #9ca3af;
}
</style>

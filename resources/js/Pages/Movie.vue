<template>
    <div>

        <Head title="Movie" />
        <div v-if="spinner" class="mt-[40vh] flex justify-center">
            <img src="https://static.wixstatic.com/media/aa050c_38acaa6725474fd2903ea52d987f9aa5~mv2.png/v1/fit/w_2500,h_1330,al_c/aa050c_38acaa6725474fd2903ea52d987f9aa5~mv2.png"
                class="spin-continuous w-[128px]" />
        </div>
        <div v-else>
            <div class="relative w-full h-screen font-roboto">
                <img v-if="!video" :src="randomBackdrop" alt="Movie Backdrop"
                    class="object-cover w-full h-full brightness-75">
                <iframe v-else id="videoIframe" width="1920" height="1080"
                    :src="`https://www.youtube.com/embed/${video.key}?autoplay=1&loop=1&controls=0&modestbranding=1&rel=0&showinfo=0&muted=1&vq=hd720p60`"
                    frameborder="0" allow="autoplay" class="object-cover w-full h-screen absolute top-0 left-0"
                    allowfullscreen>
                </iframe>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-75"></div>
                <div class="absolute top-[25%] left-[10%] p-8 text-white max-w-4xl">
                    <h1 class="text-8xl font-bold font-roboto mb-4 overflow-hidden">{{ movie.title }}</h1>
                    <p class="text-xl italic font-roboto">{{ movie.tagline }}</p>
                    <p class="mt-4 text-2xl font-roboto max-w-[820px]">{{ movie.overview }}</p>
                    <div class="mt-6 flex flex-wrap items-center space-x-4 text-lg">
                        <span class="font-medium font-roboto">Genres:</span>
                        <div class="flex space-x-2">
                            <span v-for="genre in movie.genres" :key="genre.id"
                                class="bg-[#1c1c1c] rounded-full px-3 py-1 font-roboto">{{ genre.name }}</span>
                        </div>
                    </div>
                    <div class="mt-4 text-lg">
                        <span class="font-medium font-roboto">Runtime:</span> {{ movie.runtime }} min
                    </div>
                    <div class="mt-4 text-lg">
                        <span class="font-medium font-roboto">Release Date:</span> {{ movie.release_date }}
                    </div>
                    <div class="mt-4 text-lg">
                        <span class="font-medium font-roboto">Production Companies:</span>
                        <ul class="list-disc list-inside">
                            <li v-for="company in movie.production_companies" :key="company.id">
                                {{ company.name }} ({{ company.origin_country }})
                            </li>
                        </ul>
                    </div>
                    <a :href="movie.homepage" target="_blank"
                        class="mt-6 inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl">
                        Visit Homepage
                    </a>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-20 bg-gradient-to-t from-black to-transparent"></div>
            </div>
            <div class="my-16">
                <div class="w-10/12 mx-auto">
                    <h1 class="text-6xl font-bold font-roboto mb-4 overflow-hidden">You might also like</h1>
                </div>
                <div class="w-10/12 mx-auto grid grid-cols-10 gap-3 ">
                    <div class="overflow-hidden" v-for="movie in similarTitles.results" :key="movie.id">
                        <MovieCard :movie="movie" class="overflow-hidden" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import MovieCard from '@/Components/MovieCard.vue';
import { Head } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

export default {
    components: {
        Head,
        MovieCard,
    },
    layout: Layout,
    data() {
        return {
            spinner: true,
            movie: {},
            images: {},
            similarTitles: {},
            video: null,
        };
    },
    props: {
        movie_id: Number,
    },
    computed: {
        randomBackdrop() {
            if (!this.images.backdrops || this.images.backdrops.length === 0) return '';
            const index = Math.floor(Math.random() * this.images.backdrops.length);
            return `https://media.themoviedb.org/t/p/w1920_and_h800_multi_faces/${this.images.backdrops[index].file_path}`;
        },
    },
    methods: {
        async fetchMovieData() {
            const apiKey = this.$page.props.movie_api;
            const movieId = this.movie_id;

            try {
                const movieResponse = await axios.get(`https://api.themoviedb.org/3/movie/${movieId}`, {
                    headers: { Authorization: `Bearer ${apiKey}` },
                });
                this.movie = movieResponse.data;

                const imagesResponse = await axios.get(`https://api.themoviedb.org/3/movie/${movieId}/images`, {
                    headers: { Authorization: `Bearer ${apiKey}` },
                });
                this.images = imagesResponse.data;
                const similarResponse = await axios.get(`https://api.themoviedb.org/3/movie/${movieId}/recommendations`, {
                    headers: { Authorization: `Bearer ${apiKey}` },
                });
                this.similarTitles = similarResponse.data;
                const videosResponse = await axios.get(`https://api.themoviedb.org/3/movie/${movieId}/videos`, {
                    headers: { Authorization: `Bearer ${apiKey}` },
                });
                const videos = videosResponse.data.results;
                this.video = videos.find(video => video.type.toLowerCase().includes('trailer')) || videos[0];
            } catch (error) {
                console.error('Error fetching movie data:', error);
            } finally {
                this.spinner = false;
                this.setupAutoplay();
            }
        },
        setupAutoplay() {
            const iframe = this.$refs.videoIframe;
            iframe.addEventListener('click', () => {
                iframe.src = iframe.src.replace('&autoplay=0', '&autoplay=1');
            });
        },
    },
    async created() {
        await this.fetchMovieData();
    },
};
</script>
<style scoped>
img {
    filter: brightness(0.7);
}

.vignette::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle, rgba(0, 0, 0, 0) 60%, rgba(0, 0, 0, 0.7) 100%);
    pointer-events: none;
}

.spin-continuous {
    animation: spin 5s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

@media (min-width: 1920px) {
    h1 {
        font-size: 6rem;
    }

    .p-8 {
        padding: 4rem;
    }

    .max-w-4xl {
        max-width: 1200px;
    }
}

@media (max-width: 768px) {
    h1 {
        font-size: 2rem;
    }

    p {
        font-size: 1rem;
    }

    .p-8 {
        padding: 1rem;
    }

    .max-w-4xl {
        max-width: 100%;
    }

    .text-lg {
        font-size: 0.875rem;
    }

    .mt-6 {
        margin-top: 1rem;
    }

    .space-x-4 {
        gap: 0.5rem;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    a {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
}

@media (min-width: 768px) and (max-width: 1024px) {
    h1 {
        font-size: 3rem;
    }

    .max-w-4xl {
        max-width: 800px;
    }

    p {
        font-size: 1.125rem;
    }
}
</style>

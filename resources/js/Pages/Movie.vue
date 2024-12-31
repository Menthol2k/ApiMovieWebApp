<template>

    <Head title="movie" />
    <div v-if="!spinner" class="relative w-full h-screen font-roboto">
        <img v-if="!video" :src="randomBackdrop" alt="Movie Backdrop" class="object-cover w-full h-full brightness-75">
        <iframe v-else id="videoIframe" width="1920" height="1080"
            :src="`https://www.youtube.com/embed/${video.key}?autoplay=1&loop=1&controls=0&modestbranding=1&rel=0&showinfo=0&muted=1&vq=hd720p60`"
            frameborder="0" allow="autoplay" class="object-cover w-full h-screen absolute top-0 left-0" allowfullscreen>
        </iframe>

        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-75"></div>

        <div class="absolute top-[25%] left-[10%] p-8 text-white max-w-4xl">
            <h1 class="text-8xl font-bold font-roboto mb-4">{{ movie.title }}</h1>
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


    <div v-if="!spinner" class="">
        <div class="my-16">
            <div class="w-10/12 mx-auto">
                <h1 class="text-6xl font-bold font-roboto mb-4">You might also like</h1>
            </div>
        </div>
        <div class="w-10/12 mx-auto">
            <div class="grid grid-cols-10 gap-3">
                <div v-for="movie in similar_titles.results" :key="movie.id">
                    <MovieCard :movie="movie" />
                </div>
            </div>
        </div>
    </div>
    <div v-else class="mt-[40vh]">
        <img src="https://static.wixstatic.com/media/aa050c_38acaa6725474fd2903ea52d987f9aa5~mv2.png/v1/fit/w_2500,h_1330,al_c/aa050c_38acaa6725474fd2903ea52d987f9aa5~mv2.png"
            class="spin-continuous w-[128px]" />
    </div>
</template>

<script>
import MovieCard from '@/Components/MovieCard.vue';
import Layout from '@/Layouts/Layout.vue';
import { Link } from '@inertiajs/vue3';

export default {

    components: {
        Link, MovieCard
    },

    data() {
        return {
            spinner: true,
            muted: '0',
        }
    },

    layout: Layout,

    props: {
        movie: Object,
        images: Object,
        similar_titles: Object,
        video: Object,
    },

    mounted() {
        setTimeout(() => {
            this.setupAutoplay();
        }, 100)
    },

    methods: {
        setupAutoplay() {
            const iframe = this.$refs.videoIframe;
            iframe.addEventListener('click', () => {
                iframe.src = iframe.src.replace('&autoplay=0', '&autoplay=1');
            });
        },
    },

    computed: {
        randomBackdrop() {
            let max = this.images['backdrops'].length;
            let min = 0;
            let selectedIndex = Math.floor(Math.random() * (max - min) + min);
            return `https://media.themoviedb.org/t/p/w1920_and_h800_multi_faces/${this.images['backdrops'][selectedIndex].file_path}`;
        },
    },

    created() {
        setTimeout(() => {
            this.spinner = false;
        }, 3000);
        setTimeout(() => {
            this.muted = '1';
        }, 3100);
    }
};

</script>

<style scoped>
/* Dimensiuni generale */
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

/* Container pentru iframe pentru a ocupa întregul ecran */
.video-container {
    position: relative;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
}

/* Asigură că iframe-ul ocupă 100% din lățime și înălțime */
.video-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Stiluri pentru rezoluții mari (1920x1080) */
@media (min-width: 1920px) {
    h1 {
        font-size: 6rem;
        /* Dimensiune mare pentru titluri */
    }

    .p-8 {
        padding: 4rem;
    }

    .max-w-4xl {
        max-width: 1200px;
        /* Extindere pentru ecrane mari */
    }
}

/* Stiluri pentru dispozitive mobile */
@media (max-width: 768px) {
    h1 {
        font-size: 2rem;
        /* Dimensiune mai mică pentru titluri */
    }

    p {
        font-size: 1rem;
        /* Ajustează dimensiunea fontului pentru texte */
    }

    .p-8 {
        padding: 1rem;
    }

    .max-w-4xl {
        max-width: 100%;
        /* Lățime completă pe mobil */
    }

    .text-lg {
        font-size: 0.875rem;
        /* Dimensiune mică pentru subtitluri */
    }

    .mt-6 {
        margin-top: 1rem;
    }

    .space-x-4 {
        gap: 0.5rem;
        /* Spațiere mai mică între elemente */
    }

    .mt-4 {
        margin-top: 1rem;
    }

    a {
        font-size: 0.875rem;
        /* Dimensiune mai mică pentru butoane */
        padding: 0.5rem 1rem;
    }
}

/* Alte ajustări pentru tabletă */
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

    <template>
        <div>
            <Head title="Home" />
            <div class="w-12/12 mx-auto">
                <div v-if="spinner" class="mt-[40vh]">
                    <img src="https://static.wixstatic.com/media/aa050c_38acaa6725474fd2903ea52d987f9aa5~mv2.png/v1/fit/w_2500,h_1330,al_c/aa050c_38acaa6725474fd2903ea52d987f9aa5~mv2.png"
                        class="spin-continuous w-[128px] animate-ping" />
                </div>
                <div class="relative w-full h-screen font-roboto">
                    <img v-if="images" :src="picture" alt="Movie Backdrop"
                        class="object-cover w-full h-full brightness-75" @load="onImageLoaded" @error="onImageError">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-75"></div>

                    <div v-if="!spinner" class="absolute top-[25%] left-[10%] p-8 text-white max-w-4xl">
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

                        <Link :href="`/show/${movie.id}`" target="_blank"
                            class="mt-6 inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl">
                        Play {{ movie.title }}
                        </Link>
                    </div>

                    <div v-if="!spinner"
                        class="absolute bottom-0 left-0 w-full h-20 bg-gradient-to-t from-black to-transparent"></div>
                </div>
            </div>
        </div>

        <div v-if="!spinner" class="my-20 w-10/12 mx-auto">
            <h1 class="text-8xl font-bold font-roboto mb-4 overflow-hidden">More shows for you</h1>
        </div>

        <div v-if="!spinner" class="w-10/12 mx-auto grid grid-cols-10 gap-3">
            <div class="overflow-hidden" v-for="movie in feeds.results" :key="movie.id">
                <MovieCard :movie="movie" />
            </div>
        </div>
    </template>
<script>
import MovieCard from '@/Components/MovieCard.vue';
import Layout from '@/Layouts/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import moment from 'moment';

export default {
    components: {
        Head, MovieCard, Link
    },

    data() {
        return {
            spinner: true,
            feeds: [],
            movie: [],
            images: [],
            video: [],
            picture: null,
        }
    },

    layout: Layout,

    mounted() {
        this.fetchData();
    },

    methods: {
        async fetchData() {
            let steps = 0;
            let feeds_shows = [];
            const PAGE = 1;
            const API_KEY = this.$page.props.movie_api;
            let movie_id = null;

            await axios.get(`https://api.themoviedb.org/3/movie/popular?language=en-US&page=${PAGE}`, {
                headers: {
                    Authorization: `Bearer ${API_KEY}`
                }
            })
                .then((response) => {
                    feeds_shows = response.data;
                    this.feeds = feeds_shows;
                    if (!localStorage.getItem('movie_id')) {
                        const daily_movie_index = Math.floor(Math.random() * feeds_shows.results.length);
                        const movie_id = feeds_shows.results[daily_movie_index].id;
                        localStorage.setItem('movie_id', movie_id);
                    }

                    movie_id = localStorage.getItem('movie_id');
                    console.log("Movie ID:", movie_id);
                })
                .catch((errors) => {
                    console.error(errors);
                });

            axios.get(`https://api.themoviedb.org/3/movie/${movie_id}`, {
                headers: {
                    Authorization: `Bearer ${API_KEY}`
                }
            })
                .then((response) => {
                    this.movie = response.data;
                })
                .catch((errors) => {
                    console.error(errors);
                })

            axios.get(`https://api.themoviedb.org/3/movie/${movie_id}/images`, {
                headers: {
                    Authorization: `Bearer ${API_KEY}`
                }
            })
                .then((response) => {
                    this.images = response.data;
                    this.randomBackdrop();
                })
                .catch((errors) => {
                    console.error(errors);
                });

            await axios.get(`https://api.themoviedb.org/3/movie/${movie_id}/videos`, {
                headers: {
                    Authorization: `Bearer ${API_KEY}`
                }
            })
                .then((response) => {
                    this.video = response.data['results'][0];
                    console.log("Finish!");
                })
                .catch((errors) => {
                    console.error(errors);
                });

        },

        onImageLoaded() {
            this.spinner = false;
            console.log("Imaginea s-a încărcat!");
        },

        onImageError() {
            console.error("Imaginea nu s-a încărcat.");
            this.spinner = false;
        },


        randomBackdrop() {
            if (!this.images || !this.images['backdrops'] || this.images['backdrops'].length === 0) {
                return null;
            }
            let max = this.images['backdrops'].length;
            let min = 0;
            let selectedIndex = Math.floor(Math.random() * (max - min) + min);
            this.picture = `https://media.themoviedb.org/t/p/w1920_and_h800_multi_faces/${this.images['backdrops'][selectedIndex].file_path}`;
            this.spinner = true;
        },
    },

    created() {
        this.moment = moment;

    }
}

</script>

<style scoped>
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
</style>

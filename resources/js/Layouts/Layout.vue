<template>
    <div class="bg-black text-white min-h-screen flex flex-col">
        <nav :class="navClass" id="navigation" class="fixed w-full z-10 transition-all duration-300">
            <div class="w-11/12 mx-auto flex justify-between items-center py-4 px-6">
                <Link href="/" class="text-2xl font-bold tracking-wide">
                <img class="w-24" src="https://lodgrin.com/images/lodgrin_logo_dark_background.png" />
                </Link>
                <ul class="flex space-x-6 text-lg">
                    <li>
                        <Link to="/" class="text-white hover:opacity-75 transition-all font-light text-sm">HOME</Link>
                    </li>
                    <li>
                        <Link to="/movies" class="text-white hover:opacity-75 transition-all font-light text-sm">MOVIES
                        </Link>
                    </li>
                    <li>
                        <Link to="/series" class="text-white hover:opacity-75 transition-all font-light text-sm">SERIES
                        </Link>
                    </li>
                    <li>
                        <Link to="/about" class="text-white hover:opacity-75 transition-all font-light text-sm">MY LIST
                        </Link>
                    </li>
                </ul>
                <div class="hidden sm:block">
                    <input type="text" placeholder="Search..." @click="open = true"
                        class="bg-black text-sm text-white px-8 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#E50914]" />
                </div>
            </div>
        </nav>

        <main class="flex-grow mx-auto">
            <slot />
        </main>

        <footer class="bg-transparent text-gray-400 text-center py-6">
            <p>&copy; 2024 Lodgrin. All rights reserved.</p>
        </footer>

        <TransitionRoot :show="open" as="template" @after-leave="query = ''" appear>
            <Dialog class="relative z-10 bg-[#0d0d0d]" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500/25 transition-opacity"></div>
                </TransitionChild>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100" leave="ease-in duration-200" leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel
                            class="mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-[#0d0d0d] ring-1 shadow-2xl ring-black/5 transition-all">
                            <Combobox v-slot="{ activeOption }" @update:modelValue="onSelect">
                                <div class="grid grid-cols-1">
                                    <input v-model="query" style="background-color: #0d0d0d;"
                                        class="col-start-1 row-start-1 h-12 w-full pr-4 pl-11 text-base text-gray-200 outline-none focus:ring-0 focus:outline-none border-0 placeholder:text-gray-400 sm:text-sm"
                                        placeholder="Search..." @input="refreshSearch" @blur="query = ''" />
                                    <MagnifyingGlassIcon
                                        class="pointer-events-none col-start-1 row-start-1 ml-4 size-5 self-center text-gray-200"
                                        aria-hidden="true" />
                                </div>

                                <ComboboxOptions v-if="query === '' || filteredMovie.length > 0"
                                    class="flex transform-gpu" as="div" static hold>
                                    <div v-if="query == ''" class="p-3 text-gray-200 text-sm">
                                        Search your favorite movie...
                                    </div>
                                    <div
                                        :class="['max-h-96 min-w-0 flex-auto scroll-py-4 overflow-y-auto px-6 py-4', activeOption && 'sm:h-96']">
                                    </div>
                                    <div class="flex justify-center items-center">
                                        <div class="grid grid-cols-3 gap-3">
                                            <div v-for="mov in filteredMovie" :key="mov.id">
                                                <MovieCard @click="open = false" :movie="mov" />
                                            </div>
                                        </div>
                                    </div>
                                </ComboboxOptions>


                                <div v-if="query !== '' && filteredMovie.length === 0"
                                    class="px-6 py-14 text-center text-sm sm:px-14">
                                    <UsersIcon class="mx-auto size-6 text-gray-400" aria-hidden="true" />
                                    <p class="mt-4 font-semibold text-gray-900">No movie found</p>
                                    <p class="mt-2 text-gray-500">We couldn’t find anything with that term. Please try
                                        again.</p>
                                </div>
                            </Combobox>
                        </DialogPanel>
                    </TransitionChild>
                </div>
                <pre></pre>
            </Dialog>
        </TransitionRoot>
    </div>
</template>
<script>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import { MagnifyingGlassIcon, ChevronRightIcon, UsersIcon } from '@heroicons/vue/24/outline';
import { throttle } from 'lodash';
import MovieCard from '@/Components/MovieCard.vue';


export default {
    name: 'Layout',
    components: {
        Link,
        Combobox,
        ComboboxInput,
        ComboboxOptions,
        ComboboxOption,
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        MagnifyingGlassIcon,
        ChevronRightIcon,
        UsersIcon,
        MovieCard
    },
    data() {
        return {
            isScrolled: false,
            open: false,
            query: '',
            movie: [],
            recent: [],
            onSelect: false
        };
    },
    computed: {
        filteredMovie() {
            if (!this.query) {
                return this.recent.length ? this.recent : [];
            }
            return Array.isArray(this.movie)
                ? this.movie.filter((p) => p.title && p.title.toLowerCase().includes(this.query.toLowerCase()))
                : [];
        },
    },

    methods: {
        refreshSearch() {
            const options = {
                method: 'GET',
                headers: {
                    accept: 'application/json',
                    Authorization: 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxYmZmMDU0ZGIwOGMyMzQwZjY3MWZiZjUzMDgyNmU0NiIsIm5iZiI6MTczNTA3NDUwNy41MTEwMDAyLCJzdWIiOiI2NzZiMjJjYjI0OGM5MmUzZWZhOWRkYjYiLCJzY29wZXMiOlsiYXBpX3JlYWQiXSwidmVyc2lvbiI6MX0.xuPSHRpFulk12o_z-SFZLJleJI2hMTOnmGpHg5RN7qE'
                }
            };

            fetch(`https://api.themoviedb.org/3/search/movie?query=${this.query}&include_adult=false&language=en-US&page=1`, options)
                .then(res => res.json())
                .then(res => this.movie = res.results)
                .then(res => console.log(res))
                .catch(err => console.error(err));
        }
    },
    // watch: {
    //     query: {
    //         handler: throttle(() => {
    //             this.refreshSearch();
    //         }, 350).bind(this) 
    //     }
    // }

};
</script>

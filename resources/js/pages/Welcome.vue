<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { search } from '@/routes/zenserp';

type SearchResult = {
    position: number | null;
    title: string | null;
    url: string | null;
    destination: string | null;
    description: string | null;
};

type RelatedSearch = {
    title: string | null;
    url: string | null;
};

type SearchResponse = {
    data: SearchResult[];
    related_searches: RelatedSearch[];
    pagination: {
        pages: number;
        total: number;
    };
};

type ZenserpSearchResponse = SearchResponse & {
    organic?: SearchResult[];
    relatedSearches?: RelatedSearch[];
    related?: RelatedSearch[];
    results?: SearchResult[];
    page?: {
        pages?: number;
        total?: number;
    };
};

const pageSize = 10;

const query = ref('');
const loading = ref(false);
const error = ref('');
const searched = ref(false);
const currentPage = ref(1);
const results = ref<SearchResult[]>([]);
const relatedSearches = ref<RelatedSearch[]>([]);
const pagination = ref({ pages: 0, total: 0 });
const pageCount = computed(() => {
    if (pagination.value.pages > 0) {
        return pagination.value.pages;
    }

    return Math.max(0, Math.ceil((pagination.value.total ?? 0) / pageSize));
});

const canPaginate = computed(() => pageCount.value > 1);

const runSearch = async (page = 1): Promise<void> => {
    const normalizedQuery = query.value.trim();

    if (!normalizedQuery) {
        error.value = 'Enter a search term to continue.';
        return;
    }

    loading.value = true;
    searched.value = true;
    currentPage.value = page;
    error.value = '';

    try {
        const start = (page - 1) * pageSize;

        const response = await fetch(
            search.url({
                query: { q: normalizedQuery, num: pageSize, start },
            }),
            {
                headers: {
                    Accept: 'application/json',
                },
            },
        );

        if (!response.ok) {
            throw new Error(`Search failed (${response.status})`);
        }

        const payload = (await response.json()) as SearchResponse;
        const normalizedPayload = payload as ZenserpSearchResponse;

        results.value = normalizedPayload.data
            ?? normalizedPayload.results
            ?? normalizedPayload.organic
            ?? [];

        relatedSearches.value = normalizedPayload.related_searches
            ?? normalizedPayload.relatedSearches
            ?? normalizedPayload.related
            ?? [];

        const nextPagination = normalizedPayload.pagination
            ?? normalizedPayload.page
            ?? { pages: 0, total: 0 };

        pagination.value = {
            pages: nextPagination.pages ?? 0,
            total: nextPagination.total ?? 0,
        };

    } 
    catch (requestError) 
    {
        error.value = requestError instanceof Error ? requestError.message : 'An unexpected error occurred while searching.';
        results.value = [];
        relatedSearches.value = [];
        pagination.value = { pages: 0, total: 0 };
    } 
    finally 
    {
        loading.value = false;
    }
};

const previousPage = (): void => {
    if (currentPage.value > 1 && !loading.value) {
        void runSearch(currentPage.value - 1);
    }
};

const nextPage = (): void => {
    if (currentPage.value < pagination.value.pages && !loading.value) {
        void runSearch(currentPage.value + 1);
    }
};

const submitSearch = (): void => {
    void runSearch(1);
};
</script>

<template>
    <Head title="Netwatch Global Search">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="min-h-screen bg-[linear-gradient(180deg,#f6fbff_0%,#eef5fb_100%)] font-[Manrope] text-[#0b1b2c]">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <header class="rounded-[1.5rem] border border-[#c7d9e8] bg-white/90 p-5 shadow-[0_8px_24px_rgba(18,52,86,0.08)] backdrop-blur-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <svg width="45" height="100" viewBox="0 0 122 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M122 50C122 77.6143 99.6142 100 72 100C55.8674 100 42 93.5 19.5 68.5C8.5 56.2778 0 49.5 0 49.5C0 49.5 10.5 39 19.5 29.5C48.5 0 55.8674 0 72 0C99.6142 0 122 22.3858 122 50Z" fill="#38BDF8"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#5c82a5]">
                                Internal Portal
                            </p>
                            <h1 class="mt-1 text-[1.15rem] font-bold leading-tight text-[#0e2d4d] sm:text-[1.35rem]">
                                Netwatch Global Search
                            </h1>
                        </div>
                    </div>
                    <p class="max-w-md text-sm leading-relaxed text-[#4b6c89] sm:text-right">
                        Turning public data into confident decisions, fast.
                    </p>
                </div>
            </header>

            <section class="rounded-[1.5rem] border border-[#c7d9e8] bg-white p-5 shadow-[0_8px_24px_rgba(18,52,86,0.08)] sm:p-6">
                <form class="flex flex-col gap-3 sm:flex-row" @submit.prevent="submitSearch">
                    <input
                        v-model="query"
                        type="text"
                        placeholder="Search intelligence, entities, domains, or people"
                        class="h-12 w-full rounded-xl border border-[#b8cde1] bg-[#f9fcff] px-4 text-[15px] text-[#092038] outline-none ring-[#3d82be] transition placeholder:text-[#6c8caa] focus:ring-2"
                    />
                    <button
                        type="submit"
                        :disabled="loading"
                        class="h-12 rounded-xl bg-[#0f5f9c] px-6 text-sm font-semibold text-white transition hover:bg-[#0b4e81] disabled:cursor-not-allowed disabled:opacity-70 sm:min-w-32"
                    >
                        {{ loading ? 'Searching...' : 'Search' }}
                    </button>
                </form>

                <p v-if="error" class="mt-3 text-sm text-[#ad2f2f]">{{ error }}</p>

                <div v-if="searched && !loading" class="mt-4 flex flex-wrap gap-4 text-xs font-medium text-[#4a6d8c]">
                    <span>Results: {{ pagination.total }}</span>
                    <span>Pages: {{ pagination.pages }}</span>
                </div>
            </section>

            <section v-if="loading" class="space-y-3">
                <div
                    v-for="placeholder in 5"
                    :key="placeholder"
                    class="animate-pulse rounded-[1.5rem] border border-[#d5e2ee] bg-white p-5 shadow-[0_6px_18px_rgba(18,52,86,0.05)]"
                >
                    <div class="h-4 w-3/5 rounded bg-[#d9e8f4]" />
                    <div class="mt-3 h-3 w-2/5 rounded bg-[#e4eef7]" />
                    <div class="mt-2 h-3 w-full rounded bg-[#edf4fa]" />
                </div>
            </section>

            <section v-else-if="results.length" class="space-y-3">
                <article
                    v-for="(result, index) in results"
                    :key="`${result.url}-${index}`"
                    class="rounded-[1.5rem] border border-[#d5e2ee] bg-white p-5 shadow-[0_6px_18px_rgba(18,52,86,0.05)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(18,52,86,0.08)]"
                >
                    <h2 class="mt-2 text-lg font-semibold leading-tight text-[#0c2e50]">
                        {{ result.title ?? 'Untitled result' }}
                    </h2>
                    <a
                        v-if="result.url"
                        :href="result.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-2 block break-all text-sm text-[#0f5f9c] hover:underline"
                    >
                        {{ result.destination || result.url }}
                    </a>
                    <p class="mt-2 text-sm text-[#2f4a64]">
                        {{ result.description || 'No description available.' }}
                    </p>
                </article>
            </section>

            <section
                v-if="searched && canPaginate"
                class="flex flex-col gap-3 rounded-[1.5rem] border border-[#c7d9e8] bg-white p-4 shadow-[0_8px_24px_rgba(18,52,86,0.08)] sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="text-sm text-[#35597a]">
                    Page {{ currentPage }} of {{ pageCount }}
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="rounded-xl border border-[#c7d9e8] px-4 py-2 text-sm font-semibold text-[#0e416b] transition hover:bg-[#f2f8fd] disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="loading || currentPage <= 1"
                        @click="previousPage"
                    >
                        Previous
                    </button>
                    <button
                        type="button"
                        class="rounded-xl border border-[#c7d9e8] px-4 py-2 text-sm font-semibold text-[#0e416b] transition hover:bg-[#f2f8fd] disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="loading || currentPage >= pageCount"
                        @click="nextPage"
                    >
                        Next
                    </button>
                </div>
            </section>

            <section
                v-else-if="searched && !loading && results.length === 0"
                class="rounded-[1.5rem] border border-dashed border-[#c6d7e7] bg-white p-8 text-center text-sm text-[#5a7897] shadow-[0_8px_24px_rgba(18,52,86,0.05)]"
            >
                No matching results found for this search.
            </section>

            <section v-if="relatedSearches.length" class="rounded-[1.5rem] border border-[#c7d9e8] bg-white p-5 shadow-[0_8px_24px_rgba(18,52,86,0.08)] sm:p-6">
                <h3 class="text-sm font-semibold uppercase tracking-[0.14em] text-[#4f7ba6]">Related Searches</h3>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a
                        v-for="(item, index) in relatedSearches"
                        :key="`${item.url}-${index}`"
                        :href="item.url ?? '#'
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rounded-full border border-[#c7d9e8] bg-[#f2f8fd] px-3 py-1.5 text-sm text-[#0e416b] transition hover:bg-[#e6f1fa]"
                    >
                        {{ item.title || 'Related query' }}
                    </a>
                </div>
            </section>
        </div>
    </div>
</template>

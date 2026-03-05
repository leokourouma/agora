<script>
    import PostCard from '$lib/components/PostCard.svelte';
    import { Settings, Grid, Flame, Clock, Star } from 'lucide-svelte';
    
    /** @type {import('./$types').PageData} */
    export let data;

    let currentFilter = 'recent';
    let posts = data.posts;

    // Fonction pour changer de filtre sans recharger toute la page
    async function changeFilter(filter) {
        currentFilter = filter;
        const res = await fetch(`http://localhost:8888/api.php?sort=${filter}`);
        posts = await res.json();
    }
</script>

<div class="max-w-2xl mx-auto pt-8">
    <div class="flex items-center gap-8 px-4 mb-10">
        <div class="w-24 h-24 rounded-full bg-indigo-600 p-1">
            <div class="w-full h-full rounded-full bg-gray-200 border-4 border-white flex items-center justify-center font-bold text-xl text-indigo-600">
                LK
            </div>
        </div>
        <div class="flex-1">
            <h2 class="text-xl font-bold mb-2">leokd</h2>
            <p class="text-sm text-gray-600">Bâtisseur de l'Agora • {posts.length} publications</p>
        </div>
    </div>

    <div class="flex justify-around border-t border-b border-gray-200 py-2 mb-6 bg-white sticky top-0 z-10">
        <button on:click={() => changeFilter('recent')} class="flex items-center gap-1 text-xs font-bold uppercase {currentFilter === 'recent' ? 'text-indigo-600' : 'text-gray-400'}">
            <Clock size={16} /> Récent
        </button>
        <button on:click={() => changeFilter('top')} class="flex items-center gap-1 text-xs font-bold uppercase {currentFilter === 'top' ? 'text-indigo-600' : 'text-gray-400'}">
            <Star size={16} /> Top
        </button>
        <button on:click={() => changeFilter('controversial')} class="flex items-center gap-1 text-xs font-bold uppercase {currentFilter === 'controversial' ? 'text-indigo-600' : 'text-gray-400'}">
            <Flame size={16} /> Controversé
        </button>
    </div>

    <div class="space-y-4 px-2">
        {#each posts as post}
            <PostCard 
                user={post.username} 
                content={post.content} 
                upvotes={post.upvotes - post.downvotes}
            />
        {:else}
            <p class="text-center text-gray-500 py-10">Aucun post trouvé. Lance le seed !</p>
        {/each}
    </div>
</div>
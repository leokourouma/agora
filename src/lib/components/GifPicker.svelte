<script>
  import { slide } from 'svelte/transition';
  import { Search, Loader2 } from 'lucide-svelte';
  
  let { onSelect } = $props();
  let query = $state("");
  let gifs = $state([]);
  let loading = $state(false);

  // Utilisation de Tenor (plus stable que Giphy Beta)
   async function fetchGifs() {
      if (query.length < 2) return;
      loading = true;
      
      try {
        // Clé de démo publique officielle de Tenor (Google)
        const API_KEY = "LIVDSRZULELA"; 
        const res = await fetch(`https://tenor.googleapis.com/v2/search?q=${encodeURIComponent(query)}&key=${API_KEY}&limit=15`);
        const json = await res.json();
        
        // On transforme le format Tenor pour qu'il s'affiche dans ta grille
        if (json.results) {
          gifs = json.results.map(g => ({
            id: g.id,
            preview: g.media_formats.tinygif.url,
            full: g.media_formats.gif.url
          }));
        }
      } catch (e) {
        console.error("Erreur de recherche GIF:", e);
      } finally {
        loading = false;
      }
    }
</script>

<div transition:slide class="bg-gray-50 border-t p-4">
  <div class="relative mb-3">
    <Search class="absolute left-3 top-2.5 text-gray-400" size={18} />
    <input 
      type="text" 
      bind:value={query} 
      oninput={fetchGifs}
      placeholder="Rechercher un GIF..." 
      class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
    />
  </div>

  <div class="grid grid-cols-3 gap-2 max-h-60 overflow-y-auto pr-1">
    {#if loading}
      <div class="col-span-3 py-10 flex justify-center"><Loader2 class="animate-spin text-indigo-500" /></div>
    {:else}
      {#each gifs as gif (gif.id)}
        <button 
          onclick={() => onSelect(gif.full)}
          class="hover:opacity-80 transition-opacity aspect-square"
        >
          <img src={gif.preview} alt="gif" class="w-full h-full object-cover rounded-lg" />
        </button>
      {/each}
    {/if}
  </div>
</div>
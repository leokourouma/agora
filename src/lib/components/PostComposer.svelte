<script>
  import { Image, Gift, Smile, Send } from 'lucide-svelte';
  let { onPublish } = $props();
  let content = $state("");
  let media_url = $state("");

  function handleSubmit() {
    if (content.trim() || media_url.trim()) {
      onPublish(content, media_url);
      content = "";
      media_url = "";
    }
  }
</script>
<div class="custom-composer border rounded-[2.5rem] p-6 mb-8 shadow-sm transition-colors duration-500">
  <div class="flex gap-4">
    <div class="w-12 h-12 rounded-full bg-indigo-600 flex-shrink-0 flex items-center justify-center text-white font-bold">
      A
    </div>
    
    <div class="flex-1">
      <textarea 
        bind:value={content}
        placeholder="Quoi de neuf ?" 
        class="w-full bg-transparent border-none text-lg outline-none resize-none min-h-[100px]"
      ></textarea>

      {#if media_url}
        <div class="mt-2">
          <input 
            bind:value={media_url} 
            class="w-full p-2 text-xs bg-gray-50/50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/10 outline-none"
            placeholder="URL de l'image ou du GIF..."
          />
        </div>
      {/if}

      <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
        <div class="flex gap-2 text-gray-400">
          <button onclick={() => media_url = " "} class="p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-full transition-colors">
            <Image size={20} />
          </button>
          <button class="p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-full transition-colors">
            <Gift size={20} />
          </button>
          <button class="p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-full transition-colors">
            <Smile size={20} />
          </button>
        </div>

        <button 
          onclick={handleSubmit}
          disabled={!content.trim() && !media_url.trim()}
          class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2 rounded-full font-bold text-sm flex items-center gap-2 transition-all"
        >
          <span>Publier</span>
          <Send size={16} />
        </button>
      </div>
    </div>
  </div>
</div>
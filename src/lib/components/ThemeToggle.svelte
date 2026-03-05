<script>
    import { Moon, Sun } from 'lucide-svelte';
    import { theme } from '$lib/theme.svelte.js';
</script>

<button 
    onclick={() => theme.toggle()}
    class="fixed top-4 right-4 z-[100] p-3 rounded-2xl shadow-xl transition-all active:scale-90
           bg-white dark:bg-neutral-800 border border-gray-200 dark:border-white/10"
>
    {#if theme.isDark}
        <Sun size={20} class="text-amber-500" />
    {:else}
        <Moon size={20} class="text-indigo-600" />
    {/if}
</button>
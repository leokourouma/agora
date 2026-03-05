<script>
  import { slide } from 'svelte/transition';
  let { onSelect } = $props();

  const groups = [
    { name: "Smiley", icons: ["😀", "😂", "🫠", "😎", "🤔", "🤫", "🫡", "😭"] },
    { name: "Agora", icons: ["🏛️", "⚖️", "📢", "🗳️", "🌍", "🤝", "💡", "🛡️"] },
    { name: "Action", icons: ["🔥", "✨", "🚀", "💯", "✅", "❌", "⚠️", "💎"] }
  ];
</script>

<div transition:slide class="bg-white border-t p-3 max-h-48 overflow-y-auto shadow-inner">
  {#each groups as group}
    <div class="mb-3">
      <p class="text-[9px] font-bold text-gray-400 uppercase mb-2 tracking-widest">{group.name}</p>
      <div class="grid grid-cols-8 gap-1">
        {#each group.icons as icon}
          <button 
            onclick={() => onSelect(icon)}
            class="text-xl hover:bg-gray-100 rounded-md p-1 transition-transform active:scale-90"
          >
            {icon}
          </button>
        {/each}
      </div>
    </div>
  {/each}
</div>
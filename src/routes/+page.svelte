<script>
  import { onMount } from 'svelte';
  import { fly, fade } from 'svelte/transition';
  import PostComposer from '$lib/components/PostComposer.svelte';
  import PostCard from '$lib/components/PostCard.svelte';

  let posts = $state([]);
  let currentSort = $state('recent');

  async function loadFeed(sort = 'recent') {
    currentSort = sort;
    const res = await fetch(`http://localhost:8888/api.php?sort=${sort}`);
    posts = await res.json();
  }

  async function handleNewPost(content, media_url) {
    const res = await fetch('http://localhost:8888/manage_posts.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ content, media_url })
    });
    if (res.ok) await loadFeed(currentSort);
  }

  onMount(() => loadFeed('recent'));
</script>

<div class="max-w-2xl mx-auto py-10 px-4">
  <PostComposer onPublish={handleNewPost} />

  <div class="sticky top-4 z-10 flex justify-center mb-10">
    <nav class="flex p-1 bg-gray-200/30 backdrop-blur-md rounded-2xl border border-white/40 shadow-sm ring-1 ring-black/5">
      {#each ['recent', 'top', 'controversial'] as type}
        <button 
          onclick={() => loadFeed(type)}
          class="relative px-6 py-2 text-[11px] font-bold uppercase tracking-widest transition-all duration-300 rounded-xl
          {currentSort === type ? 'text-indigo-600 shadow-inner bg-white/50' : 'text-gray-500 hover:text-gray-800'}"
        >
          {type === 'recent' ? 'Récent' : type === 'top' ? 'Populaire' : 'Débats'}
          
          {#if currentSort === type}
            <div 
              layoutId="activeTab"
              class="absolute inset-0 bg-white/40 rounded-xl -z-10 shadow-[inset_0_1px_2px_rgba(255,255,255,0.8),_0_1px_2px_rgba(0,0,0,0.05)]"
              in:fade={{ duration: 200 }}
            ></div>
          {/if}
        </button>
      {/each}
    </nav>
  </div>

  <div class="space-y-6">
    {#each posts as post (post.id)}
      <div in:fly={{ y: 20, duration: 400 }}>
        <PostCard 
            id={post.id} 
            user={post.username} 
            content={post.content} 
            media={post.media_url}
            upvotes={post.score}
            comment_count={post.comment_count} 
        />
      </div>
    {/each}
  </div>
</div>
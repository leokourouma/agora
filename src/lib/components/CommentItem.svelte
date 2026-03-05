<script>
  import { slide } from 'svelte/transition';
  import { MessageSquare } from 'lucide-svelte';
  import VoteControl from './VoteControl.svelte';

  let { comment, allComments, postId, onRefresh } = $props();
  let showReply = $state(false);
  let replyContent = $state("");

  // On trouve les enfants de ce commentaire
  const replies = $derived(allComments.filter(c => c.parent_id === comment.id));

  async function submitReply() {
    if (!replyContent.trim()) return;
    const res = await fetch('http://localhost:8888/manage_comments.php', {
      method: 'POST',
      body: JSON.stringify({ post_id: postId, parent_id: comment.id, content: replyContent })
    });
    if (res.ok) {
      replyContent = "";
      showReply = false;
      onRefresh(); // On demande au parent de recharger la liste
    }
  }
</script>

<div class="flex gap-2 mt-3">
  <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex-shrink-0 flex items-center justify-center text-[8px] font-bold text-indigo-600 dark:text-indigo-400">
    {comment.username.charAt(0).toUpperCase()}
  </div>

  <div class="flex-1">
    <div class="bg-white dark:bg-neutral-800 p-3 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5">
      <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mb-1">u/{comment.username}</p>
      <p class="text-xs text-gray-700 dark:text-gray-300">{comment.content}</p>
    </div>

    <div class="flex items-center gap-3 mt-1 px-1">
      <VoteControl commentId={comment.id} upvotes={comment.score} />
      
      <button 
        onclick={() => showReply = !showReply}
        class="flex items-center gap-1 text-[9px] font-bold text-gray-400 hover:text-indigo-600 uppercase tracking-tighter transition-colors"
      >
        <MessageSquare size={12} /> Répondre
      </button>
    </div>

    {#if showReply}
      <div class="mt-2 flex gap-2" transition:slide>
        <input 
            bind:value={replyContent} 
            class="flex-1 bg-gray-100 dark:bg-neutral-900 border-none rounded-lg px-3 py-1 text-xs text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-indigo-200 dark:focus:ring-indigo-900" 
            placeholder="Répondre..." 
        />
        <button onclick={submitReply} class="bg-indigo-600 text-white px-2 py-1 rounded-lg text-[10px] font-bold">OK</button>
      </div>
    {/if}

    {#if replies.length > 0}
      <div class="border-l-2 border-gray-100 dark:border-neutral-800 ml-1 pl-2">
        {#each replies as reply (reply.id)}
          <svelte:self comment={reply} {allComments} {postId} {onRefresh} />
        {/each}
      </div>
    {/if}
  </div>
</div>
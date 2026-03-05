<script>
  import VoteControl from './VoteControl.svelte'; // On réutilise le module de vote !
  import { MessageSquare } from 'lucide-svelte';
  
  let { comment, allComments, postId } = $props();
  let showReply = $state(false);
  let replyContent = $state("");

  // On filtre les réponses à ce commentaire
  const replies = $derived(allComments.filter(c => c.parent_id === comment.id));

  async function submitReply() {
    await fetch('http://localhost:8888/manage_comments.php', {
      method: 'POST',
      body: JSON.stringify({ 
        post_id: postId, 
        parent_id: comment.id, 
        content: replyContent 
      })
    });
    replyContent = "";
    showReply = false;
    // Ici, il faudrait idéalement un refresh via un store ou un dispatch
  }
</script>

<div class="ml-4 border-l-2 border-gray-100 pl-4 mt-4">
  <div class="flex items-center gap-2 mb-1">
    <span class="font-bold text-xs text-indigo-600">u/{comment.username}</span>
    <span class="text-[10px] text-gray-400">il y a un instant</span>
  </div>
  
  <p class="text-sm text-gray-700 mb-2">{comment.content}</p>

  <div class="flex items-center gap-4">
    <button onclick={() => showReply = !showReply} class="text-[10px] font-bold text-gray-400 hover:text-indigo-500 uppercase">
      Répondre
    </button>
  </div>

  {#if showReply}
    <div class="mt-2 flex gap-2">
      <input bind:value={replyContent} class="flex-1 bg-gray-50 border-none rounded-lg text-sm" placeholder="Ta réponse..." />
      <button onclick={submitReply} class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-xs font-bold">OK</button>
    </div>
  {/if}

  {#each replies as reply}
    <svelte:self comment={reply} {allComments} {postId} />
  {/each}
</div>
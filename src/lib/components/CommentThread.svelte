<script>
  import { onMount } from 'svelte';
  import { slide } from 'svelte/transition';
  import { Send } from 'lucide-svelte';
  import CommentItem from './CommentItem.svelte'; // L'élément récursif

  let { postId } = $props();
  let comments = $state([]);
  let newComment = $state("");
  let isLoading = $state(true);

  async function loadComments() {
    const res = await fetch(`http://localhost:8888/manage_comments.php?post_id=${postId}`);
    comments = await res.json();
    isLoading = false;
  }

  async function postComment() {
    if (!newComment.trim()) return;
    const res = await fetch('http://localhost:8888/manage_comments.php', {
      method: 'POST',
      body: JSON.stringify({ post_id: postId, content: newComment, parent_id: null })
    });
    if (res.ok) {
      newComment = "";
      await loadComments();
    }
  }

  onMount(loadComments);
</script>

<div class="p-4 bg-gray-50/50 dark:bg-black/20 space-y-4 border-t dark:border-white/5" transition:slide>
  <div class="flex gap-2">
    <input 
        bind:value={newComment} 
        placeholder="Un avis ?" 
        class="flex-1 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-900/30 outline-none" 
    />
    <button onclick={postComment} class="p-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
      <Send size={18} />
    </button>
  </div>

  {#if isLoading}
    <div class="text-center py-2 text-[10px] text-gray-400 uppercase tracking-widest animate-pulse">Chargement...</div>
  {:else}
    <div class="space-y-4">
      {#each comments.filter(c => !c.parent_id) as comment (comment.id)}
        <CommentItem {comment} allComments={comments} {postId} onRefresh={loadComments} />
      {/each}
    </div>
  {/if}
</div>
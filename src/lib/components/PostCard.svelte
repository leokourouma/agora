<script>
	import { MessageSquare, Share2 } from 'lucide-svelte';
	import { slide } from 'svelte/transition';
	import VoteControl from './VoteControl.svelte';
	import CommentThread from './CommentThread.svelte';
	
	let { id, user = "Anonyme", content = "", media = null, upvotes = 0, comment_count = 0 } = $props();
	let showComments = $state(false);

	const isExternal = media?.startsWith('http');
	const imageUrl = media ? (isExternal ? media : `http://localhost:8888${media}`) : null;
</script>

<div class="post-card-bg border rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-md transition-all mb-6">
	<div class="p-5">
		<div class="flex items-center gap-3 mb-4">
			<div class="w-10 h-10 rounded-full bg-black dark:bg-white text-white dark:text-black flex items-center justify-center font-bold text-xs uppercase tracking-tighter">
				{user.slice(0, 2)}
			</div>
			<div>
				<p class="text-sm font-bold">u/{user}</p>
				<p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Agora • Live</p>
			</div>
		</div>
		<p class="leading-relaxed font-medium px-1 mb-4">{content}</p>
		
		{#if media}
			<img src={imageUrl} alt="Post" class="w-full rounded-[1.8rem] border border-gray-100 dark:border-white/5 mb-4 object-cover max-h-[450px]" />
		{/if}
	</div>

	<div class="px-5 py-4 flex items-center justify-between bg-gray-50/30 dark:bg-white/5 border-t border-gray-100 dark:border-white/5">
		<div class="flex items-center gap-3">
			<VoteControl postId={id} upvotes={upvotes} />

			<button 
				onclick={() => showComments = !showComments}
				class="flex items-center gap-2 px-4 py-2 rounded-full transition-all text-[11px] font-black uppercase tracking-tighter
				{showComments ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100/50 dark:bg-white/10 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/20'}"
			>
				<MessageSquare size={16} strokeWidth={2.5} /> 
				<span>{comment_count}</span>
			</button>
		</div>

		<button class="p-2.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 rounded-full transition-all">
			<Share2 size={18} />
		</button>
	</div>

	{#if showComments}
		<div transition:slide>
			<CommentThread postId={id} />
		</div>
	{/if}
</div>
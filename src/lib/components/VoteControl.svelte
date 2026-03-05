<script>
  import { ArrowBigUp, ArrowBigDown } from 'lucide-svelte';
  let { postId = null, commentId = null, upvotes = 0 } = $props();
  let currentVotes = $state(upvotes);
  let userVote = $state(0);

  async function handleVote(type) {
    let voteToSend = (userVote === type) ? 0 : type;
    if (userVote === type) { currentVotes -= type; userVote = 0; }
    else { currentVotes += (type - userVote); userVote = type; }

    await fetch('http://localhost:8888/vote.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ post_id: postId, comment_id: commentId, vote_type: voteToSend })
    });
  }
</script>

<div class="flex items-center bg-gray-100/50 backdrop-blur-sm rounded-full p-0.5 border border-white/50 shadow-sm">
  <button 
    onclick={() => handleVote(1)}
    class="p-1.5 rounded-full transition-all {userVote === 1 ? 'text-orange-500 bg-white shadow-sm' : 'text-gray-400 hover:text-gray-600'}"
  >
    <ArrowBigUp size={20} fill={userVote === 1 ? 'currentColor' : 'none'} strokeWidth={2.5} />
  </button>
  
  <span class="text-[11px] font-black px-1.5 {userVote !== 0 ? 'text-gray-900' : 'text-gray-400'}">
    {currentVotes}
  </span>
  
  <button 
    onclick={() => handleVote(-1)}
    class="p-1.5 rounded-full transition-all {userVote === -1 ? 'text-indigo-500 bg-white shadow-sm' : 'text-gray-400 hover:text-gray-600'}"
  >
    <ArrowBigDown size={20} fill={userVote === -1 ? 'currentColor' : 'none'} strokeWidth={2.5} />
  </button>
</div>
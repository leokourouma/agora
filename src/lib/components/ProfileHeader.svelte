<script>
    import { X, Camera, Image as ImageIcon, Check } from 'lucide-svelte';

    // Rune Svelte 5 : $bindable pour que la page parente voit les changements
    let { user = $bindable() } = $props();
    let showModal = $state(false);
    let isSaving = $state(false);

    // Règle Agora : URLs locales concaténées au port 8888
    const getImgUrl = (url) => {
        if (!url) return '';
        return url.startsWith('http') ? url : `http://localhost:8888${url}`;
    };

    // Fonction d'upload (Avatar ou Bannière)
    async function handleUpload(event, type) {
        const file = event.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file); // 'file' correspond à ton script upload.php

        try {
            const res = await fetch('http://localhost:8888/upload.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                // Mise à jour de l'état réactif
                if (type === 'avatar') user.avatar_url = data.url;
                if (type === 'banner') user.banner_url = data.url;
            }
        } catch (err) {
            console.error("Erreur Agora Upload:", err);
        }
    }

    async function saveProfile() {
        isSaving = true;
        try {
            const res = await fetch('http://localhost:8888/profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(user)
            });
            if (res.ok) showModal = false;
        } catch (err) {
            console.error("Erreur Agora Save:", err);
        } finally {
            isSaving = false;
        }
    }
</script>

<div class="profile-header border-b border-gray-200 dark:border-zinc-800 mb-6 post-card-bg overflow-hidden rounded-b-3xl shadow-sm">
    <div class="h-44 w-full bg-indigo-900 overflow-hidden relative">
        {#if user.banner_url}
            <img src={getImgUrl(user.banner_url)} alt="" class="w-full h-full object-cover" />
        {/if}
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
    </div>

    <div class="px-6 -mt-12 flex items-end justify-between gap-4 mb-6 relative z-10">
        <div class="flex items-end gap-5">
            <div class="w-28 h-28 rounded-3xl border-4 border-white dark:border-zinc-900 bg-zinc-100 overflow-hidden shadow-xl post-card-bg">
                {#if user.avatar_url}
                    <img src={getImgUrl(user.avatar_url)} alt="" class="w-full h-full object-cover" />
                {:else}
                    <div class="w-full h-full flex items-center justify-center font-black text-3xl text-indigo-600 uppercase">
                        {user.username?.substring(0, 2)}
                    </div>
                {/if}
            </div>
            
            <div class="pb-2">
                <h1 class="text-2xl font-black dark:text-white tracking-tighter">
                    {user.first_name || ''} {user.last_name || user.username}
                </h1>
                <p class="text-sm font-bold text-indigo-500">@{user.username}</p>
            </div>
        </div>

        <button 
            onclick={() => showModal = true} 
            class="mb-2 bg-white dark:bg-zinc-800 text-xs font-black uppercase tracking-widest border border-gray-200 dark:border-zinc-700 px-6 py-3 rounded-2xl shadow-sm hover:scale-105 transition-all active:scale-95 dark:text-white"
        >
            Éditer le profil
        </button>
    </div>
</div>

{#if showModal}
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-950/80 backdrop-blur-md p-4">
    <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-2xl overflow-hidden post-card-bg border border-zinc-200 dark:border-zinc-800">
        
        <div class="flex items-center justify-between p-6 border-b border-zinc-100 dark:border-zinc-800">
            <h2 class="font-black text-xl uppercase tracking-tighter">Paramètres Profil</h2>
            <button onclick={() => showModal = false} class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full transition-colors">
                <X size={24} />
            </button>
        </div>

        <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto">
            
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest ml-1">Couverture</label>
                <div class="relative h-32 w-full rounded-3xl bg-zinc-100 dark:bg-zinc-800 overflow-hidden group border-2 border-dashed border-zinc-200 dark:border-zinc-700">
                    <img src={getImgUrl(user.banner_url)} class="w-full h-full object-cover opacity-50 group-hover:opacity-30 transition-opacity" alt="" />
                    <label class="absolute inset-0 flex flex-col items-center justify-center cursor-pointer">
                        <ImageIcon class="text-zinc-500 mb-1" size={28} />
                        <span class="text-[10px] font-black uppercase text-zinc-500">Changer la bannière</span>
                        <input type="file" class="hidden" accept="image/*" onchange={(e) => handleUpload(e, 'banner')} />
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="relative w-24 h-24 rounded-[2rem] bg-zinc-100 dark:bg-zinc-800 overflow-hidden group border-4 border-white dark:border-zinc-950 shadow-lg">
                    <img src={getImgUrl(user.avatar_url)} class="w-full h-full object-cover opacity-60 group-hover:opacity-40 transition-opacity" alt="" />
                    <label class="absolute inset-0 flex items-center justify-center cursor-pointer">
                        <Camera class="text-zinc-600" size={24} />
                        <input type="file" class="hidden" accept="image/*" onchange={(e) => handleUpload(e, 'avatar')} />
                    </label>
                </div>
                <div>
                    <h3 class="font-bold dark:text-white">Photo de profil</h3>
                    <p class="text-xs text-zinc-500 font-medium">Format WebP optimisé automatiquement.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest ml-1">Prénom</label>
                    <input bind:value={user.first_name} class="w-full p-4 rounded-2xl border-none bg-zinc-100 dark:bg-zinc-950 dark:text-white focus:ring-2 ring-indigo-500/20 outline-none" />
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest ml-1">Nom</label>
                    <input bind:value={user.last_name} class="w-full p-4 rounded-2xl border-none bg-zinc-100 dark:bg-zinc-950 dark:text-white focus:ring-2 ring-indigo-500/20 outline-none" />
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest ml-1">Bio souveraine</label>
                <textarea bind:value={user.bio} class="w-full p-4 rounded-2xl border-none bg-zinc-100 dark:bg-zinc-950 dark:text-white h-28 resize-none focus:ring-2 ring-indigo-500/20 outline-none"></textarea>
            </div>
        </div>

        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-4 bg-zinc-50/50 dark:bg-zinc-950/50">
            <button onclick={() => showModal = false} class="px-6 py-3 text-sm font-black uppercase text-zinc-400 hover:text-zinc-600 transition-colors">Annuler</button>
            <button 
                onclick={saveProfile} 
                disabled={isSaving}
                class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg shadow-indigo-500/30 flex items-center gap-2 hover:scale-105 active:scale-95 transition-all disabled:opacity-50"
            >
                {#if isSaving} Enregistrement... {:else} <Check size={18} /> Enregistrer {/if}
            </button>
        </div>
    </div>
</div>
{/if}

<style>
    /* Mode Sombre Binaire - Haute Priorité */
    :global(html:not(.dark)) .post-card-bg {
        background-color: #ffffff !important;
        color: #171717 !important;
    }

    :global(html.dark) .post-card-bg {
        background-color: #171717 !important;
        color: #ffffff !important;
    }
</style>
<script>
    import ProfileHeader from '$lib/components/ProfileHeader.svelte';
    import { UserPlus, LogIn, ShieldCheck, LogOut } from 'lucide-svelte';
    import { onMount } from 'svelte';

    // ÉTATS SVELTE 5
    let user = $state(null);      // Contiendra les infos du profil si connecté
    let loading = $state(true);   // Pour l'écran de chargement
    let isLogin = $state(false);  // Par défaut, on affiche l'inscription
    
    let email = $state('');
    let password = $state('');
    let error = $state('');

    // 1. AU CHARGEMENT : On regarde si on est déjà connecté
    onMount(async () => {
        const token = localStorage.getItem('agora_token');
        if (token) {
            await fetchProfile(token);
        } else {
            loading = false;
        }
    });

    // 2. RÉCUPÉRER LE PROFIL (via l'ID stocké)
    async function fetchProfile(id) {
        try {
            const res = await fetch(`http://localhost:8888/profile.php?id=${id}`);
            if (!res.ok) throw new Error();
            user = await res.json();
        } catch (e) {
            // Si l'ID est vieux ou invalide, on déconnecte
            logout();
        } finally {
            loading = false;
        }
    }

    // 3. ACTIONS (Inscription ou Connexion)
    async function handleAuth() {
        error = '';
        const endpoint = isLogin ? 'login.php' : 'register.php';
        
        try {
            const res = await fetch(`http://localhost:8888/${endpoint}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();
            
            if (res.ok && data.token) {
                // VICTOIRE : On enregistre l'ID dans la mémoire du navigateur
                localStorage.setItem('agora_token', data.token);
                // On charge le profil immédiatement
                await fetchProfile(data.token);
            } else {
                error = data.error || "Erreur d'authentification";
            }
        } catch (e) {
            error = "Serveur PHP injoignable (port 8888)";
        }
    }

    function logout() {
        localStorage.removeItem('agora_token');
        user = null;
        loading = false;
    }
</script>

<div class="max-w-2xl mx-auto pt-8 px-4">
    {#if loading}
        <div class="text-center py-20 animate-pulse text-gray-500 font-bold uppercase tracking-widest">Initialisation...</div>
    
    {:else if user}
        <ProfileHeader bind:user={user} />
        
        <div class="mt-8 flex justify-center">
            <button onclick={logout} class="flex items-center gap-2 text-xs font-bold text-red-500 border border-red-200 px-6 py-2 rounded-full hover:bg-red-50 transition-all">
                <LogOut size={14} /> SE DÉCONNECTER
            </button>
        </div>

    {:else}
        <div class="max-w-md mx-auto post-card-bg p-8 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-2xl">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 rotate-3 shadow-lg shadow-indigo-500/30">
                    <ShieldCheck class="text-white" size={32} />
                </div>
                <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter">
                    {isLogin ? 'Connexion' : 'Inscription'}
                </h1>
                <p class="text-gray-400 text-[10px] mt-2 uppercase tracking-[0.2em]">Agora v1.0</p>
            </div>

            <form onsubmit={(e) => { e.preventDefault(); handleAuth(); }} class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-gray-400 ml-1">Email</label>
                    <input bind:value={email} type="email" required class="w-full p-3 rounded-xl border dark:bg-zinc-950 dark:border-zinc-800 dark:text-white outline-none focus:ring-2 ring-indigo-500/20" />
                </div>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-gray-400 ml-1">Mot de passe</label>
                    <input bind:value={password} type="password" required class="w-full p-3 rounded-xl border dark:bg-zinc-950 dark:border-zinc-800 dark:text-white outline-none focus:ring-2 ring-indigo-500/20" />
                </div>

                {#if error}
                    <p class="text-red-500 text-xs font-bold text-center bg-red-50 dark:bg-red-900/20 py-3 rounded-xl border border-red-100 dark:border-red-900/50">{error}</p>
                {/if}
                
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                    {#if isLogin}<LogIn size={20} /> Se connecter{:else}<UserPlus size={20} /> Créer mon compte{/if}
                </button>

                <button 
                    type="button"
                    onclick={() => { isLogin = !isLogin; error = ''; }} 
                    class="w-full text-center text-xs text-gray-400 font-bold uppercase tracking-widest hover:text-indigo-600 transition-colors mt-6"
                >
                    {isLogin ? "Pas de compte ? S'inscrire" : "Déjà membre ? Se connecter"}
                </button>
            </form>
        </div>
    {/if}
</div>

<style>
    :global(html:not(.dark)) .post-card-bg { background-color: #ffffff !important; }
    :global(html.dark) .post-card-bg { background-color: #171717 !important; }
</style>
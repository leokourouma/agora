<script>
	import './layout.css';
	import "../app.css";
	import favicon from '$lib/assets/favicon.svg';
	import { theme } from '$lib/theme.svelte.js';
	import ThemeToggle from '$lib/components/ThemeToggle.svelte';
	import { onMount } from 'svelte';

	let { children } = $props();

	onMount(() => {
		theme.init();
	});
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
	<title>Agora</title>
</svelte:head>

<ThemeToggle />

<main class="max-w-2xl mx-auto">
	{@render children()}
</main>
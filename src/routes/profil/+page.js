/** @type {import('./$types').PageLoad} */
export async function load({ fetch }) {
    try {
        const response = await fetch('http://localhost:8888/api.php?sort=recent');
        if (!response.ok) throw new Error('Erreur API');
        
        const posts = await response.json();
        
        return {
            posts: posts
        };
    } catch (err) {
        return {
            posts: [],
            error: err.message
        };
    }
}
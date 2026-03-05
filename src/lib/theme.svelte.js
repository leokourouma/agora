let isDark = $state(false);

export const theme = {
    get isDark() { return isDark; },
    
    toggle() {
        isDark = !isDark;
        // Cette ligne est la seule qui compte : elle ajoute ou RETIRE la classe dark
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    },

    init() {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('theme');
            // On vérifie si c'était dark, sinon c'est light par défaut
            isDark = saved === 'dark';
            document.documentElement.classList.toggle('dark', isDark);
        }
    }
};
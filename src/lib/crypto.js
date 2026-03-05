export async function generateAgoraIdentity() {
    const keys = await window.crypto.subtle.generateKey(
        {
            name: "RSA-OAEP",
            modulusLength: 2048,
            publicExponent: new Uint8Array([1, 0, 1]),
            hash: "SHA-256",
        },
        true, 
        ["encrypt", "decrypt"]
    );

    // Export pour le serveur
    const publicKey = await window.crypto.subtle.exportKey("jwk", keys.publicKey);
    
    // On sauvegarde la clé privée dans IndexedDB (plus sûr que LocalStorage)
    // Pour l'instant, disons qu'on la garde en mémoire
    return { keys, publicKey };
}
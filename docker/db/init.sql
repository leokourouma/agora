-- 1. Table des comptes (Privé)
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 2. Table des Profils (Public)
CREATE TABLE IF NOT EXISTS profiles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    username TEXT UNIQUE NOT NULL,
    first_name TEXT,
    last_name TEXT,
    bio TEXT,
    avatar_url TEXT,
    banner_url TEXT,
    birth_date DATE,
    visibility_settings JSONB DEFAULT '{"show_name": true, "show_age": false}',
    created_at TIMESTAMP DEFAULT NOW()
);

-- 3. Table des Albums
CREATE TABLE IF NOT EXISTS albums (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    profile_id UUID REFERENCES profiles(id) ON DELETE CASCADE,
    name TEXT NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT NOW()
);

-- 4. TRIGGER : Automatisation à l'inscription
CREATE OR REPLACE FUNCTION initialize_new_user() RETURNS TRIGGER AS $$
DECLARE
    new_profile_id UUID;
BEGIN
    -- Crée le profil lié à l'USER
    INSERT INTO profiles (user_id, username)
    VALUES (NEW.id, 'user_' || substr(NEW.id::text, 1, 8))
    RETURNING id INTO new_profile_id;

    -- Crée l'album par défaut lié au PROFIL
    INSERT INTO albums (profile_id, name, is_default)
    VALUES (new_profile_id, 'Général', TRUE);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_init_user AFTER INSERT ON users FOR EACH ROW EXECUTE FUNCTION initialize_new_user();
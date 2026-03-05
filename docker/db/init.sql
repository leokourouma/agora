CREATE EXTENSION IF NOT EXISTS "ltree";

-- Table des profils (Le "Mur")
CREATE TABLE profiles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    username TEXT UNIQUE NOT NULL,
    bio TEXT,
    avatar_url TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);

-- Table des Posts & Stories
CREATE TABLE posts (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    author_id UUID REFERENCES profiles(id),
    content TEXT,
    is_story BOOLEAN DEFAULT FALSE,
    upvotes INTEGER DEFAULT 0,
    downvotes INTEGER DEFAULT 0,
    views INTEGER DEFAULT 0,
    controversy_score FLOAT DEFAULT 0,
    created_at TIMESTAMP DEFAULT NOW()
);

-- Table des Commentaires avec LTREE
CREATE TABLE comments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    post_id UUID REFERENCES posts(id) ON DELETE CASCADE,
    author_id UUID REFERENCES profiles(id),
    content TEXT NOT NULL,
    path ltree, -- C'est ici que la magie de l'arborescence opère
    created_at TIMESTAMP DEFAULT NOW()
);

-- Trigger pour calculer la controverse automatiquement
CREATE OR REPLACE FUNCTION update_controversy() RETURNS TRIGGER AS $$
BEGIN
    NEW.controversy_score := (NEW.upvotes + NEW.downvotes) / (ABS(NEW.upvotes - NEW.downvotes) + 1.1);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_posts_controversy 
BEFORE UPDATE ON posts 
FOR EACH ROW EXECUTE FUNCTION update_controversy();
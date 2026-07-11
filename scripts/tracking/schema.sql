CREATE TABLE IF NOT EXISTS locations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER UNIQUE NOT NULL,
    title TEXT NOT NULL,
    name_en TEXT,
    name_es_meta TEXT,
    slug TEXT,
    source TEXT,
    loc_type TEXT,
    has_scriptures INTEGER DEFAULT 0,
    scriptures_json TEXT,
    has_content INTEGER DEFAULT 0,
    es_status TEXT DEFAULT 'pending',
    alejandria_match TEXT,
    alejandria_ref TEXT,
    alt_names TEXT,
    alias_of INTEGER DEFAULT 0,
    notes TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS translation_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    action TEXT NOT NULL,
    old_value TEXT,
    new_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_es_status ON locations(es_status);
CREATE INDEX IF NOT EXISTS idx_source ON locations(source);
CREATE INDEX IF NOT EXISTS idx_post_id ON locations(post_id);

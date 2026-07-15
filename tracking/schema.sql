PRAGMA journal_mode=WAL;
PRAGMA foreign_keys=ON;

CREATE TABLE IF NOT EXISTS batches (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','in_progress','completed','failed')),
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  completed_at TEXT,
  commit_hash TEXT
);

CREATE TABLE IF NOT EXISTS locations (
  wp_id INTEGER PRIMARY KEY,
  title TEXT NOT NULL,
  name_en TEXT,
  level TEXT CHECK(level IN ('A','B','C')),
  relevancia INTEGER CHECK(relevancia IN (1,2,3)),
  batch_id INTEGER,
  status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','processing','completed','error')),
  word_count INTEGER,
  error TEXT,
  rewritten TEXT DEFAULT 'no' CHECK(rewritten IN ('yes','no')),
  regeneration_reason TEXT,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at TEXT NOT NULL DEFAULT (datetime('now')),
  FOREIGN KEY (batch_id) REFERENCES batches(id)
);

CREATE TABLE IF NOT EXISTS activity_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  session_id TEXT NOT NULL,
  batch_id INTEGER,
  action TEXT NOT NULL,
  detail TEXT,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  FOREIGN KEY (batch_id) REFERENCES batches(id)
);

CREATE INDEX IF NOT EXISTS idx_locations_status ON locations(status);
CREATE INDEX IF NOT EXISTS idx_locations_batch ON locations(batch_id);
CREATE INDEX IF NOT EXISTS idx_batches_status ON batches(status);

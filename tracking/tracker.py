import sqlite3
import os
import json
from datetime import datetime

DB_PATH = os.path.join(os.path.dirname(__file__), "locations.db")


def get_conn():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA journal_mode=WAL")
    conn.execute("PRAGMA foreign_keys=ON")
    return conn


def init_db():
    schema = os.path.join(os.path.dirname(__file__), "schema.sql")
    conn = get_conn()
    with open(schema, "r") as f:
        conn.executescript(f.read())
    conn.commit()
    conn.close()


def create_batch():
    conn = get_conn()
    cur = conn.execute("INSERT INTO batches (status) VALUES ('in_progress')")
    batch_id = cur.lastrowid
    conn.commit()
    conn.close()
    return batch_id


def complete_batch(batch_id, commit_hash):
    conn = get_conn()
    conn.execute(
        "UPDATE batches SET status='completed', completed_at=datetime('now'), commit_hash=? WHERE id=?",
        (commit_hash, batch_id),
    )
    conn.commit()
    conn.close()


def fail_batch(batch_id, error=""):
    conn = get_conn()
    conn.execute(
        "UPDATE batches SET status='failed', completed_at=datetime('now') WHERE id=?",
        (batch_id,),
    )
    conn.commit()
    conn.close()


def add_locations(batch_id, locations):
    conn = get_conn()
    for loc in locations:
        conn.execute(
            "INSERT OR REPLACE INTO locations (wp_id, title, name_en, level, relevancia, rewritten, regeneration_reason, batch_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')",
            (
                loc["wp_id"],
                loc["title"],
                loc.get("name_en"),
                loc.get("level", "C"),
                loc.get("relevancia"),
                loc.get("rewritten", "no"),
                loc.get("regeneration_reason"),
                batch_id,
            ),
        )
    conn.commit()
    conn.close()


def mark_processing(locations_ids):
    conn = get_conn()
    for wid in locations_ids:
        conn.execute(
            "UPDATE locations SET status='processing', updated_at=datetime('now') WHERE wp_id=?",
            (wid,),
        )
    conn.commit()
    conn.close()


def mark_completed(wp_id, word_count=0):
    conn = get_conn()
    conn.execute(
        "UPDATE locations SET status='completed', rewritten='yes', word_count=?, updated_at=datetime('now') WHERE wp_id=?",
        (word_count, wp_id),
    )
    conn.commit()
    conn.close()


def mark_error(wp_id, error_msg):
    conn = get_conn()
    conn.execute(
        "UPDATE locations SET status='error', error=?, updated_at=datetime('now') WHERE wp_id=?",
        (error_msg, wp_id),
    )
    conn.commit()
    conn.close()


def set_relevancia(wp_id, relevancia):
    conn = get_conn()
    conn.execute(
        "UPDATE locations SET relevancia=?, updated_at=datetime('now') WHERE wp_id=?",
        (relevancia, wp_id),
    )
    conn.commit()
    conn.close()


def log(session_id, batch_id, action, detail=""):
    conn = get_conn()
    conn.execute(
        "INSERT INTO activity_log (session_id, batch_id, action, detail) VALUES (?, ?, ?, ?)",
        (session_id, batch_id, action, detail),
    )
    conn.commit()
    conn.close()


def get_last_batch():
    conn = get_conn()
    cur = conn.execute("SELECT * FROM batches ORDER BY id DESC LIMIT 1")
    row = cur.fetchone()
    conn.close()
    return dict(row) if row else None


def get_batch_locations(batch_id):
    conn = get_conn()
    cur = conn.execute("SELECT * FROM locations WHERE batch_id=?", (batch_id,))
    rows = cur.fetchall()
    conn.close()
    return [dict(r) for r in rows]


def get_pending_count():
    conn = get_conn()
    cur = conn.execute("SELECT COUNT(*) as c FROM locations WHERE status='pending'")
    row = cur.fetchone()
    conn.close()
    return row["c"]


def get_stats():
    conn = get_conn()
    cur = conn.execute("""
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status='error' THEN 1 ELSE 0 END) as errors,
            SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending
        FROM locations
    """)
    row = dict(cur.fetchone())
    cur2 = conn.execute(
        "SELECT COUNT(*) as batches FROM batches WHERE status='completed'"
    )
    row["batches"] = dict(cur2.fetchone())["batches"]
    conn.close()
    return row


def mark_for_regeneration(wp_id, reason="new_relevance"):
    conn = get_conn()
    conn.execute(
        "UPDATE locations SET status='pending', rewritten='yes', regeneration_reason=?, updated_at=datetime('now') WHERE wp_id=?",
        (reason, wp_id),
    )
    conn.commit()
    conn.close()


def get_regeneration_queue(batch_size=10, relevance_filter=None):
    conn = get_conn()
    query = "SELECT * FROM locations WHERE status='pending'"
    params = []
    if relevance_filter:
        query += " AND relevancia=?"
        params.append(relevance_filter)
    query += " ORDER BY relevancia DESC, wp_id ASC LIMIT ?"
    params.append(batch_size)
    cur = conn.execute(query, params)
    rows = cur.fetchall()
    conn.close()
    return [dict(r) for r in rows]


if __name__ == "__main__":
    import sys

    if len(sys.argv) > 1:
        cmd = sys.argv[1]
        if cmd == "init":
            init_db()
            print("DB initialized")
        elif cmd == "stats":
            stats = get_stats()
            print(
                f"Total: {stats['total']}, Completed: {stats['completed']}, Errors: {stats['errors']}, Pending: {stats['pending']}, Batches: {stats['batches']}"
            )
        elif cmd == "last-batch":
            b = get_last_batch()
            if b:
                print(f"Batch {b['id']}: {b['status']} ({b['created_at']})")
            else:
                print("No batches yet")
    else:
        init_db()
        print("Tracking DB ready")

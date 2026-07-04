import pymysql

conn = pymysql.connect(
    host="127.0.0.1", port=3307, user="wpuser", password="wppass", database="bc_wp"
)
cur = conn.cursor()

# Check Spencer W. Kimball
cur.execute(
    "SELECT ID, post_title, post_name FROM wp_posts WHERE post_name='spencer-w-kimball' AND post_type='bc_quote_author'"
)
r = cur.fetchone()
print(f"Spencer W. Kimball: ID={r[0]}, title={r[1]}, slug={r[2]}")

# Check current thumbnail
cur.execute(
    "SELECT meta_value FROM wp_postmeta WHERE post_id=%s AND meta_key='_thumbnail_id'",
    (r[0],),
)
t = cur.fetchone()
print(f"  Current _thumbnail_id: {t[0] if t else 'NONE'}")

# Get next 60 without thumbnails (after hartman-rector-jr)
cur.execute("""
    SELECT p.ID, p.post_title, p.post_name
    FROM wp_posts p
    LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
    WHERE p.post_type = 'bc_quote_author'
      AND p.post_status = 'publish'
      AND pm.meta_id IS NULL
    ORDER BY p.post_title
""")
rows = cur.fetchall()
for i, r in enumerate(rows[:60]):
    print(f"{i + 1}. {r[0]}: {r[1]} ({r[2]})")
conn.close()

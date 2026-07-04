import pymysql, os, time, json
from PIL import Image

conn = pymysql.connect(
    host="127.0.0.1", port=3307, user="wpuser", password="wppass", database="bc_wp"
)
cur = conn.cursor()

UPLOADS = r"C:\own\wp_bc\wp-content\uploads\2026\07"
BASE_URL = "http://localhost:8080/wp-content/uploads/2026/07"
YEAR_MONTH = "2026/07"

people = [
    {
        "slug": "burton-k-farnsworth",
        "title": "Burton K. Farnsworth",
        "file": "burton-k-farnsworth.jpg",
    },
    {
        "slug": "clement-m-matswagothata",
        "title": "Clement M. Matswagothata",
        "file": "clement-m-matswagothata.jpg",
    },
    {
        "slug": "daniel-s-miles",
        "title": "Daniel S. Miles",
        "file": "daniel-s-miles.jpg",
    },
    {
        "slug": "david-lawrence-mckay",
        "title": "David Lawrence McKay",
        "file": "david-lawrence-mckay.jpg",
    },
    {
        "slug": "emily-h-bennett",
        "title": "Emily H. Bennett",
        "file": "emily-h-bennett.jpg",
    },
    {
        "slug": "florence-h-richards",
        "title": "Florence H. Richards",
        "file": "florence-h-richards.jpg",
    },
    {"slug": "glen-l-pace", "title": "Glen L. Pace", "file": "glen-l-pace.jpg"},
    {
        "slug": "isabelle-salmon-ross",
        "title": "Isabelle Salmon Ross",
        "file": "isabelle-salmon-ross.jpg",
    },
    {
        "slug": "j-devn-cornish",
        "title": "J. Devn Cornish",
        "file": "j-devn-cornish.jpg",
    },
    {"slug": "j-kimo-esplin", "title": "J. Kimo Esplin", "file": "j-kimo-esplin.jpg"},
    {
        "slug": "james-e-evanson",
        "title": "James E. Evanson",
        "file": "james-e-evanson.jpg",
    },
    {"slug": "jan-e-newman", "title": "Jan E. Newman", "file": "jan-e-newman.jpg"},
    {
        "slug": "janet-murdock-thompson",
        "title": "Janet Murdock Thompson",
        "file": "janet-murdock-thompson.jpg",
    },
    {
        "slug": "janette-hales-beckham",
        "title": "Janette Hales Beckham",
        "file": "janette-hales-beckham.jpg",
    },
    {
        "slug": "jerald-l-taylor",
        "title": "Jerald L. Taylor",
        "file": "jerald-l-taylor.jpg",
    },
    {
        "slug": "jeremy-r-jaggi",
        "title": "Jeremy R. Jaggi",
        "file": "jeremy-r-jaggi.jpg",
    },
    {
        "slug": "spencer-w-kimball",
        "title": "Spencer W. Kimball",
        "file": "spencer-w-kimball.jpg",
    },
    {
        "slug": "margaret-d-nadauld",
        "title": "Margaret D. Nadauld",
        "file": "margaret-d-nadauld.jpg",
    },
    {
        "slug": "mary-ellen-w-smoot",
        "title": "Mary Ellen W. Smoot",
        "file": "mary-ellen-w-smoot.jpg",
    },
    {
        "slug": "linda-k-burton",
        "title": "Linda K. Burton",
        "file": "linda-k-burton.jpg",
    },
    {
        "slug": "naomi-m-shumway",
        "title": "Naomi M. Shumway",
        "file": "naomi-m-shumway.jpg",
    },
    {
        "slug": "michaelene-p-grassli",
        "title": "Michaelene P. Grassli",
        "file": "michaelene-p-grassli.jpg",
    },
    {
        "slug": "patricia-p-pinegar",
        "title": "Patricia P. Pinegar",
        "file": "patricia-p-pinegar.jpg",
    },
    {
        "slug": "rosemary-m-wixom",
        "title": "Rosemary M. Wixom",
        "file": "rosemary-m-wixom.jpg",
    },
    # --- Batch: 19 cacheados + 2 Church News (IDs 2411-2431) ---
    {"slug": "edward-hunter", "title": "Edward Hunter", "file": "edward-hunter.jpg"},
    {"slug": "george-goddard", "title": "George Goddard", "file": "george-goddard.jpg"},
    {
        "slug": "george-reynolds",
        "title": "George Reynolds",
        "file": "george-reynolds.jpg",
    },
    {"slug": "john-d-amos", "title": "John D. Amos", "file": "john-d-amos.jpg"},
    {
        "slug": "jonathan-s-schmitt",
        "title": "Jonathan S. Schmitt",
        "file": "jonathan-s-schmitt.jpg",
    },
    {"slug": "joni-l-koch", "title": "Joni L. Koch", "file": "joni-l-koch.jpg"},
    {
        "slug": "jorge-f-zeballos",
        "title": "Jorge F. Zeballos",
        "file": "jorge-f-zeballos.jpg",
    },
    {
        "slug": "joseph-w-sitati",
        "title": "Joseph W. Sitati",
        "file": "joseph-w-sitati.jpg",
    },
    {
        "slug": "k-brett-nattress",
        "title": "K. Brett Nattress",
        "file": "k-brett-nattress.jpg",
    },
    {"slug": "kevin-g-brown", "title": "Kevin G. Brown", "file": "kevin-g-brown.jpg"},
    {
        "slug": "kevin-s-hamilton",
        "title": "Kevin S. Hamilton",
        "file": "kevin-s-hamilton.jpg",
    },
    {"slug": "kyle-s-mckay", "title": "Kyle S. McKay", "file": "kyle-s-mckay.jpg"},
    {
        "slug": "lynn-g-robbins",
        "title": "Lynn G. Robbins",
        "file": "lynn-g-robbins.jpg",
    },
    {"slug": "mark-a-bragg", "title": "Mark A. Bragg", "file": "mark-a-bragg.jpg"},
    {"slug": "mark-d-eddy", "title": "Mark D. Eddy", "file": "mark-d-eddy.jpg"},
    {"slug": "mark-l-pace", "title": "Mark L. Pace", "file": "mark-l-pace.jpg"},
    {
        "slug": "matthew-l-carpenter",
        "title": "Matthew L. Carpenter",
        "file": "matthew-l-carpenter.jpg",
    },
    {
        "slug": "michael-a-dunn",
        "title": "Michael A. Dunn",
        "file": "michael-a-dunn.jpg",
    },
    {
        "slug": "michael-b-strong",
        "title": "Michael B. Strong",
        "file": "michael-b-strong.jpg",
    },
    {"slug": "sheri-l-dew", "title": "Sheri L. Dew", "file": "sheri-l-dew.jpg"},
    {
        "slug": "robert-d-hales",
        "title": "Robert D. Hales",
        "file": "robert-d-hales.jpg",
    },
    {"slug": "david-a-smith", "title": "David A. Smith", "file": "david-a-smith.jpg"},
    {"slug": "henry-harriman", "title": "Henry Harriman", "file": "henry-harriman.jpg"},
    {"slug": "hiram-page", "title": "Hiram Page", "file": "hiram-page.jpg"},
    {
        "slug": "b-lloyd-poelman",
        "title": "B. Lloyd Poelman",
        "file": "b-lloyd-poelman.jpg",
    },
    {
        "slug": "clarissa-a-beesley",
        "title": "Clarissa A. Beesley",
        "file": "clarissa-a-beesley.png",
    },
    {
        "slug": "donna-d-sorensen",
        "title": "Donna D. Sorensen",
        "file": "donna-d-sorensen.jpg",
    },
    {
        "slug": "dorothy-p-holt",
        "title": "Dorothy P. Holt",
        "file": "dorothy-p-holt.jpg",
    },
    {
        "slug": "edith-hunter-lambert",
        "title": "Edith Hunter Lambert",
        "file": "edith-hunter-lambert.jpg",
    },
    {
        "slug": "elbert-r-curtis",
        "title": "Elbert R. Curtis",
        "file": "elbert-r-curtis.jpg",
    },
    {"slug": "george-miller", "title": "George Miller", "file": "george-miller.jpg"},
    {"slug": "george-r-hill", "title": "George R. Hill", "file": "george-r-hill.jpg"},
    {
        "slug": "helen-s-williams",
        "title": "Helen S. Williams",
        "file": "helen-s-williams.jpg",
    },
    {
        "slug": "christian-whitmer",
        "title": "Christian Whitmer",
        "file": "christian-whitmer.png",
    },
]

# Get next post ID
cur.execute("SELECT MAX(ID) FROM wp_posts")
next_id = cur.fetchone()[0] + 1

# Get all existing attachment post_names to avoid conflicts
cur.execute("SELECT post_name FROM wp_posts WHERE post_type='attachment'")
existing_names = set(r[0] for r in cur.fetchall())

now = time.gmtime()
post_date = time.strftime("%Y-%m-%d %H:%M:%S", now)
post_date_gmt = post_date


def make_sizes(filepath, outbase):
    img = Image.open(filepath)
    w, h = img.size
    sizes = {}

    # Original file name (basename from full path)
    orig_filename = os.path.basename(filepath)
    name_no_ext = os.path.splitext(orig_filename)[0]
    ext = os.path.splitext(orig_filename)[1].lower()

    # Generate sizes
    size_configs = [
        ("thumbnail", 150, 150, True),
        ("bc_quote_photo", 160, 160, True),
        ("medium", 300, 0, False),
    ]

    for sname, sw, sh, crop in size_configs:
        if crop:
            # Crop to square
            min_dim = min(w, h)
            left = (w - min_dim) // 2
            top = (h - min_dim) // 2
            cropped = img.crop((left, top, left + min_dim, top + min_dim))
            resized = cropped.resize((sw, sh), Image.LANCZOS)
        else:
            # Resize to max width
            if w > sw:
                ratio = sw / w
                new_h = int(h * ratio)
                resized = img.resize((sw, new_h), Image.LANCZOS)
            else:
                resized = img.copy()

        sfname = f"{name_no_ext}-{sw}x{sh if crop else resized.size[1]}{ext}"
        outpath = os.path.join(outbase, sfname)
        resized.save(outpath, quality=85, optimize=True)

        # Build size metadata
        mime = f"image/{ext[1:]}" if ext[1:] else "image/jpeg"
        if mime == "image/jpg":
            mime = "image/jpeg"

        sizes[sname] = {
            "file": sfname,
            "width": resized.size[0],
            "height": resized.size[1],
            "mime-type": mime,
        }

    return sizes


def build_manifest(orig_file, sizes):
    manifest = {}
    # Original
    manifest[orig_file] = {
        "relative_path": orig_file,
        "state": "complete",
        "remote_path": f"wp-content/uploads/{orig_file}",
        "last_error": "",
    }
    # Generated sizes
    for sname, sinfo in sizes.items():
        sfpath = f"{YEAR_MONTH}/{sinfo['file']}"
        manifest[sfpath] = {
            "relative_path": sfpath,
            "state": "complete",
            "remote_path": f"wp-content/uploads/{sfpath}",
            "last_error": "",
        }
    return manifest


def serialize_php_array(data):
    """Serialize Python data to PHP serialized format (simplified)"""
    if isinstance(data, dict):
        items = []
        for k, v in data.items():
            items.append(f's:{len(str(k))}:"{k}";{serialize_php_array(v)}')
        return f"a:{len(data)}:{{{''.join(items)}}}"
    elif isinstance(data, list):
        items = []
        for i, v in enumerate(data):
            items.append(f"i:{i};{serialize_php_array(v)}")
        return f"a:{len(data)}:{{{''.join(items)}}}"
    elif isinstance(data, str):
        return f's:{len(data)}:"{data}";'
    elif isinstance(data, int):
        return f"i:{data};"
    elif isinstance(data, float):
        return f"d:{data};"
    elif data is None:
        return "N;"
    elif isinstance(data, bool):
        return f"b:{'1' if data else '0'};"
    return "N;"


for p in people:
    src_path = os.path.join(UPLOADS, p["file"])
    name_no_ext = os.path.splitext(p["file"])[0]
    ext = os.path.splitext(p["file"])[1]
    mime = f"image/{ext[1:]}"
    if mime == "image/jpg":
        mime = "image/jpeg"

    print(f"Processing {p['file']}...")

    # Generate sizes
    sizes = make_sizes(src_path, UPLOADS)
    print(f"  Generated {len(sizes)} sizes: {', '.join(sizes.keys())}")

    # Re-open original to get dimensions
    img = Image.open(src_path)
    w, h = img.size
    fs = os.path.getsize(src_path)

    # Build post_name with suffix
    base_slug = p["slug"]
    post_name = base_slug
    suffix = 2
    while post_name in existing_names:
        post_name = f"{base_slug}-{suffix}"
        suffix += 1
    existing_names.add(post_name)

    # Attachment metadata
    att_meta = {
        "width": w,
        "height": h,
        "file": f"{YEAR_MONTH}/{p['file']}",
        "sizes": sizes,
        "image_meta": {
            "aperture": "0",
            "credit": "",
            "camera": "",
            "caption": "",
            "created_timestamp": "0",
            "copyright": "",
            "focal_length": "0",
            "iso": "0",
            "shutter_speed": "0",
            "title": "",
            "orientation": "0",
            "keywords": [],
        },
    }

    # Build manifest
    orig_rel_path = f"{YEAR_MONTH}/{p['file']}"
    manifest = {}
    manifest[orig_rel_path] = {
        "relative_path": orig_rel_path,
        "state": "complete",
        "remote_path": f"wp-content/uploads/{orig_rel_path}",
        "last_error": "",
    }
    for sname, sinfo in sizes.items():
        spath = f"{YEAR_MONTH}/{sinfo['file']}"
        manifest[spath] = {
            "relative_path": spath,
            "state": "complete",
            "remote_path": f"wp-content/uploads/{spath}",
            "last_error": "",
        }

    # Insert post
    id_ = next_id
    next_id += 1

    guid = f"{BASE_URL}/{p['file']}"

    cur.execute(
        """
        INSERT INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title,
        post_excerpt, post_status, comment_status, ping_status, post_password, post_name,
        to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent,
        guid, menu_order, post_type, post_mime_type, comment_count)
        VALUES (%s, 1, %s, %s, '', %s, '', 'inherit', 'open', 'closed', '', %s,
        '', '', %s, %s, '', 0, %s, 0, 'attachment', %s, 0)
    """,
        (
            id_,
            post_date,
            post_date_gmt,
            p["title"],
            post_name,
            post_date,
            post_date_gmt,
            guid,
            mime,
        ),
    )

    # Insert meta
    metas = [
        ("_indigetal_offloaded", "complete"),
        ("_indigetal_offload_manifest", serialize_php_array(manifest)),
        ("_wp_attached_file", f"{YEAR_MONTH}/{p['file']}"),
        ("_wp_attachment_metadata", serialize_php_array(att_meta)),
    ]
    for mk, mv in metas:
        cur.execute(
            "INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (%s, %s, %s)",
            (id_, mk, mv),
        )

    # Set _thumbnail_id on the persona post
    cur.execute(
        "SELECT ID FROM wp_posts WHERE post_name = %s AND post_type = 'bc_quote_author'",
        (p["slug"],),
    )
    row = cur.fetchone()
    if row:
        persona_id = row[0]
        # Check if already has _thumbnail_id
        cur.execute(
            "SELECT meta_id FROM wp_postmeta WHERE post_id = %s AND meta_key = '_thumbnail_id'",
            (persona_id,),
        )
        if cur.fetchone():
            cur.execute(
                "UPDATE wp_postmeta SET meta_value = %s WHERE post_id = %s AND meta_key = '_thumbnail_id'",
                (id_, persona_id),
            )
            print(
                f"  Updated _thumbnail_id for {p['slug']} (persona #{persona_id}) -> attachment #{id_}"
            )
        else:
            cur.execute(
                "INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (%s, '_thumbnail_id', %s)",
                (persona_id, id_),
            )
            print(
                f"  Set _thumbnail_id for {p['slug']} (persona #{persona_id}) -> attachment #{id_}"
            )
    else:
        print(f"  WARNING: No persona found for {p['slug']}")

    print(f"  Created attachment #{id_}: {p['title']}")

conn.commit()
conn.close()
print(f"\nDone! All {len(people)} attachments created.")

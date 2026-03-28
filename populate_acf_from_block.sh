#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   ./populate_acf_from_block.sh blocks/lc-example.php

if ! command -v python3 >/dev/null 2>&1; then
  echo "❌ python3 is required." >&2
  exit 1
fi

if [ "$#" -lt 1 ]; then
  echo "Usage: $0 <block-template-php>" >&2
  echo "Example: $0 blocks/lc-cta.php" >&2
  exit 1
fi

BLOCK_TEMPLATE="$1"

python3 - "$BLOCK_TEMPLATE" <<'PY'
import json
import os
import re
import sys
import hashlib
from datetime import datetime

block_template = sys.argv[1]

if not os.path.isfile(block_template):
    print(f"❌ Block template not found: {block_template}", file=sys.stderr)
    sys.exit(1)

base_dir = os.getcwd()
acf_dir = os.path.join(base_dir, "acf-json")
os.makedirs(acf_dir, exist_ok=True)

block_file = os.path.basename(block_template)
block_kebab, ext = os.path.splitext(block_file)
if ext.lower() != ".php":
    print(f"❌ Expected a .php block template, got: {block_template}", file=sys.stderr)
    sys.exit(1)

block_slug = block_kebab.replace("-", "_")
json_path = os.path.join(acf_dir, f"group_{block_slug}.json")

def titleize(s: str) -> str:
    return " ".join(w.capitalize() for w in re.split(r"[_\-\s]+", s.strip()) if w)

def labelize_field_name(name: str) -> str:
    # Remove leading lc_ / lc- prefix from labels (keep field names unchanged)
    cleaned = re.sub(r"^lc[_\-]+", "", name.strip(), flags=re.IGNORECASE)
    return titleize(cleaned)

def infer_type(name: str) -> str:
    n = name.lower()
    if n.startswith(("is_", "has_", "show_", "enable_", "disable_")):
        return "true_false"
    if any(k in n for k in ["image", "icon", "logo", "photo", "thumbnail"]):
        return "image"
    if any(k in n for k in ["link"]):
        return "link"
    if any(k in n for k in ["url", "href"]):
        return "url"
    if any(k in n for k in ["content", "description", "excerpt", "copy", "text", "body", "message"]):
        return "textarea"
    return "text"

def safe_key(prefix: str, name: str) -> str:
    n = re.sub(r"[^a-z0-9_]+", "_", name.lower()).strip("_")
    if not n:
        n = "field"
    digest = hashlib.sha1(f"{prefix}:{name}".encode("utf-8")).hexdigest()[:8]
    return f"field_{prefix}_{n}_{digest}"

def field_template(prefix: str, name: str, ftype: str):
    field = {
        "key": safe_key(prefix, name),
        "label": labelize_field_name(name),
        "name": name,
        "type": ftype,
        "instructions": "",
        "required": 0,
        "conditional_logic": 0,
        "wrapper": {
            "width": "",
            "class": "",
            "id": ""
        }
    }

    if ftype in ("text", "url"):
        field.update({
            "default_value": "",
            "placeholder": "",
            "prepend": "",
            "append": ""
        })
    elif ftype == "textarea":
        field.update({
            "default_value": "",
            "placeholder": "",
            "rows": 4,
            "new_lines": "br"
        })
    elif ftype == "link":
        field.update({
            "return_format": "array"
        })
    elif ftype == "image":
        field.update({
            "return_format": "array",
            "preview_size": "medium",
            "library": "all"
        })
    elif ftype == "true_false":
        field.update({
            "default_value": 0,
            "ui": 1,
            "ui_on_text": "",
            "ui_off_text": ""
        })

    return field

def repeater_template(prefix: str, name: str):
    return {
        "key": safe_key(prefix, name),
        "label": labelize_field_name(name),
        "name": name,
        "type": "repeater",
        "instructions": "",
        "required": 0,
        "conditional_logic": 0,
        "wrapper": {
            "width": "",
            "class": "",
            "id": ""
        },
        "layout": "block",
        "button_label": "Add Row",
        "sub_fields": []
    }

# --- Parse block template for field usage ---
with open(block_template, "r", encoding="utf-8") as f:
    php = f.read()

# Match function calls with first arg literal string.
# e.g. get_field('title') / the_field("title") / have_rows('items') / get_sub_field('label')
fn_re = re.compile(
    r"\b(?P<fn>get_field|the_field|have_rows|get_sub_field|the_sub_field)\s*\(\s*['\"](?P<name>[a-zA-Z0-9_\-]+)['\"]",
    re.MULTILINE,
)

seen_order = []
for m in fn_re.finditer(php):
    seen_order.append((m.group("fn"), m.group("name")))

top_fields = []
repeaters = []
sub_fields = []

def push_unique(arr, item):
    if item not in arr:
        arr.append(item)

for fn, name in seen_order:
    if fn in ("get_field", "the_field"):
        push_unique(top_fields, name)
    elif fn == "have_rows":
        push_unique(repeaters, name)
    elif fn in ("get_sub_field", "the_sub_field"):
        push_unique(sub_fields, name)

if not top_fields and not repeaters and not sub_fields:
    print("⚠️ No ACF field calls found in block template. Nothing to update.")
    sys.exit(0)

# --- Load or initialize group JSON ---
if os.path.isfile(json_path):
    with open(json_path, "r", encoding="utf-8") as f:
        data = json.load(f)
else:
    data = {
        "key": f"group_{block_slug}",
        "title": titleize(block_slug),
        "fields": [],
        "location": [[{
            "param": "block",
            "operator": "==",
            "value": f"acf/{block_kebab}"
        }]],
        "menu_order": 0,
        "position": "normal",
        "style": "default",
        "label_placement": "top",
        "instruction_placement": "label",
        "hide_on_screen": "",
        "active": 1,
        "description": "",
        "show_in_rest": 0
    }

if "fields" not in data or not isinstance(data["fields"], list):
    data["fields"] = []

fields = data["fields"]

# Normalize existing labels so lc_ prefix is not shown in ACF UI labels.
def normalize_labels(field_list):
    for fld in field_list:
        if not isinstance(fld, dict):
            continue

        # Keep message field labels/messages exactly as authored
        # (e.g. scaffolded block heading from add_block.sh).
        if fld.get("type") == "message":
            sub = fld.get("sub_fields")
            if isinstance(sub, list):
                normalize_labels(sub)
            continue

        nm = fld.get("name")
        if nm:
            fld["label"] = labelize_field_name(nm)
        sub = fld.get("sub_fields")
        if isinstance(sub, list):
            normalize_labels(sub)

normalize_labels(fields)

# Map existing fields by name.
existing = {}
for idx, fld in enumerate(fields):
    nm = fld.get("name")
    if nm:
        existing[nm] = idx

added = []

# Add top-level fields from get_field/the_field
for name in top_fields:
    if name in existing:
        continue
    ftype = infer_type(name)
    fields.append(field_template(block_slug, name, ftype))
    existing[name] = len(fields) - 1
    added.append(name)

# Ensure repeater fields from have_rows
for rep in repeaters:
    if rep not in existing:
        fields.append(repeater_template(block_slug, rep))
        existing[rep] = len(fields) - 1
        added.append(rep)
    else:
        if fields[existing[rep]].get("type") != "repeater":
            fields[existing[rep]]["type"] = "repeater"
            fields[existing[rep]].setdefault("sub_fields", [])

# Handle sub fields
warnings = []
if sub_fields:
    if len(repeaters) == 1:
        rep_name = repeaters[0]
        rep_field = fields[existing[rep_name]]
        rep_field.setdefault("sub_fields", [])

        existing_sub = {sf.get("name") for sf in rep_field["sub_fields"] if isinstance(sf, dict)}
        for sf_name in sub_fields:
            if sf_name in existing_sub:
                continue
            sf_type = infer_type(sf_name)
            rep_field["sub_fields"].append(field_template(f"{block_slug}_{rep_name}", sf_name, sf_type))
            added.append(f"{rep_name}.{sf_name}")
    elif len(repeaters) == 0:
        # No explicit repeater found, promote sub fields as top-level to avoid data loss.
        for sf_name in sub_fields:
            if sf_name in existing:
                continue
            sf_type = infer_type(sf_name)
            fields.append(field_template(block_slug, sf_name, sf_type))
            existing[sf_name] = len(fields) - 1
            added.append(sf_name)
        warnings.append("Found get_sub_field()/the_sub_field() without have_rows(); added them as top-level fields.")
    else:
        warnings.append("Multiple have_rows() repeaters found; could not reliably map sub fields. Added repeaters only.")

# Optional metadata update
data["modified"] = int(datetime.now().timestamp())

# Backup existing file before write
if os.path.isfile(json_path):
    backup = f"{json_path}.bak"
    with open(json_path, "r", encoding="utf-8") as src, open(backup, "w", encoding="utf-8") as dst:
        dst.write(src.read())

with open(json_path, "w", encoding="utf-8") as f:
    json.dump(data, f, indent=2, ensure_ascii=False)
    f.write("\n")

print(f"✅ Updated: {json_path}")
if added:
    print("➕ Added fields:")
    for x in added:
        print(f"   - {x}")
else:
    print("ℹ️ No new fields were added (already up to date).")

for w in warnings:
    print(f"⚠️ {w}")
PY

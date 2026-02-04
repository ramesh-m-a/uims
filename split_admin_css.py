import os
import re

# ==============================
# CONFIG
# ==============================
INPUT_CSS = "extracted-ui.css"   # your 10k-line file
OUTPUT_DIR = "admin"

RULE_MAP = {
    "buttons.css": [
        r"\.btn", r"\.action-btn", r"\.icon", r"\.fa-", r"\.button"
    ],
    "table.css": [
        r"table", r"\.admin-table", r"th", r"td", r"\.filter"
    ],
    "toolbar.css": [
        r"\.toolbar", r"\.search", r"\.export", r"\.header", r"\.top-bar"
    ],
    "pagination.css": [
        r"\.pagination", r"\.page-", r"\.pager"
    ],
    "modal.css": [
        r"\.modal", r"\.loader", r"\.overlay", r"\.dialog"
    ],
    "badges.css": [
        r"\.badge", r"\.status", r"\.label"
    ],
    "utilities.css": [
        r"\.flex", r"\.gap", r"\.text-", r"\.align-", r"\.justify-"
    ],
}

BASE_FILE = "base.css"
LEFTOVER_FILE = "leftovers.css"

# ==============================
# PREP
# ==============================
os.makedirs(OUTPUT_DIR, exist_ok=True)

files = {name: [] for name in RULE_MAP}
files[BASE_FILE] = []
files[LEFTOVER_FILE] = []

# ==============================
# PARSE CSS (rule-by-rule)
# ==============================
with open(INPUT_CSS, "r", encoding="utf-8") as f:
    css = f.read()

blocks = re.findall(r"[^{]+{[^}]*}", css, re.MULTILINE | re.DOTALL)

for block in blocks:
    matched = False

    for filename, patterns in RULE_MAP.items():
        for pattern in patterns:
            if re.search(pattern, block):
                files[filename].append(block)
                matched = True
                break
        if matched:
            break

    if not matched:
        # base styles or unknown
        if re.search(r"body|html|:root", block):
            files[BASE_FILE].append(block)
        else:
            files[LEFTOVER_FILE].append(block)

# ==============================
# WRITE FILES
# ==============================
for filename, blocks in files.items():
    path = os.path.join(OUTPUT_DIR, filename)
    with open(path, "w", encoding="utf-8") as f:
        f.write("\n\n".join(blocks))

print("✅ CSS successfully split into:")
for f in files:
    print(f" - admin/{f}")


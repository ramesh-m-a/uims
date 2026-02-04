import re

# ==========================
# CONFIG
# ==========================
INPUT_CSS  = "ace.min.css"           # your big css file
OUTPUT_CSS = "extracted-ui.css"  # output file

KEYWORDS = [
    "btn",
    "button",
    "table",
    "pagination",
    "breadcrumb",
    "header",
    "icon",
    "fa",
    "action",
]

# ==========================
# LOGIC
# ==========================
def should_keep(selector: str) -> bool:
    selector = selector.lower()
    return any(k in selector for k in KEYWORDS)

def extract_css_blocks(css_text: str) -> str:
    blocks = re.findall(r'([^{]+)\{([^}]+)\}', css_text, re.S)
    kept = []

    for selector, body in blocks:
        if should_keep(selector):
            kept.append(f"{selector.strip()} {{\n{body.strip()}\n}}\n")

    return "\n".join(kept)

# ==========================
# RUN
# ==========================
with open(INPUT_CSS, "r", encoding="utf-8", errors="ignore") as f:
    css = f.read()

filtered_css = extract_css_blocks(css)

with open(OUTPUT_CSS, "w", encoding="utf-8") as f:
    f.write(filtered_css)

print(f"✅ Extracted CSS written to: {OUTPUT_CSS}")
print(f"🔍 Keywords used: {', '.join(KEYWORDS)}")


import os
import json
import uuid
from flask import Flask, request, jsonify, send_from_directory, abort, session, redirect, url_for
from werkzeug.utils import secure_filename
from datetime import datetime

# -------------------------------------------------
# CONFIG
# -------------------------------------------------
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
UPLOAD_FOLDER = os.path.join(project_root, 'public', 'projects')
DATA_FILE = os.path.join(project_root, 'data', 'projects.json')
ALLOWED_EXT = {'png', 'jpg', 'jpeg', 'gif', 'webp'}
ADMIN_PASSWORD = "olat2025"

os.makedirs(UPLOAD_FOLDER, exist_ok=True)

app = Flask(__name__, static_folder='..', static_url_path='')
app.secret_key = 'your-secret-key-here'  # Change in production

# -------------------------------------------------
# LOGIN DECORATOR
# -------------------------------------------------
def login_required(f):
    from functools import wraps
    @wraps(f)
    def decorated(*args, **kwargs):
        if not session.get('logged_in'):
            return redirect('/admin/login')
        return f(*args, **kwargs)
    return decorated

# -------------------------------------------------
# 1. ROOT & STATIC
# -------------------------------------------------
@app.route('/')
def index():
    return send_from_directory('..', 'index.html')

@app.route('/<path:path>')
def serve_static(path):
    blocked = ['api/', 'projects/', 'admin/login', 'admin/upload']
    if any(path.startswith(p) for p in blocked):
        abort(404)
    return send_from_directory('..', path)

# -------------------------------------------------
# 2. PROJECT IMAGES
# -------------------------------------------------
@app.route('/projects/<filename>')
def serve_project_image(filename):
    return send_from_directory(UPLOAD_FOLDER, filename)

# -------------------------------------------------
# 3. ADMIN: LOGIN
# -------------------------------------------------
@app.route('/admin/login', methods=['GET', 'POST'])
def admin_login():
    if request.method == 'POST':
        if request.form.get('password') == ADMIN_PASSWORD:
            session['logged_in'] = True
            return redirect('/admin/upload')
        return "Invalid password", 403
    return send_from_directory('..', 'admin/login.html')

# -------------------------------------------------
# 4. ADMIN: LOGOUT
# -------------------------------------------------
@app.route('/admin/logout')
def admin_logout():
    session.pop('logged_in', None)
    return redirect('/')

# -------------------------------------------------
# 5. ADMIN: UPLOAD PAGE
# -------------------------------------------------
@app.route('/admin/upload')
@login_required
def admin_upload_page():
    return send_from_directory('..', 'admin/upload.html')

# -------------------------------------------------
# 6. API: GET PROJECTS
# -------------------------------------------------
@app.route('/api/projects/<service>')
def get_projects(service):
    if service not in ['facilities', 'digital']:
        return jsonify([])
    if not os.path.exists(DATA_FILE):
        return jsonify([])
    try:
        with open(DATA_FILE, 'r') as f:
            data = json.load(f)
        return jsonify(data.get(service, []))
    except:
        return jsonify([])

# -------------------------------------------------
# 7. API: UPLOAD
# -------------------------------------------------
@app.route('/api/upload', methods=['POST'])
@login_required
def upload():
    title = request.form.get('title')
    description = request.form.get('description', '')
    service = request.form.get('service')
    image = request.files.get('image')
    url = request.form.get('url', '')
    type_ = request.form.get('type', '')
    category = request.form.get('category', '')

    if not all([title, service, image]):
        return jsonify({"error": "Missing fields"}), 400

    ext = image.filename.rsplit('.', 1)[1].lower() if '.' in image.filename else ''
    if ext not in ALLOWED_EXT:
        return jsonify({"error": "Invalid image type"}), 400

    filename = f"{uuid.uuid4().hex}_{secure_filename(image.filename)}"
    filepath = os.path.join(UPLOAD_FOLDER, filename)
    image.save(filepath)

    image_url = f"/projects/{filename}"

    data = {"facilities": [], "digital": []}
    if os.path.exists(DATA_FILE):
        try:
            with open(DATA_FILE, 'r') as f:
                data = json.load(f)
        except:
            pass

    entry = {
        "id": str(uuid.uuid4()),
        "title": title,
        "description": description,
        "image": image_url,
        "timestamp": datetime.utcnow().isoformat() + "Z"
    }
    if service == "digital":
        entry["type"] = type_
        if type_ == "web" and url:
            entry["url"] = url
        if type_ == "graphics" and category:
            entry["category"] = category

    data[service].append(entry)

    try:
        with open(DATA_FILE, 'w') as f:
            json.dump(data, f, indent=2)
    except:
        return jsonify({"error": "Save failed"}), 500

    return jsonify({"success": True, "title": title})
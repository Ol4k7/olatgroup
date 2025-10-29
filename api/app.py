from flask import Flask, request, jsonify, send_from_directory
import os
import json
import uuid
from datetime import datetime
from werkzeug.utils import secure_filename

# === Flask App Setup ===
app = Flask(__name__, static_folder='..', static_url_path='')
app.secret_key = "olat-super-secret-2025"

# FIX: Absolute path to project root
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
app.config['UPLOAD_FOLDER'] = os.path.join(project_root, 'public', 'projects')
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024

ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif', 'webp'}


# === Helper Functions ===
def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS


def load_db():
    path = os.path.join(project_root, 'data', 'projects.json')
    if not os.path.exists(path):
        default = {"facilities": [], "digital": []}
        os.makedirs(os.path.dirname(path), exist_ok=True)
        with open(path, 'w', encoding='utf-8') as f:
            json.dump(default, f, indent=2)
        return default
    try:
        with open(path, 'r', encoding='utf-8') as f:
            return json.load(f)
    except json.JSONDecodeError:
        return {"facilities": [], "digital": []}


def save_db(data):
    path = os.path.join(project_root, 'data', 'projects.json')
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)


# === API: Admin Login ===
@app.route('/api/admin/login', methods=['POST'])
def admin_login():
    data = request.get_json()
    if data and data.get('password') == 'olat2025':
        return jsonify({"success": True})
    return jsonify({"success": False, "error": "Wrong password"}), 401


# === API: Get Projects ===
@app.route('/api/projects/<service>')
def get_projects(service):
    if service not in ['facilities', 'digital']:
        return jsonify({"error": "Invalid service"}), 400
    return jsonify(load_db().get(service, []))


# === API: Upload Project ===
@app.route('/api/upload', methods=['POST'])
def upload():
    if request.form.get('password') != 'olat2025':
        return jsonify({"error": "Unauthorized"}), 401

    if 'image' not in request.files:
        return jsonify({"error": "No image provided"}), 400

    file = request.files['image']
    if file.filename == '':
        return jsonify({"error": "No file selected"}), 400
    if not allowed_file(file.filename):
        return jsonify({"error": "Invalid file type"}), 400

    filename = secure_filename(file.filename)
    unique_name = f"{uuid.uuid4().hex}_{filename}"
    filepath = os.path.join(app.config['UPLOAD_FOLDER'], unique_name)
    os.makedirs(os.path.dirname(filepath), exist_ok=True)
    file.save(filepath)

    service = request.form.get('service')
    if service not in ['facilities', 'digital']:
        return jsonify({"error": "Invalid service"}), 400

    project = {
        "id": str(uuid.uuid4()),
        "title": request.form.get('title', '').strip(),
        "description": request.form.get('description', '').strip(),
        "image": f"/projects/{unique_name}",
        "timestamp": datetime.now().isoformat()
    }

    if service == 'digital':
        project['type'] = request.form.get('type', 'web')
        if project['type'] == 'web':
            project['url'] = request.form.get('url', '').strip()
        elif project['type'] == 'graphics':
            project['category'] = request.form.get('category', 'Uncategorized')

    data = load_db()
    data[service].insert(0, project)
    save_db(data)

    return jsonify({"success": True, "project": project})


# === SERVE UPLOADED IMAGES ===
@app.route('/projects/<filename>')
def serve_project_image(filename):
    projects_dir = os.path.join(project_root, 'public', 'projects')
    return send_from_directory(projects_dir, filename)


# === SERVE STATIC FILES ===
@app.route('/<path:path>')
def serve_static(path):
    if path.startswith('api/') or path.startswith('projects/'):
        return "Not found", 404
    return send_from_directory('..', path)


@app.route('/')
def index():
    return send_from_directory('..', 'index.html')


# === Run App ===
if __name__ == '__main__':
    os.makedirs(os.path.join(project_root, 'data'), exist_ok=True)
    os.makedirs(os.path.join(project_root, 'public', 'projects'), exist_ok=True)
    
    db_path = os.path.join(project_root, 'data', 'projects.json')
    if not os.path.exists(db_path):
        save_db({"facilities": [], "digital": []})
    
    print("\n" + "="*70)
    print("   OLAT GROUP SERVER - 404 FIXED")
    print("="*70)
    print(f"   Root: {project_root}")
    print("   Images: http://localhost:5000/projects/yourfile.jpg")
    print("="*70 + "\n")
    app.run(debug=True, host='0.0.0.0', port=5000)
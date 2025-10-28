from flask import Flask, request, jsonify, render_template, redirect, session, url_for
import json
import os
from werkzeug.utils import secure_filename
import uuid

app = Flask(__name__)
app.secret_key = "your-super-secret-key-change-this"
app.config['UPLOAD_FOLDER'] = '../public/projects'
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB

ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif', 'webp'}

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

def load_db():
    with open('../data/projects.json', 'r') as f:
        return json.load(f)

def save_db(data):
    with open('../data/projects.json', 'w') as f:
        json.dump(data, f, indent=2)

# === LOGIN ===
@app.route('/admin/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        if request.form['password'] == 'olat2025':  # Change this!
            session['logged_in'] = True
            return redirect(url_for('upload'))
        return "Wrong password", 403
    return render_template('login.html')

# === UPLOAD ===
@app.route('/admin/upload', methods=['GET', 'POST'])
def upload():
    if not session.get('logged_in'):
        return redirect(url_for('login'))

    if request.method == 'POST':
        file = request.files['image']
        if file and allowed_file(file.filename):
            filename = secure_filename(file.filename)
            unique_name = f"{uuid.uuid4().hex}_{filename}"
            filepath = os.path.join(app.config['UPLOAD_FOLDER'], unique_name)
            file.save(filepath)

            db = load_db()
            project = {
                "id": str(uuid.uuid4()),
                "title": request.form['title'],
                "description": request.form['description'],
                "image": f"/projects/{unique_name}"
            }

            service = request.form['service']
            if service == 'digital':
                project['type'] = request.form['type']
                if project['type'] == 'web':
                    project['url'] = request.form['url']
                else:
                    project['category'] = request.form['category']
            db[service].insert(0, project)
            save_db(db)

            return redirect(url_for('upload') + '?success=1')

    return render_template('upload.html')

@app.route('/admin/logout')
def logout():
    session.pop('logged_in', None)
    return redirect(url_for('login'))

# === API: Get Projects ===
@app.route('/api/projects/<service>')
def get_projects(service):
    if service not in ['facilities', 'digital']:
        return jsonify([]), 400
    db = load_db()
    return jsonify(db[service])

if __name__ == '__main__':
    os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)
    app.run(debug=True)
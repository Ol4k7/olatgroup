<?php
// === PHP upload configuration ===
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');
ini_set('memory_limit', '128M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');

session_start();
require_once '../../config.php';

// === Admin check ===
if (!($_SESSION['admin'] ?? false)) {
    header('Location: login.php');
    exit;
}

// === Constants ===
if (!defined('ALLOWED_EXT')) define('ALLOWED_EXT', ['jpg','jpeg','png','gif','webp']);
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', __DIR__ . '/../../uploads/projects'); // safe folder
if (!defined('DATA_FILE')) define('DATA_FILE', __DIR__ . '/../../data/projects.json');

// Ensure upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        die('Failed to create upload directory');
    }
}

$status = null;

// === Handle form submission ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $service  = $_POST['service'] ?? '';
    $url      = trim($_POST['url'] ?? '');
    $type     = $_POST['type'] ?? '';
    $category = $_POST['category'] ?? '';
    $image    = $_FILES['image'] ?? null;

    $allowed_services = ['facilities', 'digital', 'gallery'];

    // --- 1. Basic validation ---
    if ($title === '') {
        $status = ['error' => 'Project title is required'];
    } elseif (!in_array($service, $allowed_services)) {
        $status = ['error' => 'Invalid service selected'];
    }

    // --- 2. Validate image ---
    if (!$status) {
        if (!$image || $image['error'] === UPLOAD_ERR_NO_FILE) {
            $status = ['error' => 'An image is required'];
        } elseif ($image['error'] !== UPLOAD_ERR_OK) {
            $php_errors = [
                1 => 'The uploaded file exceeds the upload_max_filesize directive.',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                3 => 'The uploaded file was only partially uploaded.',
                4 => 'No file was uploaded.',
                6 => 'Missing a temporary folder.',
                7 => 'Failed to write file to disk.',
                8 => 'A PHP extension stopped the file upload.'
            ];
            $msg = $php_errors[$image['error']] ?? 'Unknown upload error';
            $status = ['error' => "File upload error: $msg"];
        } else {
            $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_EXT)) {
                $status = ['error' => 'Invalid image type. Allowed: ' . implode(', ', ALLOWED_EXT)];
            } elseif ($image['size'] > 10 * 1024 * 1024) {
                $status = ['error' => 'File too large. Max 10MB allowed'];
            }
        }
    }

    // --- 3. Digital service validation ---
    if (!$status && $service === 'digital') {
        if (!$type) {
            $status = ['error' => 'Select project type (Web or Graphics)'];
        } elseif ($type === 'web' && $url === '') {
            $status = ['error' => 'Web Design requires a live URL'];
        } elseif ($type === 'graphics' && $category === '') {
            $status = ['error' => 'Graphics Design requires a category'];
        }
    }

    // --- 4. Process upload ---
    if (!$status) {
        $filename = uniqid('proj_') . '.' . $ext;
        $filepath = UPLOAD_DIR . "/$filename";

        if (!move_uploaded_file($image['tmp_name'], $filepath)) {
            $status = ['error' => 'Upload failed. Check directory permissions'];
        } else {
            $data = file_exists(DATA_FILE) ? json_decode(file_get_contents(DATA_FILE), true) : [];
            if (!isset($data[$service])) $data[$service] = [];

            $entry = [
                'id'          => uniqid(),
                'title'       => $title,
                'description' => $desc,
                // --- Public path uses /public/projects/ URL (symlink or PHP serving) ---
                'image'       => "/public/projects/$filename",
                'service'     => $service,
                'timestamp'   => date('c')
            ];

            if ($service === 'digital') {
                $entry['type'] = $type;
                if ($type === 'web') $entry['url'] = $url;
                if ($type === 'graphics') $entry['category'] = $category;
            }

            $data[$service][] = $entry;

            if (file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT))) {
                $status = ['success' => true, 'title' => $title, 'id' => $entry['id']];
            } else {
                $status = ['error' => 'Failed to save project data'];
            }
        }
    }
}

// --- 5. Handle logout ---
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Upload</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root { --primary: #0066ff; --danger: #ef4444; --success: #10b981; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Inter', sans-serif; background: #f4f7fa; padding: 2rem; min-height: 100vh; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    h2 { font-family: 'Sora', sans-serif; color: var(--primary); margin-bottom: 1.5rem; font-size: 1.8rem; display: flex; justify-content: space-between; align-items: center; }
    .logout { font-size: 0.9rem; color: var(--primary); text-decoration: none; font-weight: 500; }
    .logout:hover { text-decoration: underline; }
    .upload-form { background: #f9fbfd; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #e2e8f0; }
    input, textarea, select, button { width: 100%; padding: 0.9rem; margin: 0.6rem 0; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
    input:focus, textarea:focus, select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
    button { background: var(--primary); color: white; border: none; font-weight: 600; cursor: pointer; }
    button:hover { background: #0052cc; }
    .projects-list { margin-top: 2rem; }
    .project-item { display: flex; align-items: center; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 1rem; background: #fdfdfe; }
    .project-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; margin-right: 1rem; }
    .project-info { flex: 1; }
    .project-title { font-weight: 600; color: #1e293b; margin-bottom: 0.25rem; }
    .project-meta { font-size: 0.875rem; color: #64748b; }
    .delete-btn { background: var(--danger); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; }
    .delete-btn:hover { background: #dc2626; }
    .status { margin-top: 1rem; padding: 0.75rem; border-radius: 8px; font-weight: 500; }
    .success { background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0; }
    .error { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Upload Project <a href="login.php?logout" class="logout">Logout</a></h2>

    <div class="upload-form">
      <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Project Title" required />
        <textarea name="description" placeholder="Description" rows="3"></textarea>
        <input type="url" name="url" placeholder="Live URL (Web only)" />
        <select name="service" required>
          <option value="">Select Service</option>
          <option value="facilities">Facilities</option>
          <option value="digital">Digital</option>
          <option value="gallery">Gallery</option>
        </select>
        <select name="type" style="display:none;">
          <option value="web">Web Design</option>
          <option value="graphics">Graphics Design</option>
        </select>
        <select name="category" style="display:none;">
          <option value="Logos">Logos</option>
          <option value="Banners">Banners</option>
          <option value="Social Media">Social Media</option>
          <option value="Brochures">Brochures</option>
        </select>
        <input type="file" name="image" accept="image/*" required />
        <button type="submit">Upload</button>
      </form>
      <?php if ($status ?? ''): ?>
        <p class="status <?= (!empty($status['success'])) ? 'success' : 'error' ?>">
            <?= !empty($status['success']) ? "Uploaded: \"{$status['title']}\"" : $status['error'] ?>
        </p>

      <?php endif; ?>
    </div>

    <div class="projects-list">
      <h3>Uploaded Projects</h3>
      <div id="projects"></div>
    </div>
  </div>

  <script>
    const service = document.querySelector('[name="service"]');
    const type = document.querySelector('[name="type"]');
    const category = document.querySelector('[name="category"]');
    const urlInput = document.querySelector('[name="url"]');
    const descInput = document.querySelector('[name="description"]');

    service.onchange = () => {
      const isDigital = service.value === 'digital';
      const isGallery = service.value === 'gallery';
      type.style.display = isDigital ? 'block' : 'none';
      category.style.display = 'none';
      urlInput.style.display = isDigital ? 'block' : 'none';
      descInput.style.display = isGallery ? 'none' : 'block';
    };
    type.onchange = () => {
      const isGraphics = type.value === 'graphics';
      const isWeb = type.value === 'web';
      category.style.display = isGraphics ? 'block' : 'none';
      urlInput.style.display = isWeb ? 'block' : 'none';
    };

    async function loadProjects() {
      const res = await fetch('/api/projects.php?service=facilities');
      const fac = await res.json();
      const res2 = await fetch('/api/projects.php?service=digital');
      const dig = await res2.json();
      const res3 = await fetch('/api/projects.php?service=gallery');
      const gal = await res3.json();
      const all = [...fac, ...dig, ...gal].sort((a,b) => b.timestamp.localeCompare(a.timestamp));
      document.getElementById('projects').innerHTML = all.map(p => `
        <div class="project-item">
          <img src="${p.image}" class="project-thumb" onerror="this.style.display='none'">
          <div class="project-info">
            <div class="project-title">${p.title}</div>
            <div class="project-meta">${p.service || 'Unknown'} • ${new Date(p.timestamp).toLocaleDateString()}</div>
          </div>
          <button class="delete-btn" onclick="deleteProject('${p.id}')">Delete</button>
        </div>
      `).join('') || '<p>No projects yet.</p>';
    }

    async function deleteProject(id) {
      if (!confirm('Delete permanently?')) return;
      await fetch('/api/delete.php?id=' + id, { method: 'DELETE' });
      loadProjects();
    }

    loadProjects();
  </script>
</body>
</html>
<?php if (isset($_GET['logout'])) { session_destroy(); header('Location: login.php'); exit;} ?>
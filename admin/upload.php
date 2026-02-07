<?php
/**
 * admin/upload.php - Integrated Upload & Edit
 */

require_once dirname(__DIR__) . '/config/config.php';

// 1. AUTHENTICATION & SESSION TIMEOUT
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 600) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$_SESSION['last_activity'] = time();

$status = null;
$edit_data = null;

// 2. FETCH DATA IF EDITING
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_data = $stmt->fetch();
}

// 3. HANDLE FORM SUBMISSION (Insert or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'] ?? null;
    $title      = strip_tags(trim($_POST['title'] ?? ''));
    $desc       = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $service    = $_POST['service'] ?? '';
    $url        = filter_var(trim($_POST['url'] ?? ''), FILTER_SANITIZE_URL);
    $type       = $_POST['type'] ?? '';
    $category   = $_POST['category'] ?? '';
    $image      = $_FILES['image'] ?? null;

    $allowed_services = ['facilities', 'digital', 'gallery'];

    if ($title === '' || !in_array($service, $allowed_services)) {
        $status = ['error' => 'Project title and service type are required.'];
    } else {
        try {
            $img_path = $_POST['existing_image'] ?? '';

            // Handle New Image Upload
            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_EXT)) {
                    $filename = uniqid('proj_') . '.' . $ext;
                    if (move_uploaded_file($image['tmp_name'], UPLOAD_DIR . "/$filename")) {
                        $img_path = "/public/projects/$filename";
                    }
                }
            }

            if ($project_id) {
                // UPDATE EXISTING RECORD
                $sql = "UPDATE projects SET title=:title, description=:desc, image_path=:img, service_type=:service, digital_type=:dtype, web_url=:url, graphics_category=:cat WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':title'   => $title,
                    ':desc'    => ($service === 'gallery' ? null : $desc),
                    ':img'     => $img_path,
                    ':service' => $service,
                    ':dtype'   => ($service === 'digital' ? $type : null),
                    ':url'     => ($type === 'web' ? $url : null),
                    ':cat'     => ($type === 'graphics' ? $category : null),
                    ':id'      => $project_id
                ]);
            } else {
                // INSERT NEW RECORD
                $sql = "INSERT INTO projects (title, description, image_path, service_type, digital_type, web_url, graphics_category) 
                        VALUES (:title, :desc, :img, :service, :dtype, :url, :cat)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':title'   => $title,
                    ':desc'    => ($service === 'gallery' ? null : $desc),
                    ':img'     => $img_path,
                    ':service' => $service,
                    ':dtype'   => ($service === 'digital' ? $type : null),
                    ':url'     => ($type === 'web' ? $url : null),
                    ':cat'     => ($type === 'graphics' ? $category : null)
                ]);
            }

            // Redirect to clear form and show success
            header("Location: upload.php?saved=1");
            exit;

        } catch (PDOException $e) {
            $status = ['error' => 'Database Error: ' . $e->getMessage()];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard | Olat Group</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root { --primary: #0066ff; --danger: #ef4444; --success: #10b981; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Inter', sans-serif; background: #f4f7fa; padding: 2rem; min-height: 100vh; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    h2 { font-family: 'Sora', sans-serif; color: var(--primary); margin-bottom: 1.5rem; font-size: 1.8rem; display: flex; justify-content: space-between; align-items: center; }
    .logout { font-size: 0.9rem; color: var(--danger); text-decoration: none; font-weight: 500; }
    .upload-form { background: #f9fbfd; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #e2e8f0; }
    input, textarea, select, button { width: 100%; padding: 0.9rem; margin: 0.6rem 0; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
    button { background: var(--primary); color: white; border: none; font-weight: 600; cursor: pointer; transition: 0.3s; }
    button:hover { background: #0052cc; }
    .project-item { display: flex; align-items: center; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 1rem; background: #fff; }
    .project-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; margin-right: 1rem; }
    .project-info { flex: 1; }
    .actions { display: flex; gap: 5px; }
    .edit-btn { background: #64748b; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer; border:none; }
    .delete-btn { background: var(--danger); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer; }
    .status { padding: 0.75rem; border-radius: 8px; font-weight: 500; margin-bottom: 1rem; }
    .success { background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0; }
    .error { background: #fef2f2; color: var(--danger); border: 1px solid #fca5a5; }
    .cancel-btn { background: #ddd; color: #333; text-decoration: none; display: block; text-align: center; padding: 0.8rem; border-radius: 8px; margin-top: 5px; }
  </style>
</head>
<body>
  <div class="container">
    <h2><?= $edit_data ? 'Edit Project' : 'Upload Project' ?> <a href="login.php?logout" class="logout">Logout</a></h2>

    <div class="upload-form">
      <form method="post" enctype="multipart/form-data">
        <?php if ($status || isset($_GET['saved'])): ?>
          <div class="status <?= !isset($status['error']) ? 'success' : 'error' ?>">
            <?= isset($_GET['saved']) ? "Changes saved successfully!" : $status['error'] ?>
          </div>
        <?php endif; ?>

        <input type="hidden" name="project_id" value="<?= $edit_data['id'] ?? '' ?>">
        <input type="hidden" name="existing_image" value="<?= $edit_data['image_path'] ?? '' ?>">

        <input type="text" name="title" placeholder="Project Title" value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" required />
        
        <textarea name="description" placeholder="Description" rows="3"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
        
        <select name="service" id="service" required>
          <option value="">Select Service</option>
          <option value="facilities" <?= ($edit_data['service_type'] ?? '') == 'facilities' ? 'selected' : '' ?>>Facilities</option>
          <option value="digital" <?= ($edit_data['service_type'] ?? '') == 'digital' ? 'selected' : '' ?>>Digital</option>
          <option value="gallery" <?= ($edit_data['service_type'] ?? '') == 'gallery' ? 'selected' : '' ?>>Gallery</option>
        </select>

        <div id="digital_fields" style="display: <?= ($edit_data['service_type'] ?? '') == 'digital' ? 'block' : 'none' ?>;">
          <select name="type" id="type">
            <option value="web" <?= ($edit_data['digital_type'] ?? '') == 'web' ? 'selected' : '' ?>>Web Design</option>
            <option value="graphics" <?= ($edit_data['digital_type'] ?? '') == 'graphics' ? 'selected' : '' ?>>Graphics Design</option>
          </select>
          <input type="url" name="url" id="url" placeholder="https://livedemo.com" value="<?= htmlspecialchars($edit_data['web_url'] ?? '') ?>" />
          
          <select name="category" id="category" style="display: <?= ($edit_data['digital_type'] ?? '') == 'graphics' ? 'block' : 'none' ?>;">
            <option value="Logos" <?= ($edit_data['graphics_category'] ?? '') == 'Logos' ? 'selected' : '' ?>>Logos</option>
            <option value="Banners" <?= ($edit_data['graphics_category'] ?? '') == 'Banners' ? 'selected' : '' ?>>Banners</option>
            <option value="Social Media" <?= ($edit_data['graphics_category'] ?? '') == 'Social Media' ? 'selected' : '' ?>>Social Media</option>
          </select>
        </div>

        <div style="margin: 10px 0;">
            <label style="font-size: 0.85rem; color: #666;">Project Image <?= $edit_data ? '(Keep empty to use current)' : '(Required)' ?>:</label>
            <input type="file" name="image" accept="image/*" <?= $edit_data ? '' : 'required' ?> />
        </div>

        <button type="submit"><?= $edit_data ? 'Save Changes' : 'Publish Project' ?></button>
        <?php if($edit_data): ?>
            <a href="upload.php" class="cancel-btn">Cancel Edit</a>
        <?php endif; ?>
      </form>
    </div>

    <h3>Recently Uploaded</h3>
    <div id="projects">Loading projects...</div>
  </div>

  <script>
    const service = document.getElementById('service');
    const digitalFields = document.getElementById('digital_fields');
    const type = document.getElementById('type');
    const category = document.getElementById('category');
    const urlInput = document.getElementById('url');
    const descInput = document.querySelector('[name="description"]');

    service.onchange = () => {
      digitalFields.style.display = (service.value === 'digital') ? 'block' : 'none';
      descInput.style.display = (service.value === 'gallery') ? 'none' : 'block';
    };

    type.onchange = () => {
      category.style.display = (type.value === 'graphics') ? 'block' : 'none';
      urlInput.style.display = (type.value === 'web') ? 'block' : 'none';
    };

    async function loadProjects() {
        const res = await fetch('projects.php');
        const projects = await res.json();
        const list = document.getElementById('projects');
        
        list.innerHTML = projects.map(p => `
            <div class="project-item">
                <img src="..${p.image}" class="project-thumb">
                <div class="project-info">
                    <strong>${p.title}</strong><br>
                    <small>${p.service.toUpperCase()}</small>
                </div>
                <div class="actions">
                    <button class="edit-btn" onclick="window.location.href='upload.php?edit_id=${p.id}'">Edit</button>
                    <button class="delete-btn" onclick="deleteItem(${p.id})">Delete</button>
                </div>
            </div>
        `).join('') || '<p>No projects found.</p>';
    }

    async function deleteItem(id) {
        if (!confirm('Delete this project permanently?')) return;
        await fetch('delete.php?id=' + id, { method: 'POST' }); 
        loadProjects();
    }

    loadProjects();

    // Idle Timer (60s)
    let idleTimer;
    const resetTimer = () => {
        clearTimeout(idleTimer);
        idleTimer = setTimeout(() => window.location.href = 'login.php', 600000);
    };
    ['mousemove', 'keydown', 'scroll', 'click'].forEach(e => window.addEventListener(e, resetTimer));
    resetTimer();
  </script>
</body>
</html>
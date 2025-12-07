# GitHub Setup - Step-by-Step Checklist

Follow these steps to enable automatic updates for your plugin via GitHub.

---

## ✅ Step 1: Create GitHub Repository

1. Go to https://github.com/new
2. **Repository name:** `airbnb-style-wishlists`
3. **Visibility:** Public (or Private if using GitHub token)
4. **Do NOT** initialize with README (you already have one)
5. Click **Create repository**

---

## ✅ Step 2: Update Plugin Configuration

Open `airbnb-style-wishlists.php` and find line 25:

```php
'server' => 'https://raw.githubusercontent.com/YOUR-GITHUB-USERNAME/airbnb-style-wishlists/main/uupd/',
```

**Replace `YOUR-GITHUB-USERNAME`** with your actual GitHub username.

Example:
```php
'server' => 'https://raw.githubusercontent.com/josephlewis/airbnb-style-wishlists/main/uupd/',
```

---

## ✅ Step 3: Update index.json

Open `uupd/index.json` and update line 10:

```json
"download_url": "https://github.com/YOUR-USERNAME/airbnb-style-wishlists/releases/download/v2.0/airbnb-style-wishlists.zip",
```

Replace `YOUR-USERNAME` with your GitHub username.

---

## ✅ Step 4: Initialize Git and Push to GitHub

Open Terminal/Command Prompt and run:

```bash
# Navigate to plugin folder
cd "/Users/josephlewis/Desktop/Website Projects/Ben Whistler Master Folder/BW Brand Portal/Custom Plugins/Airbnb Style Wishlists"

# Initialize git
git init

# Add all files
git add .

# Create first commit
git commit -m "Initial commit - v2.0 with UUPD integration"

# Rename branch to main
git branch -M main

# Add your GitHub repo (replace YOUR-USERNAME)
git remote add origin https://github.com/YOUR-USERNAME/airbnb-style-wishlists.git

# Push to GitHub
git push -u origin main
```

---

## ✅ Step 5: Create ZIP File for Release

Create a ZIP file of your plugin folder:

**Important:** The ZIP should extract to a folder named `airbnb-style-wishlists`

### Mac/Linux:
```bash
cd "/Users/josephlewis/Desktop/Website Projects/Ben Whistler Master Folder/BW Brand Portal/Custom Plugins"
zip -r airbnb-style-wishlists.zip "Airbnb Style Wishlists" -x "*.git*" -x "*.DS_Store" -x "*node_modules*"
```

### Windows:
Right-click the plugin folder → Send to → Compressed (zipped) folder

Rename it to: `airbnb-style-wishlists.zip`

---

## ✅ Step 6: Create GitHub Release

1. Go to your repository on GitHub
2. Click **"Releases"** (right sidebar)
3. Click **"Create a new release"**
4. Fill in the form:
   - **Tag version:** `v2.0`
   - **Release title:** `Version 2.0 - Enquiry System Added`
   - **Description:** Copy from `uupd/changelog.txt`
5. **Attach files:** Upload `airbnb-style-wishlists.zip`
6. Click **"Publish release"**

---

## ✅ Step 7: Test Updates in WordPress

1. Go to WordPress Admin → Plugins
2. You should see "Update available" for "Airbnb Style Wishlists & Enquiry System"
3. Click **"Update now"**
4. Plugin should update automatically!

---

## 🔧 Optional: Add GitHub Token (for Private Repos)

If your repository is **private** or you're hitting API rate limits:

### Generate Token:
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Click **"Generate new token (classic)"**
3. Name: `WordPress Plugin Updates`
4. Scopes: Check `repo` (full control of private repositories)
5. Click **"Generate token"**
6. Copy the token (starts with `ghp_`)

### Add to Plugin:
In `airbnb-style-wishlists.php`, uncomment line 29:

```php
'github_token' => 'ghp_YourActualTokenHere',
```

---

## 🚀 Future Updates

When you release a new version:

1. Update version in `airbnb-style-wishlists.php` (line 5 and line 13)
2. Update `uupd/index.json` (version and download_url)
3. Update `uupd/changelog.txt` (add new version info)
4. Commit and push changes to GitHub
5. Create new release with new tag (e.g., `v2.1`)
6. WordPress sites will automatically detect the update!

---

## 📝 Summary of URLs

Replace `YOUR-USERNAME` in these:

**Repository:** `https://github.com/YOUR-USERNAME/airbnb-style-wishlists`

**Update Server:** `https://raw.githubusercontent.com/YOUR-USERNAME/airbnb-style-wishlists/main/uupd/`

**Download URL:** `https://github.com/YOUR-USERNAME/airbnb-style-wishlists/releases/download/v2.0/airbnb-style-wishlists.zip`

---

## ✅ Checklist

- [ ] Created GitHub repository
- [ ] Updated GitHub username in `airbnb-style-wishlists.php`
- [ ] Updated GitHub username in `uupd/index.json`
- [ ] Initialized git and pushed to GitHub
- [ ] Created ZIP file
- [ ] Created GitHub release v2.0
- [ ] Uploaded ZIP to release
- [ ] Tested update in WordPress

---

**Need Help?**

- UUPD Documentation: https://github.com/stingray82/uupd
- Check `debug.log` if updates aren't working (enable WP_DEBUG in wp-config.php)


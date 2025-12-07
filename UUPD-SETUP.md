# UUPD Setup Instructions

## What You Need to Do

### 1. Create GitHub Repository

1. Go to GitHub and create a new repository
2. Name it: `airbnb-style-wishlists` (or your preferred name)
3. Make it **Public** (or Private if you want to use a GitHub token)

### 2. Update Plugin Configuration

In `airbnb-style-wishlists.php`, line 25, replace:
```php
'server' => 'https://raw.githubusercontent.com/YOUR-GITHUB-USERNAME/airbnb-style-wishlists/main/uupd/',
```

With your actual GitHub username:
```php
'server' => 'https://raw.githubusercontent.com/YOUR-USERNAME/airbnb-style-wishlists/main/uupd/',
```

### 3. Create Required Files for GitHub

You need to create a `uupd/` folder in your repository with these files:

**uupd/index.json** - Contains update metadata
**uupd/changelog.txt** - Contains version history

I've created these files for you in the `uupd/` folder.

### 4. Push to GitHub

```bash
cd "path/to/Airbnb Style Wishlists"
git init
git add .
git commit -m "Initial commit with UUPD integration"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/airbnb-style-wishlists.git
git push -u origin main
```

### 5. Create a Release on GitHub

1. Go to your repository on GitHub
2. Click "Releases" → "Create a new release"
3. Tag version: `v2.0`
4. Release title: `Version 2.0`
5. Description: Copy from changelog.txt
6. Upload the plugin ZIP file
7. Publish release

### 6. Test Updates

1. In WordPress admin, go to Plugins
2. You should see "Update available" for your plugin
3. Click "Update now" to test

---

## Optional: GitHub Token (for Private Repos)

If your repository is private or you hit rate limits:

1. Generate token: GitHub Settings → Developer settings → Personal access tokens
2. Add to plugin code:
```php
'github_token' => 'ghp_YourTokenHere',
```

---

## How It Works

1. WordPress checks for updates (via cron or manually)
2. UUPD fetches `index.json` from your GitHub raw URL
3. Compares version numbers
4. Shows update notification in WordPress admin
5. Downloads ZIP from GitHub release when user clicks "Update"

---

## Files You Need in Your Repo

```
airbnb-style-wishlists/
├── airbnb-style-wishlists.php
├── updater.php
├── wishlist.js
├── wishlist.css
├── README.md
├── QUICK-REFERENCE.md
├── IMPLEMENTATION-NOTES.md
└── uupd/
    ├── index.json
    └── changelog.txt
```

The `uupd/` folder **must** be in the root of your repository.


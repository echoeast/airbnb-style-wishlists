# Git/GitHub Setup in Cursor - Complete Guide

## ✅ Git is Now Initialized!

Your repository is ready. Now you can deploy from Cursor.

---

## 🔐 One-Time Authentication Setup

You need to authenticate with GitHub. Here are your options:

### **Option 1: Use Cursor's Built-in Git (Recommended)**

Cursor has a built-in Source Control panel:

1. **Open Source Control:**
   - Press `Cmd + Shift + G` (Mac) or `Ctrl + Shift + G` (Windows)
   - Or click the Source Control icon in the left sidebar (looks like a branch)

2. **Stage Changes:**
   - You'll see all your files listed
   - Click the `+` icon next to files to stage them
   - Or click `+` next to "Changes" to stage all

3. **Commit:**
   - Type a commit message in the box at the top
   - Press `Cmd + Enter` (Mac) or `Ctrl + Enter` (Windows)

4. **Push:**
   - Click the `...` menu (three dots) at the top
   - Select "Push"
   - First time: You'll be prompted to sign in to GitHub
   - A browser window will open for authentication

5. **Authenticate:**
   - Sign in to GitHub in the browser
   - Grant Cursor permission
   - Close the browser
   - Push will complete automatically

---

### **Option 2: Use Personal Access Token**

If the browser auth doesn't work:

1. **Generate GitHub Token:**
   - Go to: https://github.com/settings/tokens
   - Click "Generate new token (classic)"
   - Name: `Cursor Deploy`
   - Scopes: Check `repo` (full control)
   - Click "Generate token"
   - **Copy the token** (starts with `ghp_`)

2. **Update Git Remote:**
   ```bash
   git remote set-url origin https://YOUR-TOKEN@github.com/echoeast/airbnb-style-wishlists.git
   ```
   Replace `YOUR-TOKEN` with your actual token.

3. **Now pushing will work without prompts**

---

### **Option 3: Use SSH Keys (Most Secure)**

1. **Generate SSH key:**
   ```bash
   ssh-keygen -t ed25519 -C "your-email@example.com"
   ```
   Press Enter to accept defaults.

2. **Add to SSH agent:**
   ```bash
   eval "$(ssh-agent -s)"
   ssh-add ~/.ssh/id_ed25519
   ```

3. **Copy public key:**
   ```bash
   cat ~/.ssh/id_ed25519.pub
   ```

4. **Add to GitHub:**
   - Go to: https://github.com/settings/keys
   - Click "New SSH key"
   - Paste the key
   - Save

5. **Update remote to use SSH:**
   ```bash
   git remote set-url origin git@github.com:echoeast/airbnb-style-wishlists.git
   ```

---

## 🚀 Daily Workflow (After Authentication)

### **Method 1: Use Cursor Source Control Panel**

1. **Make changes** to your files
2. **Open Source Control** (`Cmd + Shift + G`)
3. **Stage changes** (click + icons)
4. **Write commit message**
5. **Commit** (`Cmd + Enter`)
6. **Push** (... menu → Push)

✅ **Done!** Changes are on GitHub.

---

### **Method 2: Use Deploy Script**

I've created a deploy script that automates everything:

```bash
./deploy.sh
```

This will:
- ✅ Stage all changes
- ✅ Create commit with version number
- ✅ Push to GitHub
- ✅ Show next steps for creating release

To run it in Cursor:
1. Open Terminal in Cursor (`Ctrl + ` `)
2. Type: `./deploy.sh`
3. Press Enter

---

### **Method 3: Use Terminal Commands**

If you prefer manual control:

```bash
# Stage changes
git add .

# Commit with message
git commit -m "Your message here"

# Push to GitHub
git push origin main
```

---

## 📦 Creating a Release (For Updates)

After pushing, create a GitHub release:

### **Option A: Via GitHub Website**

1. Go to: https://github.com/echoeast/airbnb-style-wishlists/releases/new
2. **Tag:** `v2.1` (or whatever version)
3. **Title:** `Version 2.1`
4. **Description:** Copy from changelog
5. **Upload ZIP file**
6. Click "Publish release"

### **Option B: Use GitHub CLI (Optional)**

Install GitHub CLI: `brew install gh`

Then authenticate: `gh auth login`

Create release:
```bash
gh release create v2.1 \
  --title "Version 2.1" \
  --notes "Test update release" \
  airbnb-style-wishlists.zip
```

---

## 🔄 Complete Update Workflow

### **When You Make Changes:**

1. **Edit files** in Cursor
2. **Update version numbers:**
   - `airbnb-style-wishlists.php` (lines 4 & 12)
   - `uupd/index.json` (line 4 & 12)
   - `uupd/changelog.txt` (add new entry)

3. **Use Source Control Panel:**
   - Stage → Commit → Push

4. **Create ZIP:**
   - Right-click plugin folder
   - Compress
   - Rename to `airbnb-style-wishlists.zip`

5. **Create GitHub Release:**
   - Tag with version (e.g., `v2.2`)
   - Upload ZIP file

6. **Test in WordPress:**
   - Plugins page → Check for updates
   - Should see new version available

---

## 🎯 Quick Reference

**Stage all changes:**
```bash
git add .
```

**Commit:**
```bash
git commit -m "Message"
```

**Push:**
```bash
git push origin main
```

**Check status:**
```bash
git status
```

**View commit history:**
```bash
git log --oneline
```

---

## 🛠️ Troubleshooting

**"Authentication failed":**
- Use Option 1 (Cursor built-in) or Option 2 (Personal Access Token)

**"Permission denied":**
- Check you're logged into correct GitHub account
- Verify token has `repo` scope

**"Nothing to commit":**
- Files haven't changed
- Or they're in `.gitignore`

**Want to undo last commit:**
```bash
git reset --soft HEAD~1
```

---

## ✅ You're All Set!

Git is configured and ready. Choose your preferred workflow above and start deploying! 🚀

**Next:** Try pushing with the Source Control panel (`Cmd + Shift + G`)


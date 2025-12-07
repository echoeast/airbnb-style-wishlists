#!/bin/bash
# Deploy script for pushing updates to GitHub

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Deploying Airbnb Style Wishlists Plugin${NC}"
echo ""

# Check if we're in the right directory
if [ ! -f "airbnb-style-wishlists.php" ]; then
    echo -e "${RED}❌ Error: Must run from plugin directory${NC}"
    exit 1
fi

# Get current version from plugin file
VERSION=$(grep "Version:" airbnb-style-wishlists.php | head -1 | sed 's/.*Version: //' | tr -d ' ')
echo -e "${BLUE}📦 Current version: ${GREEN}${VERSION}${NC}"
echo ""

# Stage all changes
echo -e "${BLUE}📝 Staging changes...${NC}"
git add .

# Check if there are changes to commit
if git diff --staged --quiet; then
    echo -e "${RED}❌ No changes to commit${NC}"
    exit 0
fi

# Show what will be committed
echo -e "${BLUE}📋 Changes to commit:${NC}"
git status --short
echo ""

# Commit message
COMMIT_MSG="Update to v${VERSION}"
echo -e "${BLUE}💾 Committing: ${GREEN}${COMMIT_MSG}${NC}"
git commit -m "${COMMIT_MSG}"

# Push to GitHub
echo -e "${BLUE}🔄 Pushing to GitHub...${NC}"
git push origin main

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ Successfully pushed to GitHub!${NC}"
    echo ""
    echo -e "${BLUE}Next steps:${NC}"
    echo "1. Create release: https://github.com/echoeast/airbnb-style-wishlists/releases/new"
    echo "2. Tag: v${VERSION}"
    echo "3. Upload ZIP file"
    echo "4. Test update in WordPress"
else
    echo ""
    echo -e "${RED}❌ Push failed. Check authentication.${NC}"
    exit 1
fi


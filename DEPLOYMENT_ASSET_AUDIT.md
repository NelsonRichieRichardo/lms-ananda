# Deployment Asset Audit Report

**Date:** June 11, 2026  
**Issue:** CSS styling not loading in production on Railway  
**Application:** SMA Ananda Batam LMS

---

## Executive Summary

The application deploys successfully on Railway, but CSS styling is not loading in production. The root cause is that the build artifacts (CSS/JS files) are not being generated during Railway deployment because the build process is not configured in Railway's deployment settings.

**Root Cause:** Missing Railway build configuration to run `npm install` and `npm run build`

**Status:** ✅ Root Cause Identified  
**Fix Required:** Create railway.toml configuration file

---

## Audit Findings

### 1. CSS Loading Mechanism in Blade Layouts

**Status:** ✅ CORRECT

**Files Audited:**
- `resources/views/layouts/app.blade.php` - Line 15: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
- `resources/views/layouts/dashboard.blade.php` - Line 14: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
- `resources/views/layouts/guest.blade.php` - Line 14: `@vite(['resources/css/app.css', 'resources/js/app.js'])`

**Finding:** All layouts correctly use the `@vite` directive to load assets. This is the standard approach for Laravel projects using Vite.

---

### 2. Vite Configuration

**Status:** ✅ CORRECT

**File:** `vite.config.js`

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

**Finding:** Standard Laravel Vite configuration with correct input files.

---

### 3. Tailwind Configuration

**Status:** ✅ CORRECT

**File:** `tailwind.config.js`

```javascript
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
```

**Finding:** Standard Tailwind configuration with correct content paths and forms plugin.

---

### 4. Public Build Directory

**Status:** ⚠️ ARTIFACTS EXIST LOCALLY BUT GITIGNORED

**Findings:**
- `public/build/` directory EXISTS
- `public/build/assets/` directory EXISTS and contains:
  - `app-B7G7t78D.js` (81,664 bytes)
  - `app-Dgb_YN7j.css` (48,152 bytes)
- `public/build/manifest.json` EXISTS (331 bytes)

**Issue:** The build artifacts exist locally but are not committed to git because the directory is gitignored.

---

### 5. .gitignore Configuration

**Status:** ⚠️ CAUSING DEPLOYMENT ISSUE

**File:** `.gitignore` - Line 16

```
/public/build
```

**Finding:** The `/public/build` directory is gitignored, which is correct for development but requires the build process to run during deployment.

---

### 6. Package.json Build Scripts

**Status:** ✅ CORRECT

**File:** `package.json`

```json
{
    "scripts": {
        "build": "vite build",
        "dev": "vite"
    },
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.2",
        "@tailwindcss/vite": "^4.0.0",
        "alpinejs": "^3.4.2",
        "autoprefixer": "^10.4.2",
        "axios": "^1.11.0",
        "concurrently": "^9.0.1",
        "laravel-vite-plugin": "^3.0.0",
        "postcss": "^8.4.31",
        "tailwindcss": "^3.1.0",
        "vite": "^8.0.0"
    }
}
```

**Finding:** Build script is correctly configured as `vite build`.

---

### 7. Railway Deployment Configuration

**Status:** ❌ MISSING

**Findings:**
- No `railway.toml` file found
- No `nixpacks.toml` file found
- No `Dockerfile` in project root

**Issue:** Railway has no configuration to run the build process during deployment.

---

### 8. CSS Source File

**Status:** ✅ CORRECT

**File:** `resources/css/app.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

:root {
    --ananda-maroon: #6B1212;
    --ananda-maroon-dark: #4F0D0D;
    --ananda-cream: #FAF7F1;
    --ananda-paper: #FFFDF9;
    --ananda-border: #E7DDD1;
    --ananda-ink: #201A17;
    --ananda-muted: #766A60;
    --ananda-gold: #B48A3C;
}

/* Custom components and styles */
```

**Finding:** CSS source file exists with Tailwind directives and custom styles.

---

### 9. PostCSS Configuration

**Status:** ✅ CORRECT

**File:** `postcss.config.js`

```javascript
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
```

**Finding:** Standard PostCSS configuration with Tailwind and Autoprefixer.

---

## Root Cause Analysis

**Primary Issue:** Railway is not configured to run the Node.js build process during deployment.

**Why CSS is not loading:**
1. The `@vite` directive in Blade templates looks for `public/build/manifest.json`
2. The `public/build/` directory is gitignored (correct for development)
3. Railway deployment does not run `npm install` and `npm run build`
4. Without the build process, `public/build/` is empty in production
5. The `@vite` directive cannot find the manifest.json or CSS/JS assets
6. CSS styling fails to load

**Browser Console Expected Error:**
```
Failed to load resource: the server responded with a status of 404 (Not Found)
GET /build/assets/app-*.css 404
GET /build/assets/app-*.js 404
```

---

## Affected Files

### Files Requiring Changes:
1. **NEW FILE:** `railway.toml` - Railway deployment configuration

### Files Reviewed (No Changes Required):
- `vite.config.js` - Correct
- `tailwind.config.js` - Correct
- `postcss.config.js` - Correct
- `package.json` - Correct
- `resources/css/app.css` - Correct
- `resources/views/layouts/app.blade.php` - Correct
- `resources/views/layouts/dashboard.blade.php` - Correct
- `resources/views/layouts/guest.blade.php` - Correct
- `.gitignore` - Correct (build should be gitignored)

---

## Required Fix

### Solution: Create railway.toml Configuration File

Create a `railway.toml` file in the project root to configure the build process:

```toml
[build]
builder = "NIXPACKS"

[build.env]
NPM_CONFIG_PRODUCTION = "false"

[build.phases]
build = "npm run build"

[build.nixpacks]
phases = {
  build = {
    cmds = [
      "npm install",
      "npm run build",
      "php artisan config:cache",
      "php artisan route:cache"
    ]
  }
}

[deploy]
startCommand = "php artisan serve --host=0.0.0.0 --port=$PORT"
healthcheckPath = "/"

[deploy.healthcheck]
path = "/"
initialDelaySeconds = 10
periodSeconds = 10
timeoutSeconds = 5
failureThreshold = 3
```

### Alternative Solution: Use Railway UI Configuration

If you prefer not to create a railway.toml file, configure the build process in Railway's web UI:

1. Go to your Railway project settings
2. Navigate to the "Build" tab
3. Set the build command to:
   ```
   npm install && npm run build
   ```
4. Set the start command to:
   ```
   php artisan serve --host=0.0.0.0 --port=$PORT
   ```

---

## Commands Required

### Local Testing (to verify fix):
```bash
# Remove existing build artifacts
rm -rf public/build

# Rebuild assets
npm install
npm run build

# Verify build artifacts exist
ls -la public/build/assets/
```

### Railway Deployment (after fix):
```bash
# Commit the railway.toml file
git add railway.toml
git commit -m "Add Railway build configuration for asset compilation"
git push
```

---

## Railway Configuration Changes Required

### Option 1: Automatic (Recommended)
Create the `railway.toml` file as specified above. Railway will automatically detect and use this configuration.

### Option 2: Manual via Railway UI
1. Access your Railway project
2. Go to Settings → Build
3. Set Build Command: `npm install && npm run build`
4. Set Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`
5. Redeploy the application

---

## Verification Steps

After implementing the fix:

1. **Check Railway Build Logs:**
   - Verify `npm install` runs successfully
   - Verify `npm run build` runs successfully
   - Verify build artifacts are created

2. **Check Production Build Artifacts:**
   - SSH into Railway instance
   - Run: `ls -la public/build/assets/`
   - Should see `app-*.css` and `app-*.js` files

3. **Check Browser Console:**
   - Open production site in browser
   - Open developer tools console
   - Should see NO 404 errors for CSS/JS files
   - Network tab should show successful CSS/JS loads

4. **Visual Verification:**
   - CSS styling should be applied
   - Custom colors and components should render correctly
   - Layout should match local development environment

---

## Summary

**Root Cause:** Railway deployment is not configured to run the Node.js build process, so Vite assets are not compiled in production.

**Fix:** Create `railway.toml` configuration file to run `npm install` and `npm run build` during deployment.

**Impact:** CSS and JavaScript assets will be compiled during Railway deployment, allowing the `@vite` directive to load them correctly in production.

**Estimated Time to Fix:** 5 minutes (create file, commit, push, Railway auto-redeploys)

---

**Report Prepared By:** Senior Application Security Engineer  
**Report Date:** June 11, 2026

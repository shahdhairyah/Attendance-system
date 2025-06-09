
<!DOCTYPE html>
<html lang="en">
<head><script>window.__sb_state = {"forwardPreviewErrors":true};</script><script src="/.localservice@runtime.6ba59070.js"></script><script src="/.localservice@wc-api-script.js"></script><script>delete window.__sb_w;document.querySelectorAll('script').forEach(n=>n.remove());</script>
  <script type="module">
import RefreshRuntime from "/@react-refresh"
RefreshRuntime.injectIntoGlobalHook(window)
window.$RefreshReg$ = () => {}
window.$RefreshSig$ = () => (type) => type
window.__vite_plugin_react_preamble_installed__ = true
</script>

  <script type="module" src="/@vite/client"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Attendance System - Smart College Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
            /* Modern Attendance System CSS - Production Ready */

/* CSS Custom Properties for Theme System */
:root {
  /* Light Theme Colors */
  --primary-50: #eff6ff;
  --primary-100: #dbeafe;
  --primary-200: #bfdbfe;
  --primary-300: #93c5fd;
  --primary-400: #60a5fa;
  --primary-500: #3b82f6;
  --primary-600: #2563eb;
  --primary-700: #1d4ed8;
  --primary-800: #1e40af;
  --primary-900: #1e3a8a;

  --success-50: #ecfdf5;
  --success-100: #d1fae5;
  --success-200: #a7f3d0;
  --success-300: #6ee7b7;
  --success-400: #34d399;
  --success-500: #10b981;
  --success-600: #059669;
  --success-700: #047857;
  --success-800: #065f46;
  --success-900: #064e3b;

  --warning-50: #fffbeb;
  --warning-100: #fef3c7;
  --warning-200: #fde68a;
  --warning-300: #fcd34d;
  --warning-400: #fbbf24;
  --warning-500: #f59e0b;
  --warning-600: #d97706;
  --warning-700: #b45309;
  --warning-800: #92400e;
  --warning-900: #78350f;

  --error-50: #fef2f2;
  --error-100: #fee2e2;
  --error-200: #fecaca;
  --error-300: #fca5a5;
  --error-400: #f87171;
  --error-500: #ef4444;
  --error-600: #dc2626;
  --error-700: #b91c1c;
  --error-800: #991b1b;
  --error-900: #7f1d1d;

  --neutral-50: #f8fafc;
  --neutral-100: #f1f5f9;
  --neutral-200: #e2e8f0;
  --neutral-300: #cbd5e1;
  --neutral-400: #94a3b8;
  --neutral-500: #64748b;
  --neutral-600: #475569;
  --neutral-700: #334155;
  --neutral-800: #1e293b;
  --neutral-900: #0f172a;

  /* Light Theme Variables */
  --bg-primary: #ffffff;
  --bg-secondary: #f8fafc;
  --bg-tertiary: #f1f5f9;
  --bg-hero: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --bg-card: rgba(255, 255, 255, 0.95);
  --bg-navbar: rgba(255, 255, 255, 0.95);
  --bg-footer: #1e293b;
  
  --text-primary: #1e293b;
  --text-secondary: #475569;
  --text-muted: #64748b;
  --text-inverse: #ffffff;
  
  --border-color: rgba(226, 232, 240, 0.8);
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);

  /* Gradients */
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
  --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  --gradient-error: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  --gradient-cosmic: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-ocean: linear-gradient(135deg, #10b981 0%, #059669 100%);
  --gradient-sunset: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);

  /* Spacing System */
  --space-1: 0.25rem;
  --space-2: 0.5rem;
  --space-3: 0.75rem;
  --space-4: 1rem;
  --space-5: 1.25rem;
  --space-6: 1.5rem;
  --space-8: 2rem;
  --space-10: 2.5rem;
  --space-12: 3rem;
  --space-16: 4rem;
  --space-20: 5rem;

  /* Typography */
  --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  --font-weight-light: 300;
  --font-weight-normal: 400;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
  --font-weight-bold: 700;
  --font-weight-extrabold: 800;

  /* Border Radius */
  --radius-sm: 0.375rem;
  --radius-md: 0.5rem;
  --radius-lg: 0.75rem;
  --radius-xl: 1rem;
  --radius-2xl: 1.5rem;
  --radius-3xl: 2rem;
  --radius-full: 9999px;

  /* Transitions */
  --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-normal: 200ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
  --transition-slower: 500ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Dark Theme */
[data-theme="dark"] {
  --bg-primary: #0f172a;
  --bg-secondary: #1e293b;
  --bg-tertiary: #334155;
  --bg-hero: linear-gradient(135deg, #1e293b 0%, #334155 100%);
  --bg-card: rgba(30, 41, 59, 0.95);
  --bg-navbar: rgba(15, 23, 42, 0.95);
  --bg-footer: #020617;
  
  --text-primary: #f8fafc;
  --text-secondary: #e2e8f0;
  --text-muted: #94a3b8;
  --text-inverse: #1e293b;
  
  --border-color: rgba(51, 65, 85, 0.8);
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.7);

  --neutral-50: #1e293b;
  --neutral-100: #334155;
  --neutral-200: #475569;
  --neutral-800: #e2e8f0;
  --neutral-900: #f8fafc;
}

/* Base Styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  scroll-behavior: smooth;
  font-size: 16px;
}

body {
  font-family: var(--font-family);
  background-color: var(--bg-primary);
  color: var(--text-primary);
  line-height: 1.6;
  min-height: 100vh;
  overflow-x: hidden;
  transition: background-color var(--transition-normal), color var(--transition-normal);
}

/* Enhanced Navigation */
.navbar {
  background: var(--bg-navbar) !important;
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border-color);
  box-shadow: var(--shadow-lg);
  transition: all var(--transition-normal);
  position: relative;
  z-index: 1000;
}

.navbar::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--bg-navbar);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  z-index: -1;
}

.navbar-brand {
  font-weight: var(--font-weight-bold);
  font-size: 1.5rem;
  background: var(--gradient-primary);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  transition: var(--transition-normal);
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.navbar-brand:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

.nav-link {
  color: var(--text-primary) !important;
  font-weight: var(--font-weight-medium);
  padding: var(--space-2) var(--space-4) !important;
  border-radius: var(--radius-lg);
  transition: var(--transition-normal);
  position: relative;
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.nav-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--gradient-primary);
  border-radius: var(--radius-lg);
  opacity: 0;
  transition: var(--transition-normal);
  z-index: -1;
}

.nav-link:hover::before {
  opacity: 0.1;
}

.nav-link:hover {
  color: var(--primary-600) !important;
  transform: translateY(-1px);
}

/* Enhanced Theme Toggle */
.theme-toggle {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 30px;
  margin-left: var(--space-4);
}

.theme-toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.theme-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: var(--gradient-primary);
  transition: var(--transition-normal);
  border-radius: var(--radius-full);
  box-shadow: var(--shadow-md);
}

.theme-slider:before {
  position: absolute;
  content: "";
  height: 22px;
  width: 22px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: var(--transition-normal);
  border-radius: 50%;
  box-shadow: var(--shadow-sm);
}

.theme-toggle input:checked + .theme-slider {
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
}

.theme-toggle input:checked + .theme-slider:before {
  transform: translateX(30px);
  background-color: #fbbf24;
}

.theme-icon {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
  color: white;
  transition: opacity var(--transition-normal);
  z-index: 1;
}

.theme-icon.sun {
  right: 6px;
  opacity: 1;
}

.theme-icon.moon {
  left: 6px;
  opacity: 0;
}

.theme-toggle input:checked ~ .theme-icon.sun {
  opacity: 0;
}

.theme-toggle input:checked ~ .theme-icon.moon {
  opacity: 1;
}

/* Hero Section */
.hero-section {
  background: var(--bg-hero);
  min-height: 100vh;
  position: relative;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,1000 1000,0 1000,1000"/></svg>');
  background-size: cover;
}

.floating-elements {
  position: absolute;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 1;
}

.floating-elements::before,
.floating-elements::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  animation: float 6s ease-in-out infinite;
}

.floating-elements::before {
  width: 100px;
  height: 100px;
  top: 20%;
  right: 20%;
  animation-delay: -2s;
}

.floating-elements::after {
  width: 150px;
  height: 150px;
  bottom: 20%;
  left: 10%;
  animation-delay: -4s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

.hero-content {
  position: relative;
  z-index: 2;
  color: white;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: var(--font-weight-bold);
  margin-bottom: var(--space-6);
  line-height: 1.2;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.hero-subtitle {
  font-size: 1.25rem;
  margin-bottom: var(--space-8);
  opacity: 0.9;
  font-weight: var(--font-weight-light);
  line-height: 1.6;
}

/* Enhanced Buttons */
.btn-hero {
  padding: var(--space-4) var(--space-10);
  font-size: 1.1rem;
  font-weight: var(--font-weight-semibold);
  border-radius: var(--radius-full);
  text-transform: uppercase;
  letter-spacing: 1px;
  transition: all var(--transition-normal);
  border: none;
  position: relative;
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
}

.btn-hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left var(--transition-slower);
}

.btn-hero:hover::before {
  left: 100%;
}

.btn-hero:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-2xl);
}

/* Enhanced Cards */
.feature-card, .login-card {
  background: var(--bg-card);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-lg);
  transition: all var(--transition-normal);
  overflow: hidden;
  position: relative;
  height: 100%;
}

.feature-card::before, .login-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-primary);
  transform: scaleX(0);
  transition: transform var(--transition-normal);
}

.feature-card:hover::before, .login-card:hover::before {
  transform: scaleX(1);
}

.feature-card:hover, .login-card:hover {
  transform: translateY(-10px);
  box-shadow: var(--shadow-2xl);
}

.feature-card {
  padding: var(--space-12) var(--space-8);
  text-align: center;
}

.login-card {
  padding: var(--space-12);
  text-align: center;
}

.login-card::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.05), transparent);
  animation: rotate 20s linear infinite;
  z-index: 0;
}

.login-card-content {
  position: relative;
  z-index: 1;
}

@keyframes rotate {
  100% { transform: rotate(360deg); }
}

/* Feature Icons */
.feature-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto var(--space-8);
  font-size: 2rem;
  color: white;
  position: relative;
  box-shadow: var(--shadow-lg);
}

.feature-icon.admin {
  background: var(--gradient-primary);
}

.feature-icon.faculty {
  background: var(--gradient-success);
}

.feature-icon.student {
  background: var(--gradient-info);
}

/* Statistics Section */
.stats-section {
  padding: var(--space-20) 0;
  background: var(--bg-secondary);
  transition: background-color var(--transition-normal);
}

.stat-item {
  text-align: center;
  padding: var(--space-8);
}

.stat-number {
  font-size: 3rem;
  font-weight: var(--font-weight-bold);
  background: var(--gradient-primary);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  display: block;
  margin-bottom: var(--space-2);
}

.stat-label {
  font-size: 1.1rem;
  color: var(--text-secondary);
  font-weight: var(--font-weight-medium);
}

/* Sections */
.login-section {
  padding: var(--space-20) 0;
  background: var(--bg-tertiary);
  transition: background-color var(--transition-normal);
}

#features {
  background: var(--bg-secondary);
  transition: background-color var(--transition-normal);
  padding: var(--space-20) 0;
}

/* Footer */
.footer {
  background: var(--bg-footer);
  color: var(--text-inverse);
  padding: var(--space-16) 0 var(--space-8);
  transition: background-color var(--transition-normal);
}

.footer-content {
  border-bottom: 1px solid var(--border-color);
  padding-bottom: var(--space-8);
  margin-bottom: var(--space-8);
}

.footer-logo {
  font-size: 1.5rem;
  font-weight: var(--font-weight-bold);
  margin-bottom: var(--space-4);
  background: var(--gradient-primary);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.footer-links a {
  color: var(--text-muted);
  text-decoration: none;
  transition: color var(--transition-normal);
  display: block;
  margin-bottom: var(--space-2);
}

.footer-links a:hover {
  color: var(--text-inverse);
}

.social-links a {
  display: inline-block;
  width: 40px;
  height: 40px;
  background: var(--gradient-primary);
  border-radius: 50%;
  text-align: center;
  line-height: 40px;
  color: white;
  margin-right: var(--space-2);
  transition: transform var(--transition-normal);
  text-decoration: none;
}

.social-links a:hover {
  transform: translateY(-3px);
  color: white;
}

/* Enhanced Button Styles */
.btn-primary {
  background: var(--gradient-primary);
  border: none;
  border-radius: var(--radius-xl);
  padding: var(--space-3) var(--space-6);
  font-weight: var(--font-weight-semibold);
  transition: all var(--transition-normal);
  position: relative;
  overflow: hidden;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left var(--transition-slower);
}

.btn-primary:hover::before {
  left: 100%;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
}

.btn-success {
  background: var(--gradient-success);
  border: none;
  border-radius: var(--radius-xl);
  padding: var(--space-3) var(--space-6);
  font-weight: var(--font-weight-semibold);
  transition: all var(--transition-normal);
}

.btn-success:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
}

.btn-info {
  background: var(--gradient-info);
  border: none;
  border-radius: var(--radius-xl);
  padding: var(--space-3) var(--space-6);
  font-weight: var(--font-weight-semibold);
  transition: all var(--transition-normal);
}

.btn-info:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
}

/* Typography Enhancements */
.display-4 {
  color: var(--text-primary) !important;
  font-weight: var(--font-weight-bold);
}

.lead {
  color: var(--text-secondary) !important;
  font-weight: var(--font-weight-normal);
}

.text-muted {
  color: var(--text-muted) !important;
}

h1, h2, h3, h4, h5, h6 {
  color: var(--text-primary);
  font-weight: var(--font-weight-semibold);
}

/* Mobile Responsive Design */
@media (max-width: 1200px) {
  .hero-title {
    font-size: 3rem;
  }
}

@media (max-width: 992px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .feature-card, .login-card {
    margin-bottom: var(--space-8);
  }

  .stat-number {
    font-size: 2.5rem;
  }
}

@media (max-width: 768px) {
  :root {
    --space-8: 1.5rem;
    --space-12: 2rem;
    --space-16: 2.5rem;
    --space-20: 3rem;
  }

  .hero-title {
    font-size: 2rem;
    margin-bottom: var(--space-4);
  }
  
  .hero-subtitle {
    font-size: 1rem;
    margin-bottom: var(--space-6);
  }

  .btn-hero {
    padding: var(--space-3) var(--space-6);
    font-size: 1rem;
  }

  .feature-card, .login-card {
    padding: var(--space-8) var(--space-6);
  }

  .feature-icon {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
    margin-bottom: var(--space-6);
  }

  .stat-number {
    font-size: 2rem;
  }

  .theme-toggle {
    margin-left: var(--space-2);
    width: 50px;
    height: 25px;
  }

  .theme-slider:before {
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
  }

  .theme-toggle input:checked + .theme-slider:before {
    transform: translateX(25px);
  }

  .theme-icon {
    font-size: 10px;
  }

  .navbar-brand {
    font-size: 1.25rem;
  }

  .nav-link {
    padding: var(--space-2) var(--space-3) !important;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 1.75rem;
  }

  .btn-hero {
    width: 100%;
    margin-bottom: var(--space-3);
  }

  .feature-card, .login-card {
    padding: var(--space-6) var(--space-4);
  }

  .stat-item {
    padding: var(--space-4);
  }

  .footer {
    padding: var(--space-12) 0 var(--space-6);
  }
}

/* Dark Mode Navbar Toggler */
[data-theme="dark"] .navbar-toggler-icon {
  filter: invert(1);
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .feature-card, .login-card {
    border: 2px solid var(--text-primary);
  }
  
  .btn-hero, .btn-primary, .btn-success, .btn-info {
    border: 2px solid var(--text-primary);
  }
}

/* Focus States */
.btn:focus,
.nav-link:focus,
.theme-toggle:focus-within {
  outline: 2px solid var(--primary-500);
  outline-offset: 2px;
}

/* Loading Animation */
@keyframes shimmer {
  0% {
    background-position: -200px 0;
  }
  100% {
    background-position: calc(200px + 100%) 0;
  }
}

.loading-shimmer {
  background: linear-gradient(90deg, var(--neutral-200) 25%, var(--neutral-100) 50%, var(--neutral-200) 75%);
  background-size: 200px 100%;
  animation: shimmer 1.5s infinite;
}

/* Pulse Animation */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.pulse-animation {
  animation: pulse 2s ease-in-out infinite;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: var(--bg-secondary);
  border-radius: var(--radius-lg);
}

::-webkit-scrollbar-thumb {
  background: var(--gradient-primary);
  border-radius: var(--radius-lg);
}

::-webkit-scrollbar-thumb:hover {
  background: var(--primary-600);
}

/* Print Styles */
@media print {
  .navbar,
  .theme-toggle,
  .floating-elements {
    display: none !important;
  }
  
  .feature-card, .login-card {
    box-shadow: none !important;
    border: 1px solid var(--neutral-300) !important;
  }
  
  .hero-section {
    background: white !important;
    color: black !important;
  }
}

/* Additional Mobile Optimizations */
@media (max-width: 480px) {
  .container {
    padding-left: var(--space-4);
    padding-right: var(--space-4);
  }

  .hero-content {
    text-align: center;
  }

  .d-flex.gap-3 {
    flex-direction: column;
    gap: var(--space-3) !important;
  }

  .btn-hero {
    justify-content: center;
  }

  .row.g-4 {
    --bs-gutter-x: var(--space-4);
    --bs-gutter-y: var(--space-4);
  }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
  .btn-hero,
  .btn-primary,
  .btn-success,
  .btn-info {
    min-height: 44px;
    min-width: 44px;
  }

  .nav-link {
    min-height: 44px;
    display: flex;
    align-items: center;
  }

  .theme-toggle {
    min-height: 44px;
    min-width: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

/* Enhanced card animations */
.feature-card,
.login-card {
  opacity: 0;
  transform: translateY(30px);
  animation: fadeInUp 0.6s ease forwards;
}

.feature-card:nth-child(1) { animation-delay: 0.1s; }
.feature-card:nth-child(2) { animation-delay: 0.2s; }
.feature-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Improved navbar scroll behavior */
.navbar.scrolled {
  background: var(--bg-navbar) !important;
  box-shadow: var(--shadow-xl);
}

/* Enhanced theme transition */
* {
  transition: background-color var(--transition-normal), 
              color var(--transition-normal), 
              border-color var(--transition-normal);
}
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                AttendanceHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <label class="theme-toggle">
                            <input type="checkbox" id="themeToggle">
                            <span class="theme-slider"></span>
                            <i class="fas fa-sun theme-icon sun"></i>
                            <i class="fas fa-moon theme-icon moon"></i>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="floating-elements"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Smart Attendance Management for Modern Education</h1>
                        <p class="hero-subtitle">
                            Revolutionize your institution's attendance tracking with our comprehensive digital solution. 
                            Real-time analytics, automated notifications, and seamless integration.
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="#login" class="btn btn-light btn-hero">Get Started</a>
                            <a href="#features" class="btn btn-outline-light btn-hero">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-chalkboard-teacher" style="font-size: 20rem; opacity: 0.2; color: white;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-4 fw-bold mb-3">Comprehensive Features</h2>
                    <p class="lead text-muted">Everything you need for efficient attendance management</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon admin">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Admin Dashboard</h4>
                        <p class="text-muted">
                            Complete administrative control with faculty management, department oversight, 
                            analytics dashboard, and comprehensive system configuration.
                        </p>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-success me-2"></i>Faculty & Student Management</li>
                            <li><i class="fas fa-check text-success me-2"></i>Department Administration</li>
                            <li><i class="fas fa-check text-success me-2"></i>Analytics & Reports</li>
                            <li><i class="fas fa-check text-success me-2"></i>System Configuration</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon faculty">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Faculty Portal</h4>
                        <p class="text-muted">
                            Streamlined interface for educators with attendance marking, student management, 
                            bulk operations, and comprehensive reporting tools.
                        </p>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-success me-2"></i>Quick Attendance Marking</li>
                            <li><i class="fas fa-check text-success me-2"></i>Student List Management</li>
                            <li><i class="fas fa-check text-success me-2"></i>Bulk Upload & Operations</li>
                            <li><i class="fas fa-check text-success me-2"></i>Detailed Reports & Analytics</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon student">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Student Dashboard</h4>
                        <p class="text-muted">
                            Personalized portal for students with real-time attendance tracking, 
                            notifications, progress monitoring, and academic insights.
                        </p>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-success me-2"></i>Real-time Attendance View</li>
                            <li><i class="fas fa-check text-success me-2"></i>Automated Notifications</li>
                            <li><i class="fas fa-check text-success me-2"></i>Progress Tracking</li>
                            <li><i class="fas fa-check text-success me-2"></i>Subject-wise Analytics</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="50">0</span>
                        <div class="stat-label">Active Students</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="10">0</span>
                        <div class="stat-label">Faculty Members</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="5">0</span>
                        <div class="stat-label">Departments</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number" data-count="95">0</span>
                        <div class="stat-label">% Accuracy</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section id="login" class="login-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-4 fw-bold mb-3">Access Your Dashboard</h2>
                    <p class="lead text-muted">Choose your role to access the attendance management system</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="login-card text-center">
                        <div class="login-card-content">
                            <div class="feature-icon admin mb-4">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Admin Login</h4>
                            <p class="text-muted mb-4">
                                Complete system administration and management access
                            </p>
                            <a href="admin/login.php" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Admin Portal
                            </a>
                            <small class="text-muted">
                                <strong>Demo:</strong> admin@college.edu / password
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="login-card text-center">
                        <div class="login-card-content">
                            <div class="feature-icon faculty mb-4">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Faculty Login</h4>
                            <p class="text-muted mb-4">
                                Mark attendance and manage your classes efficiently
                            </p>
                            <a href="faculty/login.php" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Faculty Portal
                            </a>
                            <small class="text-muted">
                                <strong>Demo:</strong> sarah.johnson@college.edu / password
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="login-card text-center">
                        <div class="login-card-content">
                            <div class="feature-icon student mb-4">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Student Login</h4>
                            <p class="text-muted mb-4">
                                View your attendance records and receive notifications
                            </p>
                            <a href="student/login.php" class="btn btn-info btn-lg w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Student Portal
                            </a>
                            <small class="text-muted">
                                <strong>Demo:</strong> john.smith@student.edu / password
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="footer-logo">AttendanceHub</div>
                        <p class="text-muted">
                            Revolutionizing education with smart attendance management solutions 
                            for modern institutions.
                        </p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="fw-bold mb-3">Quick Links</h5>
                        <div class="footer-links">
                            <a href="#home" class="d-block mb-2">Home</a>
                            <a href="#features" class="d-block mb-2">Features</a>
                            <a href="#login" class="d-block mb-2">Login</a>
                            <a href="#contact" class="d-block mb-2">Contact</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="fw-bold mb-3">Portals</h5>
                        <div class="footer-links">
                            <a href="admin/login.php" class="d-block mb-2">Admin Portal</a>
                            <a href="faculty/login.php" class="d-block mb-2">Faculty Portal</a>
                            <a href="student/login.php" class="d-block mb-2">Student Portal</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="fw-bold mb-3">Contact Info</h5>
                        <div class="footer-links">
                            <p class="mb-2"><i class="fas fa-envelope me-2"></i>info@attendancehub.edu</p>
                            <p class="mb-2"><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</p>
                            <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>123 Education St, Learning City</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <p class="mb-0">&copy; 2024 AttendanceHub. All rights reserved. | Built for Educational Excellence</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme functionality
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        body.setAttribute('data-theme', currentTheme);

        // Set the toggle state based on current theme
        if (currentTheme === 'dark') {
            themeToggle.checked = true;
        }

        // Theme toggle event listener
        themeToggle.addEventListener('change', function() {
            if (this.checked) {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                body.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            const currentTheme = body.getAttribute('data-theme');
            
            if (window.scrollY > 50) {
                if (currentTheme === 'dark') {
                    navbar.style.background = 'rgba(26, 26, 26, 0.98)';
                } else {
                    navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                }
            } else {
                if (currentTheme === 'dark') {
                    navbar.style.background = 'rgba(26, 26, 26, 0.95)';
                } else {
                    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                }
            }
        });

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 16);
            });
        }

        // Intersection Observer for counter animation
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Add loading animation to cards
        const cards = document.querySelectorAll('.feature-card, .login-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });

        // Preload theme preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                body.setAttribute('data-theme', savedTheme);
                themeToggle.checked = savedTheme === 'dark';
            }
        });
    </script>
</body>
</html>
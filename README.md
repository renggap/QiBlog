# QiBlog: Modern Flat-File CMS with Enhanced UI/UX

A beautiful, secure, and modern flat-file CMS built with PHP featuring a comprehensive design system, dark mode support, and exceptional user experience.

![QiBlog Screenshot](https://via.placeholder.com/800x400/8B6914/FFFFFF?text=QiBlog+Modern+Interface)

## ✨ Features

### 🎨 **Modern UI/UX Design**
- **Comprehensive Design System**: Cohesive warm brown/cream color palette with modern typography
- **Dark Mode Support**: Automatic system preference detection with manual toggle
- **Responsive Design**: Mobile-first approach with fluid layouts
- **Smooth Animations**: Subtle fade-in effects and hover interactions
- **Component-Based Architecture**: Reusable UI components for consistency

### 🔧 **Core Functionality**
- **Flat-File Storage**: Posts stored as HTML files with YAML frontmatter
- **WYSIWYG Editor**: CKEditor 5 for rich content creation
- **Session-Based Authentication**: Secure admin login system
- **SEO Optimized**: Meta tags, structured data, XML sitemap, friendly URLs
- **Categories & Tags**: Organize content with flexible taxonomy

### 🚀 **Enhanced User Experience**
- **Reading Progress Indicator**: Visual progress bar for long posts
- **Social Sharing**: Built-in Twitter, Facebook, and copy-link sharing
- **Breadcrumb Navigation**: Easy site navigation
- **Statistics Dashboard**: Overview cards in admin panel
- **Quick Actions**: Streamlined workflow for content management

### 🔒 **Security & Performance**
- **CSRF Protection**: Comprehensive cross-site request forgery prevention
- **XSS Prevention**: Input sanitization and output escaping
- **Directory Traversal Protection**: Secure slug sanitization
- **HTTPS Enforcement**: Automatic secure connection redirect
- **Performance Optimized**: Efficient CSS architecture and minimal dependencies

### ♿ **Accessibility**
- **WCAG 2.1 AA Compliant**: Screen reader support and keyboard navigation
- **Skip Links**: Quick navigation for assistive technologies
- **High Contrast Support**: Enhanced visibility options
- **Focus Management**: Proper focus indicators and tab order
- **ARIA Labels**: Comprehensive accessibility markup

## 🎯 Quick Start

### Installation

1. **Prerequisites**
   ```bash
   PHP 7.4+ with file system permissions
   Web server (Apache/Nginx) with URL rewriting
   ```

2. **Setup**
   ```bash
   # Clone or download the project
   git clone https://github.com/renggap/QiBlog.git
   cd QiBlog

   # Set permissions
   chmod 755 posts/
   chmod 644 assets/css/*.css
   ```

3. **Configuration**
   ```bash
   # Set admin password (recommended)
   export ADMIN_PASSWORD="your-secure-password"

   # Or for Apache, add to .htaccess:
   SetEnv ADMIN_PASSWORD "your-secure-password"
   ```

4. **Launch**
   ```bash
   # Access your blog
   http://yourdomain.com/index.php

   # Admin panel
   http://yourdomain.com/admin/
   ```

## 📖 Usage Guide

### Admin Access
- Navigate to `/admin/` or `/admin/login.php`
- Login with your configured password
- Default development password: `admin123` (⚠️ Change for production!)

### Content Creation
1. **Create Posts**: Use the "Create New Post" button in the admin dashboard
2. **Rich Editor**: CKEditor 5 with full formatting capabilities
3. **Metadata**: Add categories, tags, and custom excerpts
4. **Preview**: Live preview functionality before publishing

### Content Management
- **Dashboard Overview**: Statistics cards showing post counts and recent activity
- **Post Management**: Card-based interface replacing traditional tables
- **Quick Actions**: Edit, view, and delete posts with intuitive controls
- **Search & Filter**: Advanced content organization tools

### Frontend Experience
- **Homepage**: Modern card-based layout with hover effects
- **Post View**: Enhanced reading experience with progress indicator
- **Navigation**: Breadcrumb navigation and smooth scrolling
- **Social Features**: Built-in sharing and engagement tools

## 📁 Project Structure

```
QiBlog/
├── index.php                    # Enhanced homepage with modern design
├── post.php                     # Redesigned post view with reading features
├── sitemap.php                  # XML sitemap generator
├── .htaccess                    # URL rewriting and security headers
│
├── admin/                       # Modernized admin panel
│   ├── dashboard.php           # Statistics dashboard with card layout
│   ├── create.php              # Enhanced post creation interface
│   ├── edit.php                # Improved post editing experience
│   ├── login.php               # Secure admin authentication
│   └── logout.php              # Session management
│
├── includes/                   # Core PHP functionality
│   ├── config.php              # Site configuration and settings
│   ├── functions.php           # Core functions and utilities
│   ├── auth.php                # Authentication system
│   └── Parsedown.php           # Markdown parsing (if needed)
│
├── posts/                      # Content storage
│   └── *.html                  # Post files with YAML frontmatter
│
└── assets/                     # Enhanced design system
    ├── css/
    │   ├── style.css           # Main stylesheet with legacy support
    │   │
    │   ├── core/               # Foundation files
    │   │   ├── variables.css   # Design tokens and CSS custom properties
    │   │   ├── reset.css       # Modern CSS reset
    │   │   └── typography.css  # Typography system
    │   │
    │   ├── components/         # Reusable UI components
    │   │   ├── buttons.css     # Button variants and states
    │   │   ├── cards.css       # Card components and layouts
    │   │   ├── navigation.css  # Navigation and breadcrumbs
    │   │   └── forms.css       # Form elements and inputs
    │   │
    │   ├── utilities/          # Utility classes
    │   │   ├── animations.css  # Animation and transition utilities
    │   │   ├── responsive.css  # Responsive design helpers
    │   │   └── accessibility.css # A11y enhancement utilities
    │   │
    │   └── themes/             # Theme system
    │       ├── light.css       # Light theme styles
    │       └── dark.css        # Dark theme styles
    │
    ├── js/                     # JavaScript files
    │   ├── *.min.js            # Minified JavaScript assets
    │   └── *.js                # Development scripts
    │
    ├── fonts/                  # Web fonts
    │   └── *.woff2             # Optimized font files
    │
    └── images/                # Image assets
        └── *.jpg, *.png, *.svg # Optimized images
```

## 🎨 Design System

### Color Palette
- **Primary**: `#8B6914` (Warm golden brown)
- **Accent**: `#D4AF37` (Gold accent)
- **Background**: `#F8F4E1` (Warm cream)
- **Text**: `#543310` (Deep brown)

### Typography
- **Primary Font**: Manrope (Google Fonts)
- **Heading Font**: Manrope with serif fallback
- **Monospace**: Fira Code for code blocks

### Components
- **Buttons**: Multiple variants (primary, secondary, accent, ghost)
- **Cards**: Post cards, admin cards, statistics cards
- **Navigation**: Responsive navigation with breadcrumbs
- **Forms**: Enhanced form elements with validation states

## 🔧 Configuration

### Environment Variables
```bash
# Admin password (required for production)
ADMIN_PASSWORD="your-secure-password"

# Site configuration
SITE_TITLE="Your Blog Name"
SITE_URL="https://yourdomain.com"
TIMEZONE="Asia/Jakarta"
```

### File Permissions
```bash
# Posts directory must be writable
chmod 755 posts/

# Assets should be readable
chmod 644 assets/css/*.css
chmod 644 assets/js/*.js
```

## 🚀 Advanced Features

### Theme System
- **Automatic Detection**: Respects user's system preference
- **Manual Toggle**: Theme switcher in navigation
- **Persistent Choice**: Remembers user preference
- **Smooth Transitions**: Seamless theme switching

### Performance Optimizations
- **CSS Architecture**: Modular CSS with efficient selectors
- **Font Loading**: Optimized web font loading
- **Image Optimization**: Responsive images with proper sizing
- **Animation Performance**: GPU-accelerated animations

### Security Enhancements
- **Content Security Policy**: Comprehensive CSP headers
- **HTTPS Enforcement**: Automatic secure redirects
- **Input Validation**: Strict sanitization of all inputs
- **Session Security**: Secure session management

## 🔍 SEO & Social

### Meta Tags
- Dynamic title and description generation
- Open Graph tags for social sharing
- Twitter Card support
- Canonical URLs

### Structured Data
- JSON-LD Article schema
- Breadcrumb navigation markup
- Search engine optimization

### Social Integration
- Twitter sharing with custom text
- Facebook sharing with previews
- Copy-to-clipboard functionality
- Social media meta tags

## 📱 Responsive Design

### Breakpoints
- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

### Mobile Features
- Touch-friendly interactions
- Optimized navigation
- Readable typography
- Efficient layouts

## ♿ Accessibility Features

### WCAG 2.1 AA Compliance
- Proper heading hierarchy
- Alt text for images
- Keyboard navigation
- Focus management

### Assistive Technology Support
- Screen reader compatibility
- High contrast mode
- Reduced motion support
- Skip navigation links

## 🔧 Development

### CSS Architecture
The project uses a modern CSS architecture with:
- **CSS Custom Properties** for theming
- **Component-based organization**
- **Utility-first approach** for rapid development
- **BEM-inspired naming** for clarity

### Customization
```css
/* Override design tokens in your custom CSS */
:root {
  --color-primary: #your-color;
  --font-family-primary: 'Your Font', sans-serif;
  --space-md: 1.5rem;
}
```

## 📈 Performance

### Optimization Features
- **Minimal Dependencies**: Only essential external resources
- **Efficient CSS**: Optimized selectors and minimal redundancy
- **Font Display**: Swap for faster text rendering
- **Image Optimization**: Proper sizing and modern formats

### Best Practices
- Semantic HTML structure
- Progressive enhancement
- Mobile-first CSS
- Accessible by default

## 🤝 Contributing

### Development Setup
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

### Code Style
- Use consistent indentation (2 spaces)
- Follow BEM naming conventions
- Write semantic HTML
- Maintain accessibility standards

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🆘 Support

### Getting Help
- **Documentation**: Comprehensive README and inline comments
- **Issues**: GitHub issue tracker for bug reports
- **Discussions**: GitHub discussions for questions and ideas

### Troubleshooting
- Check PHP error logs
- Verify file permissions
- Ensure HTTPS is configured
- Test with different browsers

---

**QiBlog** - A modern, beautiful, and secure flat-file CMS that prioritizes user experience and developer happiness. Built with ❤️ and attention to detail.

# AGENTS.md - Development Guidelines for Service Marketplace

This document provides comprehensive guidelines for agentic coding agents working on this PHP-based service marketplace with affiliate monetization features.

## Project Overview

This is a freelance money-maker platform that displays tools and services for freelancers, with affiliate link tracking, ad placements, and analytics. The project uses:
- **Backend**: PHP 8+ with PDO/MySQL
- **Frontend**: HTML5, Tailwind CSS (via CDN), Vanilla JavaScript
- **Database**: MySQL with affiliate tracking and analytics
- **Architecture**: MVC-like structure with API endpoints

## Development Commands

### Testing Commands
```bash
# Run comprehensive system tests
php includes/testing.php

# Test specific functionality
php -c php.ini -r "require_once 'includes/testing.php'; \$test = new TestRunner(); \$test->testDatabaseConnection();"

# Performance testing
ab -n 100 -c 10 http://localhost/service-marketplace/api/listings.php
```

### Database Management
```bash
# Create tables (run once)
mysql -u root -p niche_site_db < database_setup.sql

# Check database connection
php -r "require_once 'db_connect.php'; echo 'Database connected successfully';"
```

### Security Testing
```bash
# Test affiliate redirect functionality
php -r "require_once 'includes/testing.php'; echo json_encode(testAffiliateRedirect(1));"

# Check error handling
php -r "require_once 'includes/error_handling.php'; trigger_error('Test error', E_USER_WARNING);"
```

## Code Style Guidelines

### PHP Standards
- **PHP Version**: 8.0+ compatible
- **Encoding**: UTF-8 without BOM
- **Indentation**: 4 spaces (no tabs)
- **Line Length**: Maximum 120 characters
- **Error Reporting**: Use provided error handling system, never expose errors in production

### File Organization
```
/                     # Root - main application (index.php)
/api/                 # API endpoints (JSON responses)
/admin/               # Admin panel interface
/includes/            # Core utilities and classes
```

### Naming Conventions
- **Files**: `snake_case.php` (e.g., `affiliate_manager.php`)
- **Classes**: `PascalCase` (e.g., `TestRunner`, `AffiliateManager`)
- **Functions**: `camelCase` (e.g., `executeQuery`, `sanitize`)
- **Variables**: `camelCase` (e.g., `$listingId`, `$affiliateManager`)
- **Database Columns**: `snake_case` (e.g., `created_at`, `affiliate_link`)
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `DB_HOST`, `DB_NAME`)

### PHP Code Patterns

#### Database Operations
```php
// Always use prepared statements via executeQuery()
$stmt = executeQuery("SELECT * FROM listings WHERE status = ?", ['active']);
$listings = $stmt->fetchAll();

// For single records
$listing = $stmt->fetch();

// For insert/update operations
$stmt = executeQuery(
    "INSERT INTO analytics (listing_id, ip_address) VALUES (?, ?)",
    [$listingId, $ipAddress]
);
$insertId = $pdo->lastInsertId();
```

#### Error Handling
```php
try {
    $result = riskyOperation();
} catch (Exception $e) {
    logMessage("Operation failed: " . $e->getMessage(), 'ERROR');
    if ($_SERVER['ENVIRONMENT'] !== 'production') {
        // Show error in development
        echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
    return false;
}
```

#### Security Patterns
```php
// Always sanitize user input
$title = sanitize($_POST['title']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

// Session management for admin
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}

// SQL injection prevention - never concatenate variables in SQL
// WRONG: "SELECT * FROM listings WHERE id = $id"
// RIGHT: "SELECT * FROM listings WHERE id = ?", [$id]
```

### API Endpoints Standards

#### Response Format
```php
<?php
header('Content-Type: application/json');

// Add caching for static data
header('Cache-Control: public, max-age=300');

try {
    $data = fetchData();
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed']);
}
?>
```

#### Standard API Responses
- **Success**: `200 OK` with JSON data
- **Not Found**: `404 Not Found` with `{"error": "Resource not found"}`
- **Server Error**: `500 Internal Server Error` with `{"error": "Descriptive message"}`

### Frontend Standards

#### JavaScript Patterns
```javascript
// Use modern ES6+ features
const API_URL = 'api/';

// Async/await for API calls
async function loadListings() {
    try {
        const response = await fetch(`${API_URL}listings.php`);
        const listings = await response.json();
        return listings;
    } catch (error) {
        console.error('Failed to load listings:', error);
        return [];
    }
}

// Event delegation for dynamic content
document.addEventListener('click', (e) => {
    if (e.target.matches('.affiliate-link')) {
        trackClick(e.target.dataset.id);
    }
});
```

#### Tailwind CSS Usage
- Use utility classes consistently
- Prefer semantic color variables: `text-purple-600`, `bg-purple-100`
- Responsive design: `md:grid-cols-2`, `lg:grid-cols-3`
- State classes: `hover:bg-purple-700`, `focus:ring-2`, `transition duration-300`

### Database Schema Guidelines

#### Table Naming
- Plural nouns: `listings`, `analytics`, `affiliate_networks`
- Consistent columns: `id` (primary key), `created_at`, `updated_at`
- Status columns use `'active'`/`'inactive'` strings

#### Index Strategy
```sql
-- Primary indexes on foreign keys
CREATE INDEX idx_analytics_listing_id ON analytics(listing_id);

-- Composite indexes for common queries
CREATE INDEX idx_listings_status_created ON listings(status, created_at);
```

## Security Requirements

### Input Validation
- All user input must be sanitized using the `sanitize()` function
- Email validation with `filter_var()` and `FILTER_VALIDATE_EMAIL`
- URL validation with `filter_var()` and `FILTER_VALIDATE_URL`
- Numeric validation with `is_numeric()` or `ctype_digit()`

### SQL Injection Prevention
- **NEVER** use string concatenation in SQL queries
- **ALWAYS** use prepared statements via `executeQuery()`
- Parameter binding is mandatory for all dynamic values

### XSS Prevention
- Use `htmlspecialchars()` for all output to HTML
- Use the provided `sanitize()` function for user input
- JSON responses should use `json_encode()` with default options

### Session Security
- Admin authentication uses `$_SESSION['admin_logged_in']`
- Session timeout should be implemented in production
- Use `session_regenerate_id()` on login for enhanced security

## Performance Guidelines

### Caching Strategy
- API endpoints should include appropriate `Cache-Control` headers
- Static listings: 10-minute cache (`max-age=600`)
- Statistics: 5-minute cache (`max-age=300`)
- Real-time data (analytics): no caching

### Database Optimization
- Use `LIMIT` clauses for large datasets
- Implement pagination for admin interfaces
- Use indexes on frequently queried columns
- Consider lazy loading for images

### Frontend Performance
- Use lazy loading for images with `IntersectionObserver`
- Implement debounced search (300ms delay)
- Use passive event listeners where appropriate
- Minimize DOM manipulations

## Testing Strategy

### Built-in Testing Suite
- Access via `/includes/testing.php`
- Tests database connectivity, CRUD operations, affiliate tracking, security, and performance
- Run tests before deploying changes

### Manual Testing Checklist
- [ ] Admin login/logout works
- [ ] Listings display correctly
- [ ] Affiliate links track clicks
- [ ] Analytics update properly
- [ ] Ad placements show in correct positions
- [ ] Forms validate input properly
- [ ] Error handling works gracefully

## Common Development Tasks

### Adding New API Endpoint
1. Create PHP file in `/api/` directory
2. Include `db_connect.php` at top
3. Set `Content-Type: application/json` header
4. Use try/catch for error handling
5. Return JSON responses with consistent format

### Adding Admin Page
1. Create PHP file in `/admin/` directory
2. Include session check at top
3. Use consistent HTML structure with dashboard
4. Include navigation and header
5. Use Tailwind CSS for styling

### Database Schema Changes
1. Write SQL migration script
2. Test on development environment
3. Update relevant PHP models/functions
4. Run built-in tests to verify compatibility

This AGENTS.md file should be kept updated with any changes to project structure, coding standards, or development workflows.
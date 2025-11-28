# Aunt Joy's Restaurant - Development Guide

## Project Overview
Web-based food ordering platform for Aunt Joy's Restaurant in Mzuzu.

**Tech Stack**: Object-oriented PHP, MySQL, HTML, CSS, JavaScript

## Technical Requirements

### Mandatory Technologies
- **Backend**: Object-oriented PHP (OOP paradigm required)
- **Database**: MySQL (normalized relational database)
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: XAMPP (Apache + MySQL)
- **Location**: C:\xampp\htdocs\auntjoys_app

### Architecture Pattern
- **MVC (Model-View-Controller)** structure
- Object-oriented programming throughout
- Prepared statements for all database queries
- Session-based authentication

## Project Structure
```
auntjoys_app/
├── config/
│   ├── database.php          # DB connection class
│   └── constants.php          # App-wide constants
├── models/
│   ├── User.php              # User model with RBAC
│   ├── Meal.php              # Meal model
│   ├── Category.php          # Category model
│   ├── Order.php             # Order model
│   └── OrderItem.php         # Order items model
├── controllers/
│   ├── AuthController.php    # Login/Register/Logout
│   ├── CustomerController.php
│   ├── AdminController.php
│   ├── SalesController.php
│   └── ManagerController.php
├── views/
│   ├── customer/
│   ├── admin/
│   ├── sales/
│   ├── manager/
│   └── auth/
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── auth_check.php        # Role-based access control
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── uploads/                   # Meal images
└── index.php                 # Entry point
```

## Database Schema (Normalized)

### Existing Tables (Already Created)
1. **Roles** (role_id, role_name)
   - role_id=1: Customer (DEFAULT)
   - role_id=2: Administrator
   - role_id=3: Sales Staff
   - role_id=4: Manager

2. **Users** (user_id, username, password_hash, email, phone_number, role_id)
   - Default role_id = 1 (Customer)
   - FK: role_id → Roles(role_id)

3. **Categories** (category_id, category_name, description)

4. **Meals** (meal_id, name, description, price, image_path, category_id, is_available)
   - is_available: 0=Out of Stock, 1=In Stock
   - FK: category_id → Categories(category_id)

5. **Orders** (order_id, user_id, order_date, delivery_address, contact_number, total_amount, status)
   - status: ENUM('Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled')
   - Default status: 'Pending'
   - FK: user_id → Users(user_id)

6. **Order_Items** (order_item_id, order_id, meal_id, quantity, unit_price)
   - unit_price: Price at time of order (for historical accuracy)
   - FK: order_id → Orders(order_id)
   - FK: meal_id → Meals(meal_id)
   - UNIQUE constraint: (order_id, meal_id)

### Important Notes
- Use **role_id** not "role" in Users table
- Use **meal_id**, **user_id**, **order_id** naming convention
- Status values: 'Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'
- Default role for new registrations is Customer (role_id=1)

## User Roles & Permissions

### Customer
- Browse menu with search and filters
- Add to cart, adjust quantities
- Submit orders with delivery details
- View own order history

### Administrator
- Full CRUD on meals (name, description, price, image, category)
- Manage meal availability (in stock/out of stock)
- Create users and assign roles
- Manage categories

### Sales Personnel
- View real-time order list
- Update order status: Preparing → Out for Delivery → Delivered
- View customer and order details

### Manager
- Generate sales reports (filter by month/year)
- View metrics: total revenue, order count, best-sellers
- Export reports to PDF and Excel formats
- Access all dashboard data

## Security Requirements

### Authentication
- Password hashing using `password_hash()` (bcrypt)
- Session-based authentication
- Role-based access control (RBAC) on all routes
- Logout functionality to destroy sessions

### Data Validation
- **Client-side**: JavaScript validation on all forms
- **Server-side**: PHP validation before database operations
- Sanitize all user inputs
- Use prepared statements (PDO or mysqli) to prevent SQL injection
- Protect against XSS attacks (htmlspecialchars on output)

### Access Control
```php
// Every protected page should check:
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'expected_role') {
    header('Location: login.php');
    exit;
}
```

## Feature Implementation Priority

### Phase 1 (Critical Path)
1. Database schema + migrations
2. User authentication system
3. RBAC implementation
4. Meal CRUD (Admin)
5. Customer menu browsing

### Phase 2 (Core Features)
6. Shopping cart functionality
7. Order submission and storage
8. Sales order management
9. Order status workflow

### Phase 3 (Advanced)
10. Manager reporting dashboard
11. PDF export (TCPDF/FPDF)
12. Excel export (PhpSpreadsheet)
13. Search and filtering

## Code Standards

### PHP Classes (OOP Required)
```php
class Meal {
    private $conn;
    private $table = "meals";
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $is_available;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() { /* ... */ }
    public function read() { /* ... */ }
    public function update() { /* ... */ }
    public function delete() { /* ... */ }
}
```

### Database Queries (Always Use Prepared Statements)
```php
$stmt = $this->conn->prepare("SELECT * FROM meals WHERE id = ?");
$stmt->bind_param("i", $this->id);
$stmt->execute();
```

### Form Validation Example
```php
// Server-side
if (empty($_POST['meal_name']) || strlen($_POST['meal_name']) < 3) {
    $errors[] = "Meal name must be at least 3 characters";
}
if (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
    $errors[] = "Price must be a positive number";
}
```

## Libraries & Tools

### Recommended
- **Bootstrap 5**: Rapid UI development
- **jQuery**: DOM manipulation (optional)
- **TCPDF** or **FPDF**: PDF generation
- **PhpSpreadsheet**: Excel export
- **Font Awesome**: Icons

### File Upload Handling
```php
// For meal images
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$max_size = 2 * 1024 * 1024; // 2MB
// Validate extension, size, move to uploads/
```

## Development Workflow

### Daily Routine
1. Pull latest changes from Git
2. Work on assigned feature branch
3. Test locally on XAMPP
4. Commit with clear messages
5. Push and create PR for review

### Testing Checklist
- [ ] All forms validate correctly
- [ ] RBAC prevents unauthorized access
- [ ] SQL injection attempts fail
- [ ] XSS attempts are sanitized
- [ ] File uploads work and are secure
- [ ] Reports generate correctly
- [ ] Exports (PDF/Excel) work
- [ ] Mobile responsive design

## Quick Commands (Windows PowerShell)

### Start XAMPP
```powershell
# Start Apache and MySQL from XAMPP Control Panel
# Or via command line:
C:\xampp\xampp-control.exe
```

### Database Access
```powershell
# MySQL command line
C:\xampp\mysql\bin\mysql.exe -u root -p
```

### Project Access
- Local URL: `http://localhost/auntjoys_app/`
- phpMyAdmin: `http://localhost/phpmyadmin/`

## Report Requirements

### Sales Report Metrics
- Date range: Month and Year selection
- Total revenue for period
- Total number of orders
- Best-selling items (top 5-10)
- Orders by status breakdown

### Export Formats
- **PDF**: Formatted report with headers, tables, totals
- **Excel**: Raw data in spreadsheet format with formulas

## Submission Requirements
1. Complete source code
2. Database export (.sql file)
3. README with setup instructions
4. Documentation of features
5. Test user credentials for each role

## Notes for AI Assistant
- Always use OOP PHP patterns
- Prioritize security (prepared statements, validation, RBAC)
- Follow MVC structure strictly
- Use Bootstrap for rapid development
- Test all features before marking complete
- Keep code DRY (Don't Repeat Yourself)
- Comment complex logic
- Handle errors gracefully with user-friendly messages

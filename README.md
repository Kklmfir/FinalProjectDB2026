# Financial Management Dashboard

A modern, premium financial management web application built with PHP Native, MySQL, and modern UI/UX inspired by top Indonesian fintech companies like Bank Jago, GoPay, and Jenius.

## 🚀 Features

### Core Features
- **Dashboard Overview** - Comprehensive financial dashboard with charts and analytics
- **Pocket Management** - Organize money into different pockets
- **Category & Sub-Category** - Hierarchical transaction categorization
- **Transaction Tracking** - Income and expense management
- **Transfer System** - Money transfers between pockets
- **Budget Management** - Set and track spending budgets
- **Goal Setting** - Financial goal tracking with progress bars
- **Contact Management** - Manage debtors and creditors
- **Debt & Loan Tracking** - Monitor loans and debts

### Technical Features
- **Dual Database Support** - MySQL (primary) + Supabase PostgreSQL (optional)
- **Modern UI/UX** - Bootstrap 5 + Custom CSS with fintech-inspired design
- **Interactive Charts** - Chart.js integration for data visualization
- **DataTables** - Advanced table management with search, sort, pagination
- **Security** - PDO prepared statements, CSRF protection, input validation
- **Responsive Design** - Mobile-first approach
- **Environment Configuration** - .env file support

## 🛠 Tech Stack

- **Backend**: PHP 7.4+ (Native)
- **Database**: MySQL 5.7+ (Primary) / PostgreSQL (Secondary via Supabase)
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **UI Framework**: Bootstrap 5.3
- **Charts**: Chart.js
- **Tables**: DataTables
- **Icons**: Font Awesome 6
- **Security**: PDO, CSRF Protection, Input Sanitization

## 📁 Project Structure

```
final-project-db2026/
│
├── main.php                          # Main dashboard
├── .env                              # Environment configuration
├── .gitignore                        # Git ignore rules
│
├── /config/
│   ├── database.php                  # Database connection handler
│   ├── app.php                       # Application configuration
│   ├── mysql.php                     # MySQL specific config
│   └── supabase.php                  # Supabase config
│
├── /components/
│   ├── header.php                    # HTML head and navigation
│   ├── sidebar.php                   # Sidebar navigation
│   ├── navbar.php                    # Top navigation bar
│   ├── footer.php                    # Page footer
│   └── alerts.php                    # Alert messages
│
├── /assets/
│   ├── /css/
│   │   └── style.css                 # Custom styles
│   ├── /js/
│   │   └── app.js                   # JavaScript functionality
│   ├── /img/                        # Images
│   └── /icons/                      # Icons
│
├── /helpers/
│   ├── functions.php                 # Utility functions
│   ├── validation.php                # Input validation
│   └── security.php                  # Security functions
│
└── /src/
    ├── /budget/                      # Budget CRUD
    ├── /category/                    # Category CRUD
    ├── /contact/                     # Contact CRUD
    ├── /debt_loan/                   # Debt & Loan CRUD
    ├── /goal/                        # Goal CRUD
    ├── /pocket/                      # Pocket CRUD
    ├── /sub_category/                # Sub Category CRUD
    ├── /transactions/                # Transactions CRUD
    └── /transfer/                    # Transfer CRUD
```

## 🗄 Database Schema

### Tables
- `budget` - Budget management
- `category` - Transaction categories
- `contact` - Contacts (debtors/creditors)
- `debt_loan` - Debt and loan tracking
- `goal` - Financial goals
- `pocket` - Money pockets
- `sub_category` - Sub-categories
- `transactions` - Income/expense transactions
- `transfer` - Money transfers

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or XAMPP)
- Composer (optional)
- Web server (Apache/Nginx)

### Step 1: Database Setup
1. Import `final-project-db2026.sql` into your MySQL database
2. Run `insert-data.sql` to populate sample data

### Step 2: Environment Configuration
1. Copy `.env` file and configure:
```env
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql

MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_DATABASE=final-project-db2026
MYSQL_USERNAME=root
MYSQL_PASSWORD=
```

### Step 3: Web Server Setup
- Place project in web root (e.g., `htdocs/` for XAMPP)
- Access via `http://localhost/FinalProjectDB2026/`

### Step 4: Initial Access
- Open `main.php` in browser
- Navigate through sidebar menu
- Start managing your finances!

## 🔧 Configuration

### Database Connection
The app supports dual database modes:

**MySQL (Default):**
- Local database storage
- Full control over data
- Offline capability

**Supabase (Optional):**
- Cloud synchronization
- Real-time features
- Cross-device access

Switch modes in `.env`:
```env
DB_CONNECTION=mysql    # or 'supabase'
```

## 📊 CRUD Operations

Each module follows the same structure:

### Available Operations
- **Index** - List with search, filter, sort, pagination
- **Create** - Add new records with validation
- **Edit** - Update existing records
- **Delete** - Safe deletion with confirmation
- **View** - Detailed record view with related data

### Example: Category Module
```
src/category/
├── index.php      # List all categories
├── create.php     # Add new category
├── edit.php       # Edit category
├── delete.php     # Delete category
└── view.php       # View category details
```

## 🎨 UI/UX Design

### Color Scheme
- **Primary**: Emerald Green (#10b981)
- **Secondary**: Light Gray (#f8fafc)
- **Accent**: Blue (#3b82f6)
- **Gold**: Gold (#fbbf24)

### Design Principles
- **Fintech Inspired** - Modern banking app aesthetics
- **Mobile First** - Responsive design
- **Clean Typography** - Inter font family
- **Card-Based Layout** - Organized information display
- **Gradient Accents** - Premium visual effects

## 🔒 Security Features

- **Prepared Statements** - SQL injection prevention
- **CSRF Protection** - Cross-site request forgery prevention
- **Input Validation** - Server-side validation
- **XSS Prevention** - Output sanitization
- **Secure Configuration** - Environment-based config

## 📈 Dashboard Analytics

### Charts & Visualizations
- Monthly transaction trends (Line chart)
- Expense categories (Pie chart)
- Goal progress bars
- Budget usage indicators
- Pocket allocation cards

### Key Metrics
- Total balance
- Monthly income/expense
- Budget utilization
- Goal achievement
- Active loans/debts

## 🧪 Testing

### Manual Testing Checklist
- [ ] Database connection
- [ ] CRUD operations for all modules
- [ ] Form validation
- [ ] Search and filtering
- [ ] Pagination
- [ ] Responsive design
- [ ] Chart rendering
- [ ] File uploads (if any)

### Sample Data
Use `insert-data.sql` for testing with sample financial data.

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set secure file permissions
- [ ] Enable HTTPS
- [ ] Configure backup system

### Performance Optimization
- Database query optimization
- Image compression
- CSS/JS minification
- Caching implementation

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- Inspired by Indonesian fintech innovations
- Built for university final project
- Modern web development best practices
- Open source community contributions

---

**Note**: This is a comprehensive financial management system designed as a university final project. It demonstrates modern PHP development, database design, and fintech UI/UX principles.

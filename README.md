# BAdiscount - Symfony Online Store

<div align="center">
  <h3>A modern and high-performance e-commerce platform built with Symfony</h3>
  <p><em>Admin interface fully localized in French</em></p>
</div>

## 📋 Overview

BAdiscount is a complete e-commerce application for clothing stores. The platform offers an intuitive shopping experience for customers and a comprehensive management system localized in French for administrators.

Designed with a robust architecture using the Symfony framework, this solution ensures performance, security, and scalability for growing businesses.

### ✨ Online Demo

[View the demo](https://badiscount.example.com) (Coming soon)

## 🚀 Features

### Customer Interface
- 👕 Product navigation by category with advanced filters
- 🔍 High-performance search with suggestions and spell correction
- 🛒 Intuitive shopping cart management with real-time updates
- 📱 Responsive design optimized for all devices
- 💳 Secure payment process with order tracking
- 👤 Complete user account management (history, favorites, preferences)

### Admin Interface (Fully in French)
- 📊 Analytical dashboard with key metrics and charts
- 📈 Detailed sales statistics with period and status filtering
  - Analysis of best-selling products
  - Monthly sales evolution
  - Graphical visualization of business performance
- 📑 PDF receipt generation and download for each order
- 📦 Complete product management (CRUD) with multi-image support
- 🚚 Order processing workflow
- 💰 Revenue tracking and financial analysis
- 🔔 Order status updates (Pending, Completed, Canceled)

## 🛠️ Technologies Used

- **Backend:** 
  - Symfony 6.3+
  - PHP 8.1+
  - Doctrine ORM
  - Dependency injection services
  - Migration system

- **Database:** 
  - MySQL 8.0
  - Query optimizations for performance

- **Frontend:** 
  - Twig with template inheritance
  - JavaScript ES6+
  - Bootstrap 5
  - Compiled SCSS
  - Mobile-first responsive system

- **Data Visualization:**
  - Chart.js for interactive charts
  - DataTables for dynamic tables

- **PDF:**
  - PDF generation system for order receipts

- **Dependencies:** 
  - Composer for PHP package management
  - npm/Webpack for asset compilation

- **Additional Libraries:** 
  - DataTables with localization support
  - Font Awesome
  - Select2 for advanced dropdown lists

## 💻 Screenshots

<div align="center">
  <h3>Customer Interface</h3>
  <img src="screenshots/front-home.png" alt="Front Homepage" width="400"/>
  <p>Modern homepage with featured products</p>
  
  <img src="screenshots/shopping-cart.png" alt="Shopping Cart" width="400"/>
  <p>Shopping cart with product management</p>
  
  <h3>Admin Interface</h3>
  <img src="screenshots/admin-dashboard.png" alt="Admin Dashboard" width="400"/>
  <p>Admin dashboard with key metrics</p>
  
  <img src="screenshots/admin-orders.png" alt="Order Management" width="400"/>
  <p>Order management with status tracking</p>
</div>

## 🔧 Installation

### Prerequisites
- PHP 8.0 or higher
- Composer
- MySQL 8.0
- Symfony CLI (optional, but recommended)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/Abderraouf-Mahmoudi/symfony-shop.git
   cd symfony-shop
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure the .env file**
   ```
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/clothing_shop"
   ```

4. **Create the database**
   ```bash
   php bin/console doctrine:database:create
   ```

5. **Run migrations**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. **Load fixtures (sample data)**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

7. **Initialize settings**
   ```bash
   php bin/console app:initialize-settings
   ```

8. **Start the development server**
   ```bash
   symfony serve
   # or
   php bin/console server:start
   ```

9. **Access the application**
   - Customer interface: `http://localhost:8000`
   - Admin interface: `http://localhost:8000/admin`
   - Default admin credentials: 
     - Email: `admin@example.com`
     - Password: `admin123`

## 📁 Project Structure

```
badiscount/
├── bin/                      # Symfony console commands
├── config/                   # Application configuration
│   ├── packages/             # Package configuration
│   ├── routes/               # Route definitions
│   └── services.yaml         # Services configuration
├── migrations/               # Database migrations
├── public/                   # Public assets and entry point
│   ├── css/                  # Compiled CSS/SCSS files
│   ├── js/                   # JavaScript files
│   ├── img/                  # Images
│   └── uploads/              # User uploads (products)
├── src/
│   ├── Command/              # Custom commands
│   ├── Controller/           # Controllers
│   │   ├── BackController.php        # Admin dashboard
│   │   ├── FrontController.php       # Customer interface
│   │   ├── OrderController.php       # Order management
│   │   └── StatisticsController.php  # Sales statistics
│   ├── Entity/               # Database entities
│   ├── Form/                 # Forms
│   ├── Repository/           # Data repositories
│   ├── Service/              # Business services
│   │   ├── StatisticsService.php     # Statistics service
│   │   └── PdfService.php            # PDF generation
│   └── Security/             # Authentication and security
├── templates/                # Twig templates
│   ├── back/                 # Admin templates (French)
│   │   ├── dashboard/        # Dashboard
│   │   ├── products/         # Product management
│   │   ├── orders/           # Order management
│   │   └── statistics/       # Statistics pages
│   ├── front/                # Customer templates
│   ├── base.html.twig        # Base admin template
│   └── base_front.html.twig  # Base customer template
└── ...
```

## 🔐 Admin Features

The admin interface, fully localized in French, offers comprehensive management capabilities:

- **Dashboard**: Visualization of key metrics such as pending orders, revenue, and number of products

- **Product Management**: 
  - Add, modify, delete and view products
  - Multiple image management with main image selection
  - Variant management (sizes, colors)
  - Organization by categories and collections

- **Order Management**: 
  - Order processing with status updates (Pending, Completed, Canceled)
  - Detailed view of customer orders
  - Generation and download of PDF receipts for each order
  - Advanced order filtering

- **Sales Statistics**:
  - Analysis of best-selling products with status filtering
  - Monthly sales evolution in number of orders and revenue
  - Interactive graphical visualizations with Chart.js
  - Export of statistical data

- **Revenue Tracking**: 
  - Revenue tracking by period (daily, monthly, yearly)
  - Summary dashboards
  - Financial performance analysis

## 🔄 Workflow

1. **Customer Journey**:
   - Navigation and product search by category/filters
   - Adding items to cart with variant selection (size, color)
   - Cart management (quantity, removal)

2. **Order Process**:
   - Order form collecting shipping and payment information
   - Order validation and confirmation
   - Creation of order with "Pending" status
   - Automatic confirmation email

3. **Administrative Management**:
   - Administrator views new orders via the dashboard
   - Processing and updating order status
   - Generation of PDF receipts for validated orders
   - Orders marked as "Completed" contribute to total revenue

4. **Analysis and Optimization**:
   - Regular review of sales statistics
   - Identification of top-performing products
   - Adjustment of inventory and business strategies
   - Tracking of monthly sales evolution

## 🤝 Contribution

Contributions are welcome! Feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/awesome-feature`)
3. Commit your changes with descriptive messages (`git commit -m 'Add awesome feature'`)
4. Push to the branch (`git push origin feature/awesome-feature`)
5. Open a Pull Request with a detailed description of your changes

### Contribution Guidelines

- Follow PSR-12 coding standards
- Make sure all tests pass before submitting your PR
- Clearly document new features
- For major features, discuss via an Issue first

## ⚙️ Technical Requirements

- PHP 8.1 or higher
- MySQL 8.0 or MariaDB 10.5+
- Composer 2.0+
- Node.js 14+ and npm for asset compilation
- PHP Extensions: intl, mbstring, xml, mysql, gd
- Compatible web server (Apache, Nginx)

## 📞 Contact

For any questions or feedback, please feel free to contact us:
- Email: abderraouf.mahmoudi2001@gmail.com
- GitHub: [Abderraouf Mahmoudi](https://github.com/Abderraouf-Mahmoudi)
- LinkedIn: [Abderraouf Mahmoudi](https://www.linkedin.com/in/abderraouf-mahmoudi-83b76321b/)

---

<div align="center">
  <p>BAdiscount - Professional e-commerce solution developed with Symfony</p>
  <p>© 2025 Abderraouf Mahmoudi. All rights reserved.</p>
</div>

# Symfony Clothing Shop

<div align="center">
  <h3>A modern e-commerce platform built with Symfony</h3>
</div>

## 📋 Overview

Symfony Clothing Shop is a full-featured e-commerce application for clothing stores. The platform provides an intuitive shopping experience for customers and a comprehensive management system for administrators.

### ✨ Live Demo

[View Live Demo](https://symfony-shop.example.com) (Coming Soon)

## 🚀 Features

### Customer Features
- 👕 Browse clothing products by category
- 🔍 Search functionality
- 🛒 Shopping cart management
- 📱 Responsive design for mobile and desktop
- 💳 Checkout process with order tracking
- 👤 User account management

### Admin Features
- 📊 Dashboard with sales analytics
- 📦 Product management (CRUD operations)
- 🚚 Order processing workflow
- 💰 Revenue tracking
- 🔔 Status updates for orders (pending, done, canceled)

## 🛠️ Technologies Used

- **Backend:** Symfony 6.0+, PHP 8.0+
- **Database:** MySQL 8.0
- **Frontend:** Twig, JavaScript, Bootstrap 4
- **Dependencies:** Composer, npm
- **Additional Libraries:** DataTables, Font Awesome

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
symfony-shop/
├── bin/                  # Symfony console commands
├── config/               # Application configuration
├── migrations/           # Database migrations
├── public/               # Public assets and entry point
│   ├── css/              # CSS files
│   ├── js/               # JavaScript files
│   └── img/              # Images
├── src/
│   ├── Command/          # Custom commands
│   ├── Controller/       # Controllers
│   │   ├── BackController.php    # Admin dashboard
│   │   ├── FrontController.php   # Customer interface
│   │   └── OrderController.php   # Order management
│   ├── Entity/           # Database entities
│   ├── Form/             # Forms
│   ├── Repository/       # Data repositories
│   └── Security/         # Authentication
├── templates/            # Twig templates
│   ├── back/             # Admin templates
│   ├── front/            # Customer templates
│   └── base.html.twig    # Base template
└── ...
```

## 🔐 Admin Features

The admin interface provides comprehensive management capabilities:

- **Dashboard**: View key metrics like pending orders, revenue, and product counts
- **Product Management**: Add, edit, delete, and view products with image uploads
- **Order Management**: Process orders with status updates (pending, done, canceled)
- **Revenue Tracking**: Monitor income from completed orders

## 🔄 Workflow

1. Customers browse products and add items to cart
2. Checkout process collects shipping and payment information
3. Order is created with "pending" status
4. Admin reviews orders and updates status as appropriate
5. When marked as "done", orders contribute to total revenue

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Contact

For questions or feedback, please reach out to:
- Email: abderraouf.mahmoudi2001@gmail.com
- GitHub: [Abderraouf Mahmoudi](https://github.com/Abderraouf-Mahmoudi)

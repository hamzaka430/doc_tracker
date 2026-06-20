# 📊 Product Stage Tracker

A professional Laravel 11 application for tracking products through various stages of production with a mobile-first, elegant UI design. Built with modern web technologies and best practices.

## ✨ Features

- **Product Management**: Add, edit, and delete products with custom or predefined stages
- **Smart Auto-Fill**: Auto-fill batch codes based on selected product names using predefined database mapping
- **Bulk Submit System**: Process and submit multiple pending documents simultaneously with a single click
- **PDF Generation**: Generate downloadable PDF lists of daily documents with multiple layout options (Single Page, Two Column)
- **Recycle Bin (Soft Deletes)**: Safely delete and restore documents without permanent data loss
- **Dashboard Analytics**: Visual line charts tracking 7-day document submission progress
- **Database-Backed Suggestions**: Auto-complete preferences and hidden items are synced securely across devices
- **Advanced Filters**: Date range and type-based filtering across all document views
- **Stage Tracking**: Track products through multiple stages like "On Process", "QA Sign", "Production", etc.
- **Submission System**: Complete pre-line clearance, in-process, and post-line clearance tasks
- **SAP Error Tracking**: Record SAP errors with T-Codes, rich descriptions, and high-quality screenshots
- **AWS S3 Integration**: Secure, robust cloud storage for user profile pictures and error screenshots
- **Custom Authentication UI**: Modern split-screen login and registration pages tailored to application design
- **Document Management**: View submitted products with detailed timestamps and sorting based on latest submission
- **Mobile-First Design**: Responsive sidebar navigation with luxury UI components
- **Export Functionality**: Export submitted products to CSV format
- **Search & Filter**: Advanced search for products by name, batch, or stage
- **Real-time Updates**: Dynamic UI updates without page reloads
- **Professional UI**: Modern, clean interface with intuitive navigation

## 🛠️ Technology Stack

- **Backend**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL
- **Frontend**: Blade Templates
- **CSS Framework**: Bootstrap 5 + Custom Design System
- **Build Tool**: Vite
- **Package Manager**: Composer, NPM
- **Icons**: Font Awesome 6
- **Styling**: TailwindCSS + Custom luxury design components

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 5.7 or MariaDB >= 10.3
- Git

## 🚀 Installation

1. **Clone the repository:**
```bash
git clone https://github.com/hamzaka430/doc_tracker.git
cd doc_tracker
```

2. **Install PHP dependencies:**
```bash
composer install
```

3. **Install Node.js dependencies:**
```bash
npm install
```

4. **Create environment file:**
```bash
# On Windows (PowerShell)
copy .env.example .env

# On Linux/Mac
cp .env.example .env
```

5. **Generate application key:**
```bash
php artisan key:generate
```

6. **Configure your database in `.env` file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doc_tracker
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. **Run migrations and seed sample data:**
```bash
php artisan migrate --seed
```

8. **Build frontend assets:**
```bash
# For development
npm run dev

# For production
npm run build
```

9. **Start the development server:**
```bash
php artisan serve
```

10. **Access the application:**
```
http://localhost:8000
```

## 📖 Usage

### Adding Products
1. Navigate to **"Add Product"** page from the sidebar
2. Enter product details:
   - Product name
   - Batch number
   - Stage (select from predefined options or enter custom)
3. Click **"Add Product"** to save

### Managing Products
1. View all pending products on the **Dashboard**
2. Click **"Open Details"** on any product card to view/edit
3. Complete clearance tasks:
   - ✅ Pre-Line Clearance
   - ✅ In-Process Clearance
   - ✅ Post-Line Clearance
4. Add remarks if needed
5. Click **"Submit Product"** when all tasks are completed

### Viewing Submitted Products
1. Navigate to **"Submitted"** page
2. Use search to filter by:
   - Product name
   - Batch number
   - Stage
3. View submission date and time for each product
4. Export data to **CSV** for reporting

## 🗄️ Database Schema

### Products Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key (auto-increment) |
| `name` | VARCHAR(255) | Product name |
| `batch_no` | VARCHAR(255) | Batch number |
| `stage` | VARCHAR(255) | Current production stage |
| `status` | ENUM | pending/submitted |
| `pre_line_clearance` | BOOLEAN | Pre-line clearance status |
| `in_process` | BOOLEAN | In-process clearance status |
| `post_line_clearance` | BOOLEAN | Post-line clearance status |
| `remarks` | TEXT | Additional notes |
| `submission_date` | DATE | Date of submission |
| `submission_time` | TIME | Time of submission |
| `created_at` | TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | Record update time |
| `deleted_at` | TIMESTAMP | Soft delete timestamp |

### User Preferences Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key (auto-increment) |
| `user_id` | BIGINT | Foreign key linking to Users |
| `key` | VARCHAR(255) | Preference key |
| `value` | JSON | JSON payload of preferences |
| `created_at` | TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | Record update time |

### SAP Errors Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key (auto-increment) |
| `user_id` | BIGINT | Foreign key linking to Users |
| `title` | VARCHAR(255) | Short description/title of the error |
| `sap_tcode` | VARCHAR(100) | SAP Transaction Code (e.g. MIGO, ME21N) |
| `description` | TEXT | Detailed explanation of the error |
| `image_path` | VARCHAR(255) | Path/URL to the screenshot hosted on AWS S3 |
| `created_at` | TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | Record update time |

## 🔌 API Endpoints / Routes

## 🔌 API Endpoints / Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Display product list page |
| GET | `/products/create` | Show add product form |
| POST | `/products` | Store new product |
| GET | `/products/{id}` | Show product details |
| PUT | `/products/{id}` | Update product details |
| POST | `/products/{id}/submit` | Submit product |
| GET | `/submitted` | Show submitted products |
| GET | `/export-csv` | Export submitted products to CSV |

## 📁 Project Structure

```
doc_tracker/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Application controllers
│   │   └── Middleware/       # Custom middleware
│   ├── Models/               # Eloquent models
│   └── Providers/            # Service providers
├── config/                   # Configuration files
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── public/                   # Public assets
│   └── assets/               # CSS, JS files
├── resources/
│   ├── css/                  # Source CSS files
│   ├── js/                   # Source JS files
│   └── views/                # Blade templates
├── routes/
│   └── web.php              # Web routes
├── storage/                  # Application storage
├── tests/                    # Unit and feature tests
├── .env.example             # Environment example
├── composer.json            # PHP dependencies
├── package.json             # Node dependencies
└── README.md               # This file
```

## 🎨 UI Features

- **Responsive Design**: Works seamlessly on mobile, tablet, and desktop
- **Sidebar Navigation**: Collapsible sidebar with smooth animations
- **Card-Based Layout**: Modern card design for product display
- **Color-Coded Status**: Visual indicators for product status
- **Custom Select Dropdowns**: Styled select elements
- **Modal Dialogs**: Elegant modals for product details
- **Export Functionality**: One-click CSV export
- **Search Interface**: Real-time search filtering

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Hamza Zaka**
- GitHub: [@hamzaka430](https://github.com/hamzaka430)
- Repository: [doc_tracker](https://github.com/hamzaka430/doc_tracker)

## 📧 Support

For support, please open an issue in the GitHub repository.

---

Made with ❤️ using Laravel 11

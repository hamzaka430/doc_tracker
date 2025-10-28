# Product Stage Tracker

A professional Laravel 11 application for tracking products through various stages of production with a mobile-first, elegant UI design.

## Features

- **Product Management**: Add, edit, and delete products with custom or predefined stages
- **Stage Tracking**: Track products through stages like "On Process", "QA Sign", "Production", etc.
- **Submission System**: Complete pre-line clearance, in-process, and post-line clearance tasks
- **Document Management**: View submitted products with timestamps
- **Mobile-First Design**: Responsive sidebar navigation with luxury UI components
- **Export Functionality**: Export submitted products to CSV
- **Search & Filter**: Search submitted products by name, batch, or stage

## Technology Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Database**: MySQL
- **Frontend**: Blade Templates, Bootstrap 5
- **UI Framework**: Custom luxury design system with mobile-first approach
- **Icons**: Font Awesome 6

## Installation

1. Clone the repository:
```bash
git clone https://github.com/hamzaka430/doc_tracker.git
cd doc_tracker
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doc_tracker
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations and seed sample data:
```bash
php artisan migrate --seed
```

8. Build frontend assets:
```bash
npm run build
```

9. Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## Usage

### Adding Products
1. Navigate to "Add Product" page
2. Enter product name, batch number, and stage (custom or from dropdown)
3. Click "Add Product" to save

### Managing Products
1. View all pending products on the main dashboard
2. Click "Open Details" to view/edit product information
3. Complete clearance tasks (Pre-Line, In-Process, Post-Line)
4. Submit products when all tasks are completed

### Viewing Submitted Products
1. Navigate to "Submitted" page
2. Search products by name, batch, or stage
3. Export data to CSV if needed

## Database Schema

**Products Table:**
- `id` - Primary key
- `name` - Product name
- `batch_no` - Batch number
- `stage` - Current stage
- `status` - pending/submitted
- `pre_line_clearance` - Boolean
- `in_process` - Boolean  
- `post_line_clearance` - Boolean
- `remarks` - Text notes
- `submission_date` - Date submitted
- `submission_time` - Time submitted
- `created_at/updated_at` - Timestamps

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Product list page |
| GET | `/products/create` | Add product form |
| POST | `/products` | Store new product |
| GET | `/products/{id}` | Product details |
| PUT | `/products/{id}` | Update product details |
| POST | `/products/{id}/submit` | Submit product |
| GET | `/submitted` | Submitted products |
| GET | `/export-csv` | Export CSV |

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

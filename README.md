# Harvviie E-commerce API Backend

A comprehensive Laravel 10+ API backend for Harvviie fashion e-commerce platform with content management capabilities. This API provides endpoints for managing products, collections, orders, banners, content, and customer messages.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
- [File Storage](#file-storage)
- [Testing](#testing)
- [API Examples](#api-examples)

## Features

- **Authentication**: Laravel Sanctum token-based authentication
- **Product Management**: Full CRUD with image uploads and soft deletes
- **Collection Management**: Product categorization with positioning
- **Order Management**: Complete order lifecycle tracking
- **Dashboard Analytics**: Sales summaries and top products
- **Content Management**: Banners and about page content
- **Message System**: Customer contact and service requests
- **File Upload**: Image management for products, banners, and collections
- **Role-based Access**: Admin and Editor roles with different permissions
- **Consistent API**: Standardized JSON response format

## Requirements

- PHP 8.1+
- Composer
- MySQL 8.0+
- Laravel 10.x
- Node.js (for asset compilation, if needed)

## Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd harvviie-api
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database in .env**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=harvviie
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Create storage symlink**
```bash
php artisan storage:link
```

7. **Start the development server**
```bash
php artisan serve
```

## Configuration

### Sanctum Configuration

The API uses Laravel Sanctum for authentication. Update your `.env` with allowed domains:

```env
SANCTUM_STATEFUL_DOMAINS=127.0.0.1,127.0.0.1:3000,localhost,127.0.0.1:8000,::1,your-frontend-domain.com
```

### CORS Configuration

Configure CORS in `config/cors.php` to allow your frontend domain.

## Database Setup

The application includes comprehensive migrations for all required tables:

### Tables Created:
- `users` - User accounts with role-based access
- `products` - Product catalog with soft deletes
- `product_images` - Product image management
- `collections` - Product collections/categories with soft deletes
- `collection_product` - Many-to-many relationship between collections and products
- `orders` - Customer orders with JSON item storage
- `banners` - Homepage and promotional banners
- `about` - Singleton table for about page content
- `messages` - Customer contact and service request messages

### Seeded Data:
- Admin user: `admin@harvviie.test` / `password`
- Editor user: `editor@harvviie.test` / `password`
- 25 sample products with images
- 8 collections with product assignments
- 30+ sample orders
- Banners and about content
- Sample customer messages

## Authentication

### Login Flow

1. **Login to get token**
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@harvviie.test",
    "password": "password"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@harvviie.test",
      "role": "admin"
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

2. **Use token in subsequent requests**
```bash
curl -X GET http://127.0.0.1:8000/api/dashboard/summary \
  -H "Authorization: Bearer 1|abc123..."
```

### User Roles

- **Admin**: Full access to all endpoints, can delete resources
- **Editor**: Can create/update most resources but cannot delete
- **Public**: Can view products, collections, banners, and submit messages

## API Endpoints

### Authentication Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/login` | No | Login user |
| POST | `/api/auth/register` | No | Register new user |
| POST | `/api/auth/logout` | Yes | Logout current user |
| GET | `/api/auth/me` | Yes | Get current user info |

### Dashboard Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/dashboard/summary` | Yes | Sales summary and metrics |
| GET | `/api/dashboard/top-products` | Yes | Top selling products |
| GET | `/api/dashboard/pending-orders` | Yes | Pending orders list |

### Product Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/products` | No | List products with filters |
| GET | `/api/products/{id}` | No | Get product details |
| POST | `/api/products` | Yes | Create new product |
| PUT | `/api/products/{id}` | Yes | Update product |
| DELETE | `/api/products/{id}` | Yes | Soft delete product |
| POST | `/api/products/{id}/restore` | Yes | Restore deleted product |
| POST | `/api/products/{id}/images` | Yes | Upload product images |
| DELETE | `/api/products/{id}/images/{imageId}` | Yes | Delete product image |

### Collection Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/collections` | No | List collections |
| GET | `/api/collections/{id}` | No | Get collection details |
| POST | `/api/collections` | Yes | Create collection |
| PUT | `/api/collections/{id}` | Yes | Update collection |
| DELETE | `/api/collections/{id}` | Yes | Soft delete collection |
| POST | `/api/collections/{id}/products` | Yes | Assign products to collection |
| DELETE | `/api/collections/{id}/products/{productId}` | Yes | Remove product from collection |

### Order Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/orders` | Yes | List orders with filters |
| GET | `/api/orders/{id}` | Yes | Get order details |
| POST | `/api/orders` | Yes | Create new order |
| PUT | `/api/orders/{id}` | Yes | Update order |
| DELETE | `/api/orders/{id}` | Yes | Cancel order |

### Banner Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/banners` | No | List banners (public) |
| GET | `/api/banners/{id}` | Yes | Get banner details |
| POST | `/api/banners` | Yes | Create banner |
| PUT | `/api/banners/{id}` | Yes | Update banner |
| DELETE | `/api/banners/{id}` | Yes | Delete banner |

### Content Management Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/about` | No | Get about content |
| PUT | `/api/about` | Yes | Update about content |

### Message Endpoints (Not Required Yet)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/messages` | Yes | List messages with filters |
| GET | `/api/messages/{id}` | Yes | Get message details |
| POST | `/api/messages` | No | Submit contact message |
| PUT | `/api/messages/{id}` | Yes | Update message status |
| DELETE | `/api/messages/{id}` | Yes | Delete message |

### Upload Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/upload` | Yes | Upload image file |
| DELETE | `/api/upload/{filename}` | Yes | Delete uploaded file |

## File Storage

### Image Upload

Images are stored in `storage/app/public/` and made accessible via the public URL.

**Upload Example:**
```bash
curl -X POST http://127.0.0.1:8000/api/upload \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "image=@/path/to/image.jpg" \
  -F "folder=products"
```

**Response:**
```json
{
  "success": true,
  "message": "Image uploaded successfully",
  "data": {
    "filename": "unique_filename.jpg",
    "url": "/storage/products/unique_filename.jpg",
    "path": "products/unique_filename.jpg"
  }
}
```

### Storage Structure
```
storage/app/public/
├── products/     # Product images
├── banners/      # Banner images
├── collections/  # Collection cover images
└── general/      # General uploads
```

## Testing

Run the test suite:
```bash
php artisan test
```

### API Testing with Postman

1. Import the API endpoints into Postman
2. Set up environment variables for base URL and auth token
3. Test authentication flow first
4. Use the token for protected endpoints

## API Implementation

### Complete Product Management Flow

1. **Create a Product**
```bash
curl -X POST http://127.0.0.1:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Premium Cotton T-Shirt",
    "sku": "HV-001",
    "description": "High-quality cotton t-shirt with premium finish",
    "price_cents": 2500,
    "discount_percent": 10,
    "stock": 50,
    "is_active": true,
    "metadata": {
      "material": "100% Cotton",
      "care_instructions": "Machine wash cold"
    }
  }'
```

2. **Upload Product Images**
```bash
curl -X POST http://127.0.0.1:8000/api/products/1/images \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/image2.jpg"
```

3. **Create Collection and Assign Products**
```bash
# Create collection
curl -X POST http://127.0.0.1:8000/api/collections \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Summer Collection",
    "description": "Light and breezy summer wear",
    "cover_image": "/storage/collections/summer_cover.jpg",
    "is_active": true
  }'

# Assign products to collection
curl -X POST http://127.0.0.1:8000/api/collections/1/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_ids": [1, 2, 3, 4]
  }'
```

4. **Create an Order**
```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "unit_price_cents": 2250
      }
    ],
    "amount_cents": 4500,
    "shipping_address": {
      "name": "John Doe",
      "address_line_1": "123 Main Street",
      "city": "Cape Town",
      "postal_code": "8000",
      "country": "South Africa"
    }
  }'
```

5. **Get Dashboard Summary**
```bash
curl -X GET http://127.0.0.1:8000/api/dashboard/summary \
  -H "Authorization: Bearer YOUR_TOKEN"
```

6. **Create New Banner**
```bash
curl -X POST 'http://127.0.0.1:8000/api/banners' \
--header 'Authorization: Bearer 1|token' \
--header 'Content-Type: application/json' \

--data '{"id":1,
"title":"Harvviie 2",
"tagline":"2 Where confidence meets elegance",
"image":"/storage/products/68bdc2b531e52_sam.jpeg",
"position":6,
"is_active":true
}'
```

### Response Format

All API responses follow this consistent format:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data here
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description",
  "data": {
    "errors": {
      // Validation errors or error details
    }
  }
}
```

### Dashboard Summary Example

The dashboard endpoint provides sales analytics:

```json
{
  "success": true,
  "message": "Dashboard summary retrieved successfully",
  "data": {
    "sales_summary": {
      "today": {
        "amount_cents": 15000,
        "amount": 150.00
      },
      "week": {
        "amount_cents": 75000,
        "amount": 750.00
      },
      "month": {
        "amount_cents": 250000,
        "amount": 2500.00
      }
    },
    "orders_summary": {
      "pending_count": 5,
      "total_count": 127
    }
  }
}
```

### Filtering and Pagination

Most list endpoints support filtering and pagination:

**Products with filters:**
```bash
curl -X GET "http://127.0.0.1:8000/api/products?q=shirt&in_stock=true&is_active=true&per_page=10&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Orders with date filter:**
```bash
curl -X GET "http://127.0.0.1:8000/api/orders?status=pending&date_from=2024-01-01&date_to=2024-12-31" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Error Handling

The API includes comprehensive error handling for:

- Authentication errors (401)
- Authorization errors (403)
- Validation errors (422)
- Resource not found (404)
- Server errors (500)

## Security Features

- Token-based authentication with Laravel Sanctum
- Role-based access control (RBAC)
- Input validation on all endpoints
- SQL injection protection via Eloquent ORM
- File upload security with type validation
- Rate limiting on sensitive endpoints

## Support

For support and questions about the Harvviie API:

- Email: cyberkru9@gmail.com
- Documentation: This README file
- API Testing: Use the provided cURL examples or Postman

---

**Made with ❤️ for Harvviie Fashion**
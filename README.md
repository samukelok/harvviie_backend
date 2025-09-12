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
git clone https://github.com/samukelok/harvviie_backend
cd harvviie_backend
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
SANCTUM_STATEFUL_DOMAINS=127.0.0.1,127.0.0.1:3000,localhost,127.0.0.1:8000,::1,harvviie.co.za
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
- `carts` - Shopping carts for users and guest sessions
- `cart_items` - Individual items in shopping carts
- `messages` - Customer contact and service request messages

### Seeded Data:
- Admin user: `admin@harvviie.test` / `password`
- Editor user: `editor@harvviie.test` / `password`
- Customer user: `customer@harvviie.test` / `password`
- 25 sample products with images
- 8 collections with product assignments
- 30+ sample orders
- Banners and about content
- Sample customer messages

## Authentication

### Login Flow

1. **Login to get token**
```bash
curl -X POST http://localhost:8000/api/auth/login \
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
      "role": "admin",
      "phone": null,
      "address": null
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Customer Registration

Customers can register for accounts to place orders:

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+27 82 123 4567",
    "address": {
      "street": "123 Main Street",
      "city": "Cape Town",
      "postal_code": "8000",
      "country": "South Africa"
    }
  }'
```

2. **Use token in subsequent requests**
```bash
curl -X GET http://127.0.0.1:8000/api/dashboard/summary \
  -H "Authorization: Bearer 1|abc123..."
```

### User Roles

- **Admin**: Full access to all endpoints, can delete resources
- **Editor**: Can create/update most resources but cannot delete
- **Customer**: Can register, login, view products, place orders, and view their order history
- **Public**: Can view products, collections, banners, and submit messages

## API Endpoints

### Authentication Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/login` | No | Login user |
| POST | `/api/auth/register` | No | Register new user |
| POST | `/api/auth/logout` | Yes | Logout current user |
| GET | `/api/auth/me` | Yes | Get current user info |
| PUT | `/api/auth/profile` | Yes | Update user profile |

### Dashboard Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/dashboard/summary` | Yes | Sales summary and metrics |
| GET | `/api/dashboard/top-products` | Yes | Top selling products |
| GET | `/api/dashboard/pending-orders` | Yes | Pending orders list |

### Product Endpoints

### Product Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/products` | No | List products with filters |
| GET | `/api/products/{id}` | No | Get product details |
| POST | `/api/products` | Admin/Editor | Create new product |
| PUT | `/api/products/{id}` | Admin/Editor | Update product |
| DELETE | `/api/products/{id}` | Admin/Editor | Soft delete product |
| POST | `/api/products/{id}/restore` | Admin/Editor | Restore deleted product |
| POST | `/api/products/{id}/images` | Admin/Editor | Upload product images |
| DELETE | `/api/products/{id}/images/{imageId}` | Admin/Editor | Delete product image |

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

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/orders` | Admin/Editor | List all orders with filters |
| GET | `/api/my-orders` | Customer | List customer's own orders |
| GET | `/api/orders/{id}` | Yes | Get order details (own orders for customers) |
| POST | `/api/orders` | Yes | Create new order |
| PUT | `/api/orders/{id}` | Admin/Editor | Update order |
| DELETE | `/api/orders/{id}` | Admin/Editor | Cancel order |

### Banner Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/banners` | No | List banners (public) |
| GET | `/api/banners/{id}` | Admin/Editor | Get banner details |
| POST | `/api/banners` | Admin/Editor | Create banner |
| PUT | `/api/banners/{id}` | Admin/Editor | Update banner |
| DELETE | `/api/banners/{id}` | Admin/Editor | Delete banner |

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

### Cart Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/cart` | No | Get current cart (user or session-based) |
| POST | `/api/cart/items` | No | Add item to cart |
| PUT | `/api/cart/items/{cartItemId}` | No | Update cart item quantity |
| DELETE | `/api/cart/items/{cartItemId}` | No | Remove item from cart |
| DELETE | `/api/cart/clear` | No | Clear entire cart |
| POST | `/api/orders/from-cart` | Customer | Create order from cart contents |

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

1. **Register as Customer**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Customer",
    "email": "jane@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+27 82 987 6543",
    "address": {
      "street": "456 Oak Avenue",
      "city": "Johannesburg",
      "postal_code": "2000",
      "country": "South Africa"
    }
  }'
```

2. **Login as Customer**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jane@example.com",
    "password": "password123"
  }'
```

3. **Place an Order (Customer)**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer CUSTOMER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "product_name": "Premium Cotton T-Shirt",
        "quantity": 2,
        "unit_price_cents": 2500
      }
    ],
    "amount_cents": 5000
  }'
```

4. **View My Orders (Customer)**
```bash
curl -X GET http://localhost:8000/api/my-orders \
  -H "Authorization: Bearer CUSTOMER_TOKEN"
```

5. **Update Profile (Customer)**
```bash
curl -X PUT http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer CUSTOMER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+27 82 111 2222",
    "address": {
      "street": "789 New Street",
      "city": "Durban",
      "postal_code": "4000",
      "country": "South Africa"
    }
  }'
```

### Complete Shopping Cart Flow

1. **Add Items to Cart (Guest or Authenticated)**
```bash
curl -X POST http://localhost:8000/api/cart/items \
  -H "Content-Type: application/json" \
  -H "X-Cart-Session: unique-session-id" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

2. **View Cart**
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "X-Cart-Session: unique-session-id"
```

3. **Update Cart Item**
```bash
curl -X PUT http://localhost:8000/api/cart/items/1 \
  -H "Content-Type: application/json" \
  -H "X-Cart-Session: unique-session-id" \
  -d '{
    "quantity": 3
  }'
```

4. **Create Order from Cart (Authenticated Customer)**
```bash
curl -X POST http://localhost:8000/api/orders/from-cart \
  -H "Authorization: Bearer CUSTOMER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": {
      "name": "Jane Customer",
      "street": "456 Oak Avenue",
      "city": "Johannesburg",
      "postal_code": "2000",
      "country": "South Africa"
    }
  }'
```

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

The cart system supports both authenticated users and guest sessions:

```json
{
  "success": true,
  "message": "Cart retrieved successfully",
  "data": {
    "cart": {
      "id": 1,
      "user_id": 3,
      "session_id": null,
      "status": "active",
      "items": [
        {
          "id": 1,
          "product_id": 1,
          "product": {
            "id": 1,
            "name": "Premium Cotton T-Shirt",
            "price_cents": 2500,
            "discounted_price_cents": 2250
          },
          "quantity": 2,
          "unit_price_cents": 2250,
          "total_cents": 4500,
          "total": 45.00
        }
      ],
      "total_items": 2,
      "subtotal_cents": 4500,
      "subtotal": 45.00,
      "tax_cents": 675,
      "tax": 6.75,
      "total_cents": 5175,
      "total": 51.75
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

### Role-based Access Control

The API implements three user roles:

- **Admin**: Full system access, can delete resources and manage users
- **Editor**: Can manage products, collections, orders, and content but cannot delete
- **Customer**: Can register, login, place orders, and view their order history

Customers are automatically assigned to orders they create, and can only view their own orders. Staff (admin/editor) can view and manage all orders.

## Security Features

- Token-based authentication with Laravel Sanctum
- Role-based access control (RBAC)
- Cart session management for guest users
- Input validation on all endpoints
- SQL injection protection via Eloquent ORM
- File upload security with type validation
- Rate limiting on sensitive endpoints
- Order access control (customers can only view their own orders)
- Stock validation during cart operations and order creation

## Support

For support and questions about the Harvviie API:

- Documentation: This README file
- API Testing: Use the provided cURL examples or Postman

---

**Made with ❤️ for Harvviie Fashion**
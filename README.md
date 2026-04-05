# PHP Native API with Authentication and CRUD

A simple PHP REST API with JWT authentication and CRUD operations using Eloquent ORM.

## Features

- JWT-based authentication
- User registration and login
- CRUD operations for posts
- RESTful API design
- MySQL database with Eloquent ORM
- Token-based session management

## Installation

1. Clone the repository
2. Run `composer install`
3. Create MySQL database named "example"
4. Configure `.env` file with your database credentials
5. Run database migrations:
   ```bash
   php database/migrations/create_users_table.php
   php database/migrations/create_posts_table.php
   ```

## API Endpoints

### Authentication

#### Register
```
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

#### Login
```
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

### Posts (Authentication Required)

#### Get All Posts
```
GET /api/posts
Authorization: Bearer {token}
```

#### Get Single Post
```
GET /api/posts/{id}
Authorization: Bearer {token}
```

#### Create Post
```
POST /api/posts
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "My First Post",
    "content": "This is the content of my post"
}
```

#### Update Post
```
PUT /api/posts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated Title",
    "content": "Updated content"
}
```

#### Delete Post
```
DELETE /api/posts/{id}
Authorization: Bearer {token}
```

## Project Structure

```
├── api/
│   └── index.php              # Main API router
├── config/
│   ├── database.php           # Database configuration
│   ├── eloquent.php           # Eloquent setup
│   └── jwt.php               # JWT handler
├── controllers/
│   ├── AuthController.php    # Authentication logic
│   └── PostController.php    # CRUD operations
├── models/
│   ├── User.php              # User model
│   └── Post.php              # Post model
├── middleware/
│   └── AuthMiddleware.php    # JWT authentication middleware
├── database/
│   └── migrations/          # Database migrations
├── vendor/
│   └── autoload.php         # Autoloader
├── composer.json
└── .env                     # Environment variables
```

## Usage Examples

### Register a new user
```bash
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'
```

### Login
```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

### Create a post (with token)
```bash
curl -X POST http://localhost/api/posts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{"title":"My Post","content":"This is my post content"}'
```

### Get all posts
```bash
curl -X GET http://localhost/api/posts \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Security Notes

- Change the JWT secret key in production
- Use HTTPS in production
- Validate and sanitize all inputs
- Implement rate limiting for production use
- Consider using prepared statements (handled by Eloquent)

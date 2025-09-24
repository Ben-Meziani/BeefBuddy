# BeefBuddy

A Symfony-based web application for booking training sessions with professional fighters. BeefBuddy connects users with fighters for personalized training experiences.

## 🥊 Features

- **Fighter Management**: Browse and manage professional fighters with detailed profiles
- **Reservation System**: Book training sessions with your favorite fighters
- **User Authentication**: Secure JWT-based authentication system
- **Payment Integration**: Stripe payment processing for training sessions
- **Email Notifications**: Automated email confirmations for reservations
- **RESTful API**: Complete API for frontend integration

## 🛠️ Technology Stack

- **Backend**: Symfony 7.3
- **Database**: PostgreSQL (configurable)
- **Authentication**: JWT (LexikJWTAuthenticationBundle)
- **Payment**: Stripe PHP SDK
- **Email**: Symfony Mailer
- **ORM**: Doctrine ORM
- **Testing**: PHPUnit
- **PHP Version**: 8.2+

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- PostgreSQL (or MySQL)
- Web server (Apache/Nginx) or PHP built-in server

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd BeefBuddy
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment Configuration**
   ```bash
   cp .env .env.local
   ```
   
   Update `.env.local` with your database credentials:
   ```env
   DATABASE_URL="postgresql://username:password@127.0.0.1:5432/beefbuddy?serverVersion=16&charset=utf8"
   ```

4. **Database Setup**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load
   ```

5. **JWT Configuration**
   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

6. **Start the development server**
   ```bash
   symfony serve
   # or
   php -S localhost:8000 -t public/
   ```

## 🔧 Configuration

### Environment Variables

Key environment variables to configure in `.env.local`:

```env
# Database
DATABASE_URL="postgresql://username:password@127.0.0.1:5432/beefbuddy?serverVersion=16&charset=utf8"

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase

# Stripe
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...

# Email
MAILER_DSN=smtp://localhost:1025
```

## 📚 API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/register` - User registration
- `POST /api/refresh` - Refresh JWT token

### Fighters
- `GET /fighters` - List all fighters (with pagination)
- `GET /fighter/{id}` - Get fighter details
- `POST /fighter` - Register new fighter (admin)
- `PUT /fighter/{id}` - Update fighter (admin)

### Reservations
- `POST /reservation` - Create new reservation
- `GET /user/{id}/reservations` - Get user reservations

### Users
- `GET /user/{id}` - Get user profile
- `PUT /user/{id}` - Update user profile

### Payments
- `POST /payment/create-intent` - Create Stripe payment intent
- `POST /payment/confirm` - Confirm payment

## 🏗️ Project Structure

```
BeefBuddy/
├── config/                 # Configuration files
├── migrations/             # Database migrations
├── public/                 # Web root
├── src/
│   ├── Controller/         # API controllers
│   ├── Entity/            # Doctrine entities
│   ├── Repository/        # Data repositories
│   ├── Service/           # Business logic services
│   ├── DTO/               # Data Transfer Objects
│   └── Security/          # Security configuration
├── templates/             # Twig templates
├── tests/                 # Test files
└── var/                   # Cache and logs
```

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php bin/phpunit

# Run specific test suite
php bin/phpunit tests/Controller/
```

## 📦 Key Dependencies

- **Symfony Framework**: 7.3.*
- **Doctrine ORM**: ^3.5
- **Lexik JWT Authentication**: ^3.1
- **Stripe PHP SDK**: ^17.5
- **Nelmio CORS Bundle**: ^2.5
- **Symfony Mailer**: 7.3.*
- **PHPUnit**: ^11.5

## 🔐 Security Features

- JWT-based authentication
- CORS configuration
- CSRF protection
- Rate limiting
- Input validation
- SQL injection prevention (Doctrine ORM)

## 📧 Email Templates

Email templates are located in `templates/` and include:
- Reservation confirmations
- Password reset emails
- Account verification

## 🚀 Deployment

### Production Setup

1. **Environment Configuration**
   ```bash
   APP_ENV=prod
   APP_DEBUG=false
   ```

2. **Optimize for Production**
   ```bash
   composer install --no-dev --optimize-autoloader
   php bin/console cache:clear --env=prod
   php bin/console doctrine:migrations:migrate --env=prod
   ```

3. **Web Server Configuration**
   - Point document root to `public/` directory
   - Configure URL rewriting for Symfony
   - Set up SSL certificates

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📄 License

This project is proprietary software.

## 🆘 Support

For support and questions, please contact the development team.

---

**BeefBuddy** - Connecting fighters with training partners worldwide! 🥊

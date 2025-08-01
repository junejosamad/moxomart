# SadaCart E-commerce Platform

SadaCart is a lightweight and flexible e-commerce platform built with PHP, designed to help you quickly set up and manage your online store. It features a simple MVC architecture, a responsive design, and essential e-commerce functionalities.

## Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation Guide](#installation-guide)
  - [1. Clone the Repository](#1-clone-the-repository)
  - [2. Configure Environment Variables](#2-configure-environment-variables)
  - [3. Install Composer Dependencies](#3-install-composer-dependencies)
  - [4. Database Setup](#4-database-setup)
  - [5. Web Server Configuration](#5-web-server-configuration)
  - [6. Run Development Server (Optional)](#6-run-development-server-optional)
- [Build Process (SCSS & JS)](#build-process-scss--js)
- [Running Tests](#running-tests)
- [Deployment](#deployment)
- [Environment Variables](#environment-variables)
- [Sitemap Generation](#sitemap-generation)
- [Contributing](#contributing)
- [License](#license)

## Features

-   **Product Management**: Add, edit, delete products with images, categories, pricing, and inventory.
-   **Category Management**: Organize products into categories.
-   **Shopping Cart**: Add, update, and remove items from the cart.
-   **Checkout Process**: Simple and secure checkout flow.
-   **User Authentication**: Register, login, and password recovery.
-   **Admin Panel**: Dashboard for managing products, orders, users, and site settings.
-   **Responsive Design**: Optimized for various devices (desktop, tablet, mobile).
-   **Blog Module**: Publish articles and news.
-   **Static Pages**: About Us, FAQ, Contact Us.
-   **SEO Friendly**: Meta tags, URL slugs, sitemap generation.
-   **Basic Analytics Integration**: Google Analytics, Facebook Pixel.

## System Requirements

-   PHP >= 7.4 (with `pdo_mysql` or `pdo_sqlite` extension)
-   Composer
-   Node.js & npm (for asset compilation)
-   MySQL / MariaDB or SQLite database
-   Apache with `mod_rewrite` or Nginx

## Installation Guide

Follow these steps to get SadaCart up and running on your local machine.

### 1. Clone the Repository

\`\`\`bash
git clone https://github.com/your-username/sadacart.git
cd sadacart
\`\`\`

### 2. Configure Environment Variables

Create a `.env` file by copying the `.env.example` file:

\`\`\`bash
cp .env.example .env
\`\`\`

Open the `.env` file and update the database credentials and other settings as needed.

```dotenv
APP_ENV=development
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sadacart_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sadacart.com
MAIL_FROM_NAME="SadaCart Support"

# Optional: Google Analytics and Facebook Pixel IDs
GA_ID=
FB_PIXEL_ID=

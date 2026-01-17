# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Articlo is a social media platform for sharing articles, built with Laravel 12 and Livewire 3. Users can create posts, follow other users, like and comment on content, and search articles.

## Common Commands

```bash
# Development
composer dev              # Start server, queue listener, and Vite concurrently
composer setup            # Full setup: install deps, generate key, migrate, build

# Testing
composer test             # Clear config and run Pest tests
./vendor/bin/pest --filter="test name"  # Run single test

# Code Quality
composer pint             # Run Laravel Pint code formatter

# Assets
npm run dev               # Vite dev server with hot reload
npm run build             # Build production assets

# Database
php artisan migrate       # Run migrations
php artisan migrate:fresh --seed  # Reset and seed database
```

## Architecture

### Controller Structure
- `app/Http/Controllers/` - Public-facing controllers (PostController, UserController, CategoryController)
- `app/Http/Controllers/Dashboard/` - Admin controllers (Dashboard\PostController for CRUD)
- `app/Http/Controllers/Auth/` - Laravel Breeze authentication controllers

### Livewire Components
Located in `app/Livewire/`, these handle real-time UI interactions:
- `PostLike` - Like/unlike functionality
- `PostComments` / `PostCommentCount` - Comment system
- `PostContentGenerator` - OpenAI content generation
- `Search` - Full-text search
- `PostRemoveImage` - Media management

### Key Models
- `User` - Has followers/following (self-referential many-to-many), posts, media (avatars)
- `Post` - Belongs to user, has categories (many-to-many), uses Laravel Scout for search, Spatie Media Library for images
- `Category` - Many-to-many with posts
- `PostLike` - Tracks post likes

### Services
- `app/Services/TextGeneration/` - OpenAI integration for AI content generation

### Middleware
- `IsAdminMiddleware` - Protects dashboard routes, checks against `ADMIN_EMAIL` env var

## Route Structure

```
/                          # Home (public)
/users, /users/{username}  # User listing and profiles (public)
/posts/{slug}              # Single post view (public)
/search/{term}             # Search results (public)
/categories/{slug}         # Posts by category (public)

/profile                   # Edit profile (auth required)
/users/{user}/follow       # Follow/unfollow (auth required)
/dashboard/posts           # Admin CRUD (auth + admin required)
```

## Testing

Uses Pest PHP with SQLite in-memory database. Tests are in `tests/Feature/` and `tests/Unit/`.

## Media Handling

Uses Spatie Media Library with automatic conversions:
- Posts: `thumb-1170`, `thumb-800`, `thumb-128`
- Users: `thumb-40` (avatars)

Files are uploaded via FilePond to temporary storage, then moved on form submission.

## Environment Variables

Key custom variables:
- `ADMIN_EMAIL` - Email address for admin access
- `OPENAI_API_KEY` / `OPENAI_API_URL` - For AI content generation
- `SCOUT_DRIVER` - Search driver (database by default)

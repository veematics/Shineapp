# Laravel CoreUI Layout Boilerplate

This project includes a comprehensive layout boilerplate built with Laravel, CoreUI Pro, and FontAwesome. The layout provides a modern, responsive admin dashboard interface.

## Features

### Layout Components
- **Responsive Sidebar Navigation** with collapsible menu items
- **Top Header** with user menu and notifications
- **Breadcrumb Navigation** for easy page tracking
- **Flash Message System** for user feedback
- **Footer** with branding and links
- **Mobile-Responsive Design** that works on all devices

### Included Assets
- **CoreUI Pro CSS/JS** (`/public/vendor/coreui/`)
- **FontAwesome Icons** (`/public/vendor/fontawesome/`)
- **Custom Styling** support via `@stack('styles')`
- **Custom JavaScript** support via `@stack('scripts')`

## File Structure

```
resources/views/
├── layouts/
│   └── app.blade.php          # Main layout boilerplate
└── dashboard.blade.php         # Example dashboard page

routes/
└── web.php                     # Routes for navigation

public/vendor/
├── coreui/                     # CoreUI assets
└── fontawesome/                # FontAwesome assets
```

## Usage

### Extending the Layout

To create a new page using the layout:

```blade
@extends('layouts.app')

@section('title', 'Your Page Title')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">Your Page</li>
@endsection

@section('content')
    <!-- Your page content here -->
@endsection

@push('styles')
<style>
    /* Custom CSS for this page */
</style>
@endpush

@push('scripts')
<script>
    // Custom JavaScript for this page
</script>
@endpush
```

### Available Sections

- `@section('title')` - Page title (appears in browser tab)
- `@section('breadcrumb')` - Breadcrumb navigation items
- `@section('content')` - Main page content
- `@push('styles')` - Additional CSS for the page
- `@push('scripts')` - Additional JavaScript for the page

### Sidebar Navigation

The sidebar includes:
- **Dashboard** link
- **Users** management
- **Settings** page
- **Components** dropdown with sub-items
- **Collapsible/Expandable** functionality

### Header Features

- **Mobile hamburger menu** for responsive design
- **Notifications dropdown** with badge counter
- **User menu** with profile and logout options
- **Breadcrumb navigation** below the header

### Flash Messages

The layout automatically displays Laravel flash messages:

```php
// In your controller
return redirect()->back()->with('success', 'Operation completed successfully!');
return redirect()->back()->with('error', 'Something went wrong!');
return redirect()->back()->with('warning', 'Please check your input!');
return redirect()->back()->with('info', 'Information updated!');
```

### FontAwesome Icons

Use FontAwesome icons throughout your application:

```html
<i class="fas fa-user"></i>          <!-- Solid icons -->
<i class="far fa-heart"></i>         <!-- Regular icons -->
<i class="fab fa-github"></i>        <!-- Brand icons -->
<i class="fal fa-star"></i>          <!-- Light icons (Pro) -->
<i class="fad fa-home"></i>          <!-- Duotone icons (Pro) -->
```

## Customization

### Changing the Brand

Edit the sidebar brand in `layouts/app.blade.php`:

```blade
<div class="c-sidebar-brand d-lg-down-none">
    <a href="{{ url('/') }}" class="c-sidebar-brand-full">
        <i class="fas fa-your-icon c-sidebar-brand-icon"></i>
        Your App Name
    </a>
</div>
```

### Adding Navigation Items

Add new sidebar navigation items:

```blade
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('your.route') }}">
        <i class="fas fa-your-icon c-sidebar-nav-icon"></i>
        Your Menu Item
    </a>
</li>
```

### Creating Dropdown Menus

```blade
<li class="c-sidebar-nav-dropdown">
    <a class="c-sidebar-nav-dropdown-toggle" href="#">
        <i class="fas fa-folder c-sidebar-nav-icon"></i>
        Your Dropdown
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="#">
                <i class="fas fa-circle c-sidebar-nav-icon"></i>
                Sub Item 1
            </a>
        </li>
    </ul>
</li>
```

## CoreUI Components

The layout supports all CoreUI components:

### Cards
```html
<div class="card">
    <div class="card-header">
        <i class="fas fa-icon"></i> Card Title
    </div>
    <div class="card-body">
        Card content
    </div>
</div>
```

### Buttons
```html
<button class="btn btn-primary">
    <i class="fas fa-save"></i> Save
</button>
```

### Alerts
```html
<div class="alert alert-success" role="alert">
    <i class="fas fa-check-circle"></i>
    Success message
</div>
```

## Development

### Running the Application

1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

2. Visit `http://127.0.0.1:8000/dashboard` to see the layout in action

### Routes

The following routes are available:
- `/` - Welcome page
- `/dashboard` - Dashboard with layout
- `/users` - Users page (placeholder)
- `/settings` - Settings page (placeholder)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Internet Explorer 11+

## Dependencies

- Laravel Framework 12.x
- CoreUI Pro 4.x
- FontAwesome Pro 6.x
- Bootstrap 5.x (included with CoreUI)

## License

This layout boilerplate is built for use with CoreUI Pro and FontAwesome Pro. Make sure you have valid licenses for both products in production environments.

## Support

For CoreUI documentation: https://coreui.io/docs/
For FontAwesome documentation: https://fontawesome.com/docs/
For Laravel documentation: https://laravel.com/docs/
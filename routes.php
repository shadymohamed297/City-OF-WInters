<?php
// Route map for api/index.php's simple router.
// NOT used by the live frontend today (it calls each .php file
// directly), but kept working as a fallback / for future use.

return [
    'GET' => [
        '/catalog/products' => __DIR__ . '/api/catalog/products.php',
        '/catalog/product' => __DIR__ . '/api/catalog/product.php',
        '/catalog/categories' => __DIR__ . '/api/catalog/categories.php',
        '/catalog/related' => __DIR__ . '/api/catalog/related.php',
        '/catalog/search' => __DIR__ . '/api/catalog/search.php',
        '/settings/public' => __DIR__ . '/api/settings/public.php',
        '/shipping/rates' => __DIR__ . '/api/shipping/rates.php',
        '/rates' => __DIR__ . '/api/shipping/rates.php',
        '/sitemap.xml' => __DIR__ . '/api/sitemap.php',
        '/auth/me' => __DIR__ . '/api/auth/me.php',
        '/orders/my-orders' => __DIR__ . '/api/orders/my-orders.php',
        '/reviews/product' => __DIR__ . '/api/reviews/product.php',
        '/wishlist' => __DIR__ . '/api/wishlist.php',
        '/admin/analytics' => __DIR__ . '/api/admin/analytics.php',
        '/admin/customers' => __DIR__ . '/api/admin/customers.php',
        '/admin/orders' => __DIR__ . '/api/admin/orders.php',
        '/admin/orders/show' => __DIR__ . '/api/admin/orders/show.php',
        '/admin/products' => __DIR__ . '/api/admin/products.php',
        '/admin/categories' => __DIR__ . '/api/admin/categories.php',
        '/admin/coupons' => __DIR__ . '/api/admin/coupons.php',
        '/admin/shipping' => __DIR__ . '/api/admin/shipping.php',
        '/admin/settings' => __DIR__ . '/api/admin/settings.php',
        '/admin/users' => __DIR__ . '/api/admin/users.php',
    ],
    'POST' => [
        '/auth/login' => __DIR__ . '/api/auth/login.php',
        '/auth/register' => __DIR__ . '/api/auth/register.php',
        '/auth/logout' => __DIR__ . '/api/auth/logout.php',
        '/auth/forgot-password' => __DIR__ . '/api/auth/forgot-password.php',
        '/auth/reset-password' => __DIR__ . '/api/auth/reset-password.php',
        '/auth/admin-init' => __DIR__ . '/api/auth/admin-init.php',
        '/auth/admin-promote' => __DIR__ . '/api/auth/admin-promote.php',
        '/auth/csrf' => __DIR__ . '/api/auth/csrf.php',
        '/orders/create' => __DIR__ . '/api/orders/create.php',
        '/coupons/validate' => __DIR__ . '/api/coupons/validate.php',
        '/reviews/create' => __DIR__ . '/api/reviews/create.php',
        '/wishlist' => __DIR__ . '/api/wishlist.php',
        '/admin/import' => __DIR__ . '/api/admin/import.php',
        '/admin/uploads' => __DIR__ . '/api/admin/uploads.php',
        '/admin/users/role' => __DIR__ . '/api/admin/users/role.php',
    ],
    'DELETE' => [
        '/reviews/delete' => __DIR__ . '/api/reviews/delete.php',
    ],
];

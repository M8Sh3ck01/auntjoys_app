# Component Usage Guide

## Card Component

```php
<?php require_once './views/components/card.php'; ?>

<!-- Simple card -->
<?php card(['title' => 'My Card', 'content' => 'Card content here']); ?>

<!-- Card with shadow and subtitle -->
<?php card([
    'title' => 'User Profile',
    'subtitle' => 'Manage your account',
    'shadow' => true,
    'content' => '<p>Hello user!</p>'
]); ?>

<!-- Card without title -->
<?php card(['content' => 'Just content, no header']); ?>
```

## Modal Component

```php
<?php require_once './views/components/modal.php'; ?>

<!-- Basic modal -->
<?php modal([
    'id' => 'deleteConfirm',
    'title' => 'Confirm Delete',
    'content' => '<p>Are you sure you want to delete this item?</p>',
    'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                 <button type="button" class="btn btn-danger">Delete</button>'
]); ?>

<!-- Large modal, centered -->
<?php modal([
    'id' => 'userForm',
    'title' => 'Edit User',
    'size' => 'lg',
    'centered' => true,
    'content' => '<form><!-- form content --></form>',
    'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                 <button type="button" class="btn btn-primary">Save</button>'
]); ?>

<!-- Modal with static backdrop (can't close by clicking outside) -->
<?php modal([
    'id' => 'processing',
    'title' => 'Processing...',
    'backdrop' => 'static',
    'content' => '<p>Please wait...</p>',
    'footer' => false  // No footer
]); ?>

<!-- Trigger modal with button -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userForm">
    Open Form
</button>
```

## Input Component

```php
<?php require_once './views/components/input.php'; ?>

<!-- Text input with icon -->
<?php input([
    'type' => 'text',
    'name' => 'username',
    'label' => 'Username',
    'icon' => 'fas fa-user',
    'placeholder' => 'Enter username',
    'required' => true,
    'minlength' => 3,
    'help_text' => 'At least 3 characters'
]); ?>

<!-- Email input -->
<?php input([
    'type' => 'email',
    'name' => 'email',
    'label' => 'Email Address',
    'icon' => 'fas fa-envelope',
    'required' => true,
    'autocomplete' => 'email'
]); ?>

<!-- Password input -->
<?php input([
    'type' => 'password',
    'name' => 'password',
    'label' => 'Password',
    'icon' => 'fas fa-lock',
    'required' => true,
    'minlength' => 6,
    'help_text' => 'At least 6 characters'
]); ?>

<!-- Number input with range -->
<?php input([
    'type' => 'number',
    'name' => 'quantity',
    'label' => 'Quantity',
    'min' => 1,
    'max' => 100,
    'value' => 5,
    'required' => true
]); ?>

<!-- Textarea -->
<?php input([
    'type' => 'textarea',
    'name' => 'description',
    'label' => 'Description',
    'placeholder' => 'Enter description...',
    'rows' => 5
]); ?>

<!-- Input with error -->
<?php input([
    'type' => 'text',
    'name' => 'username',
    'label' => 'Username',
    'icon' => 'fas fa-user',
    'value' => 'john',
    'error' => 'Username already taken'
]); ?>

<!-- Phone input -->
<?php input([
    'type' => 'tel',
    'name' => 'phone',
    'label' => 'Phone Number',
    'icon' => 'fas fa-phone',
    'placeholder' => '+265 99 123 456',
    'pattern' => '[0-9+\-\s]+'
]); ?>

<!-- Disabled/readonly input -->
<?php input([
    'type' => 'text',
    'name' => 'user_id',
    'label' => 'User ID',
    'value' => '12345',
    'readonly' => true
]); ?>
```

## Select Component

```php
<?php require_once './views/components/input.php'; ?>

<!-- Basic select -->
<?php select([
    'name' => 'role',
    'label' => 'User Role',
    'options' => [
        '1' => 'Customer',
        '2' => 'Administrator',
        '3' => 'Sales Staff',
        '4' => 'Manager'
    ],
    'required' => true
]); ?>

<!-- Select with selected value -->
<?php select([
    'name' => 'category',
    'label' => 'Category',
    'options' => [
        'breakfast' => 'Breakfast',
        'lunch' => 'Lunch',
        'dinner' => 'Dinner',
        'snacks' => 'Snacks'
    ],
    'value' => 'lunch'
]); ?>

<!-- Select with error -->
<?php select([
    'name' => 'status',
    'label' => 'Order Status',
    'options' => [
        'pending' => 'Pending',
        'preparing' => 'Preparing',
        'delivered' => 'Delivered'
    ],
    'error' => 'Please select a status'
]); ?>
```

## Complete Form Example

```php
<?php 
require_once './views/components/card.php';
require_once './views/components/input.php';
require_once './views/components/modal.php';

$errors = $_SESSION['errors'] ?? [];
?>

<?php card([
    'title' => 'Create New Meal',
    'content' => '
        <form method="POST" action="index.php?page=admin/meals&action=create">
            ' . ob_get_clean() . '
        </form>
    '
]); ?>

<?php 
ob_start(); ?>
<?php input([
    'type' => 'text',
    'name' => 'meal_name',
    'label' => 'Meal Name',
    'icon' => 'fas fa-utensils',
    'required' => true,
    'error' => $errors['meal_name'] ?? ''
]); ?>

<?php input([
    'type' => 'textarea',
    'name' => 'description',
    'label' => 'Description',
    'rows' => 3,
    'error' => $errors['description'] ?? ''
]); ?>

<?php input([
    'type' => 'number',
    'name' => 'price',
    'label' => 'Price (MWK)',
    'icon' => 'fas fa-dollar-sign',
    'step' => '0.01',
    'min' => '0',
    'required' => true,
    'error' => $errors['price'] ?? ''
]); ?>

<?php select([
    'name' => 'category_id',
    'label' => 'Category',
    'options' => $categories, // Populate from controller
    'required' => true,
    'error' => $errors['category_id'] ?? ''
]); ?>
```

# Inline Component Snippets

Copy and paste these snippets directly into your view files.

## Text Input with Icon

```html
<div class="mb-3">
    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-user"></i></span>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <small class="form-text text-muted">At least 3 characters</small>
</div>
```

## Email Input

```html
<div class="mb-3">
    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
</div>
```

## Password Input

```html
<div class="mb-3">
    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-lock"></i></span>
        <input type="password" class="form-control" id="password" name="password" minlength="6" required>
    </div>
    <small class="form-text text-muted">At least 6 characters</small>
</div>
```

## Input with Error Display

```html
<div class="mb-3">
    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-user"></i></span>
        <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
               id="username" name="username" value="<?php echo htmlspecialchars($old_username ?? ''); ?>" required>
    </div>
    <?php if (isset($errors['username'])): ?>
        <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['username']); ?></div>
    <?php endif; ?>
</div>
```

## Textarea

```html
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
</div>
```

## Select Dropdown

```html
<div class="mb-3">
    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
    <select class="form-select" id="category" name="category_id" required>
        <option value="">Select Category</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?php echo $cat['category_id']; ?>" 
                    <?php echo isset($selected_category) && $selected_category == $cat['category_id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['category_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
```

## Number Input

```html
<div class="mb-3">
    <label for="price" class="form-label">Price (MWK) <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
    </div>
</div>
```

## Phone Input

```html
<div class="mb-3">
    <label for="phone" class="form-label">Phone Number</label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-phone"></i></span>
        <input type="tel" class="form-control" id="phone" name="phone" placeholder="+265 99 123 456">
    </div>
</div>
```

## Card

```html
<div class="card shadow">
    <div class="card-header bg-light border-bottom">
        <h5 class="card-title mb-0">Card Title</h5>
        <small class="text-muted">Optional subtitle</small>
    </div>
    <div class="card-body">
        <!-- Your content here -->
    </div>
</div>
```

## Card Without Header

```html
<div class="card shadow">
    <div class="card-body">
        <!-- Your content here -->
    </div>
</div>
```

## Modal

```html
<div class="modal fade" id="exampleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Your content here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Trigger button -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Open Modal
</button>
```

## Success Alert

```html
<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
```

## Error Alerts

```html
<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endforeach; ?>
```

## Warning Alert

```html
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i> 
    Warning message here.
</div>
```

## Button Group

```html
<div class="mb-3">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save
    </button>
    <button type="reset" class="btn btn-secondary">
        <i class="fas fa-redo"></i> Reset
    </button>
    <a href="index.php?page=previous" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
```

## Form with Layout

```html
<form action="index.php?page=example&action=submit" method="POST">
    <div class="card shadow">
        <div class="card-body">
            <!-- Input fields here -->
        </div>
        <div class="card-footer bg-light">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</form>
```

<?php
/**
 * Input Component - Inline Bootstrap form inputs
 * 
 * INLINE USAGE:
 * 
 * <!-- Text Input with Icon -->
 * <div class="mb-3">
 *     <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
 *     <div class="input-group">
 *         <span class="input-group-text"><i class="fas fa-user"></i></span>
 *         <input type="text" class="form-control" id="username" name="username" required>
 *     </div>
 *     <small class="form-text text-muted">At least 3 characters</small>
 * </div>
 * 
 * <!-- Error State -->
 * <div class="mb-3">
 *     <label for="email" class="form-label">Email</label>
 *     <input type="email" class="form-control <?php echo $errors ? 'is-invalid' : ''; ?>" 
 *            id="email" name="email" value="<?php echo htmlspecialchars($old_value ?? ''); ?>">
 *     <?php if (isset($errors['email'])): ?>
 *         <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['email']); ?></div>
 *     <?php endif; ?>
 * </div>
 * 
 * HELPER FUNCTIONS:
 */

if (!function_exists('input_text')) {
    /**
     * Simple text input helper
     */
    function input_text($name, $label = '', $icon = '', $value = '', $placeholder = '', $required = false, $error = '') {
        $input_class = $error ? 'form-control is-invalid' : 'form-control';
        ?>
        <div class="mb-3">
            <?php if ($label): ?>
                <label for="<?php echo htmlspecialchars($name); ?>" class="form-label">
                    <?php echo htmlspecialchars($label); ?>
                    <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
                </label>
            <?php endif; ?>
            <div class="<?php echo $icon ? 'input-group' : ''; ?>">
                <?php if ($icon): ?>
                    <span class="input-group-text"><i class="<?php echo htmlspecialchars($icon); ?>"></i></span>
                <?php endif; ?>
                <input type="text" class="<?php echo htmlspecialchars($input_class); ?>" 
                       id="<?php echo htmlspecialchars($name); ?>" name="<?php echo htmlspecialchars($name); ?>"
                       value="<?php echo htmlspecialchars($value); ?>"
                       placeholder="<?php echo htmlspecialchars($placeholder); ?>"
                       <?php echo $required ? 'required' : ''; ?>>
                <?php if ($error): ?>
                    <div class="invalid-feedback d-block">{{error}}</div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

function input($options = []) {
    $type = $options['type'] ?? 'text';
    $name = $options['name'] ?? '';
    $id = $options['id'] ?? $name;
    $label = $options['label'] ?? '';
    $placeholder = $options['placeholder'] ?? '';
    $value = htmlspecialchars($options['value'] ?? '');
    $icon = $options['icon'] ?? '';
    $required = $options['required'] ?? false;
    $disabled = $options['disabled'] ?? false;
    $readonly = $options['readonly'] ?? false;
    $minlength = $options['minlength'] ?? '';
    $maxlength = $options['maxlength'] ?? '';
    $min = $options['min'] ?? '';
    $max = $options['max'] ?? '';
    $step = $options['step'] ?? '';
    $pattern = $options['pattern'] ?? '';
    $autocomplete = $options['autocomplete'] ?? '';
    $class = $options['class'] ?? '';
    $rows = $options['rows'] ?? 4;
    $help_text = $options['help_text'] ?? '';
    $error = $options['error'] ?? '';
    
    $is_textarea = $type === 'textarea';
    $input_class = $error ? 'form-control is-invalid' : 'form-control';
    $input_class .= ' ' . $class;
    
    // Build attributes
    $attrs = [];
    if ($name) $attrs[] = "name=\"" . htmlspecialchars($name) . "\"";
    if ($id) $attrs[] = "id=\"" . htmlspecialchars($id) . "\"";
    if (!$is_textarea && $type) $attrs[] = "type=\"" . htmlspecialchars($type) . "\"";
    if ($placeholder) $attrs[] = "placeholder=\"" . htmlspecialchars($placeholder) . "\"";
    if (!$is_textarea && $value) $attrs[] = "value=\"" . $value . "\"";
    if ($required) $attrs[] = "required";
    if ($disabled) $attrs[] = "disabled";
    if ($readonly) $attrs[] = "readonly";
    if ($minlength) $attrs[] = "minlength=\"" . htmlspecialchars($minlength) . "\"";
    if ($maxlength) $attrs[] = "maxlength=\"" . htmlspecialchars($maxlength) . "\"";
    if ($min !== '') $attrs[] = "min=\"" . htmlspecialchars($min) . "\"";
    if ($max !== '') $attrs[] = "max=\"" . htmlspecialchars($max) . "\"";
    if ($step !== '') $attrs[] = "step=\"" . htmlspecialchars($step) . "\"";
    if ($pattern) $attrs[] = "pattern=\"" . htmlspecialchars($pattern) . "\"";
    if ($autocomplete) $attrs[] = "autocomplete=\"" . htmlspecialchars($autocomplete) . "\"";
    ?>
    <div class="mb-3">
        <?php if ($label): ?>
            <label for="<?php echo htmlspecialchars($id); ?>" class="form-label">
                <?php echo htmlspecialchars($label); ?>
                <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
            </label>
        <?php endif; ?>
        
        <div class="<?php echo $icon ? 'input-group' : ''; ?>">
            <?php if ($icon): ?>
                <span class="input-group-text"><i class="<?php echo htmlspecialchars($icon); ?>"></i></span>
            <?php endif; ?>
            
            <?php if ($is_textarea): ?>
                <textarea class="<?php echo htmlspecialchars($input_class); ?>" rows="<?php echo htmlspecialchars($rows); ?>" <?php echo implode(' ', $attrs); ?>><?php echo $value; ?></textarea>
            <?php else: ?>
                <input class="<?php echo htmlspecialchars($input_class); ?>" <?php echo implode(' ', $attrs); ?>>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="invalid-feedback d-block">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($help_text && !$error): ?>
            <small class="form-text text-muted d-block mt-1"><?php echo htmlspecialchars($help_text); ?></small>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Select Component
 * Reusable form select
 * 
 * @param array $options {
 *     'name' => string (required),
 *     'id' => string (default: name),
 *     'label' => string,
 *     'options' => array [value => label],
 *     'value' => string (selected value),
 *     'required' => boolean,
 *     'disabled' => boolean,
 *     'class' => string,
 *     'help_text' => string,
 *     'error' => string
 * }
 */
function select($options = []) {
    $name = $options['name'] ?? '';
    $id = $options['id'] ?? $name;
    $label = $options['label'] ?? '';
    $opts = $options['options'] ?? [];
    $value = htmlspecialchars($options['value'] ?? '');
    $required = $options['required'] ?? false;
    $disabled = $options['disabled'] ?? false;
    $class = $options['class'] ?? '';
    $help_text = $options['help_text'] ?? '';
    $error = $options['error'] ?? '';
    
    $select_class = $error ? 'form-select is-invalid' : 'form-select';
    $select_class .= ' ' . $class;
    ?>
    <div class="mb-3">
        <?php if ($label): ?>
            <label for="<?php echo htmlspecialchars($id); ?>" class="form-label">
                <?php echo htmlspecialchars($label); ?>
                <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
            </label>
        <?php endif; ?>
        
        <select class="<?php echo htmlspecialchars($select_class); ?>" 
                name="<?php echo htmlspecialchars($name); ?>" 
                id="<?php echo htmlspecialchars($id); ?>"
                <?php echo $required ? 'required' : ''; ?>
                <?php echo $disabled ? 'disabled' : ''; ?>>
            <option value="">-- Select --</option>
            <?php foreach ($opts as $opt_value => $opt_label): ?>
                <option value="<?php echo htmlspecialchars($opt_value); ?>" 
                        <?php echo $value === htmlspecialchars($opt_value) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($opt_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if ($error): ?>
            <div class="invalid-feedback d-block">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($help_text && !$error): ?>
            <small class="form-text text-muted d-block mt-1"><?php echo htmlspecialchars($help_text); ?></small>
        <?php endif; ?>
    </div>
    <?php
}
?>

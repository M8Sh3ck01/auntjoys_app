<?php
/**
 * Modal Component
 * Reusable Bootstrap modal
 * 
 * @param array $options {
 *     'id' => string (required, modal ID),
 *     'title' => string,
 *     'size' => string ('sm', 'lg', 'xl' - default: 'md'),
 *     'centered' => boolean (default: false),
 *     'backdrop' => string ('static', true, false - default: true),
 *     'content' => string (HTML content for body),
 *     'footer' => string (HTML content for footer - if false, no footer)
 * }
 */
function modal($options = []) {
    $id = $options['id'] ?? 'modal_' . uniqid();
    $title = $options['title'] ?? '';
    $size = $options['size'] ?? 'md';
    $centered = $options['centered'] ?? false;
    $backdrop = $options['backdrop'] ?? true;
    $content = $options['content'] ?? '';
    $footer = $options['footer'] ?? '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
    
    $size_class = $size !== 'md' ? 'modal-' . $size : '';
    $centered_class = $centered ? 'modal-dialog-centered' : '';
    $backdrop_attr = $backdrop === false ? 'data-bs-backdrop="false"' : ($backdrop === 'static' ? 'data-bs-backdrop="static" data-bs-keyboard="false"' : '');
    ?>
    <div class="modal fade" id="<?php echo htmlspecialchars($id); ?>" tabindex="-1" <?php echo $backdrop_attr; ?>>
        <div class="modal-dialog <?php echo htmlspecialchars($size_class . ' ' . $centered_class); ?>">
            <div class="modal-content">
                <?php if ($title): ?>
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo htmlspecialchars($title); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                <?php endif; ?>
                
                <div class="modal-body">
                    <?php echo $content; ?>
                </div>
                
                <?php if ($footer !== false): ?>
                    <div class="modal-footer">
                        <?php echo $footer; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
?>

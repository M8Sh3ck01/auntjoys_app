<?php
/**
 * Card Component - Simple inline card wrapper
 * Usage: Place directly in your view files
 * <?php
 *     $title = 'My Card';
 *     $subtitle = 'Optional subtitle';
 *     $shadow = true;
 * ?>
 * <div class="card <?php echo $shadow ? 'shadow' : ''; ?>">
 *     <?php if ($title): ?>
 *         <div class="card-header bg-light border-bottom">
 *             <h5 class="card-title mb-0"><?php echo htmlspecialchars($title); ?></h5>
 *             <?php if ($subtitle): ?>
 *                 <small class="text-muted"><?php echo htmlspecialchars($subtitle); ?></small>
 *             <?php endif; ?>
 *         </div>
 *     <?php endif; ?>
 *     <div class="card-body">
 *         <!-- Your content here -->
 *     </div>
 * </div>
 *
 * OR use as a helper function:
 */

if (!function_exists('card_component')) {
    function card_component($title = '', $subtitle = '', $content = '', $shadow = true, $class = '') {
        $shadow_class = $shadow ? 'shadow' : '';
        ?>
        <div class="card <?php echo htmlspecialchars($shadow_class . ' ' . $class); ?>">
            <?php if ($title): ?>
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($title); ?></h5>
                    <?php if ($subtitle): ?>
                        <small class="text-muted"><?php echo htmlspecialchars($subtitle); ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <?php echo $content; ?>
            </div>
        </div>
        <?php
    }
}
?>

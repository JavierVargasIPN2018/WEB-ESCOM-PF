<?php if (isset($errors) && !empty($errors)): ?>
  <div class="alert alert-danger">
    <?php foreach ($errors as $error): ?>
      <div><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

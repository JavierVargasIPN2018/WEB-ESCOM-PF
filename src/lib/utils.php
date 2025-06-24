<?php
function renderView($viewPath, $data = [])
{
  extract($data);
  include 'components/layouts/header.php';
  include $viewPath;
  include 'components/layouts/footer.php';
}

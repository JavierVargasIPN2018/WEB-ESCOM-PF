<?php
require_once 'components/icons/favorite-icon.php';

function favoriteButton($isFavorite, $placeId, $attributes = [])
{
  $defaultAttrs = [];

  $attrs = mergeAttributes($defaultAttrs, $attributes);
  $attrString = renderAttributes($attrs);

  $title = $isFavorite ? 'En favoritos' : 'Agregar a favoritos';

?>
  <form method="POST" action="/toggle-favorite" class="favorite-form">
    <input type="hidden" name="place_id" value="<?= $placeId ?>">
    <input type="hidden" name="redirect" value="<?= $placeId ?>">
    <input type="hidden" name="redirect_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

    <button type="submit" class="favorite-button card-star <?= $isFavorite ? 'active' : '' ?>"
      title="<?= $title ?>"
      <?= $attrString ?>>
      <?= favoriteIcon() ?>
      <span> <?= $title ?> </span>
    </button>
  </form>
<?php
}

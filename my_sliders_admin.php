<div class="wrap my_custom">

  <?php
  $sliders = get_option('my_sliders');
   ?>
  <h2>Réglages de mes sliders :</h2>

  <div id="message" class="notice" style="display:none"></div>

  <table class="list_sliders">
    <tr>
      <th>ID</th>
      <th>Nom du slider</th>
      <th>Nombre de slides</th>
      <th>Modifier</th>
      <th>Supprimer</th>
    </tr>
    <?php
    if (!empty($sliders)) {
      foreach ($sliders as $slider) {
        $slide_number = count($slider);
        echo '<tr>
                <td class="id_slider">'.$slider[0]['ID'].'</td>
                <td>'.$slider[0]['name'].'</td>
                <td>'.--$slide_number.'</td>
                <td class="modify_slider"><span class="dashicons-admin-generic dashicons"></span></td>
                <td class="delete_slider"><span class="dashicons dashicons-no"></span></td>
              </tr>';
      }
    }
    ?>
  </table>

  <button id="add_slider" type="button" name="add_slider">Ajouter un slider</button>

  <form id="form_add_slider" method="post" name="add_sliders" style="display:none;">

    <div class="text-center">
      <label class="slider-name">Nom du slider :</label>
      <input type="text" class="clean" name="slider_name" value="" required>
    </div>

    <div class="slide_div slide_number_0" data-slide="1">
      <p class="h3">Slide 1:</p>
      <img src="" alt="" class="clean_img">
      <input class="picture_slide" type="button" name="slide" value="Choisir une image">
      <input class="picture_slide_url clean" type="hidden" name="slide_url">
      <div class="caption_slide">
        <label class="caption_label">Légende de la slide :</label>
        <input class="caption_input clean" type="text" name="caption_slide" value="">
      </div>
      <label>Lien de la slide :</label>
      <select class="select_link" name="">
        <option value="">Aucun lien</option>
        <option value="custom">Lien personnalisé</option>
        <?php
          $args = array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'posts_per_page' => -1
          );
          $pages = get_posts($args);
         ?>
         <?php foreach ($pages as $page): ?>
           <option value="<?php echo get_permalink($page->ID); ?>"><?php echo $page->post_title ?></option>
         <?php endforeach; ?>
      </select>
      <div class="custom_link_container" style="display:none">
        <label>Lien personnalisé : </label>
        <input type="text" class="custom_link" value="">
      </div>
      <input type="hidden" class="link_slide clean" name="link_slide" value="">
    </div>
    <button id="add_slides_button" type="button" name="add_slide">Ajouter une slide</button>
    <input id="id_du_slider" type="hidden" name="id_du_slider" value="">
    <button id="delete_slides_button" type="button" class="delete_slide">Supprimer une slide</button>
    <input type="hidden" name="action" value="add_slider">
    <button id="add_slider_btn" type="submit" name="save_slides" data-action=""><span class="dashicons dashicons-yes"></span>Ajouter le slider !</button>
  </form>

</div>
<!-- echo str_replace('%7E', '~', $_SERVER['REQUEST_URI'])  -->
<script type="text/javascript">
<?php
  $js_array = json_encode($pages);
  echo "var pages = ". $js_array . ";\n";
?>
</script>

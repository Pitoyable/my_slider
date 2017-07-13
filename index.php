<?php
/*
Plugin Name: My sliders
Plugin URI: http://goyer.fr
Description: PLugin pour ajouter un slider et choisir les différentes slides.
Version: 1.0
Author: Paul Goyer
Author URI: http://goyer.fr
*/
function my_sliders_page() {
  add_menu_page( 'Mes sliders | Reglages', 'Mes sliders', 'administrator', 'my_sliders', 'my_slider_function', 'dashicons-slides', $position = null );
}
add_action( 'admin_menu' , 'my_sliders_page' );

function my_slider_function() {
  include('my_sliders_admin.php');
};

function my_sliders_admin_js() {
  wp_enqueue_script( 'my_sliders_js', plugins_url('my_sliders_admin.js', __FILE__), array('jquery'), '1.0');
}
add_action( 'admin_menu', 'my_sliders_admin_js' );

function admin_style()
{
    wp_enqueue_media();
    wp_enqueue_style('my_css_admin', plugins_url('my_sliders_admin.css', __FILE__));
}
add_action("admin_enqueue_scripts", "admin_style");

//
// FONCTIONS PROPRES A LA CREATION/MODIFICATION/SUPPRESSION DE SLIDER
//

function add_slider() {
  $content = get_option('my_sliders');

  if (empty($content)) {
    $content = array();
    $id = 0;
  } else {
    $max = max(array_keys($content));
    $id = $max+1;
  }
  $response = slider_function($id);

  header( "Content-Type: application/json" );
  echo json_encode($response);
  wp_die();
}
add_action( 'wp_ajax_add_slider', 'add_slider' );

function delete_slider() {
  $content = get_option('my_sliders');
  $id = intval($_POST['ID']);

  unset($content[$id]);
  update_option( 'my_sliders', $content );

  $response = 'ok';

  header( "Content-Type: application/json" );
  echo json_encode($response);
  wp_die();
};
add_action( 'wp_ajax_delete_slider', 'delete_slider' );

function modify_slider() {
  $content = get_option('my_sliders');
  $id = intval($_POST['ID']);

  $response = $content[$id];

  header( "Content-Type: application/json" );
  echo json_encode($response);
  wp_die();
};
add_action( 'wp_ajax_modify_slider', 'modify_slider' );


function slider_function($id = 0) {
  $content = get_option('my_sliders');
  $slider_name = sanitize_text_field( $_POST['slider_name'] );
  if (!empty($_POST['id_du_slider'])) {
    $id = intval($_POST['id_du_slider']);
  };
  $content[$id][0]['name'] = $slider_name;
  $content[$id][0]['ID'] = $id;
  // Premier slide
  $slide_caption = sanitize_text_field( $_POST['caption_slide'] );
  $content[$id][1]['caption'] = $slide_caption;
  $slides_number = 1;
  if (filter_var($_POST['slide_url'], FILTER_VALIDATE_URL)) {
    $slide_picture_url = sanitize_text_field( $_POST['slide_url'] );
    $content[$id][1]['url'] = $slide_picture_url;
    if (!empty($_POST['link_slide'])) {
      if (filter_var($_POST['link_slide'], FILTER_VALIDATE_URL)) {
        $slide_link = sanitize_text_field( $_POST['link_slide'] );
        $content[$id][1]['link'] = $slide_link;

      } else {
        $response = "error";
      }
    } else {
      $content[$id][1]['link'] = '';
    }
  } else {
    $response = 'error';
  }
  // Deuxième slide
  if (isset($_POST['slide_url_2'])) {
    $slide_caption_2 = sanitize_text_field( $_POST['caption_slide_2'] );
    $content[$id][2]['caption'] = $slide_caption_2;
    $slides_number = 2;
    if (filter_var($_POST['slide_url_2'], FILTER_VALIDATE_URL)) {
      $slide_picture_url_2 = sanitize_text_field( $_POST['slide_url_2'] );
      $content[$id][2]['url'] = $slide_picture_url_2;
      if (!empty($_POST['link_slide_2'])) {
        if (filter_var($_POST['link_slide_2'], FILTER_VALIDATE_URL)) {
          $slide_link_2 = sanitize_text_field( $_POST['link_slide_2'] );
          $content[$id][2]['link'] = $slide_link_2;

        } else {
          $response = 'error';
        }
      } else {
        $content[$id][2]['link'] = '';
      }
    } else {
      $response = 'error';
    }
  }
  // Troisième slide
  if (isset($_POST['slide_url_3'])) {
    $slide_caption_3 = sanitize_text_field( $_POST['caption_slide_3'] );
    $content[$id][3]['caption'] = $slide_caption_3;
    $slides_number = 3;
    if (filter_var($_POST['slide_url_3'], FILTER_VALIDATE_URL)) {
      $slide_picture_url_3 = sanitize_text_field( $_POST['slide_url_3'] );
      $content[$id][3]['url'] = $slide_picture_url_3;
      if (!empty($_POST['link_slide_3'])) {
        if (filter_var($_POST['link_slide_3'], FILTER_VALIDATE_URL)) {
          $slide_link_3 = sanitize_text_field( $_POST['link_slide_3'] );
          $content[$id][3]['link'] = $slide_link_3;

        } else {
          $response = 'error';
        }
      } else {
        $content[$id][3]['link'] = '';
      }
    } else {
      $response = 'error';
    }
  }
  if ($response != 'error') {
    update_option( 'my_sliders', $content );
  }
  return array('ID' => $id, 'nom' => $slider_name, 'slides' => $slides_number);
};

//
// CUSTOM FIELDS
//

// Fonction pour ajouter un custom fields sur les pages
function add_slider_list_meta_box() {
  add_meta_box(
    'slider_list',
    'Slider à afficher',
    'slider_list_meta',
    'page',
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'add_slider_list_meta_box' );

function slider_list_meta() {
  global $post;
  $meta = get_post_meta( $post->ID, 'slider_list', true);
  $sliders = get_option('my_sliders');
  ?>
  <input type="hidden" name="slider_list_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

  <p>Si vous désirez afficher un slider sur cette page, choisissez le dans la liste ci-dessous. Vous pouvez consulter vos sliders sur la page "Mes sliders" se trouvant en bas de votre menu d'administration. N'oubliez pas de mettre à jour votre page après toutes modifications.</p>

  <p>
  	<label for="slider_list[select]">Quel slider à afficher :</label>
  	<br>
    <select name="slider_list[select]" id="slider_list[select]">
			<option value="aucun" <?php selected( $meta['select'], 'aucun' ); ?>>Aucun</option>
      <?php foreach ($sliders as $slider): ?>
        <option value="<?php echo $slider[0]['ID'] ?>" <?php selected( $meta['select'], $slider[0]['ID'] ); ?>><?php echo $slider[0]['name'] ?></option>
      <?php endforeach; ?>

	  </select>
  </p>

  <p>
    <label for="slider_list[checkbox]">Afficher les légendes :
		<input type="checkbox" name="slider_list[checkbox]" value="checkbox" <?php if ( $meta['checkbox'] === 'checkbox' ) echo 'checked'; ?>>
	</label>
  </p>

  <?php
}

function save_slider_list_meta( $post_id ) {
	// verify nonce
	if ( !wp_verify_nonce( $_POST['slider_list_nonce'], basename(__FILE__) ) ) {
		return $post_id;
	}
	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	// check permissions
	if ( 'page' === $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	}

	$old = get_post_meta( $post_id, 'slider_list', true );
	$new = $_POST['slider_list'];

	if ( $new && $new !== $old ) {
		update_post_meta( $post_id, 'slider_list', $new );
	} elseif ( '' === $new && $old ) {
		delete_post_meta( $post_id, 'slider_list', $old );
	}
}
add_action( 'save_post', 'save_slider_list_meta' );

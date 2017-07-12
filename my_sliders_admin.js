jQuery(document).ready(function($) {

  // Fonction pour ouvrir l'uploader au click sur le bouton choisir image
  function mediaUploaderFunction() {
    var mediaUploader;

    $('.picture_slide').click(function(e) {
      var $this = $(this);
      e.preventDefault();
      e.stopImmediatePropagation();

      if (mediaUploader) {
        mediaUploader.open();
        return;
      }

      mediaUploader = wp.media({
        title: 'Choisir image',
        button: {
          text: 'Choisir image'
        }, multiple: false });

      mediaUploader.on('select', function() {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        $this.siblings('.picture_slide_url').val(attachment.url);
        $this.siblings('img').attr('src', attachment.url);
      });

      mediaUploader.open();
    });
  }

  // Fonction pour injecter la valeur de lien de la page sélectionnée dans l'input hidden
  function linkSlideValue() {
    $('.select_link').on('change', function() {
      $(this).siblings('.link_slide').val($(this).val());
    })
  }
  // Fonction pour le formulaire d'ajout/modification de slider
  function cleanForm() {
    $('.more_slide').remove();
    $('.clean').val('');
    $('.clean_img').attr('src', '');
    $('#id_du_slider').val('');
  };

  // Fonction pour supprimer un slider
  function delete_slider() {
    $('.delete_slider').on('click', function(e) {
      e.stopImmediatePropagation();
      e.preventDefault();
      var $this = $(this);

      var datas = {
        'action':'delete_slider',
        'ID': $(this).siblings('.id_slider').html()
      };

      jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: datas,
        success: function(data) {
          $this.parents('tr').remove();
          $('#message').addClass('notice-success');
          $('#message').removeClass('notice-error');
          $('#message').html('<p>Le slider a bien été supprimé !</p>');
          $('#message').show(300).delay(5000).hide(300);
        },
        error: function(data) {
          $('#message').removeClass('notice-success');
          $('#message').addClass('notice-error');
          $('#message').html('<p>Une erreur est survenue. Merci de réessayer ultérieurement. Si le problème persiste merci de contacter le développeur</p>');
          $('#message').show(300).delay(5000).hide(300);
        }
      })
    })
  }
  // Supprimer un slider au chargement de la page
  delete_slider();

  // Fonction pour modifier un slider
  function modify_slider() {
    $('.modify_slider').on('click', function(e) {
      e.stopImmediatePropagation();
      e.preventDefault();
      var $this = $(this),
      datas = {
        'action': 'modify_slider',
        'ID': $(this).siblings('.id_slider').html()
      };

      cleanForm();

      jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: datas,
        success: function(data) {
          $('#form_add_slider').show(300);
          mediaUploaderFunction();
          linkSlideValue();
          $('input[name=slider_name]').val(data[0]['name']);
          $('#id_du_slider').val(data[0]['ID']);
          data.shift();
          $.each(data, function(key, value) {
            if (key === 0) {
              return;
            } else {
              addSlide();
            }
          }) /* fin du each */
          $.each(data, function(key, value) {
            var slide_div = $('.slide_number_'+key);

            slide_div.children('.clean_img').attr('src', value.url);
            slide_div.children('.picture_slide_url').val(value.url);
            slide_div.children('.caption_slide').children('.caption_input').attr("value", value.caption);
            slide_div.children('.link_slide').val(value.link);
            slide_div.children('.select_link').val(value.link);
          }) /* fin deuxième each */

        },
        error: function(data) {
          $('#message').removeClass('notice-success');
          $('#message').addClass('notice-error');
          $('#message').html('<p>Une erreur est survenue. Merci de réessayer ultérieurement. Si le problème persiste merci de contacter le développeur</p>');
          $('#message').show(300).delay(5000).hide(300);
        }
      })

      add_slider_function('Le slider à bien été modifié, actualisé la page afin de le voir.', false);
    })
  }
  // Modifier un slider au chargement de la page
  modify_slider();


  // Fonction pour ajouter une slide
  function addSlide() {
    var slide_data     = $('#add_slides_button').prev().data('slide'),
        new_slide_data = slide_data+1;

    if (slide_data == 3) {
      return
    } else {
      $('<div class="slide_div more_slide slide_number_'+slide_data+'" data-slide="'+new_slide_data+'"><p class="h3">Slide '+new_slide_data+'</p><img src="" alt="" class="clean_img"><input class="picture_slide" type="button" name="slide" value="Choisir une image"><input class="picture_slide_url" type="hidden" name="slide_url_'+new_slide_data+'"><div class="caption_slide"><label class="caption_label">Légende de la slide :</label><input class="caption_input" type="text" name="caption_slide_'+new_slide_data+'" value=""></div><label>Lien de la slide :</label><select class="select_link select_number'+new_slide_data+'" name=""><option value="">Aucun lien </option></select><input type="hidden" class="link_slide" name="link_slide_'+new_slide_data+'" value=""></div>').insertBefore($('#add_slides_button'));

      mediaUploaderFunction();

      $.each(pages, function(key, value) {
        $('<option value="'+value.guid+'">'+value.post_title+'</option>').appendTo($('.select_number'+new_slide_data));
      })
      linkSlideValue();
    }
  }

  // Fonction pour ajouter un slider
  function add_slider_function($message = 'Le slider a bien été ajouté !', $td = true) {
    $('#form_add_slider').on('submit', function(e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      var datas = $(this).serializeArray();

      jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: datas,
        success: function(data) {
          if ($td == true) {
            $('<tr><td class="id_slider">'+data.ID+'</td><td>'+data.nom+'</td><td>'+data.slides+'</td><td class="modify_slider"><span class="dashicons dashicons-admin-generic"></span></td><td class="delete_slider"><span class="dashicons dashicons-no"></span></td></tr>').appendTo($('.list_sliders'));
          }

          delete_slider();
          $('#message').addClass('notice-success');
          $('#message').removeClass('notice-error');
          $('#message').html('<p>'+$message+'</p>');
          $('#message').show(300).delay(5000).hide(300);
          cleanForm();
        },
        error: function(data) {
          $('#message').removeClass('notice-success');
          $('#message').addClass('notice-error');
          $('#message').html('<p>Une erreur est survenue. Merci de réessayer ultérieurement. Si le problème persiste merci de contacter le développeur</p>');
          $('#message').show(300).delay(5000).hide(300);
        }
      })
    });
  }

  // Apparition du formulaire d'ajout de slider
  $('#add_slider').on('click', function() {
    cleanForm();
    $('#form_add_slider').show(300);
    mediaUploaderFunction();
    linkSlideValue();
    add_slider_function();

    // Ajout de slide au slider.
    $('#add_slides_button').on('click', function(e) {
      addSlide();
    })

    // Pour supprimer la dernière slide du slider
    $('.delete_slide').on('click', function(e) {
      e.preventDefault();
      var last = $('.slide_div:last-of-type');

      if (last.data('slide') == 1) {
        return;
      } else {
        $('.slide_div:last-of-type').remove();
      }
    })
  })
})

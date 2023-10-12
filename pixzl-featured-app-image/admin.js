jQuery(document).ready(function($) {
	var mediaUploader;

	$('#pixzl_upload_image_button').on('click', function(e) {
		e.preventDefault();

		// Wenn der Media-Uploader bereits existiert, öffne ihn
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		// Erstellen Sie den Media-Uploader.
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Wählen Sie ein Bild',
			button: {
				text: 'Bild auswählen'
			},
			multiple: false
		});

		// Nachdem ein Bild ausgewählt wurde, holen Sie die Bildinformationen und setzen Sie sie als Wert des versteckten Feldes
		mediaUploader.on('select', function() {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#pixzl_featured_app_image_id').val(attachment.id);
		});

		// Öffnen Sie den Media-Uploader
		mediaUploader.open();
	});

	// Bild entfernen und die ID aus dem verborgenen Feld entfernen
	$('#pixzl_remove_image_button').on('click', function(e) {
		e.preventDefault();
		$('#pixzl_featured_app_image_id').val('');
	});
});

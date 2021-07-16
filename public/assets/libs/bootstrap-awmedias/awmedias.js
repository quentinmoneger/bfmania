(function($) {

	var parseURL = document.currentScript.src.split('/');
	var baseURL = parseURL.slice(0, parseURL.length - 1).join('/');

	$.fn.awMedias = function(options, ) {
		var settings = $.extend({
			url_get: '',
			url_save: '',
			type: 'file',
			max_size: 3000000,
			title: 'Liste des médias',
			closeLabel: 'Fermer',
			showName: true,
		}, options);
		
		return this.click(function(e){
			_target = e.target;
			if($('#awMediasModal').length == 0){
				addBox();
			}
			$('#awMediasModal').modal('show');

			var accept = $(this).attr('accept') != undefined ? $(this).attr('accept') : 'image/*';
			var $myCrop;
			var cropped = ($(this).data('width') && $(this).data('height')) ? true : false;
			// Preparation des variables pour le crop
			if(cropped){
				var crop_width = $(this).data('width');
				var crop_height = $(this).data('height');
				var crop_ratio = crop_width / crop_height;
			}

			// On charge les medias
			getMedias();
			$('#awMediasModal').on('shown.bs.modal', function(e){
				var mediaBox = $('#aw-media-list').width();
				var figure = countColItem(mediaBox);
				var size = figure - 16;

				$('figure.is-media').css({
					'height': size + 'px',
					'width': size + 'px',
				});


				$('#dropbtn').click( function(){
					$('#dropzone').slideToggle('slow', function(){
						switchButton();
					});
				});

				$('#awMediasModal').on('click', 'button[name="open_file"]', function(el){
					el.preventDefault();
					el.stopPropagation();
					$('input#file').click();
					$('#dropfile').css('border', '3px dashed green');
					$(el.target).blur();
				});

				$('#awMediasModal').on('change', '#file', function(element){
					element.preventDefault();
					element.stopPropagation();

					var file = document.getElementById('file').files[0];

					if(file.size < settings.max_size){
						var fd = new FormData($(element.target).closest('form')[0]);
						uploadData(fd);
					}
					else {
						alert('Le poids du fichier dépasse la taille maximum autorisée');
					}

				});

				$('#awMediasModal').on('dragenter', '#dropfile', function() {
					$(this).css('border', '3px dashed red');
					
					return false;
				}).on('dragover', '#dropfile', function(e){
					e.preventDefault();
					e.stopPropagation();
					
					$(this).css('border', '3px dashed red');
					
					return false;
				}).on('dragleave', '#dropfile', function(e) {
					e.preventDefault();
					e.stopPropagation();
					
					$(this).css('border', '3px dashed #bbb');
					
					return false;

				}).on('drop', '#dropfile', function(e) {
					if(e.originalEvent.dataTransfer){
						if(e.originalEvent.dataTransfer.files.length) {
							e.preventDefault();
							e.stopPropagation();
							$(this).css('border', '3px dashed green');
							
							var file = e.originalEvent.dataTransfer.files;
							if(file[0].size < settings.max_size){
								var fd = new FormData();
								fd.append('file', file[0]);

								uploadData(fd);
							}
							else {
								alert('Le poids du fichier dépasse la taille maximum autorisée');
							}
						}
					}
					else {
						$(this).css('border', '3px dashed #bbb');
					}

					return false;
				});



				$('#awMediasModal').one('click', '.selectable', function(event){
					event.preventDefault();
					event.stopPropagation();

					myFile = $(event.currentTarget);

					if(cropped){
						
						var cropZoneWidth = 600, // Largeur de la zone crop 
							cropZoneHeight = cropZoneWidth / crop_ratio; // Hauteur de la zone crop

						var script = document.createElement('script');
							script.type = 'text/javascript';
							script.src = baseURL+'/croppie.min.js';

						if($('body script[src="' + baseURL+'/croppie.min.js"]').length <= 0){
							$('body').append(script);
						}

						var linkCSS = document.createElement('link');
							linkCSS.rel = 'stylesheet';
							linkCSS.href = baseURL+'/croppie.css';
						
						if($('head link[href="' + baseURL+'/croppie.css"]').length <= 0){
							$('head').append(linkCSS);
						}

						$('#aw-media-list').html(myFile);
						$('#dropbtn').remove();
						myFile.removeClass('is-media img-thumbnail selectable').addClass('is-crop').removeAttr('style');
						$myCrop = myFile.find('img').croppie({
							viewport: {
								width: cropZoneWidth, // Largeur de la zone crop
								height: cropZoneHeight, // Hauteur de la zone crop
							},
							boundary: {
								width: cropZoneWidth + 100,
								height: cropZoneHeight + 100,
							},
							enforceBoundary: true,
							enableOrientation: true,
						});


						$('#awMediasModal').find('.modal-footer').html('<button id="saveCroppped" class="btn btn-first">Sauvegarder</button>');

						$('#saveCroppped').click(function(e){
							e.stopPropagation();
							e.preventDefault();
							// Sauvegarde
							$myCrop.croppie('result', {
								type: 'canvas',
								size: {
									width: crop_width,
									height: crop_height,
								},
								format: 'jpg',
							}).then(function(response) {

								saveCroppedImage({img_crop_base64: response, filename: myFile.data('awmediaName')}, function(result){
									if(result.status == true){

										imageFile = {
											awmediaName: result.medias.name,
											awmediaType: result.medias.type,
											awmediaSize: result.medias.size,
											awmediaTmpName: result.medias.tmp_name,
										}
										addInput(_target, imageFile);
									}
									else {
										alert('Une erreur est survenue lors du crop de l\'image');
										console.log(result);
									}
								});
							});
						});

					}
					else {
						imageFile = {
							awmediaName: myFile.data('awmediaName'),
							awmediaType: myFile.data('awmediaType'),
							awmediaSize: myFile.data('awmediaSize'),
							awmediaTmpName: myFile.data('awmediaTmpName'),
						}
						addInput(_target, imageFile);
					}
				});

			});

			// On détruit la modal
			$('#awMediasModal').on('hidden.bs.modal', function(e){
				$('#awMediasModal').modal('dispose');
				$('#awMediasModal').remove();
			});
		});

		function addBox()
		{
			html = '<div class="modal fade" id="awMediasModal" tabindex="-1" role="dialog">';
			html+= '<div class="modal-dialog modal-dialog-centered" role="document" style="max-width:90%">'
			html+= '<div class="modal-content">';
			html+= '<div class="modal-header">';
				html+= '<h5 class="modal-title">' + settings.title + ' <button id="dropbtn" class="btn btn-outline-dark btn-sm ml-3 px-3">Ajouter</button>';
				html+= '</h5>';
				html+='<button type="button" class="close" data-dismiss="modal" aria-label="'+settings.closeLabel+'"><span aria-hidden="true">&times;</span></button>';
			html+='</div>';
			html+= '<div class="modal-body row no-gutters" id="aw-media-list">';
			html+= '</div>';
			//html+= '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">' + settings.closeLabel + '</button><button type="button" class="btn btn-primary">Save changes</button></div>';
			html+= '<div class="modal-footer">&nbsp;</div>';
			html+= '</div>';
			html+= '</div>';
			html+= '</div>';

			var popup = $(html);
			$(popup).appendTo('body');
		}

		function getMedias(accept)
		{
			if(settings.url != ''){
				$.get(settings.url_get, {action: settings.type, mime: accept}, function(e){
					$('#aw-media-list').html(e);
					getDropZone();
				});
			}
		}
		// Permet de mettre un max d'image visible 
		function countColItem(widthBox)
		{
			var figure = widthBox / 10;
			if(figure < 100){
				nb_items = Math.round(widthBox / 100) - 1;
				figure = widthBox / nb_items;
			}
			return figure;
		}

		function getDropZone()
		{

			html = '<div class="col-12 mb-4" id="dropzone" style="display: none;">';
			html+= '<div id="dropfile" class="d-flex justify-content-center align-items-center">';
			html+= '<div>'+
						'<p class="mb-2 font20">Glissez votre fichier dans cette zone pour les uploader</p>'+
						'<p class="small text-gray mb-2">ou</p>'+
						'<form method="post" enctype="multipart/form-data" class="d-none">'+
							'<input type="hidden" name="MAX_FILE_SIZE" value="'+ settings.max_size +'">'+
							'<input type="file" id="file" name="file">'+
						'</form>'+
						'<button type="button" name="open_file" class="btn btn-outline-secondary">Sélectionner un fichier</button>'+
						'<br><br><small class="text-muted">Taille de fichier maximale : '+ fileConvertSize(settings.max_size) +'</small>'+
						'<div class="progress mt-3">'+
							'<div class="progress-bar progress-bar-striped bg-first" id="progressup" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>'+
						'</div>'+
					'</div>'+
		 		'</div>'+
			'</div>';

			html+= '<div class="w-100"></div>';

			if($('#dropzone').length == 0){
				$('#aw-media-list').prepend(html);
				switchButton();
			}
		}

		function fileConvertSize(aSize){
			aSize = Math.abs(parseInt(aSize, 10));
			var def = [[1, ''], [1000, 'ko'], [1000*1000, 'Mo'], [1000*1000*1000, 'Go'], [1000*1000*1000*1000, 'To']];
			for(var i=0; i<def.length; i++){
				if(aSize<def[i][0]) return Math.round(aSize/def[i-1][0]) +' '+def[i-1][1];
			}
		}


		function saveCroppedImage(datas, func)
		{
			$.post(settings.url_save, datas, function(result){
				return func(result);
			});
		}


		// Ajout des champs pour simuler un fichier
		function addInput(target, myFile)
		{
			_name = $(target).attr('name');

			// Suppression si existant
			if($('.aw-media-filedata-'+_name).length){
				$('.aw-media-filedata-'+_name).remove();
			}
			// Ajout
			var input = '<span class="aw-media-filedata-'+_name+'">';
			if(settings.showName){
				input+= '<span class="aw-media-filename">'+myFile.awmediaName+'</span>';
			}
			input+= '<input type="hidden" name="'+_name+'[name]" value="'+myFile.awmediaName+'">';
			input+= '<input type="hidden" name="'+_name+'[type]" value="'+myFile.awmediaType+'">';
			input+= '<input type="hidden" name="'+_name+'[size]" value="'+myFile.awmediaSize+'">';
			input+= '<input type="hidden" name="'+_name+'[tmp_name]" value="'+myFile.awmediaTmpName+'">';
			input+= '<input type="hidden" name="'+_name+'[error]" value="0">';
			input+= '</span>';
			$(_target).after(input);

			if($('#'+_name+'_preview').length){
				$('#'+_name+'_preview').attr('href', myFile.awmediaTmpName); 
				$('#'+_name+'_preview > img').attr('src', myFile.awmediaTmpName); 
			}
			$('#awMediasModal').modal('hide');
		}


		// Upload de l'image dragdrop / file
		function uploadData(formdata){

			$.ajax({
				url: settings.url_save, 
				type: 'post',
				data: formdata,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function(res){
				},
				xhr: function(){
					var xhr = $.ajaxSettings.xhr() ;

					xhr.upload.onprogress = function(evt){ 
						total = evt.loaded/evt.total*100;
						$('[role="progressbar"]').css('width', parseInt(total) + '%').attr('aria-valuenow', parseInt(total)).text(parseInt(total) + ' %');
					};
					xhr.upload.onload = function(){ 
						$('#dropfile').css('border', '3px dashed #bbb');
						// getMedias();
					};
					return xhr;
				}
			}).done(function(data){
				getMedias();
				setTimeout(function(){
					$('[role="progressbar"]').css('width', '0%').attr('aria-valuenow', 0).text(' ');
				}, 1000);
			});
		}


		// Change le bouton
		function switchButton()
		{
			display = $('#dropzone').css('display');

			if(display == 'block'){
				$('#dropbtn').text('Masquer').blur();
			}
			else if(display == 'none'){
				$('#dropbtn').text('Ajouter').blur();
			}
		}

	};

}(jQuery));
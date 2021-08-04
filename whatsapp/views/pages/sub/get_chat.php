<?php
if( isset($contact->pushname) ){
	$name = $contact->pushname;
}else if( isset($contact->name) ){
	$name = $contact->name;
}else{
	$name = "+".$contact->number;
}
?>

<div class="user-chat w-100 overflow-hidden" data-chat-id="<?php _e($chat_id)?>" data-endpoint="<?php _e( get_module_url("get/send_message?chat_id=".$chat_id) )?>" data-get-message="<?php _e( get_module_url("get/get_message?chat_id=".$chat_id) )?>">
	<div class="d-lg-flex h-100">
		<div class="w-100 h-100 overflow-hidden position-relative">
			<div class="p-20 p-lg-4 border-bottom">
				<div class="row align-items-center">
					<div class="col-sm-4 col-8">
						<div class="media align-items-center">
							<div class="d-block d-lg-none mr-2">
								<a href="javascript:void(0);" class="user-chat-remove wa-back-submenu text-muted fs-16 p-2">
									<i class="ri-arrow-left-s-line"></i>
								</a>
							</div>
							<div class="mr-3">
								<img src="<?php _e(isset($contact->avatar)?$contact->avatar:get_avatar($name))?>" class="rounded-circle avatar-xs"></img>
							</div>
							<div class="media-body overflow-hidden">
								<div class="font-size-16 mb-0 text-truncate">
									<a href="javascript:void(0);" class="text-reset user-profile-show"><?php _e($name)?></a>
									<i class="ri-record-circle-fill font-size-10 text-success d-inline-block ml-1"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="chat-conversation">
				<div class="wa-body wa-scroll">
					<?php if(!empty($result)){?>

						<?php foreach ($result as $key => $value):?>

							<?php if($value->type == "chat"){?>
								<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>"  data-id="<?php _e( $value->id->id )?>">
									<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
	                                <div class="message">
	                                  	<span><?php _e( turnUrlIntoHyperlink($value->body), false)?></span>
	                                  	<div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
	                                </div>
	                            </div>
	                          	<div class="clearfix"></div>
							<?php }else if($value->type == "image"){?>
								<?php if( isset($value->attachmentData) ){?>
								<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
									<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
                                  	<div class="media">
	                                    <a href="data:<?php _e( $value->attachmentData->mimetype )?>;base64,<?php _e( $value->attachmentData->data )?>" data-fancybox="images"><img src="data:<?php _e( $value->attachmentData->mimetype )?>;base64,<?php _e( $value->attachmentData->data )?>"></a>
	                                    <div class="text"><?php _e( turnUrlIntoHyperlink($value->body), false)?></div>
	                                    <div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
                                  	</div>
                                </div>
                                <div class="clearfix"></div>
                                <?php }?>
							<?php }else if($value->type == "sticker"){?>
								<?php if( isset($value->attachmentData) ){?>
								<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
									<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
                                  	<div class="media bg-transparent">
	                                    <a href="data:<?php _e( $value->attachmentData->mimetype )?>;base64,<?php _e( $value->attachmentData->data )?>" data-fancybox="images"><img src="data:<?php _e( $value->attachmentData->mimetype )?>;base64,<?php _e( $value->attachmentData->data )?>"></a>
	                                    <div class="text"><?php _e( turnUrlIntoHyperlink($value->body), false)?></div>
	                                    <div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
                                  	</div>
                                </div>
                                <div class="clearfix"></div>
                            	<?php }?>
							<?php }else if($value->type == "video"){?>
								<?php if( isset($value->attachmentData) ){?>
								<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
									<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
                                  	<div class="media">
	                                    <a href="javascript:void(0);">
	                                      	<video class="video" controls="">
	                                        	<source type="video/mp4" src="data:<?php _e( $value->attachmentData->mimetype )?>;base64,<?php _e( $value->attachmentData->data )?>">
	                                      	</video>
	                                      	<div class="text"><?php _e( turnUrlIntoHyperlink($value->body), false)?></div>
	                                    </a>
	                                    <div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
                                  	</div>
                                </div>
                                <div class="clearfix"></div>
                                <?php }?>
							<?php }else if($value->type == "document"){?>
								<?php if( isset($value->attachmentData) ){?>
								<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
									<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
                                  	<div class="file">
	                                    <a href="javascript:void(0);" onclick="WhatsappJs.download('<?php _e( $value->attachmentData->data )?>', '<?php _e( $value->attachmentData->mimetype )?>', '<?php _e( $value->attachmentData->filename )?>')">
	                                      	<div class="download">
	                                        	<div class="icon-file"><i class="fas fa-file"></i></div>
	                                        	<div class="file-name"><?php _e( turnUrlIntoHyperlink($value->body), false)?></div>
	                                        	<div class="icon-download"><i class="fas fa-download"></i></div>
	                                      	</div>
	                                      	<div class="text"></div>
	                                    </a>
	                                    <div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
                                  	</div>
                            	</div>
                                <div class="clearfix"></div>
                                <?php }?>
							<?php }else if($value->type == "location"){?>
								<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
									<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
                                  	<div class="media">
	                                    <iframe width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=<?php _e($value->location->latitude)?>,<?php _e($value->location->longitude)?>+(title)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
	                                    <?php if($value->location->description){?>
	                                    <div class="text"><?php _e( turnUrlIntoHyperlink($value->location->description), false)?></div>
	                                	<?php }?>
	                                    <div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
                                  	</div>
                                </div>
                                <div class="clearfix"></div>
                            <?php }else if($value->type == "vcard"){
                            	
                            ?>

                            	<?php if ( !empty($value->vCards) ): ?>
                            		
                            		<?php foreach ($value->vCards as $vCard): ?>
                            			
                            			<?php 
                            			$parser = new JeroenDesloovere\VCard\VCardParser($vCard);
                            			if( isset( $parser->getCardAtIndex(0)->fullname ) || isset( $parser->getCardAtIndex(0)->phone ) || isset( $parser->getCardAtIndex(0)->email ) ){
                            			?>
										<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
											<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
		                                  	<div class="media">
		                                  		<div class="fs-20 text-success"><i class="ri-contacts-book-line"></i></div>
		                                  		<div class="fw-6"><?php _e( $parser->getCardAtIndex(0)->fullname , false)?></div>
		                                  		<?php if( isset( $parser->getCardAtIndex(0)->phone ) ){?>
				                                    <?php foreach ($parser->getCardAtIndex(0)->phone as $phones): ?>
				                                    	
				                                    	<?php foreach ($phones as $key => $phone): ?>
				                                    		<div><?php _e( $phone , false)?></div>
				                                    	<?php endforeach ?>

				                                    <?php endforeach ?>
				                               	<?php }?>
				                               	<?php if( isset( $parser->getCardAtIndex(0)->email ) ){?>
				                                    <?php foreach ($parser->getCardAtIndex(0)->email as $emails): ?>
				                                    	
				                                    	<?php foreach ($emails as $key => $email): ?>
				                                    		<div><?php _e( $email , false)?></div>
				                                    	<?php endforeach ?>

				                                    <?php endforeach ?>
				                               	<?php }?>
				                               	<div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
		                                  	</div>
		                                </div>
		                                <div class="clearfix"></div>
		                                <?php }?>

                            		<?php endforeach ?>

                            	<?php endif ?>

                            <?php }else if($value->type == "multi_vcard"){
                            	
                            ?>

                            	<?php if ( !empty($value->vCards) ): ?>
                            		
                            		<?php foreach ($value->vCards as $vCard): ?>
                            			
                            			<?php 
                            			$parser = new JeroenDesloovere\VCard\VCardParser($vCard);
                            			if( isset( $parser->getCardAtIndex(0)->fullname ) || isset( $parser->getCardAtIndex(0)->phone ) || isset( $parser->getCardAtIndex(0)->email ) ){
                            			?>
										<div class="wa-item <?php _e( $value->fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
											<a href="<?php _e( get_module_url("get/delete_message?chat_id=".$value->id->remote->_serialized."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
		                                  	<div class="media">
		                                  		<div class="fs-20 text-success"><i class="ri-contacts-book-line"></i></div>
		                                  		<div class="fw-6"><?php _e( $parser->getCardAtIndex(0)->fullname , false)?></div>
		                                  		<?php if( isset( $parser->getCardAtIndex(0)->phone ) ){?>
				                                    <?php foreach ($parser->getCardAtIndex(0)->phone as $values): ?>
				                                    	
				                                    	<?php foreach ($values as $key => $phone): ?>
				                                    		<div><?php _e( $phone , false)?></div>
				                                    	<?php endforeach ?>

				                                    <?php endforeach ?>
				                               	<?php }?>
				                               	<?php if( isset( $parser->getCardAtIndex(0)->email ) ){?>
				                                    <?php foreach ($parser->getCardAtIndex(0)->email as $values): ?>
				                                    	
				                                    	<?php foreach ($values as $key => $email): ?>
				                                    		<div><?php _e( $email , false)?></div>
				                                    	<?php endforeach ?>

				                                    <?php endforeach ?>
				                               	<?php }?>
				                               	<div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
		                                  	</div>
		                                </div>
		                                <div class="clearfix"></div>
		                                <?php }?>

                            		<?php endforeach ?>

                            	<?php endif ?>

							<?php }else{?>

							<?php }?>

							
		
						<?php endforeach ?>

					<?php }?>
				</div>
			</div>
			<div class="wa-editor">
				<div class="editor">
					<?php if( _p("whatsapp_chat_text") ){?>
				    <textarea class="input-message wa-text-message" placeholder="<?php _e("Enter message")?>"></textarea>
				    <?php }?>
			  	</div>
			  	<div class="action">
			  		<?php if( _p("whatsapp_chat_text") ){?>
				    <a href="javascript:void(0);" class="wa-btn-send-message"><i class="fas fa-paper-plane"></i></a>
				    <?php }?>
			  		<?php if( _p("whatsapp_chat_media") ){?>
				    <a href="javascript:void(0);" class="wa-fileinput-button"><i class="fas fa-paperclip"></i><input id="wa_send_media" type="file" name="file"></a>
			  		<?php }?>
			  	</div>
			</div>
		</div>
	</div>
</div>


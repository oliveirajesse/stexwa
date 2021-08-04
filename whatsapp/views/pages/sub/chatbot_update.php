<div class="p-25 wa-chatbot-update">
	<h4 class="mb-4 align-items-center d-flex">
		<div class="d-block d-lg-none mr-2">
			<a href="javascript:void(0);" class="user-chat-remove wa-back-submenu text-muted fs-16 p-2">
				<i class="ri-arrow-left-s-line"></i>
			</a>
		</div>
		<div><?php _e("Update")?></div>
	</h4>

	<div class="row">
		<div class="col-md-12 post">
			<form class="actionForm post-create p-t-10" action="<?php _e( get_module_url("chatbot_save") )?>" data-call-after="WhatsappJs.reload_chatbot(result);">
				<div class="post-content m-b-15">
		            <h5 class="mb-3 fs-14"><?php _e('Status')?></h5>
		            <div class="mb-2">
		                <label class="i-radio i-radio--tick i-radio--brand m-r-10">
		                    <input type="radio" name="status" checked="true" value="1" <?php _e( get_data($result, 'status', 'radio', 1) )?> > <?php _e('Enable')?>
		                    <span></span>
		                </label>
		                <label class="i-radio i-radio--tick i-radio--brand m-r-10">
		                    <input type="radio" name="status" value="0" <?php _e( get_data($result, 'status', 'radio', 0) )?> > <?php _e('Disable')?>
		                    <span></span>
		                </label>
		            </div>

		            <h5 class="mb-3 fs-14"><?php _e('Type')?></h5>
		            <div class="mb-2">
		                <label class="i-radio i-radio--tick i-radio--brand m-r-10">
		                    <input type="radio" name="type" checked="true" value="1" <?php _e( get_data($result, 'type', 'radio', 1) )?> > <?php _e('Message contains the keyword')?>
		                    <span></span>
		                </label>
		                <label class="i-radio i-radio--tick i-radio--brand m-r-10">
		                    <input type="radio" name="type" value="2" <?php _e( get_data($result, 'type', 'radio', 2) )?> > <?php _e('Message contains whole keyword')?>
		                    <span></span>
		                </label>
		            </div>

					<?php if(!empty($result)){?>
						<input type="hidden" class="form-control" name="ids" required="" value="<?php _e( $result->ids )?>">
					<?php }?>
					<input type="hidden" class="form-control" name="instance_id" required="" value="<?php _e( $instance_id )?>">
					<div class="form-group">
						<input class="form-control" name="name" required="" placeholder="<?php _e("Name")?>" value="<?php _e( get_data($result, 'name') )?>">
					</div>
					<div class="form-group">
						<input class="form-control tagsinput" type="text" name="keywords" data-role="tagsinput" placeholder="<?php _e("Enter keywords")?>" value="<?php _e( get_data($result, 'keywords') )?>">
					</div>
					<?php if( _p("whatsapp_chatbot_media") ){?>
				    <div class="mt-3">
			            <?php _e( $file_manager, false) ?>
				    </div>
				    <?php }?>
					<div class="post">
				        <div class="post-content">
			                <?php _e( $block_caption, false)?>
			                <ul class="text-success small m-b-0 m-t-3">
	                            <li><?php _e("Random message by Spintax")?></li>
	                            <li><?php _e("Ex: {Hi|Hello|Hola}")?></li>
	                        </ul>
				        </div>
				    </div>
					
				</div>

			  	<button type="submit" class="btn btn-info m-b-25"><?php _e('Save')?></a>
			</form>
		</div>
	</div>

</div>
<?php if(!empty($result)){?>
    <script type="text/javascript">
        var caption = `<?php _e($result->caption)?>`;

        <?php if($result->media != NULL){?>
        var medias = <?php _e($result->media)?>;
    	<?php }else{?>
		var medias = [];
    	<?php }?>

        $(function(){

            setTimeout(function(){
                var el = $("textarea[name=caption]").emojioneArea();
                el[0].emojioneArea.setText(caption);

                if(medias != null){
                    for (var i = 0; i < medias.length; i++) {
                        File_Manager.addFile(medias[i], medias[i]);
                    }
                }
            }, 1000);

        });

    </script>
<?php }?>
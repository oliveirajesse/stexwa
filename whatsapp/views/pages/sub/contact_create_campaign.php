<div class="p-25 wa-bulk-update p-25">
	<h4 class="mb-4 d-flex">
		<div class="d-block d-lg-none mr-2">
			<a href="javascript:void(0);" class="user-chat-remove wa-back-submenu text-muted fs-16 p-2">
				<i class="ri-arrow-left-s-line"></i>
			</a>
		</div>
		<div><?php _e('Create campaign')?></div>
	</h4>

	<div class="row">
		<div class="col-md-12 post">
			<form class="actionForm post-create p-t-10" action="<?php _e( get_module_url("bulk_save") )?>" data-call-after="WhatsappJs.reload_bulk_schedules(result);">
				<div class="post-content m-b-15">
					<?php if(!empty($item)){?>
						<input type="hidden" class="form-control" name="ids" required="" value="<?php _e( $item->ids )?>">
					<?php }?>
					<input type="hidden" class="form-control" name="instance_id" required="" value="<?php _e( $instance_id )?>">
					<div class="form-group">
						<input class="form-control" name="name" required="" placeholder="<?php _e("Campaign name")?>">
					</div>
					<div class="form-group">
						<select class="form-control" name="group" required="">
							<option value=""><?php _e("Select contact group")?></option>
							<?php if(!empty($groups)){
								foreach ($groups as $key => $value) {
							?>
							<option value="<?php _e($value->id)?>"><?php _e($value->name)?></option>
							<?php }}?>
						</select>
					</div>
					<?php if( _p("whatsapp_bulk_media") ){?>
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

				<div class="post-schedule m-b-15 active">
					<input type="hidden" name="is_schedule" value="1" >
					<?php if(empty($item)){?>
					<div class="post-schedule-content">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label><?php _e('Time post')?></label>
									<input type="text" class="form-control datetime" autocomplete="off" name="time_post" value="">
								</div>
							</div>
							<div class="col-6">
								<div class="form-group">
									<label><?php _e('Random message interval by minimum (minute)')?></label>
									<input type="number" class="form-control" autocomplete="off" name="min_interval_per_post" value="1">
								</div>
							</div>
							<div class="col-6">
								<div class="form-group">
									<label><?php _e('Random message interval by maximum (minute)')?></label>
									<input type="number" class="form-control" autocomplete="off" name="max_interval_per_post" value="5">
								</div>
							</div>
						</div>
					</div>
					<?php }else{?>
					<div class="post-schedule-content">
						<div class="row">
							<div class="col-6">
								<div class="form-group">
									<label><?php _e('Random message interval by minimum (minute)')?></label>
									<input type="number" class="form-control" autocomplete="off" name="min_interval_per_post" value="1">
								</div>
							</div>
							<div class="col-6">
								<div class="form-group">
									<label><?php _e('Random message interval by maximum (minute)')?></label>
									<input type="number" class="form-control" autocomplete="off" name="max_interval_per_post" value="5">
								</div>
							</div>
						</div>
					</div>
					<?php }?>
				</div>

			  	<button type="submit" class="btn btn-info m-b-25"><?php _e('Schedule')?></a>
			</form>
		</div>
	</div>

</div>
<?php if(!empty($item)){?>
    <script type="text/javascript">
        
        var contact_group_id = `<?php _e($item->contact_group_id)?>`;
        var name = `<?php _e($item->name)?>`;
        var caption = `<?php _e($item->data)?>`;

        <?php if($item->media != NULL){?>
        var medias = <?php _e($item->media)?>;
    	<?php }else{?>
		var medias = [];
    	<?php }?>
        var min_delay = '<?php _e( $item->min_delay )?>';
        var max_delay = '<?php _e( $item->max_delay )?>';

        $(function(){

            setTimeout(function(){
                $("[name=name]").val(name);
                $("[name=min_interval_per_post]").val(min_delay);
                $("[name=max_interval_per_post]").val(max_delay);
                $("[name=group]").val(contact_group_id);

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
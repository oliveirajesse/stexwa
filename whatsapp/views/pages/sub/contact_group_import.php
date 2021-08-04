<div class="modal fade wa-contact-group-import-modal" id="wa-contact-group-import-modal" tabindex="-1"
    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

        	<form class="actionForm" action="<?php _e( get_module_url( 'ajax_add_phone/'.segment(4) ) )?>" data-call-after="WhatsappJs.reload_contact_group();">
            <div class="modal-header">
                <h3 class="modal-title"><i class="ri-user-add-line"></i> <?php _e("Import contact")?></h3>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
					
			    	<label for="phone_numbers">
			    		<?php _e('Add multiple phone numbers')?>
			    		<ul class="text-success small m-b-0">
			    			<li><?php _e("Every phone number must be all one with it's dial code. Each phone number is separated by break line")?></li>
			    			<li><?php _e("E.g. (+84) 1234567890 must me 841234567890")?></li>
			    		</ul>
			    	</label>
			    	<textarea class="form-control" name="phone_numbers" id="phone_numbers" rows="20" placeholder="<?php _e("Validate exapmle:")?>

841234567890
840123456789
+840123456798"></textarea>
            </div>
	        <div class="modal-footer">
	        	<button type="button" class="btn btn-dark" data-dismiss="modal"><?php _e('Close')?></button>
			  	<button type="submit" class="btn btn-primary"><?php _e('Submit')?></button>
	        </div>
        </div>
		</form>
    </div>
</div>
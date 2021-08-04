<!-- Start chats tab-pane -->
<div class="tab-pane h-100 show active autoresponder" id="pills-chat" role="tabpanel post" aria-labelledby="pills-chat-tab">
        <div class="px-4 pt-4">
            <h4 class="mb-4"><?php _e('Auto responder')?></h4>
        </div> <!-- .p-4 -->

        <div class="chat-message-detail wa-scroll px-2">
            <form class="actionForm post-create p-t-10 position-relative" action="<?php _e( get_module_url( 'autoresponder_save/'.segment(4) ) )?>">
            <div class="px-2">
                <div class="px-3">
                    <input type="hidden" class="form-control" name="instance_id" value="<?php _e( $instance_id )?>">
                </div>
            </div>
            <!-- Start chat-message-list -->
            <div class="px-2">
                <h5 class="mb-3 px-3 fs-14"><?php _e('Status')?></h5>
                <div class="px-3">
                    <label class="i-radio i-radio--tick i-radio--brand m-r-10">
                        <input type="radio" name="status" checked="true" value="1" <?php _e( get_data($result, 'status', 'radio', 1) )?> > <?php _e('Enable')?>
                        <span></span>
                    </label>
                    <label class="i-radio i-radio--tick i-radio--brand m-r-10">
                        <input type="radio" name="status" value="0" <?php _e( get_data($result, 'status', 'radio', 0) )?> > <?php _e('Disable')?>
                        <span></span>
                    </label>
                </div>
            </div>
            <?php if( _p("whatsapp_autoresponder_media") ){?>
            <div class="px-2 mt-3">
                <div class="px-3">
                    <?php _e( $file_manager, false) ?>
                </div>
            </div>
            <?php }?>
            <div class="post">
                <div class="px-2 post-content">
                    <div class="px-3">
                        <?php _e( $block_caption, false)?>
                        <ul class="text-success small m-b-0 m-t-3">
                            <li><?php _e("Random message by Spintax")?></li>
                            <li><?php _e("Ex: {Hi|Hello|Hola}")?></li>
                        </ul>
                    </div>

                </div> 
            </div>
            <div class="px-4 mt-3">
                <h5 for="delay" class="fs-14"><?php _e('Resubmit message only after (minute)')?></h5>
                <select class="form-control" id="delay" name="delay">
                    <?php for ($i=1; $i <= 4; $i++) {?>
                        <?php if( _p("whatsapp_autoresponder_delay") <= $i ){?>
                            <option value="<?php _e($i)?>" <?php _e( !empty($result) && $result->delay == $i ? "selected":"" )?> ><?php _e($i)?></option>
                        <?php }?>
                    <?php } ?>
                    <?php 
                        for ($i=1; $i <= 3600; $i++) { 
                            if($i%5 == 0){
                    ?>
                        <?php if( _p("whatsapp_autoresponder_delay") <= $i ){?>
                        <option value="<?php _e($i)?>" <?php _e( !empty($result) && $result->delay == $i ? "selected":"" )?>><?php _e($i)?></option>
                        <?php }?>
                    <?php
                            }       
                        }
                    ?>
                </select>
            </div>
            <div class="px-4 mt-3">
                <h5 for="delay" class="fs-14"><?php _e("Except contacts")?></h5>
                <div id="ms1" class="form-control"></div>
            </div>
            <div class="px-4 mt-3 m-b-10">
                <button type="submit" class="btn btn-primary"><?php _e('Submit')?></button>
                <button type="button" class="btn btn-primary wa-btn-open-content d-block d-lg-none float-right"><?php _e('Preview')?></button>
            </div>
            </form>
        </div>
    </div>
        <!-- End chat-message-list -->
</div>
<!-- End chats tab-pane -->


<?php if($result){?>
    <script type="text/javascript">

        var caption = `<?php _e($result->data)?>`;
        var delay = `<?php _e( $result->delay )?>`;

        <?php if($result->media != NULL){?>
        var medias = <?php _e($result->media)?>;
        <?php }else{?>
        var medias = [];
        <?php }?>

        <?php 
            if($result->except != NULL){
                $except_data = [];
                $excepts = json_decode($result->except);
                if(!empty($excepts)){
                    foreach ($excepts as $value) {
                        $arr = explode("{|}", $value);
                        $except_data[] = [
                            "id" => $value,
                            "name" => $arr[1]
                        ];
                    }
                }
                $except_data = json_encode($except_data);
            }else{
                $except_data = "[]";
            }
        ?>

        $(function(){
            WhatsappJs.search_contact(<?php _e($except_data)?>);
            setTimeout(function(){
                $("[name=delay]").val(delay);

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
<?php }else{?>
<script type="text/javascript">
    $(function(){
        WhatsappJs.search_contact([]);
    });
</script>
<?php }?>



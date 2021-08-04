<?php
$fromMe = false;
if(isset($value->id) && isset($value->id->fromMe) && $value->id->fromMe == "true"){
  $fromMe = true;
}

if(isset($value->fromMe) && $value->fromMe == "true"){
  $fromMe = true;
}

$chat_id = false;
if(isset($value->id) && isset($value->id->remote) && is_object($value->id->remote)){
  $chat_id = $value->id->remote->_serialized;
}

if(isset($value->id) && isset($value->id->remote) && is_string($value->id->remote)){
  $chat_id = $value->id->remote;
}
?>


<?php if($value->type == "chat"){?>
  <div class="wa-item <?php _e( $fromMe?"right":"left" )?>"  data-id="<?php _e( $value->id->id )?>">
      <a href="<?php _e( get_module_url("get/delete_message?chat_id=".$chat_id."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
      <div class="message">
          <span><?php _e( turnUrlIntoHyperlink($value->body), false)?></span>
          <div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
      </div>
  </div>
  <div class="clearfix"></div>
<?php }else if($value->type == "image"){?>
  <?php if( isset($value->attachmentData) ){?>
  <div class="wa-item <?php _e( $fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
      <a href="<?php _e( get_module_url("get/delete_message?chat_id=".$chat_id."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
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
  <div class="wa-item <?php _e( $fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
      <a href="<?php _e( get_module_url("get/delete_message?chat_id=".$chat_id."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
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
  <div class="wa-item <?php _e( $fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
      <a href="<?php _e( get_module_url("get/delete_message?chat_id=".$chat_id."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
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
  <div class="wa-item <?php _e( $fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
      <a href="<?php _e( get_module_url("get/delete_message?chat_id=".$chat_id."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
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
  <div class="wa-item <?php _e( $fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
    <a href="<?php _e( get_module_url("get/delete_message?chat_id=".$chat_id."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
      <div class="media">
        <iframe width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=<?php _e($value->location->latitude)?>,<?php _e($value->location->longitude)?>+(title)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
        <?php if($value->location->description){?>
        <div class="text"><?php _e( turnUrlIntoHyperlink($value->location->description), false)?></div>
      <?php }?>
        <div class="time"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
      </div>
  </div>
  <div class="clearfix"></div>
<?php }else if($value->type == "vcard"){?>

  <?php if ( !empty($value->vCards) ): ?>
    
    <?php foreach ($value->vCards as $vCard): ?>
      
      <?php 
      $parser = new JeroenDesloovere\VCard\VCardParser($vCard);
      if( isset( $parser->getCardAtIndex(0)->fullname ) || isset( $parser->getCardAtIndex(0)->phone ) || isset( $parser->getCardAtIndex(0)->email ) ){
      ?>
          <div class="wa-item <?php _e( $fromMe?"right":"left" )?>" data-id="<?php _e( $value->id->id )?>">
            <a href="<?php _e( get_module_url("get/delete_message?chat_id=".$chat_id."&message_id=".$value->id->id) )?>" class="wa-remove wa-action-item" data-remove="wa-item"><i class="ft-trash"></i></a>
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
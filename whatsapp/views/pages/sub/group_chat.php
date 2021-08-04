<!-- Start chats tab-pane -->
<div class="tab-pane h-100 show active" id="pills-chat" role="tabpanel" aria-labelledby="pills-chat-tab">
    <div class="px-4 pt-4">
        <h4 class="mb-4">
            <?php _e('Export participants')?>
        </h4>
        <div class="search-box chat-search-box">            
            <div class="input-group mb-3 rounded-3">
                <span class="input-group-text text-muted bg-white pe-1 ps-3">
                    <i class="ri-search-line search-icon fs-18"></i>
                </span>
                <input type="text" class="form-control search-input" placeholder="<?php _e('Search messages or users')?>">
            </div> 
        </div> <!-- Search Box-->
    </div> <!-- .p-4 -->

    <!-- Start chat-message-list -->
    <div class="px-2">
        <h5 class="mb-3 px-3 fs-16"><?php _e('Recent')?></h5>
    </div>
    <div class="chat-message-list wa-scroll px-2">

        <?php if(!empty($result)){?>
        <ul class="list-unstyled chat-list chat-user-list">
            <?php foreach ($result as $key => $value): ?>

            <?php if($value->isGroup){?>
            <li class="wa-submenu-item unread search-list">
                <a href="<?php _e( get_module_url("get/download_participants?chat_id=".$value->id->_serialized) )?>" class="wa-action-item open-chat-item" data-redirect="true" data-chat-id="<?php _e($value->id->_serialized)?>" data-result-content="wa-content" >
                    <div class="d-flex">                            
                        <div class="chat-user-img online align-self-center mr-3 ms-0">
                            <img src="<?php _e( get_avatar($value->name) )?>" class="rounded-circle avatar-xs" alt="">
                            <span class="user-status"></span>
                        </div>

                        <div class="flex-1 overflow-hidden">
                            <h5 class="text-truncate fs-15 mb-1"><?php _e( $value->name )?></h5>
                            <p class="chat-user-message text-truncate fs-11 mb-0"><?php _e( $value->isGroup?__("Group"):__("User") )?></p>
                        </div>
                        <div class="fs-11"><?php _e( time_elapsed_string( $value->timestamp ) )?></div>
                    </div>
                </a>
            </li>
        	<?php }?>
            <?php endforeach ?>
        </ul>
        <?php }else{?>
            <div class="h-100">
                <div class="empty p-t-30">
                    <div class="icon"></div>
                </div>
            </div>
        <?php }?>
    </div>
    <!-- End chat-message-list -->
</div>
<!-- End chats tab-pane -->
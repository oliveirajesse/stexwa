<?php
class whatsapp extends MY_Controller {

	public $tb_team = 'sp_team';
	public $tb_whatsapp_schedules = "sp_whatsapp_schedules";
	public $tb_account_manager = "sp_account_manager";
	public $tb_whatsapp_autoresponder = "sp_whatsapp_autoresponder";
	public $tb_whatsapp_contacts = "sp_whatsapp_contacts";
	public $tb_whatsapp_phone_numbers = "sp_whatsapp_phone_numbers";
	public $tb_whatsapp_chatbot = "sp_whatsapp_chatbot";
	public $tb_whatsapp_stats = "sp_whatsapp_stats";

	public $module_name;

	public function __construct(){
		parent::__construct();
		_permission(get_class($this)."_enable");
		$this->load->model(get_class($this).'_model', 'model');

		$module_path = get_module_directory(__DIR__);
		include $module_path.'libraries/vendor/autoload.php';

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		//
	}

	public function index($page = "", $ids = "")
	{
		$team_id = _t('id');
		$result = [];
		$page_type = is_ajax()?false:true;

		$accounts = $this->model->fetch("*", $this->tb_account_manager, "team_id = '{$team_id}' AND status = 1 AND social_network = 'whatsapp'");

		//
		$data = [  'accounts' => $accounts, 'access_token' => _s("team_id") ];
		switch ($page) {
			case 'update':
				$data['result'] = $result;
				break;

			default:
				$data['result'] = $result;
				break;
		}

		$page = page($this, "pages", "general", $page, $data, $page_type);
		//

		if( !is_ajax() ){

			$views = [
				"subheader" => view( 'main/subheader', [ 'module_name' => $this->module_name, 'module_icon' => $this->module_icon ], true ),
				"column_one" => view("main/content", [ 'view' => $page ] ,true), 
			];
			
			views( [
				"title" => $this->module_name,
				"fragment" => "fragment_one",
				"views" => $views
			] );

		}else{
			_e( $page, false );
		}

	}

	public function block(){}

	public function get($page = ""){
		$team_id = _t('id');
		$instance_id = addslashes(post("instance_id"));
		$access_token = addslashes(post("access_token"));
		$body = post("body");
		$caption = post("caption");
		$chat_id = addslashes(post("chat_id"));
		$filename = addslashes(post("filename"));
		$message_id = addslashes(post("message_id"));
		$server_url = get_option('whatsapp_server_url', '');

		switch ($page) {
			case 'menu':
				$result = json_decode( wa_get_curl( $server_url."instance?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => false,
							"data" => $result->data,
							"content" => view("pages/sub/menu", [ 'instance_id' => $instance_id, 'access_token' => $access_token ], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				
				break;

			case 'profile':
				$result = json_decode( wa_get_curl( $server_url."instance?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						$account = $this->model->get("*", $this->tb_account_manager, "token = '{$instance_id}' AND team_id = '{$team_id}'");
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/profile", [ 'result' => $result->data, 'account' => $account ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'contact':
				$result = json_decode( wa_get_curl( $server_url."get_contacts?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){

						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/contact", [ 'result' => $result->data ], true),
							"content" => false,
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'contact_group':
				$result = $this->model->get_contact_groups($team_id);
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"content" => view("pages/sub/contact_group", [ 'result' => $result ], true),
					"submenu" => false,
 				]);
				break;

			case 'contact_group_list':
				wa_ms([
					"status" => "success",
					"content" => view("pages/sub/contact_group_list", [], true),
					"submenu" => false,
 				]);
				break;

			case 'contact_group_import':
				wa_ms([
					"status" => "success",
					"content" => view("pages/sub/contact_group_import", [], true),
					"submenu" => false,
 				]);
				break;

			case 'contact_group_update':
				$groups = $this->model->fetch('*', $this->tb_whatsapp_contacts, "status = '1' AND team_id = '{$team_id}'");
				$result = $this->model->get('*', $this->tb_whatsapp_contacts, "ids = '".segment(4)."' AND team_id = '{$team_id}'");
				wa_ms([
					"status" => "success",
					"content" => view("pages/sub/contact_group_update", [ 'groups' => $groups, 'result' => $result ], true),
					"submenu" => false,
 				]);
				break;

			case 'contact_schedules':
				$schedules = $this->model->get_bulk_schedules();
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"content" => view("pages/sub/contact_schedules", [ 'schedules' => $schedules ], true),
					"submenu" => false,
 				]);
				break;

			case 'contact_create_campaign':
				$result = json_decode( wa_get_curl( $server_url."get_contacts?instance_id=".$instance_id."&access_token=".$access_token ) );
				$item = $this->model->get("*", $this->tb_whatsapp_schedules, "ids = '".segment(4)."'");

				$groups = $this->model->fetch('*', $this->tb_whatsapp_contacts, " status = '1' AND team_id = '{$team_id}'");
				$block_caption = Modules::run("whatsapp/block_caption");
				$file_manager = Modules::run("file_manager/block_file", "single", "all", "upload_media");
				Modules::run(get_class($this)."/block");
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"content" => view("pages/sub/contact_create_campaign", [ 'file_manager' => $file_manager, 'block_caption' => $block_caption, 'groups' => $groups, "instance_id" => $instance_id, 'item' => $item ], true),
							"submenu" => false,
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'reload_chat':
				$result = json_decode( wa_get_curl( $server_url."get_chats?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/chat", [ 'result' => $result->data ], true),
							"content" => false
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'export_participants':
				$result = json_decode( wa_get_curl( $server_url."get_chats?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/group_chat", [ 'result' => $result->data ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'export_participants':
				$result = json_decode( wa_get_curl( $server_url."get_chats?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/group_chat", [ 'result' => $result->data ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'download_participants':
				$result = json_decode( wa_get_curl( $server_url."get_group_participants?group_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						$participants = [];
						foreach ($result->data as $value) {
							$participants[] = [
								'id' => $value->id->_serialized,
								'user' => $value->id->user,
							];
						}

						download_send_headers("data_export_participants-" . date("Y-m-d") . ".csv");
						echo array2csv($participants);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'chat':
				$result = json_decode( wa_get_curl( $server_url."get_chats?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/chat", [ 'result' => $result->data ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'pin_chat':
				$result = json_decode( wa_get_curl( $server_url."pin_chat?chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/chat", [ 'result' => $result->data ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'unpin_chat':
				$result = json_decode( wa_get_curl( $server_url."unpin_chat?chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/chat", [ 'result' => $result->data ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'clear_chat':
				$result = json_decode( wa_get_curl( $server_url."clear_chat?chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/chat", [ 'result' => $result->data ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'delete_chat':
				$result = json_decode( wa_get_curl( $server_url."delete_chat?chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/chat", [ 'result' => $result->data ], true),
							"content" => view("pages/sub/empty", [], true)
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'get_chat':
				$result = json_decode( wa_get_curl( $server_url."get_messages?chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				$contact = json_decode( wa_get_curl( $server_url."get_contact?contact_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result) && !empty($contact)){
					if($result->status == "success" && $contact->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"content" => view("pages/sub/get_chat", [ 'result' => $result->data, 'chat_id' => $chat_id, 'contact' => $contact->data ], true),
							"submenu" => false,
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __("Cannot get chats. Please try again later")
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'get_message':
				$data = post("data");
				if($data){
					$data = json_decode( json_encode($data) );
					wa_ms([
						"status" => "success",
						"message" => __('Success'),
						"content" => view("pages/sub/get_message", [ 'value' => $data, 'chat_id' => $chat_id ], true),
						"submenu" => false,
	 				]);
				}
				break;

			case 'delete_message':
				$result = json_decode( wa_get_curl( $server_url."delete_message?chat_id=".$chat_id."&message_id=".$message_id."&instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){
						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => false,
							"content" => false
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => __($result->message)
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'send_message':
				switch (post("type")) {
					case 2:
						$result = json_decode( wa_post_curl( $server_url."send_message?type=media&chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token, [
							"body" => $body,
							"chat_id" => $chat_id,
							"caption" => $caption,
							"filename" => $filename
						] ) );
						if(!empty($result)){
							if($result->status == "success"){
								wa_ms([
									"status" => "success",
									"message" => __('Success'),
									"content" => view("pages/sub/get_message", [ 'value' => $result->data ], true),
									"submenu" => false,
				 				]);
							}else{
								wa_ms([
									"status" => "error",
									"relogin" => isset($result->relogin)?1:0,
									"message" => __($result->message)
				 				]);
							}
						}else{
							wa_ms([
								"status" => "error",
								"message" => __("Cannot connect to server. Please try again later")
			 				]);
						}
						break;
					
					default:
						$result = json_decode( wa_post_curl( $server_url."send_message?type=chat&chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$access_token, [
							"body" => $body,
							"chat_id" => $chat_id
						] ) );
						if(!empty($result)){
							if($result->status == "success"){
								wa_ms([
									"status" => "success",
									"message" => __('Success'),
									"content" => view("pages/sub/get_message", [ 'value' => $result->data ], true),
									"submenu" => false
				 				]);
							}else{
								wa_ms([
									"status" => "error",
									"relogin" => isset($result->relogin)?1:0,
									"message" => __($result->message)
				 				]);
							}
						}else{
							wa_ms([
								"status" => "error",
								"message" => __("Cannot connect to server. Please try again later")
			 				]);
						}
						break;
				}
				break;

			case 'autoresponder':
				$result = $this->model->get("*", $this->tb_whatsapp_autoresponder, "team_id = '{$team_id}' AND instance_id = '{$instance_id}'");
				$block_caption = Modules::run("whatsapp/block_caption");
				$file_manager = Modules::run("file_manager/block_file", "single", "all", "upload_media");
				Modules::run(get_class($this)."/block");
				
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"submenu" => view("pages/sub/autoresponder", [ 'file_manager' => $file_manager, 'block_caption' => $block_caption, "instance_id" => $instance_id, 'result' => $result ], true),
					"content" => view("pages/sub/autoresponder_preview", [ 'file_manager' => $file_manager, 'block_caption' => $block_caption, "instance_id" => $instance_id, 'result' => $result ], true),
 				]);
				break;

			case 'search_contact':
				$result = json_decode( wa_get_curl( $server_url."get_contacts?instance_id=".$instance_id."&access_token=".$access_token ) );
				$data = [];
				if(!empty($result)){
					if($result->status == "success"){
						if(!empty($result->data) && !empty($result->data)){
							foreach ($result->data as $value) {
								if( isset($value->pushname) ){
									$name = $value->pushname;
								}else if( isset($value->name) ){
									$name = $value->name;
								}else{
									$name = $value->number;
								}
								$data[] = [
									"id" => $value->id->_serialized."{|}".$name,
	                    			"name" => $name
								];
							}
						}
					}
				}

				echo json_encode($data);
				break;

			case 'bulk':
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"submenu" => view("pages/sub/bulk", [], true),
					"content" => view("pages/sub/empty", [], true)
 				]);
				break;

			case 'chatbot':
				$result = $this->model->fetch("*", $this->tb_whatsapp_chatbot, "team_id = '{$team_id}' AND instance_id = '{$instance_id}'");
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"submenu" => view("pages/sub/chatbot_list", [ 'result' => $result, 'instance_id' => $instance_id ], true),
					"content" => view("pages/sub/empty", [], true)
 				]);
				break;

			case 'chatbot_update':
				$result = $this->model->get("*", $this->tb_whatsapp_chatbot, "team_id = '{$team_id}' AND ids = '".segment(5)."'");
				$block_caption = Modules::run("whatsapp/block_caption");
				$file_manager = Modules::run("file_manager/block_file", "single", "all", "upload_media");
				Modules::run(get_class($this)."/block");
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"content" => view("pages/sub/chatbot_update", [ 'file_manager' => $file_manager, 'block_caption' => $block_caption, "instance_id" => $instance_id, 'result' => $result ], true),
					"submenu" => view("pages/sub/empty", [], true)
 				]);
	
				break;

			case 'logout':
				$result = json_decode( wa_get_curl( $server_url."logout?instance_id=".$instance_id."&access_token=".$access_token ) );
				if(!empty($result)){
					if($result->status == "success"){

						wa_ms([
							"status" => "success",
							"message" => __('Success'),
							"submenu" => view("pages/sub/start", [], true),
							"content" => view("pages/sub/empty", [], true),
							"logout" => true
		 				]);
					}else{
						wa_ms([
							"status" => "error",
							"relogin" => isset($result->relogin)?1:0,
							"message" => $result->message
		 				]);
					}
				}else{
					wa_ms([
						"status" => "error",
						"message" => __("Cannot connect to server. Please try again later")
	 				]);
				}
				break;

			case 'api':
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"submenu" => view("pages/sub/api_menu", [], true),
					"content" => view("pages/sub/api_content", [], true)
 				]);
				break;

			case 'empty':
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"content" => view("pages/sub/empty", [], true),
					"submenu" => view("pages/sub/start", [], true),
 				]);
				break;

			default:
				wa_ms([
					"status" => "success",
					"message" => __('Success'),
					"content" => view("pages/sub/empty", [], true),
					"submenu" => view("pages/sub/start", [], true),
 				]);
				break;
		}
	}

	public function block_caption()
	{
		return view($this->dir."pages/block_caption", [], true, $this);
	}

	/*
	* AUTORESPONDER
	*/
	public function autoresponder_save(){
		$team_id = _t("id");
		$status = (int)post('status');
		$medias = post("media");
		$caption = post('caption');
		$delay = post('delay');
		$instance_id = post('instance_id');
		$except = post('except');

		validate('null', __('Delay'), $delay);

		$account = $this->model->get("*", $this->tb_account_manager, "token = '{$instance_id}' AND team_id = '{$team_id}'");

		if(empty($account)){
			wa_ms([
				"status" => "error",
				"message" => __('Profile does not exist')
			]);
		}

		if( _p("whatsapp_autoresponder_media") ){
			if(!is_array($medias) && $caption == ""){
				wa_ms([
					"status" => "error",
					"message" => __('Please enter a caption or add a media')
				]);
			}
		}else{
			validate('null', __('Caption'), $caption);
		}

		if((int)_p("whatsapp_autoresponder_delay") > (int)$delay){
			wa_ms([
				"status" => "error",
				"message" => sprintf( __('You can only set autoresponder delays greater than %s minutes'), (int)_p("whatsapp_autoresponder_delay") )
			]);
		}

		$item = $this->model->get("*", $this->tb_whatsapp_autoresponder, "ids = '{$account->ids}' AND team_id = '{$team_id}'");

		if(!$item ){
			$this->model->insert($this->tb_whatsapp_autoresponder , [
				"team_id" => $team_id,
				"ids" => $account->ids,
				"instance_id" => $account->token,
				"data" => $caption,
				"media" => empty($medias)?"[]":json_encode($medias),
				"except" => empty($except)?"[]":json_encode($except),
				"path" => FCPATH,
				"delay" => $delay,
				"status" => $status,
				"changed" => now(),
				"created" => now()
			]);
		}else{
			$this->model->update(
				$this->tb_whatsapp_autoresponder, 
				[
					"team_id" => $team_id,
					"instance_id" => $account->token,
					"data" => $caption,
					"media" => empty($medias)?"[]":json_encode($medias),
					"except" => empty($except)?"[]":json_encode($except),
					"path" => FCPATH,
					"delay" => $delay,
					"status" => $status,
					"changed" => now()
				], 
				array("ids" => $account->ids)
			);
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	public function chatbot_delete(){
		$ids = post('id');

		if( empty($ids) ){
			wa_ms([
				"status" => "error",
				"message" => __('Please select an item to delete')
			]);
		}

		if( is_array($ids) ){
			foreach ($ids as $id) {
				$this->model->delete($this->tb_whatsapp_chatbot, ['ids' => $id]);
			}
		}
		elseif( is_string($ids) )
		{
			$this->model->delete($this->tb_whatsapp_chatbot, ['ids' => $ids]);
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	/*
	* CONTACT GROUP
	*/
	public function save_contact_group($ids = "")
	{
		$status = post('status');
		$name = post('name');
		$team_id = _t("id");

		$item = $this->model->get("*", $this->tb_whatsapp_contacts, "ids = '{$ids}'");
		if(!$item){
			$total_contact_group = $this->model->get("count(id) as count", $this->tb_whatsapp_contacts, "team_id = '{$team_id}'");
			$max_contact_group = (int)_p('whatsapp_bulk_max_contact_group');

			if($max_contact_group <= $total_contact_group->count){
				wa_ms([
					"status" => "error",
					"message" => sprintf( __( 'You can only create a maximum of %s contact groups' ), $max_contact_group )
				]);
			}

			$item = $this->model->get("*", $this->tb_whatsapp_contacts, "name = '{$name}'");
			validate('null', __('Group contact name'), $name);

			$this->model->insert($this->tb_whatsapp_contacts , [
				"ids" => ids(),
				"team_id" => $team_id,
				"name" => $name,
				"status" => $status,
				"changed" => now(),
				"created" => now()
			]);
		}else{
			$item = $this->model->get("*", $this->tb_whatsapp_contacts, "ids != '{$ids}' AND name = '{$name}'");
			validate('null', __('Group contact name'), $name);
			validate('not_empty', __('This group contact name already exists'), $item);

			$this->model->update(
				$this->tb_whatsapp_contacts, 
				[
					"team_id" => $team_id,
					"name" => $name,
					"status" => $status,
					"changed" => now()
				], 
				array("ids" => $ids)
			);
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);

	}

	public function delete_contact_group(){
		$ids = post('id');

		if( empty($ids) ){
			wa_ms([
				"status" => "error",
				"message" => __('Please select an item to delete')
			]);
		}

		if( is_array($ids) ){
			foreach ($ids as $id) {
				$item = $this->model->get("*", $this->tb_whatsapp_contacts, "ids = '{$id}'");
				if(!empty($item)){
					$this->model->delete($this->tb_whatsapp_contacts, ['ids' => $id]);
					$this->model->delete($this->tb_whatsapp_phone_numbers, ['pid' => $item->id]);
				}
			}
		}
		elseif( is_string($ids) )
		{
			$item = $this->model->get("*", $this->tb_whatsapp_contacts, "ids = '{$ids}'");
			if(!empty($item)){
				$this->model->delete($this->tb_whatsapp_contacts, ['ids' => $ids]);
				$this->model->delete($this->tb_whatsapp_phone_numbers, ['pid' => $item->id]);
			}
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	public function ajax_load_contact_group($ids = ""){
		$team_id = _t("id");
		$numbers = $this->model->get("*", $this->tb_whatsapp_contacts, "ids = '{$ids}' AND team_id = '{$team_id}'");
		if( !$numbers ) return false;

		$page = (int)post("page");

		$data = [
			'page' => $page,
			'result' => $this->model->get_phone_numbers($numbers->id, $page)
		];

		view("pages/sub/ajax_load_phone_numbers", $data, false);
	}

	public function delete_phone(){
		$ids = post('id');

		if( empty($ids) ){
			wa_ms([
				"status" => "error",
				"message" => __('Please select an item to delete')
			]);
		}

		if( is_array($ids) ){
			foreach ($ids as $id) {
				$this->model->delete($this->tb_whatsapp_phone_numbers, ['ids' => $id]);
			}
		}
		elseif( is_string($ids) )
		{
			$this->model->delete($this->tb_whatsapp_phone_numbers, ['ids' => $ids]);
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	public function ajax_add_phone($ids = ""){
		$team_id = _t("id");
		$phone_numbers = post("phone_numbers");
		validate('null', __('Phone numbers'), $phone_numbers);
		$phone_numbers = explode("\n", $phone_numbers);

		$item = $this->model->get("*", $this->tb_whatsapp_contacts, "ids = '{$ids}'");

		if(!empty($item)){
			$total_phone_numbers = $this->model->get("count(id) as count", $this->tb_whatsapp_phone_numbers, "team_id = '{$team_id}' AND pid = '{$item->id}'");
			$max_phone_number = (int)_p('whatsapp_bulk_max_phone_numbers');

			if($max_phone_number < $total_phone_numbers->count + count($phone_numbers)){
				wa_ms([
					"status" => "error",
					"message" => sprintf( __( 'You can only add up to %s phone numbers per contact group' ), $max_phone_number )
				]);
			}

			foreach ($phone_numbers as $key => $phone_number) {
				$phone_number = str_replace("+", "", $phone_number);
				$phone_number = str_replace(" ", "", $phone_number);
				$phone_number = str_replace("'", "", $phone_number);
				$phone_number = str_replace("`", "", $phone_number);
				$phone_number = str_replace("\"", "", $phone_number);
				$phone_number = trim($phone_number);

				if(is_numeric($phone_number)){

					$check = $this->model->get("*", $this->tb_whatsapp_phone_numbers, "pid = '{$item->id}' AND phone = '{$phone_number}'");
					if(empty($check)){
						$this->model->insert($this->tb_whatsapp_phone_numbers , [
							"ids" => ids(),
							"team_id" => $item->team_id,
							"pid" => $item->id,
							"phone" => $phone_number,
						]);
					}

				}
			}
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	/*BULK*/
	public function bulk_save(){
		$team_id = _t("id");
		$ids = post("ids");
		$account = post("account");
		$group = post("group");
		$name = post("name");
		$caption = post("caption");  
		$medias = post("media");
		$instance_id = post("instance_id");
		$time_post = timestamp_sql(post("time_post"));
		$is_schedule = post("is_schedule");
		$min_interval_per_post = (int)post("min_interval_per_post");
		$max_interval_per_post = (int)post("max_interval_per_post");

		$item = $this->model->get("*", $this->tb_whatsapp_schedules, "ids = '{$ids}' AND team_id = '{$team_id}'");

		validate('null', __('Campaign name'), $name);
		validate("max_length", "Campaign name", $name, 30);
		validate('null', __('Contact group'), $group);

		if( _p("whatsapp_bulk_media") ){
			if(!is_array($medias) && $caption == ""){
				wa_ms([
					"status" => "error",
					"message" => __('Please enter a caption or add a media')
				]);
			}
		}else{
			validate('null', __('Caption'), $caption);
		}

		validate("min_number", __("Min interval"), $min_interval_per_post, 1);
		validate("min_number", __("Max interval"), $max_interval_per_post, 1);

		if($min_interval_per_post > $max_interval_per_post){
			wa_ms([
				"status" => "error",
				"message" => __('Max interval must be greater than or equal to min interval')
			]);
		}

		if(empty($item)){
			validate('null', __('Time post'), $time_post);
		}

		$account = $this->model->get("*", $this->tb_account_manager, "token = '{$instance_id}' AND team_id = '{$team_id}'");
		$group = $this->model->get("*", $this->tb_whatsapp_contacts, "id = '{$group}' AND team_id = '{$team_id}'");

		validate('empty', __('Please select at least a profile'), $account);
		validate('empty', __('Please select a contact group'), $group);

		if( $account->status == 0 ){
			wa_ms([
				"status" => "error",
				"message" => __("Relogin is required")
			]);
		}

		if(!empty($item)){
			$data = [
				"team_id" => $team_id,
				"account_id" => $account->id,
				"contact_group_id" => $group->id,
				"min_delay" => $min_interval_per_post,
				"max_delay" => $max_interval_per_post,
				"name" => $name,
				"data" => $caption,
				"media" => empty($medias)?"[]":json_encode($medias),
				"path" => FCPATH,
				"changed" => now()
			];

			$result = $this->db->update( $this->tb_whatsapp_schedules, $data, ["id" => $item->id]);
		}else{
			$data = [
				"ids" => ids(),
				"team_id" => $team_id,
				"account_id" => $account->id,
				"contact_group_id" => $group->id,
				"time_post" => $time_post,
				"min_delay" => $min_interval_per_post,
				"max_delay" => $max_interval_per_post,
				"name" => $name,
				"data" => $caption,
				"media" => empty($medias)?"[]":json_encode($medias),
				"path" => FCPATH,
				"time_post" => $time_post,
				"status" => 1,
				"changed" => now(),
				"created" => now()
			];

			$result = $this->db->insert( $this->tb_whatsapp_schedules, $data);
		}

		wa_ms([
			"status" => "success",
			"message" => __("Success")
		]);
	}

	public function bulk_delete(){
		$ids = post('id');

		if( empty($ids) ){
			wa_ms([
				"status" => "error",
				"message" => __('Please select an item to delete')
			]);
		}

		if( is_array($ids) ){
			foreach ($ids as $id) {
				$this->model->delete($this->tb_whatsapp_schedules, ['ids' => $id]);
			}
		}
		elseif( is_string($ids) )
		{
			$this->model->delete($this->tb_whatsapp_schedules, ['ids' => $ids]);
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	public function bulk_schedule_action($ids, $status = ""){
		$status = (int)$status;

		$item = $this->model->get("*", $this->tb_whatsapp_schedules, "ids = '{$ids}'");
		if(!empty($item)){
			switch ($status) {
				case 0:
					$status = 0;
					$result = '<a href="'.get_module_url("bulk_schedule_action/".$ids."/1").'" class="btn-wa-schedule-action btn-play text-info"><i class="ri-play-fill" title="'.__("Play").'"></i></a>';
					break;

				case 1:
					$status = 1;
					$result = '<a href="'.get_module_url("bulk_schedule_action/".$ids."/0").'" class="btn-wa-schedule-action btn-pause text-danger"><i class="ri-pause-circle-line" title="'.__("Pause").'"></i></a>';
					break;
				
				default:
					$status = 2;
					$result = '<div class="btn-success text-success"><i class="ri-check-double-line" title="'.__('Complete').'"></i></div>';
					break;
			}

			$this->db->update($this->tb_whatsapp_schedules, ['status' => $status], ["ids" => $item->ids]);

			wa_ms([
				"status" => "success",
				"message" => __('Success'),
				"content" => $result,
			]);
		}

		wa_ms([
			"status" => "error",
			"message" => __('Something went wrong, please try again later'),
		]);
	}

	public function cron(  )
	{
		$posts = $this->model->get_bulk_posts();
		if(!$posts){ 
			_e("Empty schedule");
			exit(0);
		}

		foreach ($posts as $post) {
			$id = $post->id;
			$ids = $post->ids;
			$team_id = $post->team_id;
			$account_id = $post->account_id;
			$contact_group_id = $post->contact_group_id;
			$name = $post->name;
			$body = $post->data;
			$media = $post->media;
			$time_post = $post->time_post;
			$min_delay = $post->min_delay;
			$max_delay = $post->max_delay;
			$instance_id = $post->token;
			$sent = $post->sent;
			$failed = $post->failed;
			$result = json_decode($post->result);
			$status = $post->status;
			$changed = $post->changed;
			$created = $post->created;
			$username = $post->username."@c.us";
			$phone_number = $post->username."@c.us";

			$phone_numbers = [];
			$this->db->select("*");
			$this->db->from($this->tb_whatsapp_phone_numbers);
			$this->db->where("pid = '{$contact_group_id}'");
			$this->db->where_not_in("phone", $result);
			$query = $this->db->get();
			if($query->result()){
				$phone_numbers = $query->result();

				if(empty($phone_numbers)){
					$this->db->update(
						$this->tb_whatsapp_schedules,
						[
							"status" => 2
						],
						['id' => $id]
					);
				}
			}else{
				$this->db->update(
					$this->tb_whatsapp_schedules,
					[
						"status" => 2
					],
					['id' => $id]
				);
			}

			$team = $this->model->get("ids", $this->tb_team, "id = '{$team_id}'");
			$stats = $this->model->get("*", $this->tb_whatsapp_stats, "team_id = '{$team->ids}'");
		 	$phone_number_index = array_rand($phone_numbers);
		 	$phone_number = $phone_numbers[$phone_number_index]->phone;
		 	$chat_id = $phone_number."@c.us";
		 	$server_url = get_option('whatsapp_server_url', '');

		 	$media = json_decode($media);

		 	if(empty($media)){
				$response = json_decode( wa_post_curl( $server_url."send_message?type=chat&chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$team->ids, [
					"body" => $body,
					"chat_id" => $chat_id
				] ) );
		 	}else{
		 		$response = json_decode( wa_post_curl( $server_url."send_message?type=media&chat_id=".$chat_id."&instance_id=".$instance_id."&access_token=".$team->ids, [
					"body" => wa_base64($media[0]),
					"chat_id" => $chat_id,
					"caption" => $body
				] ) );
		 	}

			if(empty($result)){
				$update_phone_list = [$phone_number];
			}else{
				$update_phone_list = $result;
				$update_phone_list[] = $phone_number;
			}

			$data_update = [ "result" => json_encode($update_phone_list) ];

			if(!empty($response) && $response->status == "success"){
				$new_sent = 1;
				$new_failed = 0;
				$this->db->update( $this->tb_whatsapp_stats, [ "wa_bulk_sent_count" => (int)$stats->wa_bulk_sent_count + 1 ] , [ "id" => $stats->id ]);
			}else{
				$new_sent = 0;
				$new_failed = 1;
				$this->db->update( $this->tb_whatsapp_stats, [ "wa_bulk_failed_count" => (int)$stats->wa_bulk_failed_count + 1 ] , [ "id" => $stats->id ]);
			}

			$this->db->update( $this->tb_whatsapp_stats, [ "wa_bulk_total_count" => (int)$stats->wa_bulk_total_count + 1 ] , [ "id" => $stats->id ]);

			$total_sent = $sent + $new_sent;
			$total_failed = $failed + $new_failed;
			$total_complete = $total_sent + $total_failed;

			$count = $this->model->get("count(*) as count", $this->tb_whatsapp_phone_numbers, "pid = '{$contact_group_id}'")->count;
			if($total_complete == $count){
				$this->db->update(
					$this->tb_whatsapp_schedules,
					[
						"status" => 2
					],
					['id' => $id]
				);
			}

			$next_time = $time_post + ( rand($min_delay,$max_delay) * 60 );

			if($next_time < time() ){
				$next_time = time() + ( rand($min_delay,$max_delay) * 60 );
			}

			$this->db->update(
				$this->tb_whatsapp_schedules,
				[
					"result" => json_encode($update_phone_list),
					"sent" => $total_sent,
					"failed" => $total_failed,
					"time_post" => $next_time
				],
				['id' => $id]
			);
		}

		_e("Success");

	}

	/*CHATBOT*/
	public function chatbot_save(){
		$team_id = _t("id");
		$ids = post("ids");
		$type = post("type");
		$name = post("name");
		$keywords = post("keywords");  
		$caption = post("caption");  
		$medias = post("media");
		$status = (int)post("status");
		$instance_id = post("instance_id");
		$interval_per_post = (int)post("interval_per_post");

		$item = $this->model->get("*", $this->tb_whatsapp_chatbot, "ids = '{$ids}' AND team_id = '{$team_id}'");

		validate('null', __('Bot name'), $name);
		validate("max_length", "Bot name", $name, 30);
		validate('null', __('Keywords'), $keywords);

		$account = $this->model->get("*", $this->tb_account_manager, "token = '{$instance_id}' AND team_id = '{$team_id}'");
		validate('empty', __('Please select at least a profile'), $account);

		if( $account->status == 0 ){
			wa_ms([
				"status" => "error",
				"message" => __("Relogin is required")
			]);
		}

		if( _p("whatsapp_chatbot_media") ){
			if(!is_array($medias) && $caption == ""){
				wa_ms([
					"status" => "error",
					"message" => __('Please enter a caption or add a media')
				]);
			}
		}else{
			validate('null', __('Caption'), $caption);
		}

		$run = 0;
		$chatbot_item = $this->model->get("*", $this->tb_whatsapp_chatbot, "instance_id = '{$instance_id}' AND team_id = '{$team_id}'");
		if(!empty($chatbot_item) && $chatbot_item->run){
			$run = 1;
		}

		$keywords = wa_keyword_trim($keywords);
		
		if(!empty($item)){
			$data = [
				"team_id" => $team_id,
				"instance_id" => $instance_id,
				"type" => $type,
				"name" => $name,
				"keywords" => strtolower($keywords),
				"caption" => $caption,
				"media" => empty($medias)?"[]":json_encode($medias),
				"path" => FCPATH,
				"run" => $run,
				"status" => $status,
				"changed" => now()
			];

			$result = $this->db->update( $this->tb_whatsapp_chatbot, $data, ["id" => $item->id]);
		}else{
			$data = [
				"ids" => ids(),
				"team_id" => $team_id,
				"instance_id" => $instance_id,
				"type" => $type,
				"name" => $name,
				"keywords" => strtolower($keywords),
				"caption" => $caption,
				"media" => empty($medias)?"[]":json_encode($medias),
				"path" => FCPATH,
				"run" => $run,
				"status" => $status,
				"changed" => now(),
				"created" => now()
			];

			$result = $this->db->insert( $this->tb_whatsapp_chatbot, $data);
		}

		wa_ms([
			"status" => "success",
			"message" => __("Success")
		]);
	}

	public function chatbot_status(){
		$team_id = _t('id');
		$instance_id = post("instance_id");
		$access_token = post("access_token");

		$chatbot_item = $this->model->get("*", $this->tb_whatsapp_chatbot, "instance_id = '{$instance_id}' AND team_id = '{$team_id}'");
		if(!empty($chatbot_item)){
			if($chatbot_item->run){
				$this->db->update($this->tb_whatsapp_chatbot, [ 'run' => 0 ], [ 'instance_id' => $instance_id ]);
			}else{
				$this->db->update($this->tb_whatsapp_chatbot, [ 'run' => 1 ], [ 'instance_id' => $instance_id ]);
			}
		}

		wa_ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}
}
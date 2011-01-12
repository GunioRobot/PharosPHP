<?php
	
	/**
	 * NavigationController
	 *
	 * @package PharosPHP.Application.Controllers
	 * @author Matt Brewer
	 **/
	
	class NavigationController extends TableController {
		
		static $colors = array(
			"blue" => "blue",
			"charcoal" => "charcoal",
			"gray" => "gray",
			"green" => "green",
			"orange" => "orange",
			"purple" => "purple",
			"red" => "red",
			"yellow" => "yellow"			
		);
		
		static $icons = array(
			"add-to-cloud" => "add-to-cloud",
			"airplane" => "airplane",
			"atm-card" => "atm-card",
			"bookmark" => "bookmark",
			"briefcase" => "briefcase",
			"brightness" => "brightness",
			"calendar-day-of-week" => "calendar-day-of-week",
			"calendar-day" => "calendar-day",
			"calendar-month" => "calendar-month",
			"calendar-week" => "calendar-week",
			"chat" => "chat",
			"chats" => "chats",
			"checkmark" => "checkmark",
			"clipboard" => "clipboard",
			"clock" => "clock",
			"closed-mail" => "closed-mail",
			"cloud" => "cloud",
			"contrast" => "contrast",
			"crop" => "crop",
			"cut" => "cut",
			"film-roll" => "film-roll",
			"folder" => "folder",
			"gear" => "gear",
			"go-light" => "go-light",
			"grocery-bag" => "grocery-bag",
			"key" => "key",
			"license" => "license",
			"link" => "link",
			"lock" => "lock",
			"marker" => "marker",
			"microphone" => "microphone",
			"monitor" => "monitor",
			"movie" => "movie",
			"music" => "music",
			"open-mail" => "open-mail",
			"paste" => "paste",
			"picture" => "picture",
			"polaroid" => "polaroid",
			"portfolio" => "portfolio",
			"radio-tower" => "radio-tower",
			"radio" => "radio",
			"remove-from-cloud" => "remove-from-cloud",
			"scanner" => "scanner",
			"shopping-bag" => "shopping-bag",
			"stop-light" => "stop-light",
			"tag" => "tag",
			"tools" => "tools",
			"tv" => "tv",
			"unlocked" => "unlocked",
			"wallet" => "wallet",
			"widescreen" => "widescreen"			
		);
		
		protected $parents = array(0 => "(No Parent)");
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Called once, in index.php (to create the controller)
		//
		//////////////////////////////////////////////////////////////////
		
		public function __construct() {
			
			parent::__construct("Navigation","admin_nav");
			
			for ( $info = $this->db->Execute(sprintf("SELECT * FROM `%s` WHERE parent_id = '0'", $this->table->id)); !$info->EOF; $info->moveNext() ) {
				$this->parents[$info->fields['id']] = $info->fields['name'];
			}
						
			$this->dataKey = "id";			
			$this->tableColumns();
						
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Builds the columns array to use for the Table output
		//
		//////////////////////////////////////////////////////////////////
		
		protected function tableColumns() {
			$this->table->columns = array();
			$this->table->columns[] = array('name' => "ID", "key" => $this->dataKey, "class" => "listCheckBox center");
			$this->table->columns[] = array('name' => 'Name', 'key' => 'name', 'class' => 'center');
			$this->table->columns[] = array('name' => 'Min', 'key' => 'min_lvl', 'class' => 'center');
			$this->table->columns[] = array('name' => 'Max', 'key' => 'max_lvl', 'class' => 'center');
			$this->table->columns[] = array('name' => 'Date Added', 'key' => 'date_added', 'class' => 'center');
			$this->table->columns[] = array('name' => 'Last Updated', 'key' => 'last_updated', 'class' => 'center');
		}
		

		
		//////////////////////////////////////////////////////////////////
		//
		//	Builds the data array to pass to the Table class
		//
		//////////////////////////////////////////////////////////////////
		
		protected function buildData($sql) {
			
			$data = array();
			if ( $sql ) {
				
				$levels = user_levels_array(Settings::get('application.users.levels.super'));
							
				for ( $info = $this->db->Execute($sql), $i = 1; !$info->EOF; $info->moveNext(), $i++ ) {

					$id = $info->fields[$this->dataKey];

					$class = ( $i % 2 ) ? 'listTier1' : 'listTier2';
					$row = array('class' => $class, 'data' => array());

					$row['data'][] = $id;
					
					$hovers = array();					
					$hovers[] = (object)array("name" => "Edit", "href" => Template::edit(__CLASS__,$id), "title" => "Edit this ".$this->type, "class" => "edit-link");
					$hovers[] = (object)array("name" => "Delete", "href" => Template::delete(__CLASS__,$id), "title" => "Delete this ".$this->type, "class" => "delete-link confirm-with-popup");

					$row['data'][] = table_hover_cell(format_title($info->fields['name']), $hovers);
				
					$row['data'][] = $levels[$info->fields['min_lvl']];
					$row['data'][] = $levels[$info->fields['max_lvl']];
					$row['data'][] = format_date($info->fields['date_added'],true);
					$row['data'][] = format_date($info->fields['last_updated'],true);
				
					$data[] = $row;
				}

			} $this->table->data = $data;
			
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Lists out the table of settings
		//
		//////////////////////////////////////////////////////////////////
		
		public function manage($orderField='last_updated',$orderVal='desc',$page=1,$filter='') {
															
			$this->table->current_page = intval($page);

			$where = $this->search($filter);
			$order = $this->order($orderField,$orderVal);
			
			$this->table->basic_a = Template::controller_link(__CLASS__,"/".__FUNCTION__."/");
			$this->table->get_links = "$orderField/$orderVal/";

			$sql = "SELECT COUNT(".$this->table->id.'.'.$this->dataKey.") as total FROM ".$this->table->id." ".$where.$order;
			list($total, $page_count, $start) = $this->table->paginate($sql);
			
			// Build the data array to pass to the table
			$sql = "SELECT * FROM ".$this->table->id." ".$where.$order." LIMIT ".$start.",".$this->table->rows_per_page;
			$this->buildData($sql);

			$view =  '<div class="titleTabs">
		            <h1 id="page_title"><strong>'.$this->type.' Library</strong></h1>
		            <form id="'.$this->table->id.'_form" action="'.$this->table->basic_a."$orderField/$orderVal/$page/".'" method="post">
		            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$this->table->search.'"/><a href="#" onClick="$('."'".'#'.$this->table->id.'_form'."'".').submit();" class="inputPress">Search</a></div>';

				if ( is_super() ) {
					$view .= '<div class="contentTabCap"></div><div class="contentTab"><a href="'.Template::create(__CLASS__).'" title="Create New '.$this->type.'" class="tabAdd">Add</a></div>';
				}

		       $view .= '</form>
		            <br clear="all" />
		          </div>'.
		          $this->table->get_html($this->table->current_page, $page_count, $start, $total);

			echo $view;

		}
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Shows the edit page
		//
		//////////////////////////////////////////////////////////////////
		
		public function edit($id, $repost=false) {
									
			$repost = ( $repost === "true" ) ? true : false;
												
			// Required by profile class and repost_mod
			@define('PROFILE_TABLE', $this->table->id);
			@define('PROFILE_TITLE', $this->type);
			@define('PROFILE_ID', $this->dataKey);
			@define('CURRENT_HTML_FILE', "edit_navigation.html");
			
			$levels = user_levels_array(Settings::get('application.users.levels.super'));

			// Template tags to pull from database and replace
			$fields = array(

				array('name' => PROFILE_ID, 'type' => 'display'),
				array('name' => '{TYPE}', 'type' => 'static', 'value' => PROFILE_TITLE),
				array('name' => '{manage}', 'type' => 'static', 'value' => Template::controller_link(__CLASS__)),
				array('name' => '{new}', 'type' => 'static', 'value' => Template::create(__CLASS__)),
				array('name' => '{form_link}', 'type' => 'static', 'value' => Template::save(__CLASS__,$id)),
				array('name' => '{data_key}', 'type' => 'static', 'value' => PROFILE_ID),
				
				array('name' => 'parent_id' ,'type' => 'dropdown', 'option' => $this->parents, 'default' => 0),
				array('name' => 'name' ,'type' => 'text', 'size' => '50' , 'max' => '200'),
				array('name' => 'page' ,'type' => 'text', 'size' => '50' , 'max' => '100'),
				array('name' => 'order_num' ,'type' => 'text', 'size' => '2' , 'max' => '2'),
				array('name' => 'display', 'type' => 'dropdown', 'option' => array('hidden' => 'None', 'visible' => 'Visible'), 'default' => 'visible'),
				array('name' => 'min_lvl', 'type' => 'dropdown', 'option' => $levels, 'default' => Settings::get('application.users.levels.admin')),
				array('name' => 'max_lvl', 'type' => 'dropdown', 'option' => $levels, 'default' => Settings::get('application.users.levels.super')),
				array('name' => 'icon', 'type' => 'dropdown', 'option' => self::$icons),
				array('name' => 'color', 'type' => 'dropdown', 'option' => self::$colors, 'default' => 'charcoal'),
				
				array('name' => 'description', 'type' => 'text_area', 'row' => '8', 'col' => '89', 'width' => '738px', 'height' => '100px'),
				array('name' => 'date_added', 'type' => 'date_added'),
				array('name' => 'last_updated', 'type' => 'last_updated')

			);
		
			// Run throught the parser and spit out the page
			$profile = new Profile($fields);
			
			if ( $id > 0 ) echo $profile->display($this->dataKey, $id, $repost);
			else echo $profile->display();
						
		}
		
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Deletes an entry, used in conjunction with "confirmDelete"
		//	javascript jAlert stuff
		//
		//////////////////////////////////////////////////////////////////
		
		public function delete($id) {
			
			if ( ($confirmed = Input::post("confirmed")) === "true" ) {
				
				// Delete the navigtaion entry itself
				$sql = "DELETE FROM ".$this->table->id." WHERE $this->dataKey = '$id' LIMIT 1";
				$this->db->Execute($sql);
								
				$obj = array('error' => false, 'redirect' => Template::manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;
				
			} else {
				
				echo "[link]".Template::delete(__CLASS__,$id)."[/link]";
				echo "This will remove the navigation item and all it's tracking information from the system.<br /><br />";
				echo "<strong>This action cannot be undone.</strong><br />";
				exit;
				
			}
			
		}
		
	}
          

?>
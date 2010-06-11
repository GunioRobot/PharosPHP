<?php

	///////////////////////////////////////////////////////////////////////////
	//
	//	Table.php
	//
	//	Used to display tabular data.  Includes support for pagination,
	//	searching, and column order out of the box.
	//
	///////////////////////////////////////////////////////////////////////////
	


	class Table {

		public $id, $class, $head_class, $display_pages, $columns, $data, $target, $iframe_src, $extra_href, $ordered_row, $order, $rows_per_page, $current_page;
		protected $db;

		public function __construct($info=null) {
			global $db;
			$this->db =& $db;
			$this->setInfo($info);
		}
		
		public function setInfo($info) {
			if ( is_array($info) ) {

				// Store vars
				if ( isset($info['table_id']) AND $info['table_id'] != '' ) $this->id = $info['table_id'];	
				else die("Table() requires id");

				if ( isset($info['table_class']) AND $info['table_class'] != '' ) $this->class = $info['table_class'];

				if ( isset($info['head_class']) AND $info['head_class'] != '' ) $this->head_class = $info['head_class'];

				if ( isset($info['columns']) AND is_array($info['columns']) ) $this->columns = $info['columns'];
				else die("Table() requires columns");

				if ( isset($info['data']) AND is_array($info['data']) ) $this->data = $info['data'];
				else die("Table() requires data");		

				if ( isset($info['basic_a']) ) $this->basic_a = $info['basic_a'];

				if ( isset($info['iframe']) AND $info['iframe'] != '' ) $this->target = 'target="'.$info['iframe'].'"';
				else $this->target = '';

				if ( isset($info['iframe_src']) AND $info['iframe_src'] != '') $this->iframe_src = $info['iframe_src'];
				else $this->iframe_src = '';

				if ( isset($info['extra_href']) AND $info['extra_href'] != '') $this->extra_href = $info['extra_href'];
				else $this->extra_href = '';

				if ( isset($info['ordered_row']) AND $info['ordered_row'] != '' ) $this->ordered_row = $info['ordered_row'];
				else $this->ordered_row = '';

				if ( isset($info['order']) AND $info['order'] != '' ) $this->order = $info['order'];
				else $this->order = 'asc';
				
				if ( isset($info['current_page']) AND $info['current_page'] != '' ) $this->current_page = $info['current_page'];
				else $this->current_page = 1;

				if ( isset($info['display_pages']) AND $info['display_pages'] != '' ) $this->display_pages = $info['display_pages'];
				else $this->display_pages = 5;
				
			}
		}
	
		
		public function paginate($sql) {
			if ( $this->id && $this->rows_per_page > 0 && $sql ) {
				$total = $this->db->Execute($sql);
				$total = $total->fields['total'] != '' ? $total->fields['total'] : '0';
				$page_count = intval(ceil($total/$this->rows_per_page));
				$page_count = $page_count > 0 ? $page_count : 1;
				$start = ($this->current_page-1) * $this->rows_per_page;
				return array($total,$page_count,$start);
			}
		}
	
		public function get_html($page, $page_count, $showing, $total) {
	
			if(!is_int($page)) die("Table->get_html(page)");
			if(!is_int($page_count)) die("Table->get_html(page_count)");
		
			if ( isset($this->basic_a) ) $basic_a = '<a href="'.$this->basic_a;
			else {
			
				// Build the basic <a> ( used with iframes as well )
				if ( $this->target != '' ) {	// IFrames
					$basic_a = '<a '.$this->target.' href="'.$this->iframe_src;
				} else $basic_a = '<a href="index.php?';
			}
				
		
				
			// Opening table line
			$html = '<div class="contentTitleBar"><b class="btop"><b></b></b></div><table id="'.$this->id.'"';
			if ( isset($this->class) ) $html .= ' class="'.$this->class.'"';
			$html .= ' cellpadding="0" cellspacing="0" width="100%">';
		
		
			// Build table header
			$html .= '<thead';
			if(isset($this->head_class)) $html.=' class="'.$this->head_class.'"';
			$html.=' style="text-align:center;"><tr>';			// Want the table headers to be centered always
		
		
		
			foreach($this->columns as $c) {
				if ( $c['key'] != '' ) {
			
					// Support for classes on the <th>
					$html .= '<th';
					if ( $c['class'] != '' ) $html .= ' class="'.$c['class'].'" ';
					$html .= '>'.$basic_a.$c['key'].'/';
				
					if ( $this->ordered_row == $c['key'] ) {
						$html .= $this->order == "asc" ? "desc/" : "asc/";
						$ORDER = $this->order == "asc" ? "ordered-up" : "ordered-down";
						$class = '" class="'. $ORDER . '"';
					} else {
						$html .= "asc/";
						$class = "";
					}
					
					// Always on page 1 when we reorder
					$html .= "1/";
					
					if ( $this->search != '' ) {
						$html .= $this->search."/";
					} else $html .= "_/";
					
					if ( $this->extra_href != '' ) $html .= $this->extra_href;
					
					$html .= '"';
					$html .= $class;
				
					$html .= '>'.format_title($c['name']).'</a></th>';
				
				} else $html .= '<th>'.format_title($c['name']).'</th>';
			} $html .= '</tr></thead>';
		
		
			// Build the body of the table
			$html .= '<tbody>';
			foreach($this->data as $d) {
			
				$html .= '<tr';
				if(isset($d['class'])) $html .= ' class="'.$d['class'].'"';
				$html .= '>';
			
				$i = 0;
				foreach($this->columns as $c) {
				
					$html .= '<td';
					if(isset($c['class'])) $html.=' class="'.$c['class'].'"';
					if(isset($c['width'])) $html.=' style="width:'.$c['width'].'"';
					$html .= '>';
				
					$html .= $d['data'][$i];
				
					$html .= '</td>';
					$i++;
			
				} $html .= '</tr>';
			} $html .= '</tbody></table>';
		
			// Add the footer area
			$html .= '<div class="contentBottomBar"><b class="bbot"><b></b></b></div>';
		
		
			// Results area
			if ( count($this->data) == 0 ) $s = 0;
			else $s = $showing + 1;
			$html .= '<div class="floatLeft">Showing <strong>'.$s.'</strong> to <strong>'.($showing+count($this->data)).'</strong> of <strong>'.$total.'</strong> Results';
			if ( $this->search != '' ) $html .= ' ( Filtered for: <strong>"'.$this->search.'"</strong> )';
			$html .= '</div>';		
		
		
			// Pagination area
			$html .= '<div class="pagination">';
				
			// Keep start in valid ranges
			$start = floor($page/$this->display_pages)*$this->display_pages + 1;	// +1 b/c not zero indexed
			if ( $page % $this->display_pages == 0 ) $start -= $this->display_pages;	// Mod b/c 5,10,15 etc didn't show up 
		
			// Keep end in valid ranges (don't display too many links)
			$end = ceil($page/$this->display_pages)*$this->display_pages;
			if ( $end > $page_count ) {
				$end = $page_count;
			}

			// If can go left a set of pages
			if ( $page - $this->display_pages > 0 ) $html .= $basic_a.$this->get_links.($start-1).'/'.($this->search!=""?$this->search."/":"_/").'">&laquo;</a>';
			$html .= ' Page ';
				
			// Print out the page links
			for ( $i = $start; $i <= $end; $i++ ) {
				$page_class = ($i == $page) ? 'pagSelected' : 'pagLink';
				$html .= $basic_a.$this->get_links.$i.'/'.($this->search!=""?$this->search."/":"_/").'" class="'.$page_class.'">'.$i.'</a>';
			} 
		
			// if can go right a set of pages
			if ( $page_count > $end ) $html .= $basic_a.$this->get_links.++$end.'/'.($this->search!=""?$this->search."/":"_/").'">&raquo;</a>';
		
			$html .= '</div><br clear="all" /><br /><br />';
				
			return $html;
		}
	}

?>
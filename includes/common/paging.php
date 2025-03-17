<?php

class Pager

	{

	function getPagerData($numHits, $limit, $page)

		{

			$numHits  = (int) $numHits;

			$limit    = max((int) $limit, 1);

			$page     = (int) $page;

			$numPages = ceil($numHits / $limit);

			$page = max($page, 1);

			$page = min($page, $numPages);

			$offset = ($page - 1) * $limit;

			$ret = new stdClass;

			$ret->offset   = $offset;

			$ret->limit    = $limit;

		//	$ret->limit	= 15;

			$ret->numPages = $numPages;

			$ret->page     = $page;

			return $ret;

		}

	 }

function do_pages_new($total, $page_size=25) {		
		
			$index_limit = 10;
		
			$query = preg_replace('/page=[0-9]+/', '', $_SERVER['QUERY_STRING']);
			$query = preg_replace('/^&*(.*)&*$/', "$1", $query);
			if (!empty($query)) $query = "&amp;$query";
			
			$current = GetVar("iPageNum");
			if (intval($current) <= 0)
			$current = 1;
			
			$total_pages = ceil($total / $page_size);
			$start = max($current - intval($index_limit / 2), 1);
			$end = $start + $index_limit - 1;			
		
			if ($start>1) {
				$i = 1;
				//echo '&nbsp; <a href="javascript:paging('.$i.')" title=""> '.$i.' </a> &nbsp;';
				//echo ' ...';
			}
			echo "<td width='75%' class='bodytext'> Pages : &nbsp; ";
			for ($i = $start; $i <= $end && $i <= $total_pages; $i++) {
				if ($i == $current) {
					echo ' <span color="#E04D4D" class="page_nav">'.$i.'</span>  ';
				} else {
					if(($i == $end) || ($i == $total_pages)) {
					echo ' <a href="javascript:paging('.$i.')" title="" class="page_nav">'.$i.'</a> ';
					}else{
					echo ' <a href="javascript:paging('.$i.')" title="" class="page_nav">'.$i.'</a> ';
					}
				}
			}
			if ($total_pages > $end) {
				$i = $total_pages;
				echo ' . . . ';
				echo '<a href="javascript:paging('.$i.')" title="" class="page_nav">'.$i.'</a> &nbsp;';
			}
			//echo " &nbsp; ... &nbsp; $total_pages";
			echo '</td><td width="25%" height="29" align=right>';
			if ($total > 0) {
				if ($current == 1) {				
					echo '<span class="page_nav">Previous</span>&nbsp;';
				} else {
					$i = $current - 1;
					echo '<a href="javascript:paging('.$i.')" class="page_nav">Previous</a>&nbsp;';
					
				}
				if ($current < $total_pages) {
					$i = $current + 1;				
					echo '&nbsp;&nbsp;<a href="javascript:paging('.$i.')" class="page_nav">Next</a>&nbsp;';
				} else {
					echo '&nbsp;&nbsp;<span class="page_nav">Next</span>&nbsp;';
				}
			}						
		}


function do_pages_cis($total, $page_size=25,$show_cnt=1) {		
		
			$index_limit = 5;
		
			$query = preg_replace('/page=[0-9]+/', '', $_SERVER['QUERY_STRING']);
			$query = preg_replace('/^&*(.*)&*$/', "$1", $query);
			if (!empty($query)) $query = "&amp;$query";
			
			$current = GetVar("iPageNum");
			if (intval($current) <= 0)
			$current = 1;
			
			$total_pages = ceil($total / $page_size);
			$start = max($current - intval($index_limit / 2), 1);
			$end = $start + $index_limit - 1;			
		
			if ($start>1) {
				$i = 1;
				//echo '&nbsp; <a href="javascript:paging('.$i.')" title=""> '.$i.' </a> &nbsp;';
				//echo ' ...';
			}
			
			$showing_start = ((($current-1)*$page_size)+1);
			$showing_end = $current*$page_size;
			if($showing_end >= $total){
				$showing_end = $total;
			}
			
			echo "<table width='100%'><tr>";
			
			if($show_cnt == 1){
				echo "<td align='left'> Showing ".$showing_start." to ".$showing_end." of ".$total." entries </td>";
			}
			
			echo "<td align='right'>";
			
			if ($total > 0) {
				if ($current == 1) {				
					echo '<a href="javascript:;" class="paginate_disabled">Previous</a>';
				} else {
					$i = $current - 1;
					echo '<a href="javascript:paging('.$i.')" class="paginate_button">Previous</a>';
				}
			}
			
			for ($i = $start; $i <= $end && $i <= $total_pages; $i++) {
				if ($i == $current) {
					echo '<a href="javascript:;" class="paginate_active">'.$i.'</a>';
				} else {
					if(($i == $end) || ($i == $total_pages)) {
					echo '<a href="javascript:paging('.$i.')" class="paginate_button">'.$i.'</a>';
					}else{
					echo '<a href="javascript:paging('.$i.')" class="paginate_button">'.$i.'</a>';
					}
				}
			}
			if ($total_pages > $end) {
				$i = $total_pages;
				echo ' . . . ';
				echo '<a href="javascript:paging('.$i.')" class="paginate_button">'.$i.'</a>';
			}
			//echo " &nbsp; ... &nbsp; $total_pages";
			//echo '</td><td width="25%" height="29" align=right>';
			
			if ($total > 0) {
				if ($current < $total_pages) {
					$i = $current + 1;				
					echo '<a href="javascript:paging('.$i.')" class="paginate_button">Next</a>';
				} else {
					echo '<a href="javascript:;" class="paginate_disabled">Next</a>';
				}
			}	
			
			echo '</td></tr></table>';					
}

?>
<?php

/**
 * 
 */

function pagination($con, $table, $query, $per_page, $page, $url = '?')
{
	//list($query) = explode('LIMIT', $query);
	// Safely remove the LAST limit clause only
    $query = preg_replace('/LIMIT\s+\d+\s*,\s*\d+\s*$/i', '', $query);
	$rows = mysqli_query($con, $query);
	$total = mysqli_num_rows($rows);
	$adjacents = 2;

	$page = ($page == 0 ? 1 : $page);
	$start = ($page - 1) * $per_page;

	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($total / $per_page);
	$lpm1 = $lastpage - 1;

	// Remove existing 'page' param from the query string
	parse_str($_SERVER['QUERY_STRING'], $params);
	unset($params['page']);
	$base_query = http_build_query($params);
	$base_url = $url . ($base_query ? $base_query . '&' : '');

	$pagination = "";
	if ($lastpage > 1) {
		$pagination .= "<ul class='pagination'>";
		$pagination .= "<li class='details'>Page $page of $lastpage out of $total records</li>";

		if ($lastpage < 7 + ($adjacents * 2)) {
			for ($counter = 1; $counter <= $lastpage; $counter++) {
				if ($counter == $page)
					$pagination .= "<li><a class='current'>$counter</a></li>";
				else
					$pagination .= "<li><a href='{$base_url}page=$counter'>$counter</a></li>";
			}
		} elseif ($lastpage > 5 + ($adjacents * 2)) {
			if ($page < 1 + ($adjacents * 2)) {
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
					if ($counter == $page)
						$pagination .= "<li><a class='current'>$counter</a></li>";
					else
						$pagination .= "<li><a href='{$base_url}page=$counter'>$counter</a></li>";
				}
				$pagination .= "<li class='dot'>...</li>";
				$pagination .= "<li><a href='{$base_url}page=$lpm1'>$lpm1</a></li>";
				$pagination .= "<li><a href='{$base_url}page=$lastpage'>$lastpage</a></li>";
			} elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
				$pagination .= "<li><a href='{$base_url}page=1'>1</a></li>";
				$pagination .= "<li><a href='{$base_url}page=2'>2</a></li>";
				$pagination .= "<li class='dot'>...</li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
					if ($counter == $page)
						$pagination .= "<li><a class='current'>$counter</a></li>";
					else
						$pagination .= "<li><a href='{$base_url}page=$counter'>$counter</a></li>";
				}
				$pagination .= "<li class='dot'>..</li>";
				$pagination .= "<li><a href='{$base_url}page=$lpm1'>$lpm1</a></li>";
				$pagination .= "<li><a href='{$base_url}page=$lastpage'>$lastpage</a></li>";
			} else {
				$pagination .= "<li><a href='{$base_url}page=1'>1</a></li>";
				$pagination .= "<li><a href='{$base_url}page=2'>2</a></li>";
				$pagination .= "<li class='dot'>..</li>";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $page)
						$pagination .= "<li><a class='current'>$counter</a></li>";
					else
						$pagination .= "<li><a href='{$base_url}page=$counter'>$counter</a></li>";
				}
			}
		}

		if ($page < $counter - 1) {
			$pagination .= "<li><a href='{$base_url}page=$next'>Next</a></li>";
			$pagination .= "<li><a href='{$base_url}page=$lastpage'>Last</a></li>";
		} else {
			$pagination .= "<li><a class='current'>Next</a></li>";
			$pagination .= "<li><a class='current'>Last</a></li>";
		}
		$pagination .= "</ul>\n";
	}

	return $pagination;
}

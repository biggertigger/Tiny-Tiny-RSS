<?php
	function module_popup_dialog($link) {
		$id = $_GET["id"];
		$param = db_escape_string($_GET["param"]);

		if ($id == "explainError") {

			print "<div id=\"infoBoxTitle\">".__('Notice')."</div>";
			print "<div class=\"infoBoxContents\">";

			if ($param == 1) {
				print __("Update daemon is enabled in configuration, but daemon
					process is not running, which prevents all feeds from updating. Please
					start the daemon process or contact instance owner.");

				$stamp = (int)read_stampfile("update_daemon.stamp");

				print "<p>" . __("Last update:") . " " . date("Y.m.d, G:i", $stamp); 

			}

			if ($param == 2) {
				$msg = check_for_update($link, false);

				if (!$msg) {
					print __("You are running the latest version of Tiny Tiny RSS. The
						fact that you are seeing this dialog is probably a bug.");
				} else {
					print $msg;
				}

			}

			if ($param == 3) {
				print __("TT-RSS has detected that update daemon is taking too long to
					perform a feed update. This could indicate a problem like crash
					or a hang. Please check the daemon process or contact instance
					owner.");

				$stamp = (int)read_stampfile("update_daemon.stamp");

				print "<p>" . __("Last update:") . " " . date("Y.m.d, G:i", $stamp); 

			}

			print "</div>";

			print "<div align='center'>";

			print "<input class=\"button\"
				type=\"submit\" onclick=\"return closeInfoBox()\" 
				value=\"".__('Close this window')."\">";

			print "</div>";

			return;
		}

		if ($id == "quickAddFeed") {

			print "<div id=\"infoBoxTitle\">".__('Subscribe to Feed')."</div>";
			print "<div class=\"infoBoxContents\">";

			print "<form id='feed_add_form' onsubmit='return false'>";

			print "<input type=\"hidden\" name=\"op\" value=\"pref-feeds\">";
			print "<input type=\"hidden\" name=\"subop\" value=\"add\">"; 
			print "<input type=\"hidden\" name=\"from\" value=\"tt-rss\">"; 

			print "<div class=\"dlgSec\">".__("Feed")."</div>";
			print "<div class=\"dlgSecCont\">";

			print __("URL:") . " ";

			print "<input size=\"40\" onblur=\"javascript:enableHotkeys()\" 
					onkeypress=\"return filterCR(event, subscribeToFeed)\"
					onkeyup=\"toggleSubmitNotEmpty(this, 'fadd_submit_btn')\"
					onchange=\"toggleSubmitNotEmpty(this, 'fadd_submit_btn')\"
					onfocus=\"javascript:disableHotkeys()\" name=\"feed_url\"></td></tr>";

			print "<br/>";

			if (get_pref($link, 'ENABLE_FEED_CATS')) {
				print __('Place in category:') . " ";
				print_feed_cat_select($link, "cat_id");			
			}

			print "</div>";


/*			print "<tr><td colspan='2'><div class='insensitive'>";

			print __("Some feeds require authentication. If you subscribe to such
				feed, you will have to enter your login and password in Feed Editor");

			print "</div></td></tr>"; */

/*			print "<div id='fadd_login_prompt'><br/>
				<a href='javascript:appearBlockElement(\"fadd_login_container\", 
				\"fadd_login_prompt\")'>".__('Click here if this feed requires authentication.')."</a></div>"; */

			print "<div id='fadd_login_container' style='display:none'>
	
					<div class=\"dlgSec\">".__("Authentication")."</div>
					<div class=\"dlgSecCont\">".

					__('Login:') . " <input name='auth_login' size=\"20\" 
							onfocus=\"javascript:disableHotkeys()\" 
							onfocus=\"javascript:disableHotkeys()\" 
							onkeypress=\"return filterCR(event, subscribeToFeed)\"> ".
					__('Password:') . "<input type='password'
							name='auth_pass' size=\"20\" 
							onfocus=\"javascript:disableHotkeys()\" 
							onfocus=\"javascript:disableHotkeys()\" 
							onkeypress=\"return filterCR(event, subscribeToFeed)\">
				</div></div>";


			print "<div style=\"clear : both\">				
				<input type=\"checkbox\" id=\"fadd_login_check\" 
						onclick='checkboxToggleElement(this, \"fadd_login_container\")'>
					<label for=\"fadd_login_check\">".
					__('This feed requires authentication.')."</div>";

			print "</form>";

			print "<div class=\"dlgButtons\">
				<input class=\"button\"
					id=\"fadd_submit_btn\" disabled=\"true\"
					type=\"submit\" onclick=\"return subscribeToFeed()\" value=\"".__('Subscribe')."\">
				<input class=\"button\"
					type=\"submit\" onclick=\"return closeInfoBox()\" 
					value=\"".__('Cancel')."\"></div>";
			
			return;
		}

		if ($id == "search") {

			print "<div id=\"infoBoxTitle\">".__('Search')."</div>";
			print "<div class=\"infoBoxContents\">";

			print "<form id='search_form'  onsubmit='return false'>";

			#$active_feed_id = db_escape_string($_GET["param"]);

			$params = split(":", db_escape_string($_GET["param"]));

			$active_feed_id = sprintf("%d", $params[0]);
			$is_cat = $params[1] == "true";

			print "<div class=\"dlgSec\">".__('Search')."</div>";

			print "<div class=\"dlgSecCont\">";

			print "<input name=\"query\" size=\"30\" type=\"search\"
				onkeypress=\"return filterCR(event, search)\"
				onchange=\"toggleSubmitNotEmpty(this, 'search_submit_btn')\"
				onkeyup=\"toggleSubmitNotEmpty(this, 'search_submit_btn')\"
				value=\"\">";

			print " " . __('match on:')." ";

			$search_fields = array(
				"title" => __("Title"),
				"content" => __("Content"),
				"both" => __("Title or content"));

			print_select_hash("match_on", 3, $search_fields); 


			print "<br/>".__('Limit search to:')." ";
			
			print "<select name=\"search_mode\">
				<option value=\"all_feeds\">".__('All feeds')."</option>";
			
			$feed_title = getFeedTitle($link, $active_feed_id);

			if (!$is_cat) {
				$feed_cat_title = getFeedCatTitle($link, $active_feed_id);
			} else {
				$feed_cat_title = getCategoryTitle($link, $active_feed_id);
			}
			
			if ($active_feed_id && !$is_cat) {				
				print "<option selected value=\"this_feed\">$feed_title</option>";
			} else {
				print "<option disabled>".__('This feed')."</option>";
			}

			if ($is_cat) {
			  	$cat_preselected = "selected";
			}

			if (get_pref($link, 'ENABLE_FEED_CATS') && ($active_feed_id > 0 || $is_cat)) {
				print "<option $cat_preselected value=\"this_cat\">$feed_cat_title</option>";
			} else {
				//print "<option disabled>".__('This category')."</option>";
			}

			print "</select>"; 

			print "</div>";

			print "</form>";

			print "<div class=\"dlgButtons\">
			<input type=\"submit\" 
				class=\"button\" onclick=\"javascript:search()\" 
				id=\"search_submit_btn\" disabled=\"true\"
				value=\"".__('Search')."\">
			<input class=\"button\"
				type=\"submit\" onclick=\"javascript:searchCancel()\" 
				value=\"".__('Cancel')."\"></div>";

			print "</div>";

			return;

		}

		if ($id == "quickAddLabel") {
			print "<div id=\"infoBoxTitle\">".__('Create Label')."</div>";
			print "<div class=\"infoBoxContents\">";

			print "<form id=\"label_edit_form\" onsubmit='return false'>";

			print "<input type=\"hidden\" name=\"op\" value=\"pref-labels\">";
			print "<input type=\"hidden\" name=\"subop\" value=\"add\">"; 

			print "<div class=\"dlgSec\">".__("Caption")."</div>";

			print "<div class=\"dlgSecCont\">";

			print "<input onkeypress=\"return filterCR(event, labelEditSave)\"
					onkeyup=\"toggleSubmitNotEmpty(this, 'infobox_submit')\"
					onchange=\"toggleSubmitNotEmpty(this, 'infobox_submit')\"
					 name=\"description\" size=\"30\" value=\"$description\">";
			print "</div>";

			print "<div class=\"dlgSec\">".__("Match SQL")."</div>";

			print "<div class=\"dlgSecCont\">";

			print "<textarea onkeyup=\"toggleSubmitNotEmpty(this, 'infobox_submit')\"
					 rows=\"6\" name=\"sql_exp\" class=\"labelSQL\" cols=\"50\">$sql_exp</textarea>";

			print "</div>";

			print "</form>";

			print "<div style=\"display : none\" id=\"label_test_result\"></div>";

			print "<div class=\"dlgButtons\">";

			print "<div style='float : left'>";
			print "<input type=\"submit\" 
				class=\"button\" onclick=\"return displayHelpInfobox(1)\" 
				value=\"".__('Help')."\"> ";
			print "</div>";

			print "<input type=\"submit\" onclick=\"labelTest()\" value=\"".__('Test')."\">
				";

			print "<input type=\"submit\" 
				id=\"infobox_submit\"
				disabled=\"true\"
				class=\"button\" onclick=\"return addLabel()\" 
				value=\"".__('Create')."\"> ";

			print "<input class=\"button\"
				type=\"submit\" onclick=\"return labelEditCancel()\" 
				value=\"".__('Cancel')."\">";

			return;
		}

		if ($id == "quickAddFilter") {

			$active_feed_id = db_escape_string($_GET["param"]);

			print "<div id=\"infoBoxTitle\">".__('Create Filter')."</div>";
			print "<div class=\"infoBoxContents\">";

			print "<form id=\"filter_add_form\" onsubmit='return false'>";

			print "<input type=\"hidden\" name=\"op\" value=\"pref-filters\">";
			print "<input type=\"hidden\" name=\"quiet\" value=\"1\">";
			print "<input type=\"hidden\" name=\"subop\" value=\"add\">"; 

//			print "<div class=\"notice\"><b>Note:</b> filter will only apply to new articles.</div>";
		
			$result = db_query($link, "SELECT id,description 
				FROM ttrss_filter_types ORDER BY description");
	
			$filter_types = array();
	
			while ($line = db_fetch_assoc($result)) {
				//array_push($filter_types, $line["description"]);
				$filter_types[$line["id"]] = __($line["description"]);
			}

/*			print "<table width='100%'>";

			print "<tr><td>".__('Match:')."</td>
				<td><input onkeypress=\"return filterCR(event, createFilter)\"
					onkeyup=\"toggleSubmitNotEmpty(this, 'infobox_submit')\"
 					onchange=\"toggleSubmitNotEmpty(this, 'infobox_submit')\"
					 name=\"reg_exp\" class=\"iedit\">";		

			print "</td></tr><tr><td>".__('On field:')."</td><td>";

			print_select_hash("filter_type", 1, $filter_types, "class=\"_iedit\"");	
	
			print "</td></tr>";
			print "<tr><td>".__('Feed:')."</td><td colspan='2'>";

			print_feed_select($link, "feed_id", $active_feed_id);
			
			print "</td></tr>";
	
			print "<tr><td>".__('Action:')."</td>";
	
			print "<td colspan='2'><select name=\"action_id\" 
				onchange=\"filterDlgCheckAction(this)\">";
	
			$result = db_query($link, "SELECT id,description FROM ttrss_filter_actions 
				ORDER BY name");

			while ($line = db_fetch_assoc($result)) {
				printf("<option value='%d'>%s</option>", $line["id"], __($line["description"]));
			}
	
			print "</select>";

			print "</td></tr>";

			print "<tr><td>".__('Params:')."</td>";

			print "<td><input disabled class='iedit' name='action_param'></td></tr>";

			print "<tr><td valign='top'>".__('Options:')."</td><td>";

			print "<input type=\"checkbox\" name=\"inverse\" id=\"inverse\">
				<label for=\"inverse\">".__('Inverse match')."</label></td></tr>";

			print "</table>";

			print "</form>"; */

			print "<div class=\"dlgSec\">".__("Match")."</div>";

			print "<div class=\"dlgSecCont\">";

			print "<input onkeypress=\"return filterCR(event, filterEditSave)\"
					 onkeyup=\"toggleSubmitNotEmpty(this, 'infobox_submit')\"
					 onchange=\"toggleSubmitNotEmpty(this, 'infobox_submit')\"
					 name=\"reg_exp\" size=\"30\" value=\"$reg_exp\">";

			print " " . __("on field") . " ";
			print_select_hash("filter_type", 1, $filter_types);

			print "<br/>";

			print __("in") . " ";
			print_feed_select($link, "feed_id", $active_feed_id);

			print "</div>";

			print "<div class=\"dlgSec\">".__("Perform Action")."</div>";

			print "<div class=\"dlgSecCont\">";

			print "<select name=\"action_id\"
				onchange=\"filterDlgCheckAction(this)\">";
	
			$result = db_query($link, "SELECT id,description FROM ttrss_filter_actions 
				ORDER BY name");

			while ($line = db_fetch_assoc($result)) {
				printf("<option value='%d'>%s</option>", $line["id"], __($line["description"]));
			}
	
			print "</select>";

			print " " . __("with params") . " ";

			print "<input disabled size=\"20\"
				name=\"action_param\">";

			print "</div>";

			print "<div class=\"dlgSec\">".__("Options")."</div>";
			print "<div class=\"dlgSecCont\">";

			print "<div style=\"line-height : 100%\">";

			print "<input type=\"checkbox\" name=\"enabled\" id=\"enabled\" checked=\"1\">
					<label for=\"enabled\">".__('Enabled')."</label><br/>";

			print "<input type=\"checkbox\" name=\"inverse\" id=\"inverse\">
				<label for=\"inverse\">".__('Inverse match')."</label>";

			print "</div>";
			print "</div>";

			print "</form>";

			print "<div class=\"dlgButtons\">";

			print "<input type=\"submit\" 
				id=\"infobox_submit\"
				class=\"button\" onclick=\"return createFilter()\" 
				disabled=\"true\" value=\"".__('Create')."\"> ";

			print "<input class=\"button\"
				type=\"submit\" onclick=\"return closeInfoBox()\" 
				value=\"".__('Cancel')."\">";

			print "</div>";

//			print "</td></tr></table>"; 

			return;
		}

		if ($id == "feedUpdateErrors") {

			print "<div id=\"infoBoxTitle\">".__('Update Errors')."</div>";
			print "<div class=\"infoBoxContents\">";

			print __("These feeds have not been updated because of errors:");

			$result = db_query($link, "SELECT id,title,feed_url,last_error
			FROM ttrss_feeds WHERE last_error != '' AND owner_uid = ".$_SESSION["uid"]);

			print "<ul class='feedErrorsList'>";

			while ($line = db_fetch_assoc($result)) {
				print "<li><b>" . $line["title"] . "</b> (" . $line["feed_url"] . "): " . 
					"<em>" . $line["last_error"] . "</em>";
			}

			print "</ul>";
			print "</div>";

			print "<div align='center'>";

			print "<input class=\"button\"
				type=\"submit\" onclick=\"return closeInfoBox()\" 
				value=\"".__('Close')."\">";

			print "</div>";

			return;
		}

		if ($id == "editArticleTags") {

			print "<div id=\"infoBoxTitle\">".__('Edit Tags')."</div>";
			print "<div class=\"infoBoxContents\">";

			print "<form id=\"tag_edit_form\" onsubmit='return false'>";

			print __("Tags for this article (separated by commas):")."<br>";

			$tags = get_article_tags($link, $param);

			$tags_str = join(", ", $tags);

			print "<table width='100%'>";

			print "<tr><td colspan='2'><input type=\"hidden\" name=\"id\" value=\"$param\"></td></tr>";

			print "<tr><td colspan='2'><textarea rows='4' class='iedit' id='tags_str' 
				name='tags_str'>$tags_str</textarea>
			<div class=\"autocomplete\" id=\"tags_choices\" 
					style=\"display:none\"></div>	
			</td></tr>";

/*			print "<tr><td>".__('Add existing tag:')."</td>";

			$result = db_query($link, "SELECT DISTINCT tag_name FROM ttrss_tags 
				WHERE owner_uid = '".$_SESSION["uid"]."' ORDER BY tag_name");

			$found_tags = array();

			array_push($found_tags, '');

			while ($line = db_fetch_assoc($result)) {
				array_push($found_tags, truncate_string($line["tag_name"], 20));
			}

			print "<td align='right'>";

			print_select("found_tags", '', $found_tags, "onchange=\"javascript:editTagsInsert()\"");

			print "</td>"; 

			print "</tr>"; */

			print "</table>";

			print "</form>";

			print "<div align='right'>";

			print "<input class=\"button\"
				type=\"submit\" onclick=\"return editTagsSave()\" 
				value=\"".__('Save')."\"> ";

			print "<input class=\"button\"
				type=\"submit\" onclick=\"return closeInfoBox()\" 
				value=\"".__('Cancel')."\">";


			print "</div>";

			return;
		}

		if ($id == "printTagCloud") {
			print "<div id=\"infoBoxTitle\">".__('Tag cloud')."</div>";
			print "<div class=\"infoBoxContents\">";

			print __("Showing most popular tags ")." (<a 
			href='javascript:toggleTags(true)'>".__('browse more')."</a>):<br/>"; 

			print "<div class=\"tagCloudContainer\">";

			printTagCloud($link);

			print "</div>";

			print "<div align='center'>";
			print "<input class=\"button\"
				type=\"submit\" onclick=\"return closeInfoBox()\" 
				value=\"".__('Close this window')."\">";
			print "</div>";

			print "</div>";

			return;
		}

		print "<div id='infoBoxTitle'>Internal Error</div>
			<div id='infoBoxContents'>
			<p>Unknown dialog <b>$id</b></p>
			</div></div>";
	
	}
?>

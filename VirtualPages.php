<?php

/**
 * Copyright (c) 2010 Dave Ross <dave@csixty4.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

/**
 * Based heavily on the Fake Page Plugin by Scott Sherrill-Mix
 * @see http://scott.sherrillmix.com/blog/blogger/creating-a-better-fake-post-with-a-wordpress-plugin/
 */
class VirtualPages {
	
    var $pageID = false;
    var $options = array();
    var $errors = array();

    /**
     * Class constructor
     */
    function VirtualPages() {
        // Configuration
        add_filter('init', array(&$this, 'init'));

        /**
         * We'll wait til WordPress has looked for posts, and then
         * check to see if the requested url matches our target.
         */
        add_filter('the_posts',array(&$this,'detectPost'));
        add_filter('posts_where', array(&$this, 'filter_where'));
        add_filter('template_redirect', array(&$this, 'template'));
        add_filter('wp_title', array(&$this, 'title'), 11, 3);
        add_action('wp_print_scripts', array(&$this, 'print_scripts'));

        /**
         * Register the admin menu
         */
        add_action('admin_menu', array('VirtualPages', 'admin_menu'));
    }

    function print_scripts() {
        if(is_admin()) {
            $pluginPath = VirtualPages::getPluginPath();
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-datepicker', $pluginPath.'js/ui.datepicker.js', 'jquery-ui-core');
        }
    }

    function init() {
        $this->options = unserialize(base64_decode(get_option('virtual-pages')));
    }

    /**
     *
     * This is where the magic happens.
     * @param <type> $posts ignored
     * @uses $wp WordPress request object
     * @uses $wp_query WordPress results object
     * @return <type>
     */
    function detectPost($posts) {
        global $wp;
        global $wp_query;
        global $paged;

	if(!is_array($this->options)) {
		$this->options = array();
	}

        foreach($this->options as $id=>$options) {

            /**
             * Check if the requested page matches our target
             */

			$pageNameMatches = isset($wp->query_vars) && array_key_exists('pagename', $wp->query_vars) && ($wp->query_vars['pagename'] == strtolower($options['permalink']));
			$pageIDMatches = isset($wp->query_vars) && array_key_exists('page_id', $wp->query_vars) && ($wp->query_vars['page_id'] == $options['permalink']);
            if ( $pageNameMatches || $pageIDMatches) {
                
                $this->pageID = $id;

                // Pagination
                if(array_key_exists('paged', $wp->query_vars)) {
                    $this->paged = $wp->query_vars['paged'];
                }
                else {
                    $this->paged = 1;
                }

                remove_filter('the_posts',array(&$this,'detectPost'));

                $parameters = VirtualPages::buildQueryPostsParameters($options, $this->paged);
                $posts = query_posts($parameters);
                $wp_query = new WP_Query($parameters);

                

                $wp_query->posts = $posts;
                
                /**
                 * Trick wp_query into thinking this is a page (necessary for wp_title() at least)
                 * Not sure if it's cheating or not to modify global variables in a filter
                 * but it appears to work and the codex doesn't directly say not to.
                 */
                $wp_query->is_page = true;
                //Not sure if this one is necessary but might as well set it like a true page
                $wp_query->is_singular = true;
                $wp_query->is_home = false;
                $wp_query->is_archive = false;
                $wp_query->is_category = false;

               //Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
                unset($wp_query->query["error"]);
                $wp_query->query_vars["error"]="";
                $wp_query->is_404=false;


                $wp_query->post->post_title = $options['page_title'];
                /**
                 * Fake post ID to prevent WP from trying to show comments for
                 * a post that doesn't really exist.
                 */
                $wp_query->post->ID = -1;

                /**
                 * Static means a page, not a post.
                 */
                $wp_query->post->post_status = 'static';

                /**
                 * Turning off comments for the post.
                 */
                $wp_query->post->comment_status = 'closed';

                /**
                 * Let people ping the post?  Probably doesn't matter since
                 * comments are turned off, so not sure if WP would even
                 * show the pings.
                 */
                $wp_query->post->ping_status = 'closed';

                $wp_query->post->comment_count = 0;

                /**
                 * You can pretty much fill these up with anything you want.  The
                 * current date is fine.  It's a fake post right?  Maybe the date
                 * the plugin was activated?
                 */
                $wp_query->post->post_date = current_time('mysql');
                $wp_query->post->post_date_gmt = current_time('mysql', 1);

                /**
                 * Not sure if this is even important.  But gonna fill it up anyway.
                 */
                $wp_query->post->guid = get_bloginfo('wpurl') . '/' . $options['permalink'];
                
                /**
                 * The author ID for the post.  Usually 1 is the sys admin.  Your
                 * plugin can find out the real author ID without any trouble.
                 */
                $wp_query->post->post_author = 1;


                return $wp_query->posts;
            }
        }

        return $posts;
    }

    function filter_where($where = '') {
        if(!empty($this->pageID)) {
            $settings = $this->options[$this->pageID];

            // Filter by date
            if(!empty($settings['start_date']) && preg_match('/\d{4}-\d{2}-\d{2}/', $settings['start_date'])) {
                $where .= " AND post_date >= '{$settings['start_date']}' ";
            }
            if(!empty($settings['end_date']) && preg_match('/\d{4}-\d{2}-\d{2}/', $settings['end_date'])) {
                $where .= " AND post_date < '{$settings['end_date']}' ";
            }

            // Filter by author(s)
            // query_posts doesn't seem to support querying for multiple authors
            if(!empty($settings['authors'])) {
                // clean the author data
                foreach($settings['authors'] as $index=>$author) {
                    $settings['authors'][$index] = intval($author);
                }

                $where .= " AND post_author IN (".implode(',', $settings['authors']).')';
            }
        }

        return $where;
    }

    /**
     * Render the page with the specified template
     * @global WP_Query $wp_query
     * @global integer $paged
     * @param string $template 
     */
    public function template($template) {

        if(isset($this->pageID) && !empty($this->pageID) && array_key_exists($this->pageID, $this->options) && $this->options[$this->pageID]['page_template'] != '') {


            $templateName = $this->options[$this->pageID]['page_template'];
            $templatePath = TEMPLATEPATH."/{$templateName}";

            // Force our values back into wp_query
            global $wp_query, $paged;
            $paged = $this->paged;
            $wp_query->query_vars['paged'] = $this->paged;
            $wp_query->is_paged = true;

            // Various functions called throughout the rendering
            // process cause the query to be parsed again and
            // we were losing this vital value. Even if we're
            // displaying posts in a particular category, this
            // must be false or else the template is going to
            // try rendering a category name and throw a bunch
            // of errors.
            $wp_query->is_category = false;

            // According to http://codex.wordpress.org/Plugin_API/Action_Reference
            // we can safely exit here. The template will take care of the rest.
            include($templatePath);
            exit;
        }
    }

    function title($title, $sep, $seplocation) {
        if($this->options[$this->pageID]['page_title'] != '') {
            switch($seplocation) {
                case 'right':
                    return $this->options[$this->pageID]['page_title']." $sep $title";
                    break;
                case 'left':
                default:
                    return "$title $sep ".$this->options[$this->pageID]['page_title'];
            }
        }
        else {
            return $title;
        }
    }


    ///////////////
    // Admin Pages
    ///////////////

    /**
     * Include the Virtual Pages options page in the admin menu
     * @return void
     */
    public function admin_menu() {
        if(current_user_can('manage_options')) {
            add_options_page("Virtual Pages", __('Virtual Pages', 'mt_trans_domain'), 8, __FILE__, array('VirtualPages', 'plugin_options'));
        }
    }

    /**
     * Display & process the Live Search admin options
     * @uses $wpdb WordPress database object for queries.
     */
    public function plugin_options($method = false, $options = false) {

        global $wpdb;

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $inst = new VirtualPages();
        $inst->init();

        if('POST' == $method) {
            return $inst->handlePost();
        }
        else {
            return $inst->handleGet();
        }
    }

    /**
     *
     */
    private function handleGet() {

            global $wpdb;

            $vars = array(
            			'options' => $this->options,
            			'users' => array(),
          			);

			// Force the default action if none was provided
			if(!array_key_exists('action', $_REQUEST)) {
				$_REQUEST['action'] = '';
			}
			
            switch($_REQUEST['action']) {
                case 'delete':
                    $vars['options'] = array_intersect_key($vars['options'], array_flip($_REQUEST['ids']));
                    VirtualPages::renderTemplate('virtual-pages-admin-delete', $vars);
                    break;
                case 'edit':
                case 'update':
		    if(!array_key_exists('options', $vars) || !is_array($vars['options'])) {
		    	$vars['options'] = array();
		    }

                    $vars['options'] = array_intersect_key($vars['options'], array_flip($_REQUEST['ids']));
                    // Add new
                    if(count($_REQUEST['ids']) == 1 && $_REQUEST['ids'][0] == -1) {
                        $vars['options'][-1] = array();
                    }

                    // Get a list of users
                    $vars['users'] = array();
                    
                    $allUsersIDs = $wpdb->get_col($wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = {$wpdb->usermeta}.user_id WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}user_level' ORDER BY %s ASC", 'user_nicename' ));
                    foreach($allUsersIDs as $userID) {
                        $vars['users'][$userID] = get_userdata($userID);
                    }

                    // Get a list of page templates
                    $vars['pageTemplates'] = get_page_templates();

                    // Tags & Categories
                    $vars['categories'] = get_categories(array('type' => 'post', 'orderby' => 'name', 'order' => 'ASC'));
                    $vars['tags'] = VirtualPages::getAllTags();

                    $vars['errors'] = $this->errors;

                    // Date formatting
                    //$dateFormat = get_option('date_format');
                    //$vars['start_date'] = date_i18n($dateFormat, strtotime($vars['start_date']));
                    //$vars['end_date'] = date_i18n($dateFormat, strtotime($vars['end_date']));

                    VirtualPages::renderTemplate('virtual-pages-admin-edit', $vars);
                    break;
                default:
                    VirtualPages::renderTemplate('virtual-pages-admin-index', $vars);
            }
    }

    /**
     * Handle updates from the Virtual Page editor
     */
    private function handlePost() {

        // Check the nonce
        $nonce=$_POST['_wpnonce'];
        if(!wp_verify_nonce($nonce, 'virtual-pages')) {
            die('Security check');
        }

        switch($_POST['action']) {
            case 'delete':
                foreach($_POST['ids'] as $index=>$id) {
                    unset($this->options[$id]);
                }
                $this->saveOptions();
                // TODO queue up a "success" message
                VirtualPages::doJSRedirect(VirtualPages::strstrb($_SERVER['REQUEST_URI'], '?')."?page={$_POST['page']}");
                break;
            case 'update':
                $id = $_POST['ids'][0];
                if(empty($id) || -1 == $id) {
                    $id = uniqid();
                }

                $this->options[$id] = array(
                        'post_type' => $_POST['post_type'],
                        'page_title' => $_POST['page_title'],
                        'post_parent' => $_POST['post_parent'],
                        'categories' =>     VirtualPages::getPostVar('categories', true),
                        'tags' =>     VirtualPages::getPostVar('tags', true),
                        'authors' =>     VirtualPages::getPostVar('authors', true),
                        'orderby' => $_POST['orderby'],
                        'order' => $_POST['order'],
                        'posts_per_page' => intval($_POST['posts_per_page']),
                        'page_template' => $_POST['page_template'],
                        'update_date' => date('r'),
                        'status' => 'published',
                        'permalink' => str_replace(' ', '-', $_POST['permalink']),
                        'start_date' => $_POST['start_date'],
                        'end_date' => $_POST['end_date']
                );

                // Validation
                $this->errors = array();

                // Page title required
                if(empty($this->options[$id]['page_title'])) {
                    $this->errors['page_title'] = 'Please provide a page title';
                }

                // Permalink required
                if(empty($this->options[$id]['permalink'])) {
                    $this->errors['permalink'] = 'Invalid permalink';
                }
                elseif(strpos($this->options[$id]['permalink'], '&') || strpos($this->options[$id]['permalink'], '?')) {
                    $this->errors['permalink'] = "Permalinks can't contain ? or &";
                }

                // Default posts per page
                if(empty($this->options[$id]['posts_per_page'])) {
                    $this->options[$id]['posts_per_page'] = 10;
                }

                // Start date format
                if(!empty($this->options[$id]['start_date']) && !preg_match('/\d{4}-\d{2}-\d{2}/', $this->options[$id]['start_date'])) {
                    $this->errors['start_date'] = 'Start date should be YYYY-MM-DD';
                }

                // End date format
                if(!empty($this->options[$id]['end_date']) && !preg_match('/\d{4}-\d{2}-\d{2}/', $this->options[$id]['end_date'])) {
                    $this->errors['end_date'] = 'End date should be YYYY-MM-DD';
                }

                // Make sure start date <= end date
                if(!empty($this->options[$id]['start_date']) && !empty($this->options[$id]['end_date'])) {
                    $startDate = strtotime($this->options[$id]['start_date']);
                    $endDate = strtotime($this->options[$id]['end_date']);
                    if($startDate > $endDate) {
                        $this->errors['end_date'] = 'End Date must be the same as or later than Start Date';
                    }
                }

                // These fields need to be arrays, or else we'll force them
                $multiGroups = array('categories', 'authors', 'tags');
                foreach($multiGroups as $multiGroup) {
                    if(!is_array($this->options[$id][$multiGroup])) {
                        $this->options[$id][$multiGroup] = array();
                    }
                }

                if(empty($this->errors)) {
                    // Success - Save settings & return to index
                    $this->saveOptions();
                    VirtualPages::doJSRedirect(VirtualPages::strstrb($_SERVER['REQUEST_URI'], '?')."?page={$_POST['page']}");
                }
                else {
                    // Failure - Redisplay the form with errors
                    $this->handleGet();
                }
                break;
            default:
                // do nothing
        }
    }

    /**
     * Write options to WordPress's options table
     */
    private function saveOptions() {
        update_option('virtual-pages', base64_encode(serialize($this->options)));
    }

    /**
     * Render a template
     * @param string $templateName
     * @param array $vars variables to populate in the template
     */
    private function renderTemplate($templateName, $vars = array()) {
        $thisPluginsDirectory = dirname(__FILE__);

        // Rebuild the query string minus the "id" setting
        $queryStringComponents = $_REQUEST;
        unset($queryStringComponents['id']);
        unset($queryStringComponents['_wpnonce']);
        $queryString = array();
        foreach($queryStringComponents as $key=>$value) {
            $queryString[]= "{$key}={$value}";
        }
        $queryString = implode('&', $queryString);

        extract($vars);
        $nonce = wp_create_nonce('virtual-pages');
        include("$thisPluginsDirectory/{$templateName}.tpl.php");
    }

    /**
     * Modify plugin path as needed for compatiblity with WP-Subdomains
     * @return string
     */
    public static function getPluginPath() {

        $pluginPath = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));

        if(defined('WPS_VERSION')) {

            //--- Create the SubDomains Object
            $wps_subdomains = new WpsSubDomains( );

            //--- Grab This Subdomain object (if we're on one)
            $wps_this_subdomain = $wps_subdomains->getThisSubdomain();

            // WP Subdomains is running
            if ( $wps_this_subdomain ) {
                $pluginPath = $wps_this_subdomain->changeGeneralLink( $pluginPath );
            }
        }

        return $pluginPath;
    }

    /**
     * Insert a short redirect script to redirect after headers have been
     * sent
     * @param string $url
     */
    private function doJSRedirect($url) {
        echo <<<SCRIPT
		   <script type="text/javascript">
		   <!--
		      window.location= "{$url}";
		   //-->
		   </script>		
SCRIPT;
    }

    /**
     * Retrieves all tags defined in the system
     * @uses $wpdb WordPress database object for queries.
     * @return array id=>name
     */
    private function getAllTags() {
        global $wpdb;

        $query = <<<QUERY
            SELECT wp_terms.term_id, wp_terms.name
            FROM wp_terms
            INNER JOIN wp_term_taxonomy ON wp_term_taxonomy.term_id = wp_terms.term_id
            WHERE wp_term_taxonomy.taxonomy = 'post_tag'
            ORDER BY wp_terms.name
QUERY;
        $records = $wpdb->get_results($query, ARRAY_A);
        if(!is_array($records)) { 
                $records = array(); 
        }

        $tags = array();

        foreach($records as $record) {
            $tags[$record['term_id']] = $record['name'];
        }

        return $tags;
    }

    /**
     * Convert options to a set of parameters that can be passed to
     * query_posts(). Some options cannot be handled by query_posts() and are
     * handled elsewhere in a where clause.
     * @param array $options
     * @param integer $pageNum
     * @return array query_posts parameters
     */
    private function buildQueryPostsParameters($options, $pageNum) {
        $params = array();

        if(count($options['categories']) > 0) {
            $params['cat'] = implode(',', $options['categories']);
        }

        if(count($options['tags']) > 0) {
            $params['tag__in'] = $options['tags'];
        }

        if($options['orderby'] != '') {
            $params['orderby'] = $options['orderby'];
        }

        if($options['order'] != '') {
            $params['order'] = $options['order'];
        }

        if($options['posts_per_page'] != '') {
            $params['posts_per_page'] = $options['posts_per_page'];
        }

	if($pageNum > 1) {
		$params['paged'] = $pageNum;
	}

        if($options['post_type'] != '') {
            $params['post_type'] = $options['post_type'];
        }

        if($options['post_parent'] != '') {
            $params['post_parent'] = $options['post_parent'];
        }
        
        return $params;
    }
    
    public function renderVariable($arr, $var) {
        
    	if(is_array($arr) && array_key_exists($var, $arr)) {
    		return $arr[$var];
    	}
    	else {
    		return '';
    	}	
    }

    private function getPostVar($var, $isArray) {
    	if(array_key_exists($var, $_POST)) {
    		return $_POST[$var];
    	}
    	else {
    		if($isArray) {
    			return array();
    		}
    		else {
	    		return null;
    		}
    	}	
    }
    
    /**
     * Simulates strstr with the $before_needle option
     * in PHP < 5.3
     * @see http://www.php.net/manual/en/function.strstr.php#92923
     * $h = haystack, $n = needle 
     */
	public function strstrb($h,$n){
	    return array_shift(explode($n,$h,2));
	}
}

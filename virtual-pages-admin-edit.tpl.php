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

?>
<h2>Edit Virtual Page</h2>

<?php foreach($options as $id=>$settings) : ?>

<form action="" method="post" name="updatevirtualpages" id="updatevirtualpages" class="metabox-holder">
    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
    <input type="hidden" name="ids[]" value="<?php echo $id; ?>" />

    <div id="poststuff">

        <div class="stuffbox">
            <h3><label for="page_title">Page title</label></h3>
            <?php if(array_key_exists('page_title', $errors)) : ?><p class="error"><?php echo $errors['page_title']; ?></p><?php endif; ?>
            <div class="inside"><input type="text" name="page_title" id="page_title" size="50" value="<?php echo VirtualPages::renderVariable($settings, 'page_title'); ?>" /></div>
        </div>

        <div class="stuffbox">
            <h3><label for="permalink">Permalink</label></h3>
            <?php if(array_key_exists('permalink', $errors)) : ?><p class="error"><?php echo $errors['permalink']; ?></p><?php endif; ?>
            <div class="inside"><input type="text" id="permalink" name="permalink" size="50" value="<?php echo VirtualPages::renderVariable($settings, 'permalink'); ?>" /></div>
        </div>

        <div class="stuffbox">
            <h3><label>Categories</label></h3>
            <?php if(array_key_exists('categories', $errors)) : ?><p class="error"><?php echo $errors['categories']; ?></p><?php endif; ?>
            <div class="inside"><fieldset>
                        <?php foreach($categories as $category) : ?>
                    <p><label><input type="checkbox" name="categories[]" value="<?php echo $category->term_id; ?>" <?php if(array_key_exists('categories', $settings) && in_array($category->term_id, $settings['categories'])) : ?>checked="checked"<?php endif; ?> /> <?php echo $category->name; ?></label></p>
                        <?php endforeach; ?>
                </fieldset></div>
        </div>

        <div class="stuffbox">
            <h3><label>Tags</label></h3>
            <?php if(array_key_exists('tags', $errors)) : ?><p class="error"><?php echo $errors['tags']; ?></p><?php endif; ?>
            <div class="inside"><fieldset>                        
                        <?php foreach($tags as $tagID=>$tag) : ?>
                    <p><label><input type="checkbox" name="tags[]" value="<?php echo $tagID; ?>" <?php if(array_key_exists('tags', $settings) && in_array($tagID, $settings['tags'])) : ?>checked="checked"<?php endif; ?> /> <?php echo $tag; ?></label></p>
                        <?php endforeach; ?>
                </fieldset></div>
        </div>

        <div class="stuffbox">
            <h3><label>Authors</label></h3>
            <?php if(array_key_exists('authors', $errors)) : ?><p class="error"><?php echo $errors['authors']; ?></p><?php endif; ?>
            <div class="inside">
                <fieldset>
                        <?php foreach($users as $userID=>$user) : ?>

                    <p><label><input type="checkbox" name="authors[]" value="<?php echo $userID; ?>" <?php if(array_key_exists('authors', $settings) && in_array($userID, $settings['authors'])) : ?>checked="checked"<?php endif; ?> /> <?php echo $user->user_login; ?> <?php if(isset($user->first_name) && isset($user->last_name)) : ?>(<?php echo $user->first_name.' '.$user->last_name; ?>)<?php endif; ?></label></p>
                        <?php endforeach; ?>
                </fieldset>
            </div>
        </div>

        <div class="stuffbox">
            <h3><label for="start_date">Date Range</label></h3>
            <?php if(array_key_exists('start_date', $errors)) : ?><p class="error"><?php echo $errors['start_date']; ?></p><?php endif; ?>
            <?php if(array_key_exists('end_date', $errors)) : ?><p class="error"><?php echo $errors['end_date']; ?></p><?php endif; ?>
            <div class="inside">
                <input type="text" class="date" id="start_date" name="start_date" value="<?php echo VirtualPages::renderVariable($settings, 'start_date'); ?>" /><span>&nbsp;to&nbsp;</span>
                <input type="text" class="date" id="end_date" name="end_date" value="<?php echo VirtualPages::renderVariable($settings, 'end_date'); ?>" />
                <p>Enter dates in YYYY-MM-DD format. Both dates are optional.</p>
                <script type="text/javascript">
                    jQuery(function() {
                       var options = { dateFormat: 'yy-mm-dd' };
                       jQuery('#start_date').datepicker(options);
                       jQuery('#end_date').datepicker(options);
                       
                    });
                </script>
                <link href="<?php echo VirtualPages::getPluginPath(); ?>/css/smoothness/jquery-ui-1.7.3.custom.css" rel="stylesheet" />
            </div>
        </div>

        <div class="stuffbox">
            <h3><label>Sort</label></h3>
            <?php if(array_key_exists('orderby', $errors)) : ?><p class="error"><?php echo $errors['orderby']; ?></p><?php endif; ?>
            <?php if(array_key_exists('order', $errors)) : ?><p class="error"><?php echo $errors['order']; ?></p><?php endif; ?>
            <div class="inside">
                <select name="orderby">
                        <?php $orderByOptions = array(
                                'author' => 'Author',
                                'date' => 'Date',
                                'title' => 'Title',
                                'modified' => 'Modified',
                                'ID' => 'ID',
                                'rand' => 'Random'
                        ); ?>
                        <?php foreach($orderByOptions as $key=>$value) : ?>
                    <option value="<?php echo $key; ?>" <?php if(array_key_exists('orderby', $settings) && $key == $settings['orderby']) : ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                </select>
                <select name="order">
                        <?php $orderOptions = array('ASC' => 'Ascending', 'DESC' => 'Descending'); ?>
                        <?php foreach($orderOptions as $key=>$value) : ?>
                    <option value="<?php echo $key; ?>" <?php if(array_key_exists('order', $settings) && $key == $settings['order']) : ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="stuffbox">
            <h3><label for="posts_per_page">Posts per page</label></h3>
            <?php if(array_key_exists('posts_per_page', $errors)) : ?><p class="error"><?php echo $errors['posts_per_page']; ?></p><?php endif; ?>
            <div class="inside"><input type="text" name="posts_per_page" size="3" value="<?php echo VirtualPages::renderVariable($settings, 'posts_per_page'); ?>" /></div>
        </div>

        <div class="stuffbox">
            <h3><label for="page_template">Page Template</label></h3>
            <?php if(array_key_exists('page_template', $errors)) : ?><p class="error"><?php echo $errors['page_template']; ?></p><?php endif; ?>
            <div class="inside"><select name="page_template" id="page_template">
                    <option value="index.php">Default</option>
                        <?php foreach($pageTemplates as $name=>$value) : ?>
                    <option value="<?php echo $value; ?>" <?php if(array_key_exists('page_template', $settings) && $value == $settings['page_template']) : ?>selected="selected"<?php endif; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                </select>
            </div>
        </div>

        <input type="hidden" name="post_type" value="post" />
        <input type="hidden" name="post_parent" value="" />
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
        <br class="clear" />
        <div><input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="Update" /> <a href="<?php echo "{$_SERVER['PHP_SELF']}?page={$_REQUEST['page']}"; ?>">cancel</a></div>

    </div>
</form>
    <?php break;
endforeach; ?>
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

function currentPagePath() { return VirtualPages::strstrb($_SERVER['REQUEST_URI'], '?'); }

?>
<h2>Edit Virtual Pages <a href="<?php echo currentPagePath().'?'.$queryString; ?>&action=edit&ids[]=-1" class="button add-new-h2">Add New</a> </h2>

<div class="clear"></div>

<form action="" method="get" name="updatevirtualpages" id="updatevirtualpages">
    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
    <table class="widefat post fixed" cellspacing="0">
        <thead>
            <tr>

                <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                <th scope="col" id="title" class="manage-column column-title" style="">Title</th>
                <th scope="col" id="author" class="manage-column column-permalink" style="">Permalink</th>
                <th scope="col" id="date" class="manage-column column-date" style="">Date</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                <th scope="col"  class="manage-column column-title" style="">Title</th>
                <th scope="col"  class="manage-column column-author" style="">Permalink</th>

                <th scope="col"  class="manage-column column-date" style="">Date</th>
            </tr>
        </tfoot>

        <tbody>

            <?php if(is_array($options)) : foreach($options as $id=>$settings) : ?>
            <tr id='post-3' class='alternate author-self status-publish iedit' valign="top">
                <th scope="row" class="check-column"><input type="checkbox" name="ids[]" value="<?php echo $id; ?>" /></th>
                <td class="post-title column-title"><strong><a class="row-title" href="<?php echo currentPagePath().'?'.$queryString; ?>&ids[]=<?php echo $id; ?>&_wpnonce=<?php echo $nonce; ?>&action=edit" title="Edit &#8220;<?php echo $settings['page_title']; ?>&#8221;"><?php echo $settings['page_title']; ?></a></strong>
                    <div class="row-actions"><span class='edit'><a href="<?php echo currentPagePath().'?'.$queryString; ?>&ids[]=<?php echo $id; ?>&_wpnonce=<?php echo $nonce; ?>&action=edit" title="Edit this page">Edit</a> | </span><span class='delete'><a class='submitdelete' title='Delete this page?' href='<?php echo currentPagePath().'?'.$queryString; ?>&ids[]=<?php echo $id; ?>&_wpnonce=<?php echo $nonce; ?>&action=delete'>Delete Permanently</a> | </span><span class='view'><a href="<?php echo bloginfo('url').'/'.$settings['permalink']; ?>" title="View &#8220;<?php echo $settings['page_title']; ?>&#8221;" rel="permalink" target="_blank">View</a></span></div>
                    <div class="hidden" id="inline_3">
                        <div class="post_title">Test cat post</div>
                        <div class="post_name">test-cat-post</div>
                        <div class="post_author">1</div>
                        <div class="comment_status">open</div>
                        <div class="ping_status">open</div>
                        <div class="_status">publish</div>
                        <div class="jj">11</div>
                        <div class="mm">05</div>
                        <div class="aa">2010</div>
                        <div class="hh">20</div>
                        <div class="mn">12</div>
                        <div class="ss">10</div>
                        <div class="post_password"></div>
                        <div class="tags_input"></div>
                        <div class="post_category">3</div>
                        <div class="sticky"></div></div>		</td>
                <td class="author column-author"><a href="<?php echo bloginfo('url').'/'.$settings['permalink']; ?>" target="_blank"><?php echo $settings['permalink']; ?></a></td>

                <td class="date column-date"><abbr title="<?php echo date('Y/m/d H:i:s A', strtotime($settings['update_date'])); ?>"><?php echo date('Y-m-d', strtotime($settings['update_date'])); ?></abbr><br /><?php echo $settings['status']; ?></td>	</tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>


    <div class="tablenav">
        <div class="alignleft actions">
            <select name="action">
                <option value="-1" selected="selected">Bulk Actions</option>
                <option value="delete">Delete</option>
            </select>
            <input type="submit" value="Apply" class="button-secondary action" />
            <br class="clear" />
        </div>
        <br class="clear" />
    </div>
</form>
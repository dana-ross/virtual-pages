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
<h2>Delete Virtual Pages</h2>
<p>You have specified these virtual pages for deletion:</p>
<form action="" method="post" name="updatevirtualpages" id="updatevirtualpages">
    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
    <ul>
        <?php foreach($options as $id=>$settings) : ?>
        <li><input type="hidden" name="ids[]" value="<?php echo $id;?>" />ID #<?php echo $id; ?>: <?php echo $settings['page_title']; ?></li>
        <?php endforeach; ?>
    </ul>

    <p>These virtual pages will be removed, but the posts they displayed will remain on the site.</p>
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
    <p class="submit"><input type="submit" name="submit" value="Confirm Deletion" class="button-secondary" /> <a href="<?php echo currentPagePath()."?page={$_REQUEST['page']}"; ?>">cancel</a></p>
</form>
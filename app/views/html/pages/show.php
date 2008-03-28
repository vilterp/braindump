<p><?php echo getLink('Edit',"pages/edit/$page->name",array('accesskey'=>'e')) ?></p>
<?php echo do_filters('page_body',$page->body()) ?>
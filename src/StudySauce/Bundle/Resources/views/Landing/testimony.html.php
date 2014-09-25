<div class="testimonials">
    <div class="testimony clearfix">
        <h2>What our students are saying</h2>

        <div class="testimony-inner clearfix">
            <?php foreach ($view['assetic']->image(array('@StudySauceBundle/Resources/public/images/testimonial.png'), array(), array('output' => 'bundles/studysauce/images/*')) as $url): ?>
                <img width="58" height="58" src="<?php echo $view->escape($url) ?>" alt="PHOTO" />
            <?php endforeach; ?>
            <p>&ldquo;I never knew how to actually study until Study Sauce showed me. &nbsp;Now I'm
                organized and I don't cram for tests.&rdquo;</p>
            <p class="author">
                - Justin C.<br />
                Arizona State University
            </p>
        </div>
    </div>
</div>
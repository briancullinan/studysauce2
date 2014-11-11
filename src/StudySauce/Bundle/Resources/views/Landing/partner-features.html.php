<div class="features clearfix">
    <div class="two-column-guide">
        <h2>Study Sauce features</h2>
        <div class="grid_6">
            <div>
                <h3>Proven science</h3>
                <p>We incorporate the leading science in memory retention to ensure study time is maximized.  Improve study skills and stop cramming for exams only to forget all of the information a few days later.</p>
            </div>
            <div>
                <h3>Check in</h3>
                <p>Students log into study sessions and retrain themselves to use the most effective study methods. We teach them as they go.</p>
            </div>
            <div>
                <h3>Create incentives</h3>
                <p>We incorporate the Incentive Theory of Motivation to help establish meaningful rewards.  In fact, many of our sponsors establish extra incentives for student achievements.</p>
            </div>
        </div>
        <div class="grid_6">
            <div>
                <h3>Study plans</h3>
                <p>Take studying to the next level with one of our custom study plans. We build personalized plans based on the student's study preferences and individual needs.</p>
            </div>
            <div>
                <h3>Study tips</h3>
                <p>We provide invaluable tips on how to study, and more importantly how not to study.  It is a safe bet that you will be surprised by the insights.</p>
            </div>
            <div>
                <h3>Study metrics</h3>
                <p>Track study sessions over time. See all of the hard work aggregated in custom charts that we create when students check in.</p>
            </div>
        </div>
    </div>
</div>

<div class="support-box clearfix">
    <h3><a href="#contact-support" class="cloak highlighted-link" data-toggle="modal">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/chat_icon.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="CHAT" />
            <?php endforeach; ?>Still have questions? <span class="reveal">Talk to a study tutor.</span></a>
    </h3>

    <p class="highlighted-link"><a class="more" href="<?php print $view['router']->generate('register'); ?>">Sign up to help - it's free</a></p>
</div>

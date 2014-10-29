<?php use Symfony\Component\HttpKernel\Controller\ControllerReference; ?>

<div class="features clearfix">

    <div class="two-column-guide">
        <h2>Study Sauce features</h2>
        <div class="grid_6">
            <div>
                <h3>Proven science</h3>
                <p>We incorporate the leading science in memory retention to ensure you are maximizing your study time.  Improve your study skills and stop cramming for exams only to forget all of the information a few days later.</p>
            </div>
            <div>
                <h3>Set goals</h3>
                <p>Goal setting is a terrific way to improve your performance.  Establish different types of goals and incentives to improve your academic results.</p>
            </div>
            <div>
                <h3>Study plans</h3>
                <p>Take your studying to the next level with one of our custom study plans. We build your personalized plan based on your study preferences and your goals.</p>
            </div>
        </div>
        <div class="grid_6">
            <div>
                <h3>Check in</h3>
                <p>Start logging your study sessions and retrain yourself to use the most effective study methods. We will teach you as you go.</p>
            </div>
            <div><span class="metrics">&nbsp;</span>
                <h3>Study metrics</h3>
                <p>Track your study sessions over time. See all of your hard work aggregated in custom charts that we create when you check in.</p>
            </div>
            <div>
                <h3>Study tips</h3>
                <p>We provide invaluable tips on how to study, and more importantly how not to study. It is a safe bet that you will be surprised by the results.</p>
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

    <p class="highlighted-link"><a class="more" href="<?php print $view['router']->generate('account_register'); ?>">Join for free</a></p>
</div>

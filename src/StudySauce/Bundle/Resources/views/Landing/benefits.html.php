<div class="page-top clearfix">
    <div class="scr">
        <h2>Learn to be a great studier</h2>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?></div>
        <div class="grid_6">
            <h3><span>Learn</span></h3>
            <p>- Has anyone ever actually taught you how to study?  Learn how to study effectively, and more importantly how not to study.<br />
                - Study Sauce uses the leading science in memory retention, so you can stop cramming for exams only to forget all of the information a few days later.<br />
                - Study Sauce goes where you go - use on your tablet or mobile phone.</p>
        </div>
        <div class="swap clearfix">
            <div class="grid_6">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
                <?php endforeach; ?></div>
            <div class="grid_6">
                <h3><span>Organize</span></h3>
                <p>- Learn to take better notes and keep them all in one place with our study notes that are integrated with Evernote.<br />
                    - Get organized with a custom study plan tailored to your schedule.<br />
                    - Goal setting is a terrific way to improve your performance. Establish different types of goals and incentives to improve your academic results.</p>
            </div>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/resolution_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Resolution"/>
            <?php endforeach; ?></div>
        <div class="grid_6">
            <h3><span>Track</span></h3>
            <p>- Enter in your important dates and Study Sauce will send you email reminders so nothing sneaks up on you.<br />
                - Track your study sessions over time. See all of your hard work aggregated in custom charts that we create when you check in.<br />
                - Take the guesswork out of calculating your class grades. Know where you stand and what you need to make your target grades.</p>
        </div>
        <div class="swap clearfix">
            <div class="grid_6">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
                <?php endforeach; ?></div>
            <div class="grid_6">
                <h3><span>Succeed</span></h3>
                <p>- Stop cramming<br />
                    - Stop procrastinating<br />
                    - Take control of your school life.<br />
                    - Reach your potential!</p>
            </div>
        </div>
        <p class="highlighted-link"><a class="more" href="<?php print $view['router']->generate('register'); ?>">Join for free</a></p>
    </div>
</div>
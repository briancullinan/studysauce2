<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view->extend('StudySauceBundle:Shared:dashboard.html.php');

 $view['slots']->start('stylesheets');

 foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/partner.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

 $view['slots']->start('body'); ?>

<div class="panel-pane" id="partner">

    <div class="pane-content">

        <h2>Choosing an accountability partner can be invaluable to achieving your goals</h2>

        <div class="partner-setup">

            <div class="plupload" id="partner-plupload">
                <h3>I am accountable to:</h3>

                <div class="plup-list-wrapper">
                    <ul class="plup-list clearfix ui-sortable">
                        <img src="/sites/studysauce.com/themes/successinc/images/empty-photo.png" height="200"
                             width="200" alt="Upload">
                    </ul>
                </div>
                <div id="p192im745h1jo6pdv1g0n13bl12a44_html5_container" class="plupload html5"
                     style="position: absolute; width: 100px; height: 100px; overflow: hidden; z-index: 99999; opacity: 0; background: transparent;">
                    <input id="p192im745h1jo6pdv1g0n13bl12a44_html5"
                           style="font-size: 999px; position: absolute; width: 100%; height: 100%;" type="file"
                           accept="image/png,image/gif,image/jpeg,image/*" multiple="multiple"></div>
            </div>

            <div id="partner-invite">
                <div class="first-name">
                    <label class="input">
                        <input type="text" value="Frank" size="60" maxlength="128" placeholder="First name">
                    </label>
                </div>
                <div class="last-name">
                    <label class="input">
                        <input type="text" value="Herbert" size="60" maxlength="128" placeholder="Last name">
                    </label>
                </div>
                <div class="email">
                    <label class="input">
                        <input type="text" value="fh876@example.org" size="60" maxlength="128"
                               placeholder="Email address">
                    </label>
                </div>
            </div>

            <div id="partner-permissions">
                <h3>My partner is allowed to see:</h3>
                <ul>
                    <label class="checkbox"><input type="checkbox" value="goals"><i></i><span>My goals</span></label>
                    <label class="checkbox"><input type="checkbox"
                                                   value="metrics"><i></i><span>My metrics</span></label>
                    <label class="checkbox"><input type="checkbox"
                                                   value="deadlines"><i></i><span>My deadlines</span></label>
                    <label class="checkbox"><input type="checkbox"
                                                   value="uploads"><i></i><span>My uploaded content </span><sup
                            class="premium">Premium</sup></label>
                    <label class="checkbox"><input type="checkbox" value="plan"><i></i><span>My study plan </span><sup
                            class="premium">Premium</sup></label>
                    <label class="checkbox"><input type="checkbox"
                                                   value="profile"><i></i><span>My study profiles </span><sup
                            class="premium">Premium</sup></label>
                </ul>
            </div>
        </div>
        <div class="partner-faqs">
            <h3>FAQs:</h3>
            <h4>Why do I need an accountability partner?</h4>

            <p>
                Research shows that simply writing down your goals makes you more likely to achieve them. Having an
                accountability partner takes it to a new level. We all have ups and downs in school and finding someone
                to help motivate and challenge you along the way can be invaluable.
            </p>
            <h4>How do I choose an accountability partner?</h4>

            <p>
                An accountability partner is someone that will keep you on track to achieve your goals. Here are some
                attributes to consider as you decide. Choose someone that:
            </p>
            <ul>
                <li>Will challenge you (you will need more than just encouragement)</li>
                <li>Will celebrate your successes with you</li>
                <li>Is invested in your education</li>
                <li>You trust</li>
            </ul>
            <p>Take a few minutes to think about who best fits this description. Sometimes a parent or best friend are
                not your best options. Maybe some other family member, classmate, or even a non-family mentor can be the
                ideal choice.</p>
            <h4>Now that I have chosen my accountability partner, what should I do?</h4>

            <p>Communication is the key! Outline your expectations and ask to be held accountable. Set up regular
                check-ins (try to talk at least once every week). Be transparent about your struggles and your successes
                during the conversations.</p>
            <h4>Can I change my accountability partner in Study Sauce?</h4>

            <p>Sure you can. You can change your accountability partner or what they can see at any time. Just use the
                edit function next to the photograph on the Accountability partner tab.</p>
        </div>

    </div>

</div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerinvite'), ['strategy' => 'sinclude']);
$view['slots']->stop();

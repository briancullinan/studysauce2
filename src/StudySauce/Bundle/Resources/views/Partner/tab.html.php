<?php
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\PartnerInvite;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var $partner PartnerInvite */
$permissions = !empty($partner) && $partner instanceof PartnerInvite ? $partner->getPermissions() : [
    'goals',
    'metrics', 
    'deadlines',
    'uploads',
    'plan',
    'profile'
];

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/partner.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/partner.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="partner">
        <div class="pane-content">
            <h2>Choosing an accountability partner can be invaluable to achieving your goals</h2>
            <form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
            <div class="partner-setup">
                <div class="plupload">
                    <h3>I am accountable to:</h3>
                    <a href="#partner-select" class="plup-select" id="partner-upload-select">Click here to select an image</a>
                    <div class="plup-filelist" id="partner-filelist">
                        <?php if(!empty($partner) && !empty($partner->getPhoto())) {
                            ?><img width="200" height="200" src="<?php echo $view->escape($partner->getPhoto()->getUrl()) ?>" alt="LOGO" /><?php
                        }
                        else {
                            foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
                                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
                            <?php endforeach;
                            } ?>
                    </div>
                    <input type="hidden" name="partner-plupload">
                </div>

                <div id="partner-invite" class="<?php print ($isReadOnly ? 'read-only' : 'edit'); ?>">
                    <div class="first-name">
                        <label class="input">
                            <input type="text" value="<?php print (!empty($partner) ? $partner->getFirst() : ''); ?>"
                                   size="60" maxlength="128" placeholder="First name">
                        </label>
                    </div>
                    <div class="last-name">
                        <label class="input">
                            <input type="text" value="<?php print (!empty($partner) ? $partner->getLast() : ''); ?>"
                                   size="60" maxlength="128" placeholder="Last name">
                        </label>
                    </div>
                    <div class="email">
                        <label class="input">
                            <input type="text" value="<?php print (!empty($partner) ? $partner->getEmail() : ''); ?>"
                                   size="60" maxlength="128" placeholder="Email address">
                        </label>
                    </div>
                    <div class="form-actions highlighted-link invalid">
                        <div class="invalid-only">You must complete all fields before moving on.</div>
                        <button type="submit" value="#partner-save" class="more">Invite</button>
                    </div>
                </div>

                <div class="permissions <?php print ($isReadOnly ? 'read-only' : 'edit'); ?>">
                    <h3>My partner is allowed to see:</h3>
                    <ul>
                        <li><label class="checkbox"><input type="checkbox" value="goals" <?php print (in_array(
                                    'goals',
                                    $permissions
                                ) ? 'checked="checked"' : ''); ?>><i></i><span>My goals</span></label></li>
                        <li><label class="checkbox"><input type="checkbox" value="metrics" <?php print (in_array(
                                    'metrics',
                                    $permissions
                                ) ? 'checked="checked"' : ''); ?>><i></i><span>My metrics</span></label></li>
                        <li><label class="checkbox"><input type="checkbox" value="deadlines" <?php print (in_array(
                                    'deadlines',
                                    $permissions
                                ) ? 'checked="checked"' : ''); ?>><i></i><span>My deadlines</span></label></li>
                        <li><label class="checkbox"><input type="checkbox" value="uploads" <?php print (in_array(
                                    'uploads',
                                    $permissions
                                ) ? 'checked="checked"' : ''); ?>><i></i><span>My uploaded content </span><sup
                                    class="premium">Premium</sup></label></li>
                        <li><label class="checkbox"><input type="checkbox" value="plan" <?php print (in_array(
                                    'plan',
                                    $permissions
                                ) ? 'checked="checked"' : ''); ?>><i></i><span>My study plan </span><sup
                                    class="premium">Premium</sup></label></li>
                        <li><label class="checkbox"><input type="checkbox" value="profile" <?php print (in_array(
                                    'profile',
                                    $permissions
                                ) ? 'checked="checked"' : ''); ?>><i></i><span>My study profiles </span><sup
                                    class="premium">Premium</sup></label></li>
                    </ul>
                </div>
            </div>
            </form>
            <div class="partner-faqs">
                <h3>FAQs:</h3>
                <h4>Why do I need an accountability partner?</h4>

                <p>
                    Research shows that simply writing down your goals makes you more likely to achieve them. Having an
                    accountability partner takes it to a new level. We all have ups and downs in school and finding
                    someone
                    to help motivate and challenge you along the way can be invaluable.
                </p>
                <h4>How do I choose an accountability partner?</h4>

                <p>
                    An accountability partner is someone that will keep you on track to achieve your goals. Here are
                    some
                    attributes to consider as you decide. Choose someone that:
                </p>
                <ul>
                    <li>Will challenge you (you will need more than just encouragement)</li>
                    <li>Will celebrate your successes with you</li>
                    <li>Is invested in your education</li>
                    <li>You trust</li>
                </ul>
                <p>Take a few minutes to think about who best fits this description. Sometimes a parent or best friend
                    are
                    not your best options. Maybe some other family member, classmate, or even a non-family mentor can be
                    the
                    ideal choice.</p>
                <h4>Now that I have chosen my accountability partner, what should I do?</h4>

                <p>Communication is the key! Outline your expectations and ask to be held accountable. Set up regular
                    check-ins (try to talk at least once every week). Be transparent about your struggles and your
                    successes
                    during the conversations.</p>
                <h4>Can I change my accountability partner in Study Sauce?</h4>

                <p>Sure you can. You can change your accountability partner or what they can see at any time. Just use
                    the
                    edit function next to the photograph on the Accountability partner tab.</p>
            </div>
        </div>
    </div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerConfirm'),['strategy' => 'sinclude']);
$view['slots']->stop();

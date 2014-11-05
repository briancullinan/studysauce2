<?php

use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/buy.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/buy.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="checkout">
        <div class="pane-content clearfix">
            <?php echo $view->render('StudySauceBundle:Buy:funnel.html.php'); ?>
            <fieldset id="billing-pane">
                <legend>Billing information</legend>
                <div class="first-name">
                    <label class="input"><span>First name</span><input name="first-name" type="text" value="Brian"></label>
                </div>
                <div class="last-name">
                    <label class="input"><span>Last name</span><input name="last-name" type="text" value="Cullinan"></label>
                </div>
                <label class="input"><span>Street address</span><input name="street1" type="text" value="6934 E Sandra Ter"></label>
                <label class="input"><input name="street2" type="text" value=""></label>
                <div class="city">
                    <label class="input"><span>City</span><input name="city" type="text" value="Scottsdale"></label>
                </div>
                <div class="zip">
                    <label class="input"><span>Postal code</span><input name="zip" type="text" value="85254"></label>
                </div>
                <label class="select"><span>State/Province</span><select name="state">
                        <option value="" selected="selected">- Select -</option>
                        <option value="1">Alabama</option>
                        <option value="2">Alaska</option>
                        <option value="3">American Samoa</option>
                        <option value="4">Arizona</option>
                        <option value="5">Arkansas</option>
                        <option value="6">Armed Forces Africa</option>
                        <option value="7">Armed Forces Americas</option>
                        <option value="8">Armed Forces Canada</option>
                        <option value="9">Armed Forces Europe</option>
                        <option value="10">Armed Forces Middle East</option>
                        <option value="11">Armed Forces Pacific</option>
                        <option value="12">California</option>
                        <option value="13">Colorado</option>
                        <option value="14">Connecticut</option>
                        <option value="15">Delaware</option>
                        <option value="16">District of Columbia</option>
                        <option value="17">Federated States Of Micronesia</option>
                        <option value="18">Florida</option>
                        <option value="19">Georgia</option>
                        <option value="20">Guam</option>
                        <option value="21">Hawaii</option>
                        <option value="22">Idaho</option>
                        <option value="23">Illinois</option>
                        <option value="24">Indiana</option>
                        <option value="25">Iowa</option>
                        <option value="26">Kansas</option>
                        <option value="27">Kentucky</option>
                        <option value="28">Louisiana</option>
                        <option value="29">Maine</option>
                        <option value="30">Marshall Islands</option>
                        <option value="31">Maryland</option>
                        <option value="32">Massachusetts</option>
                        <option value="33">Michigan</option>
                        <option value="34">Minnesota</option>
                        <option value="35">Mississippi</option>
                        <option value="36">Missouri</option>
                        <option value="37">Montana</option>
                        <option value="38">Nebraska</option>
                        <option value="39">Nevada</option>
                        <option value="40">New Hampshire</option>
                        <option value="41">New Jersey</option>
                        <option value="42">New Mexico</option>
                        <option value="43">New York</option>
                        <option value="44">North Carolina</option>
                        <option value="45">North Dakota</option>
                        <option value="46">Northern Mariana Islands</option>
                        <option value="47">Ohio</option>
                        <option value="48">Oklahoma</option>
                        <option value="49">Oregon</option>
                        <option value="50">Palau</option>
                        <option value="51">Pennsylvania</option>
                        <option value="52">Puerto Rico</option>
                        <option value="53">Rhode Island</option>
                        <option value="54">South Carolina</option>
                        <option value="55">South Dakota</option>
                        <option value="56">Tennessee</option>
                        <option value="57">Texas</option>
                        <option value="58">Utah</option>
                        <option value="59">Vermont</option>
                        <option value="61">Virginia</option>
                        <option value="60">Virgin Islands</option>
                        <option value="62">Washington</option>
                        <option value="63">West Virginia</option>
                        <option value="64">Wisconsin</option>
                        <option value="65">Wyoming</option>
                    </select></label>
                <label class="select"><span>Country</span><select name="country">
                        <option value="124">Canada</option>
                        <option value="840" selected="selected">United States</option>
                    </select></label>
                <a href="#show-coupon" class="cloak">Have a coupon code? Click <span class="reveal">here</span>.</a>
            </fieldset>
            <fieldset id="payment-pane">
                <legend>Payment method</legend>
                <div class="fieldset-description">
                    <div>
                        <label class="radio"><input name="reoccurs" type="radio" value="monthly" checked="checked"><i></i><span>$9.99/mo</span></label><br />
                        <label class="radio"><input name="reoccurs" type="radio" value="yearly"><i></i><span>$99/year <sup>Recommended</sup></span></label>
                    </div>
                    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/money_back_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                        <img src="<?php echo $view->escape($url) ?>" />
                    <?php endforeach; ?>
                </div>
                <div class="cc-number">
                    <label class="input">
                        <span>Card number</span><input name="cc-number" type="text" value="4744880045591912">
                        <div class="cards">
                            <img alt="VISA" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/visa.gif')) ?>" />
                            <img alt="MC" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/mc.gif')) ?>" />
                            <img alt="DISC" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/disc.gif')) ?>" />
                            <img alt="AMEX" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/amex.gif')) ?>" />
                        </div>
                    </label>
                </div>
                <div class="cc-month">
                    <label class="select"><span>Expiration date</span>
                        <select name="cc-month">
                            <option value="01" selected="selected">01 - January</option>
                            <option value="02">02 - February</option>
                            <option value="03">03 - March</option>
                            <option value="04">04 - April</option>
                            <option value="05">05 - May</option>
                            <option value="06">06 - June</option>
                            <option value="07">07 - July</option>
                            <option value="08">08 - August</option>
                            <option value="09">09 - September</option>
                            <option value="10">10 - October</option>
                            <option value="11">11 - November</option>
                            <option value="12">12 - December</option>
                        </select></label>
                </div>
                <div class="cc-year">
                    <label class="select"><span>Expiration year</span>
                        <select name="cc-year">
                            <?php
                            $first = true;
                            for($y = 0; $y < 20; $y++)
                            {
                                ?><option value="<?php print intval(date('y')) + $y; ?>" <?php print ($first ? 'selected="selected"': ''); ?>><?php print intval(date('Y')) + $y; ?></option><?php
                                $first = false;
                            } ?></select></label>
                </div>
                <label class="input"><span>CCV</span><input name="cc-ccv" type="text" value="111">
                    <img src="https://www.studysauce.com/sites/all/modules/ubercart/payment/uc_credit/images/info.png" alt="">
                    <a href="#ccv-info" data-toggle="modal">What's the CVV?</a>
                </label>
            </fieldset>
            <fieldset id="coupon-pane">
                <legend>Coupon discount</legend>
                <div class="coupon-code">
                    <label class="input"><input name="coupon-code" type="text" placeholder="Enter code" value=""></label>
                </div>
                <a href="#coupon-apply" class="more">Apply to order</a>
            </fieldset>
            <div class="form-actions highlighted-link invalid"><a href="#submit-order" class="more">Complete order</a></div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:ccvInfo'), ['strategy' => 'sinclude']);
$view['slots']->stop();

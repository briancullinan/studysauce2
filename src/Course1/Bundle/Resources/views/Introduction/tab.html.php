<?php

$view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="course1_introduction">
    <div class="pane-content">
        <h2>Introduction to Study Sauce <span class="time">10<small> minutes</small></span></h2>
        <div class="learn-bullets">
            <h3><span>In this course you will learn:</span></h3>
            <ol>
                <li><a><span>1</span>Some common misconceptions about studying</a></li>
                <li><a><span>2</span>Why studying is important</a></li>
                <li><a><span>3</span>How Study Sauce will help you take control of your academic life</a></li>
            </ol>
        </div>
        <div class="player-divider">
            <div class="player-wrapper">
                <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/Introduction.png'], [], ['output' => 'bundles/course1/images/*']) as $url): ?>
                    <img src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?>
            </div>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course1_introduction', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        <!-- Facebook Conversion Code for Facebook sale -->
        <script>(function() {
                var _fbq = window._fbq || (window._fbq = []);
                if (!_fbq.loaded) {
                    var fbds = document.createElement('script');
                    fbds.async = true;
                    fbds.src = '//connect.facebook.net/en_US/fbds.js';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(fbds, s);
                    _fbq.loaded = true;
                }
            })();
            window._fbq = window._fbq || [];
            window._fbq.push(['track', '6008770262329', {'value':'0.00','currency':'USD'}]);
        </script>
        <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6008770262329&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" /></noscript>
    </div>
</div>
<?php $view['slots']->stop();


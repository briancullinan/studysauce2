<div class="header-wrapper header navbar navbar-inverse navbar-fixed-top">
    <div id="site-name" class="container navbar-header">
        <a title="Home" href="/">
            <?php foreach ($view['assetic']->image(array('@StudySauceBundle/Resources/public/images/Study_Sauce_Logo.png'), array(), array('output' => 'bundles/studysauce/images/*')) as $url): ?>
                <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
            <?php endforeach; ?><strong>Study</strong> Sauce</a>
        <div id="site-slogan">Discover the secret sauce to studying</div>
    </div>
    <div id="partner-message">
        <?php foreach ($view['assetic']->image(array('@StudySauceBundle/Resources/public/images/empty-photo.png'), array(), array('output' => 'bundles/studysauce/images/*')) as $url): ?>
            <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>" />
        <?php endforeach; ?>
        <div style="display:inline-block;">
            I am accountable to: <br><a href="#partner">Click to set up</a>
        </div>
    </div>
    <div id="welcome-message"><span>Welcome </span><strong>Brian</strong>
        <a href="/user/logout" title="Log out">logout</a>    </div>
    <div id="jquery_jplayer" style="width: 0px; height: 0px;"><audio id="jp_audio_0" preload="metadata"></audio></div>
    <script type="text/javascript">
        window.musicLinks = ["\/sites\/studysauce.com\/themes\/successinc\/music\/Mozart - Concerto No.6 in B flat major - II. Andante un poco adagio.mp3","\/sites\/studysauce.com\/themes\/successinc\/music\/Mozart - Piano Concerto No. 15 in B flat major, K450 - II. Andante.mp3","\/sites\/studysauce.com\/themes\/successinc\/music\/Piano Concerto no.2 in Bb [K. 39]-0-Mozart - Concerto No.2 in B flat major - II. Andante-2081-6966.mp3","\/sites\/studysauce.com\/themes\/successinc\/music\/Mozart - Concerto No.17 in G - II. Andante.mp3","\/sites\/studysauce.com\/themes\/successinc\/music\/Mozart - Concertone in C Major, K. 190 - II. Andante.mp3","\/sites\/studysauce.com\/themes\/successinc\/music\/Mozart - Concerto No.16 in D for piano - II. Andante.mp3","\/sites\/studysauce.com\/themes\/successinc\/music\/Mozart - Concerto No.1 in F major - II. Andante.mp3","\/sites\/studysauce.com\/themes\/successinc\/music\/Mozart - Concerto No.5 in D major - II. Andante ma un poco Adagio.mp3"];
        window.musicIndex = 0;
    </script>
</div>
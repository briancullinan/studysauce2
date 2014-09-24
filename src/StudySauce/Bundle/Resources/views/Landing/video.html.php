<div class="video clearfix">
    <div class="flexslider">
        <h1>Learn how to learn</h1>
        <ul class="slides clearfix">
            <li class="clone">
                <div class="video-floater">
                    <div style="width:100%;padding-bottom:56%;position:relative;">
                        <!--<iframe id="ytplayer" width="560" height="315" src="https://www.youtube.com/embed/vJG9PDaXNaQ?rel=0&controls=0&modestbranding=1&showinfo=0&enablejsapi=1&playerapiid=ytplayer&origin=[site:url]" frameborder="0" style="width:100%;height:100%;position:absolute;top:0;left:0;" allowfullscreen></iframe>-->
                        <div id="ytplayer"></div>
                        <script type="text/javascript">
                            var tag = document.createElement('script');

                            tag.src = "https://www.youtube.com/iframe_api";
                            var firstScriptTag = document.getElementsByTagName('script')[0];
                            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                            function onYouTubeIframeAPIReady(playerId) {
                                player = new YT.Player('ytplayer', {
                                    height: '315',
                                    width: '560',
                                    videoId: 'vJG9PDaXNaQ',
                                    playerVars: {
                                        rel:0,
                                        controls:0,
                                        modestbranding:1,
                                        showinfo:0,
                                        enablejsapi:1,
                                        playerapiid:'ytplayer'
        //            origin:'[site:url]'
                                    },
                                    events: {
                                        'onStateChange': function (e) {
                                            _gaq.push(['_trackPageview', location.pathname + location.search  + '#yt' + e.data]);
                                        }
                                    }
                                });
                            }
                        </script>
                    </div>
                </div>
                <div class="highlighted-link">
                    <p><a class="more" href="/user/register">Sign up for free</a><br /><span style="color:#ffffff;">or </span><a href="/user" style="font-weight:bold;padding:8px 25px 8px 10px">Sign in</a></p>
                </div>
            </li>
        </ul>
    </div>
</div>

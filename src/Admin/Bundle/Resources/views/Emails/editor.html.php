<div id="left-column">
    <div id="top_panels_container">
        <div class="top_panel" id="quick-reference">
            <div class="close">×</div>

            <h2>Quick Reference</h2>

            <table>
                <tr>
                    <td>
                        <pre><code><span class="highlight">*</span>This is italicized<span class="highlight">*</span>, <wbr/>and <span class="highlight">**</span>this is bold<span class="highlight">**</span>.</code></pre>
                    </td>
                    <td><p>Use <code>*</code> or <code>_</code> for emphasis.</p></td>
                </tr>
                <tr>
                    <td>
                        <pre><code><span class="highlight">#</span> This is a first level header</code></pre>
                    </td>
                    <td><p>Use one or more hash marks for headers: <code>#&nbsp;H1</code>, <code>##&nbsp;H2</code>, <code>###&nbsp;H3</code>…</p></td>
                </tr>
                <tr>
                    <td>
                        <pre><code>This is a link to <wbr/><span class="highlight">[Google](http://www.google.com)</span></code></pre>
                    </td>
                    <td><p></p></td>
                </tr>
                <tr>
                    <td>
								<pre><code>First line.<span class="highlight">  </span>
                                        Second line.</code></pre>
                    </td>
                    <td><p>End a line with two spaces for a linebreak.</p></td>
                </tr>
                <tr>
                    <td>
								<pre><code><span class="highlight">- </span>Unordered list item
                                        <span class="highlight">- </span>Unordered list item</code></pre>
                    </td>
                    <td><p>Unordered (bulleted) lists use asterisks, pluses, or hyphens (<code>*</code>, <code>+</code>, or <code>-</code>) as list markers.</p></td>
                </tr>
                <tr>
                    <td>
								<pre><code><span class="highlight">1. </span>Ordered list item
                                        <span class="highlight">2. </span>Ordered list item</code></pre>
                    </td>
                    <td><p>Ordered (numbered) lists use regular numbers, followed by periods, as list markers.</p></td>
                </tr>
                <tr>
                    <td><pre><code><span class="highlight">    </span>/* This is a code block */</code></pre></td>
                    <td><p>Indent four spaces for a preformatted block.</p></td>
                </tr>
                <tr>
                    <td><pre><code>Let's talk about <span class="highlight">`</span>&lt;html&gt;<span class="highlight">`</span>!</code></pre></td>
                    <td><p>Use backticks for inline code.</p></td>
                </tr>
                <tr>
                    <td>
                        <pre><code><span class="highlight">![](http://w3.org/Icons/valid-xhtml10)</span></code></pre>
                    </td>
                    <td><p>Images are exactly like links, with an exclamation mark in front of them.</p></td>
                </tr>
            </table>

            <p><a href="http://daringfireball.net/projects/markdown/syntax" target="_blank">Full Markdown documentation</a></p>
        </div>
        <div class="top_panel" id="about">
            <div class="close">×</div>

            <h2>About MME</h2>

            <p>Hi, I'm <a href="http://pioul.fr/a-propos/" target="_blank">Philippe Masset</a>.</p>
            <p>I made the Minimalist Online Markdown Editor because I love Markdown and simple things.<br/>
                The whole source code is on <a href="https://github.com/pioul/Minimalist-Online-Markdown-Editor" target="_blank">GitHub</a>, and this editor is also available offline and with file support as a <a href="https://chrome.google.com/webstore/detail/minimalist-markdown-edito/pghodfjepegmciihfhdipmimghiakcjf" target="_blank">Chrome app</a>.</p>
            <p>If you have any suggestions or remarks whatsoever, just click on my name above and you'll have plenty of ways of contacting me.</p>

            <h3>Privacy</h3>

            <ul>
                <li>No data is sent to any server – everything you type stays inside your browser</li>
                <li>The editor automatically saves what you write locally for future use.<br/>
                    If using a public computer, either empty the left panel before leaving the editor or use your browser's privacy mode</li>
            </ul>
        </div>
    </div>
    <div class="wrapper">
        <div class="topbar hidden-when-fullscreen">
            <div class="buttons-container clearfix">
                <a href="#" class="button toppanel" data-toppanel="quick-reference">Quick Reference</a>
                <a href="#" class="button toppanel" data-toppanel="about">About</a>
                <a href="#" class="button icon-arrow-expand feature" data-feature="fullscreen" data-tofocus="markdown" title="Go fullscreen"></a>
            </div>
        </div>
        <textarea id="markdown" class="full-height" placeholder="Write Markdown"><?php print $view->escape($value); ?></textarea>
    </div>
</div>
<div id="right-column">
    <div class="wrapper">
        <div class="topbar hidden-when-fullscreen">
            <div class="buttons-container clearfix">
                <div class="button-group">
                    <a href="#" class="button switch" data-switchto="html">HTML</a>
                    <a href="#" class="button switch" data-switchto="preview">Preview</a>
                </div>
                <a href="#" class="button icon-arrow-down-a feature" data-feature="auto-scroll" title="Toggle auto-scrolling to the bottom of the preview panel"></a>
                <a href="#" class="button icon-arrow-expand feature" data-feature="fullscreen" data-tofocus="" title="Go fullscreen"></a><!-- data-tofocus is set dynamically by the HTML/preview switch -->
            </div>
        </div>
        <div class="bottom-bar hidden-when-fullscreen clearfix">
            <div class="word-count"></div>
        </div>
        <label class="input"><textarea id="html" class="full-height"></textarea></label>
        <div id="preview" class="full-height"></div>
    </div>
</div>
<div class="topbar visible-when-fullscreen">
    <div class="buttons-container clearfix">
        <div class="button-group">
            <a href="#" class="button switch" data-switchto="markdown">Markdown</a>
            <a href="#" class="button switch" data-switchto="html">HTML</a>
            <a href="#" class="button switch" data-switchto="preview">Preview</a>
        </div>
        <a href="#" class="button icon-arrow-expand feature" data-feature="fullscreen" title="Exit fullscreen"></a>
    </div>
</div>
<div class="bottom-bar visible-when-fullscreen clearfix">
    <div class="word-count"></div>
</div>
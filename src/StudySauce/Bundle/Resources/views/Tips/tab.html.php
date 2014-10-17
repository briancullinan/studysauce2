<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/tips.css'
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
        '@StudySauceBundle/Resources/public/js/tips.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="tips">

    <div class="pane-content">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tips-time" data-target="#tips-time" data-toggle="tab">Time Management</a></li>
            <li><a href="#tips-env" data-target="#tips-env" data-toggle="tab">Study Environment</a></li>
            <li><a href="#tips-strategy" data-target="#tips-strategy" data-toggle="tab">Strategies</a></li>
        </ul>
        <div class="tab-content">
            <div id="tips-time" class="tab-pane active">
                <h2>Time management study tips</h2>

                <div class="grid_6">
                    <div>
                        <h4>Cramming</h4>

                        <p>Cramming does tend to produce short term results, however, the brain has a tendency to “dump”
                            the information after the test. You will end up having to restudy the material to commit it
                            to long term memory…which can be problematic for your Final Exams… Not to mention the fact
                            that the material you are studying will most likely need to be used again in a more advanced
                            course.</p>
                    </div>
                    <div>
                        <h4>Pulling all-nighters</h4>

                        <p>The hallmark of poor time management is the all-nighter. Although it is often paraded as a
                            badge of honor, it is very clearly a terrible study habit. All-nighters are bad for your
                            brain. Sleep is a critical mechanism for your brain to repair itself and by interfering with
                            your sleep cycle, you reduce attention span, memory retention, and recall ability (among
                            many other things). Additionally, all-nighters are linked to lower GPAs in academic
                            performance research.</p>
                    </div>
                    <div>
                        <h4>Procrastinating</h4>

                        <p>"I work better under pressure." Most of us either believe this or have friends that recite
                            this mantra. Unfortunately, the research shows that this is simply not true. Heavy
                            procrastinators are linked to lower grades. There are several tips we can offer to help
                            limit your procrastination, but you will have to find what works best for you. Here are some
                            of our favorite methods are 1) Take the first step and 2) Set aside time to study. Read on
                            to learn more about this.</p>
                    </div>
                    <div>
                        <h4>Take the first step</h4>

                        <p>Productivity gurus commonly emphasize the importance of taking the very first step. This
                            simple piece of advice is powerful for a few reasons. First, big projects can be
                            overwhelming if you look at them from afar. It is critical to think of them as a group of
                            smaller, more manageable problems. Once you do that, it becomes much easier to get started.
                            Second, once you get started, you will often be surprised by how quickly things go. The
                            faster you take the first step, the faster you get started, the faster you finish. Try this
                            - think about the very first thing you need to do to get started. Then do it.</p>
                    </div>
                </div>
                <div class="grid_6">
                    <div>
                        <h4>Set aside time to study</h4>

                        <p>College is full of distractions. You can always find great reasons to push off studying to a
                            later time. By designating specific time for studying, you can force yourself to overlook
                            many distractions. We highly recommend building a study schedule (and will build one for you
                            if you upgrade to premium). Think through where and when you want to study and commit to it
                            ahead of time. This prior commitment is key to fighting off distractions and will help
                            reduce procrastination.</p>
                    </div>
                    <div>
                        <h4>Write down your key deadlines</h4>

                        <p>Get organized by writing down the deadlines for all of your classes. This will help you get a
                            better sense of the ebbs and flows of your schedule. There will likely be weeks that have
                            quite a few deadlines, and you can save yourself some pain by spotting these busy weeks
                            ahead of time. Check out our key <a href="#deadlines">deadlines tool</a> to help you plan
                            ahead and set up notifications.</p>
                    </div>
                    <div>
                        <h4>Create a midterm or finals study schedule</h4>

                        <p>The few weeks before midterms and finals can be very stressful. We recommend creating a
                            detailed schedule of your exams/papers/etc. and penciling in what material you want to cover
                            ahead of each exam. This exercise will get you started thinking on how much studying you
                            will need to do, and will help you allocate enough time to study sufficiently for each
                            topic.</p>
                    </div>
                </div>
            </div>
            <div id="tips-env" class="tab-pane">
                <h2>Environment study tips</h2>

                <div class="grid_6">
                    <div>
                        <h4>Alternating study location</h4>

                        <p>Changing location has shown a significant improvement in memory retention studies by
                            cognitive scientists. According to researchers, the brain associates material being learned
                            with the studier's environment. Varied environments allow the brain more opportunities to
                            associate the material with something unique - which in turn increases the likelihood of
                            retention.</p>
                    </div>
                    <div>
                        <h4>Study multiple subjects during the same session</h4>

                        <p>Think of this as athletics for your brain. Elite athletes have been cross-training in
                            multiple disciplines for years because of the proven benefits of varied activity. Studies
                            have shown that retention of study material improves dramatically when different parts of
                            the brain are activated. When the brain must use different approaches to solve problems, it
                            is better prepared for the range of questions that a student is likely to encounter on a
                            test. Studies have shown that test takers can double their performance, and the results have
                            been corroborated for both adults and children. This is most effective if you study very
                            different types of courses. For example, alternating Spanish with Calculus is more effective
                            than alternating Economics with Statistics because the latter subjects are more similar.</p>
                    </div>
                    <div>
                        <h4>Ditching the phone</h4>

                        <p>Did you know that research shows it takes people 25 minutes to rest after looking at their
                            phone? Your cell phone is your enemy when it comes to studying. If you are like us, severing
                            your electronic umbilical cord is extraordinarily tough to do. If you have a smartphone, put
                            it on airplane mode to avoid any distractions. The texts and calls can wait…we promise. PS -
                            this goes for tablets, ipods, kindles, laptops, and anything else that will draw your
                            attention away from the task at hand.</p>
                    </div>
                    <div>
                        <h4>Taking breaks</h4>

                        <p>This will ultimately be a personal preference. A good rule of thumb is to study hard for 50
                            minutes, then take a 10 minute break. You can then repeat the process for days that you need
                            to study longer. There is a lot of conflicting research in this area, so try to experiment
                            and find what works best for you. One interesting study concluded that students who took
                            nature walk breaks outperformed students that took walks in an urban setting. If you have
                            access to a nature walk, give it a shot.</p>
                    </div>
                    <div>
                        <h4>Spending significant time highlighting or underlining</h4>

                        <p>Highlighting can actually be counter-productive. This one would have been good to know before
                            we had used a bathtub worth of highlighting fluid as a student. Highlighting, underlining,
                            and rereading are all very passive approaches that have been used as a crutch for years. To
                            be effective, you must translate the material into an active exercise. For example, try to
                            create flash cards – they actually work quite well.</p>
                    </div>
                    <div>
                        <h4>Multitasking</h4>

                        <p>In our opinion, the ability to multitask is one of the big jokes of our time. Doing several
                            things inefficiently at once is a poor substitute for doing one thing efficiently. Being
                            able to multitask has somehow become a badge of honor for people when almost no one can do
                            it well. Studies show that multitasking can decrease performance by up to 40%! One such
                            study to read to understand the cognitive implications of multitasking is a paper entitled
                            “Executive Control of Cognitive Processes in Task Switching “ by Rubenstein, Meyer, and
                            Evans.</p>
                    </div>
                </div>
                <div class="grid_6">
                    <div>
                        <h4>Listing to music</h4>

                        <p>This is a hotly debated topic. From the many studies we have sifted through, there doesn’t
                            appear to be much evidence to support the benefits of music during studying. It can,
                            however, definitely be a distraction. If you do decide to experiment with listening to
                            music, we suggest avoiding popular music with fast beats. Instead, listen to musical scores
                            with no lyrics (we have a playlist that you can try when you check in to study). If you
                            choose to listen to music, you should not consciously realize the music is playing in the
                            background, otherwise it is a distraction. At the end of the day, this is gray area, find
                            what works for you.</p>
                    </div>
                    <div>
                        <h4>Studying in bed</h4>

                        <p>When studying, you want to put your body into a receptive mode that is conducive to learning
                            and retention. Getting too comfortable can be counterproductive and can make you drowsy.
                            Good posture helps you maintain alertness and enables you to study longer and more
                            effectively.</p>
                    </div>
                    <div>
                        <h4>Diet and exercise</h4>

                        <p>Many studies show the link between diet/exercise and improved cognitive performance. Taking
                            care of your body by eating nutritious foods and exercising frequently helps contribute to a
                            healthy brain. Specifically, scientists see improvement in attention span, memory retention,
                            and recall ability.</p>
                    </div>
                    <div>
                        <h4>Study groups</h4>

                        <p>Study groups can be a helpful tool, but are often misused. Here are a couple of things to
                            consider. First, when forming your group, try to keep the group to 3-6 people in most
                            instances. Larger groups tend to lose focus and lead to diminished results. Second, group
                            study tends to take much longer than individual study, so make sure you and the group are
                            focused and have explicit goals defined for the study session. Third, prepare for your group
                            study sessions before you show up. Having specific questions for the other group members
                            and/or teaching the other group members concepts that you have already studied will help you
                            better learn the material.</p>
                    </div>
                    <div>
                        <h4>Sleep</h4>

                        <p>Sleep is a critical component to brain health and academic performance. The average adult
                            needs 7-9 hours of sleep, while the average teenager needs 9-9 1/2 hours. In addition to
                            this quantity of sleep, the quality of sleep is also extremely important. If you are
                            struggling with sleep, we recommend checking out the Sleep Foundation for some really
                            helpful tips.</p>
                    </div>
                </div>
            </div>
            <div id="tips-strategy" class="tab-pane">
                <h2>Strategy study tips</h2>

                <div class="grid_6">
                    <div>
                        <h4>Being positive</h4>

                        <p>Ever wonder why self-help gurus always hammer this point? The ever-expanding research on this
                            topic shows that this may be a panacea to many of life’s ills. Positive thinking is
                            correlated with improved performance in almost all facets of life - school, career, and even
                            life expectancy. What does it actually mean to be positive? One of the most interesting ways
                            we have seen this described is through the concept of a person’s default explanatory style.
                            Optimists tend to explain unfortunate events as setbacks that can be remedied while
                            pessimists view them as an inherent failure that will follow them through life. There is a
                            ton of fascinating research on this topic – if you are interested we recommend looking into
                            the work of Dr. Seligman on Positive Psychology or Dr. Valliant’s work with Harvard’s Grant
                            Study.</p>
                    </div>
                    <div>
                        <h4>Ask for help</h4>

                        <p>You will be surprised by how many people are willing to help you. Your school wants you to do
                            well and almost certainly has tons of resources dedicated to helping you succeed. Often
                            times there are tutors available and/or subject-specific resources for students that simply
                            ask.</p>
                    </div>
                    <div>
                        <h4>Active vs passive learning</h4>

                        <p>This one is critical. When studying, one of your primary goals should be to move from passive
                            to active learning. Think of passive learning as receiving information while active learning
                            is interacting with the information to understand it more deeply. Passive learning can be
                            characterized as thinking of the student as an empty vessel waiting for knowledge to be
                            transferred by the teacher. Symptoms of passive learning can include: rote memorization of
                            material, making detailed notes of lectures that accept everything said at face value, or
                            simply reading the assigned material. Active learning seeks to improve understanding of
                            material by encouraging critical thinking. Examples include: asking thought-provoking
                            questions about the material to understand how things are related, why the student thinks
                            the teacher has chosen this particular exercise/reading, or encouraging students to think
                            about both sides of an argument.</p>
                    </div>
                    <div>
                        <h4>Spaced repetition</h4>

                        <p>For classes with a lot of memorization, spaced repetition can work wonders. Simply put, by
                            revisiting the material you are trying to remember at defined intervals, you greatly
                            increase your chance of retaining the information. Our study plans incorporate this study
                            strategy and will build out a schedule for you. We highly recommend either writing
                            flashcards by hand, or using an online tool like Anki.</p>
                    </div>
                </div>
                <div class="grid_6">
                    <div>
                        <h4>Active reading</h4>

                        <p>How often do you find yourself reading when suddenly you realize that you have no idea what
                            you have been covering? In order to get the most out of reading, you must move from passive
                            reading to active reading. Specifically, prime your brain by asking questions before you get
                            started. What else was happening during the time period of this reading? Why has this
                            reading been selected at this time in the course? By doing so, you will then be more curious
                            about the material and will pay closer attention. At the end of your reading, try to
                            summarize the main points by writing them down or by speaking them out loud. This is a great
                            way to check how much you have learned.</p>
                    </div>
                    <div>
                        <h4>Teaching to learn</h4>

                        <p>Have you ever taken a foreign language class and realized how much more difficult it is to
                            speak than to simply read the language? This is because you must understand the information
                            at a much deeper level. To speak, you must create the information in your mind and then
                            verbalize it to someone else in a way that makes sense. Similarly, by teaching someone else,
                            you are forcing yourself to explain a concept at a much deeper level. By doing so, you
                            greatly improve both your understanding of the topic as well as improving your overall
                            retention of the subject.</p>
                    </div>
                    <div>
                        <h4>Setting up goals</h4>

                        <p>Goal setting is a terrific way to improve your performance. Research shows that by simply
                            saying a goal aloud, you are more likely to achieve it! Try to set goals that stretch you to
                            perform better than you otherwise would. We also suggest setting three types of goals -
                            behavioral goals for how many hours to study per week, interim goals for grades that you
                            would like to achieve throughout the term, and finally the overall outcome goal of a grade
                            point average. Check out our <a href="#goals">Goals</a> tab to set up yours.</p>
                    </div>
                    <div>
                        <h4>Accountability partner</h4>

                        <p>Take your goal setting to the next level by holding yourself accountable to someone else. By
                            sharing your goals with an accountability partner, you will further motivate yourself to
                            achieve the goals you establish. Finding a good accountability partner is crucial. See our
                            <a href="#partner">Accountability Partner</a> tab for more.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

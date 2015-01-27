<?php
namespace Admin\Bundle\Tests;

use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class PageLoaderCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class PageLoaderCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    /**
     * @param AcceptanceTester $I
     */
    public function tryLandingPages(AcceptanceTester $I)
    {
        $I->wantTo('see StudySauce in title');
        $I->amOnPage('/');
        $I->seeInTitle('StudySauce');
        $I->wantTo('read the About us page');
        $I->seeLink('About us');
        $I->click('About us');
        $I->seeInCurrentUrl('/about');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->wantTo('read the privacy policy');
        $I->seeLink('Privacy policy');
        $I->click('Privacy policy');
        $I->seeInCurrentUrl('/privacy');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->wantTo('read terms of service');
        $I->seeLink('Terms of service');
        $I->click('Terms of service');
        $I->seeInCurrentUrl('/terms');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->wantTo('read refund policy');
        $I->seeLink('Refund policy');
        $I->click('Refund policy');
        $I->seeInCurrentUrl('/refund');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->test('tryContactUs');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryContactUs(AcceptanceTester $I)
    {
        $I->wantTo('contact the site\'s administrators');
        $I->wait(5);
        $I->seeLink('Contact us');
        $I->click('Contact us');
        $I->fillField('#contact-support input[name="your-name"]', 'test testers');
        $I->fillField('#contact-support input[name="your-email"]', 'tester@mailinator.com');
        $I->fillField('#contact-support textarea', 'I love this site.');
        $I->click('Send');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryGuestCheckout(AcceptanceTester $I)
    {
        // TODO: test torch and laurel because there is a link to checkout?
        $I->wantTo('checkout as a student');
        $I->amOnPage('/torchandlaurel');
        $I->seeLink('Get the Deal');
        $I->click('Get the Deal');
        $I->see('75% off');
        $I->wantTo('complete the checkout');
        $I->fillField('input[name="first-name"]', 'test');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('input[name="last-name"]', $last);
        $I->fillField('input[name="email"]', 'test' . $last . '@mailinator.com');
        $I->fillField('input[name="password"]', 'password');
        $I->fillField('input[name="street1"]', '6934 E sandra ter');
        $I->fillField('input[name="city"]', 'scottsdale');
        $I->fillField('input[name="zip"]', '85254');
        $I->selectOption('select[name="state"]', 'Arizona');
        $I->selectOption('select[name="country"]', 'United States');
        $I->fillField('input[name="cc-number"]', '4007000000027');
        $I->selectOption('select[name="cc-month"]', '09');
        $I->selectOption('select[name="cc-year"]', '2019');
        $I->fillField('input[name="cc-ccv"]', '123');
        $I->seeLink('Complete order');
        $I->click('Complete order');
        $I->wait(10);
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function tryStudyFunnel(AcceptanceTester $I)
    {
        $I->wantTo('finish the study assessment');
        $I->test('tryFunnelProfile');
        $I->test('tryFunnelSchedule');
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function tryFunnelProfile(AcceptanceTester $I)
    {
        $I->wantTo('fill out the study profile');
        $I->seeInCurrentUrl('/profile/funnel');
        $I->checkOption('Nothing but As');
        $I->checkOption('Hit hard, keep weeks open');
        $I->checkOption('input[name="profile-11am"][value="2"]');
        $I->checkOption('input[name="profile-4pm"][value="3"]');
        $I->checkOption('input[name="profile-9pm"][value="4"]');
        $I->checkOption('input[name="profile-2am"][value="5"]');
        $I->click('#profile .highlighted-link a');
        $I->wait(10);
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function tryFunnelSchedule(AcceptanceTester $I)
    {
        $I->wantTo('fill out my class schedule');
        $I->seeInCurrentUrl('/schedule/funnel');
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('div[data-value="Arizona State University"]');
        $I->fillField('.class-row:nth-child(1) .class-name input', 'PHIL 101');
        $I->checkOption('.class-row:nth-child(1) input[value="M"] + i');
        $I->click('.class-row:nth-child(1) input[value="W"] + i');
        $I->click('.class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(1) .start-time input', '11');
        $I->canSeeInField('.class-row:nth-child(1) .start-time input', 'AM');
        $I->fillField('.class-row:nth-child(1) .end-time input', '12');
        $I->canSeeInField('.class-row:nth-child(1) .start-time input', 'PM');
        $I->fillField('.class-row:nth-child(1) .start-date input', (new \DateTime())->format('m/d/y'));
        $I->click('.class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->click('#schedule .highlighted-link a[href="#save-class"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryStudentRegister(AcceptanceTester $I)
    {
        $I->wantTo('register as a new student');
        $I->amOnPage('/students');
        $I->seeLink('Sign up for free');
        $I->click('Sign up for free');
        $I->seeInCurrentUrl('/register');
        $I->seeLink('register with email');
        $I->click('register with email');
        $I->fillField('.first-name input', 'test');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('.last-name input', $last);
        $I->fillField('.email input', 'test' . $last . '@mailinator.com');
        $I->fillField('.password input', 'password');
        $I->seeLink('Register');
        $I->click('Register');
        $I->wait(10);
    }

    /**
     * @depends tryStudentRegister
     * @param AcceptanceTester $I
     */
    public function tryFreeCourse(AcceptanceTester $I)
    {
        $I->wantTo('complete all the free courses');
        $I->seeInCurrentUrl('/course/1/lesson/1/step');
        $I->test('tryLesson1');
        $I->seeInCurrentUrl('/course/1/lesson/2/step');
        $I->test('tryLesson2');
        $I->seeInCurrentUrl('/goals');
        $I->test('tryNewGoals');
        // use the menu to get to lesson 3
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 1');
        $I->click('Distractions');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/3/step');
        $I->test('tryLesson3');
        $I->seeInCurrentUrl('/schedule');
        $I->test('tryNewSchedule');
        // use the menu to get to lesson 4
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 1');
        $I->click('Procrastination');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/4/step');
        $I->test('tryLesson4');
        $I->seeInCurrentUrl('/deadlines');
        $I->test('tryNewDeadlines');
        // use the menu to get to lesson 5
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 1');
        $I->click('Study environment');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/5/step');
        $I->test('tryLesson5');
        $I->seeInCurrentUrl('/checkin');
        $I->test('tryNewCheckin');
        $I->seeInCurrentUrl('/metrics');
        // use the menu to get to lesson 6
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 1');
        $I->click('Partners');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/6/step');
        $I->test('tryLesson6');
        $I->seeInCurrentUrl('/partner');
        $I->test('tryNewPartner');
        // use the menu to get to lesson 7
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 1');
        $I->click('End of Level 1');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/7/step');
        $I->test('tryLesson7');
        $I->seeInCurrentUrl('/premium');
        $I->test('tryBillMyParents');
        $I->seeInCurrentUrl('/parents');
        $I->test('tryPrepayParent');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryLesson1(AcceptanceTester $I)
    {
        $I->wantTo('complete course 1');
        $I->seeAmOnUrl('/course/1/lesson/1/step');
        $I->seeLink('Launch');
        $I->click('Launch');
        $I->wait(10);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_introduction-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->click('#course1_introduction-step1 .highlighted-link a');
        $I->wait(5);
        $I->checkOption('input[value="college-senior"]');
        $I->checkOption('input[value="born"]');
        $I->checkOption('input[value="cram"]');
        $I->checkOption('input[value="on"]');
        $I->checkOption('input[value="two"]');
        $I->click('#course1_introduction-step2 .highlighted-link a:first-child');
        $I->wait(15);
        $I->click('#course1_introduction-step2 .highlighted-link a:last-child');
        $I->wait(5);
        $I->click('#course1_introduction-step3 .highlighted-link a');
        $I->wait(5);
        $I->fillField('#course1_introduction-step4 textarea', 'To get better grades!');
        $I->click('#course1_introduction-step4 .highlighted-link a');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryLesson2 (AcceptanceTester $I) {
        $I->wantTo('complete course 2');
        $I->seeAmOnUrl('/course/1/lesson/2/step');
        $I->click('#course1_setting_goals .highlighted-link a');
        $I->wait(10);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_setting_goals-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->click('#course1_setting_goals-step1 .highlighted-link a');
        $I->wait(5);
        $I->checkOption('input[value="60"]');
        $I->fillField('input[name="quiz-smart-acronym-S"]', 'specific');
        $I->fillField('input[name="quiz-smart-acronym-M"]', 'measurable');
        $I->fillField('input[name="quiz-smart-acronym-A"]', 'achievable');
        $I->fillField('input[name="quiz-smart-acronym-R"]', 'relevant');
        $I->fillField('input[name="quiz-smart-acronym-T"]', 'time-bound');
        $I->fillField('input[name="quiz-motivation-I"]', 'intrinsic');
        $I->fillField('input[name="quiz-motivation-E"]', 'extrinsic');
        $I->click('#course1_setting_goals-step2 .highlighted-link a:first-child');
        $I->wait(15);
        $I->click('#course1_setting_goals-step2 .highlighted-link a:last-child');
        $I->wait(5);
        $I->click('#course1_setting_goals-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Set up my goals');
        $I->click('Set up my goals');
        $I->wait(15);

    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewGoals(AcceptanceTester $I) {

        $I->wantTo('complete goals');
        $I->seeAmOnUrl('/goals');
        $I->selectOption('.goal-row .behavior select', '15');
        $I->fillField('.goal-row .reward textarea', 'No studying on saturday');
        $I->selectOption('.goal-row + .goal-row select', 'B');
        $I->fillField('.goal-row + .goal-row textarea', 'One free spending');
        $I->selectOption('.goal-row + .goal-row + .goal-row select', '3.75');
        $I->fillField('.goal-row + .goal-row + .goal-row textarea', 'Special dinner out');
        $I->seeLink('Save');
        $I->click('Save');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryLesson3 (AcceptanceTester $I) {
        $I->wantTo('complete course 3');
        $I->seeAmOnUrl('/course/1/lesson/3/step');
        $I->click('#course1_distractions .highlighted-link a');
        $I->wait(10);
        //$I->switchToIFrame($I->)
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_distractions-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->click('#course1_distractions-step1 .highlighted-link a');
        $I->wait(5);
        $I->checkOption('input[name="quiz-multitask"][value="false"]');
        $I->checkOption('input[name="quiz-downside"][value="remember"]');
        $I->checkOption('input[name="quiz-lower-score"][value="30"]');
        $I->checkOption('input[name="quiz-distraction"][value="40"]');
        $I->click('#course1_distractions-step2 .highlighted-link a:first-child');
        $I->wait(15);
        $I->click('#course1_distractions-step2 .highlighted-link a:last-child');
        $I->wait(5);
        $I->click('#course1_distractions-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Enter class schedule');
        $I->click('Enter class schedule');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewSchedule(AcceptanceTester $I)
    {
        $I->wantTo('fill out my class schedule');
        $I->seeAmOnUrl('/schedule');
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('div[data-value="Arizona State University"]');
        $I->fillField('.class-row:nth-child(1) .class-name input', 'PHIL 101');
        $I->click('.class-row:nth-child(1) input[value="M"] + i');
        $I->click('.class-row:nth-child(1) input[value="W"] + i');
        $I->click('.class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(1) .start-time input', '11');
        $I->fillField('.class-row:nth-child(1) .end-time input', '12');
        $I->fillField('.class-row:nth-child(1) .start-date input', (new \DateTime())->format('m/d/y'));
        $I->click('.class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->click('#schedule .highlighted-link a[href="#save-class"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryLesson4 (AcceptanceTester $I) {
        $I->wantTo('complete course 4');
        $I->seeAmOnUrl('/course/1/lesson/4/step');
        $I->click('#course1_procrastination .highlighted-link a');
        $I->wait(10);
        //$I->switchToIFrame($I->)
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_procrastination-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->click('#course1_procrastination-step1 .highlighted-link a');
        $I->wait(5);
        $I->fillField('input[name="quiz-memory-A"]', 'active');
        $I->fillField('input[name="quiz-memory-R"]', 'reference');
        $I->fillField('input[name="quiz-study-goal"]', 'retain information');
        $I->fillField('input[name="quiz-stop-procrastinating"]', 'space studying');
        $I->fillField('input[name="quiz-reduce-procrastination-D"]', 'deadlines');
        $I->fillField('input[name="quiz-reduce-procrastination-P"]', 'study plan');
        $I->click('#course1_procrastination-step2 .highlighted-link a:first-child');
        $I->wait(15);
        $I->click('#course1_procrastination-step2 .highlighted-link a:last-child');
        $I->wait(5);
        $I->click('#course1_procrastination-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Set up deadlines');
        $I->click('Set up deadlines');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewDeadlines(AcceptanceTester $I)
    {
        $I->wantTo('set up a deadline');
        $I->seeAmOnUrl('/deadlines');
        $I->selectOption('header + .deadline-row .class-name select', 'PHIL 101');
        $I->fillField('header + .deadline-row .assignment input', 'Exam 1');
        $I->checkOption('header + .deadline-row input[value="86400"]');
        $I->checkOption('header + .deadline-row input[value="172800"]');
        $I->checkOption('header + .deadline-row input[value="345600"]');
        $I->click('header + .deadline-row .due-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->fillField('header + .deadline-row .percent input', '10');
        $I->click('#deadlines .highlighted-link a[href="#save-deadline"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryLesson5 (AcceptanceTester $I) {
        $I->wantTo('complete course 5');
        $I->seeAmOnUrl('/course/1/lesson/5/step');
        $I->click('#course1_environment .highlighted-link a');
        $I->wait(10);
        //$I->switchToIFrame($I->)
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_environment-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->click('#course1_environment-step1 .highlighted-link a');
        $I->wait(5);
        $I->checkOption('input[name="quiz-environment-bed"][value="0"]');
        $I->checkOption('input[name="quiz-environment-mozart"][value="0"]');
        $I->checkOption('input[name="quiz-environment-nature"][value="1"]');
        $I->checkOption('input[name="quiz-environment-breaks"][value="1"]');
        $I->click('#course1_environment-step2 .highlighted-link a:first-child');
        $I->wait(15);
        $I->click('#course1_environment-step2 .highlighted-link a:last-child');
        $I->wait(5);
        $I->click('#course1_environment-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Check in');
        $I->click('Check in');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewCheckin (AcceptanceTester $I)
    {
        $I->wantTo('checkin for the first time');
        $I->seeAmOnUrl('/checkin');
        $I->wait(5);
        $I->click('#checkin .classes a:first-child');
        $I->seeLink('Continue to session');
        $I->click('Continue to session');
        $I->wait(20);
        $I->click('#checkin .classes a:first-child');
        $I->click('#timer-expire a[href="#close"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryLesson6 (AcceptanceTester $I) {
        $I->wantTo('complete course 6');
        $I->seeAmOnUrl('/course/1/lesson/6/step');
        $I->click('#course1_partners .highlighted-link a');
        $I->wait(10);
        //$I->switchToIFrame($I->)
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_partners-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->click('#course1_partners-step1 .highlighted-link a');
        $I->wait(5);
        $I->checkOption('input[value="focus"]');
        $I->checkOption('input[value="incentive"]');
        $I->checkOption('input[value="knows"]');
        $I->fillField('input[name="quiz-partners-often"]', 'Once a week');
        $I->checkOption('input[value="dieting"]');
        $I->click('#course1_partners-step2 .highlighted-link a:first-child');
        $I->wait(15);
        $I->click('#course1_partners-step2 .highlighted-link a:last-child');
        $I->wait(5);
        $I->click('#course1_partners-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Invite a partner');
        $I->click('Invite a partner');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewPartner(AcceptanceTester $I)
    {
        $I->wantTo('invite a new accountability partner');
        $I->seeAmOnUrl('/partner');
        $I->fillField('#partner .first-name input', 'Test');
        $I->fillField('#partner .last-name input', 'Partner');
        $I->fillField('#partner .email input', 'TestPartner@mailinator.com');
        $I->seeLink('Invite');
        $I->click('Invite');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryLesson7 (AcceptanceTester $I) {
        $I->wantTo('complete course 7');
        $I->seeAmOnUrl('/course/1/lesson/7/step');
        $I->click('#course1_upgrade .highlighted-link a');
        $I->wait(10);
        //$I->switchToIFrame($I->)
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_upgrade-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->click('#course1_upgrade-step1 .highlighted-link a');
        $I->wait(5);
        $I->checkOption('input[name="quiz-enjoyed"][value="1"]');
        $I->click('#course1_upgrade-step2 .highlighted-link a:first-child');
        $I->wait(15);
        $I->click('#course1_upgrade-step2 .highlighted-link a:last-child');
        $I->wait(5);
        $I->click('#course1_upgrade-step3 .highlighted-link a');
        $I->wait(5);
        $I->checkOption('input[name="investment-net-promoter"][value="5"]');
        $I->seeLink('Upgrade to premium');
        $I->click('Upgrade to premium');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryBillMyParents(AcceptanceTester $I)
    {
        $I->wantTo('bill my parents');
        $I->seeAmOnUrl('/premium');
        $I->wait(5);
        $I->seeLink('Bill my parents');
        $I->click('Bill my parents');
        $I->fillField('#bill-parents .first-name input', 'Test');
        $I->fillField('#bill-parents .last-name input', 'Parent');
        $I->fillField('#bill-parents .email input', 'TestParent@mailinator.com');
        $I->click('#bill-parents a[href="#submit-contact"]');
        $I->wait(10);
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'TestParent');
        $I->click('.input-append btn');
        $I->seeLink('test has asked for your help with school.');
        $I->click('.message a');
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
            });
        $I->seeLink('Go to Study Sauce');
        $I->click('Go to Study Sauce');
        $I->wait(5);
        $I->executeInSelenium(function (WebDriver $webdriver) {
                $handles=$webdriver->getWindowHandles();
                $last_window = end($handles);
                $webdriver->switchTo()->window($last_window);
            });
        $I->seeInCurrentUrl('/parents');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryPrepayParent(AcceptanceTester $I)
    {
        $I->wantTo('prepay for my student as a parent');
        $I->seeAmOnUrl('/parents');
    }
}



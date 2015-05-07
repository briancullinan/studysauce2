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
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[src*="youtube.com"]')));
        });
        $I->click('[role="button"]');
        $I->wait(2);
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->window($driver->getWindowHandle());
        });
        $I->click('a[href="#yt-pause"]');
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
    public function tryTorchAndLaurel(AcceptanceTester $I)
    {
        $I->wantTo('checkout as a student');
        $I->amOnPage('/torchandlaurel');
        $I->seeLink('Get the Deal');
        $I->click('Get the Deal');
        $I->see('75% off');
        $I->test('tryGuestCheckout');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryBillMyParents(AcceptanceTester $I)
    {
        $I->wantTo('bill my parents');
        $I->seeAmOnUrl('/torchandlaurel');
        $I->wait(5);
        $I->seeLink('Ask your parents');
        $I->click('Ask your parents');
        $I->fillField('#bill-parents .first-name input', 'Test');
        $I->fillField('#bill-parents .last-name input', 'Parent');
        $I->fillField('#bill-parents .email input', 'TestParent@mailinator.com');
        $I->fillField('#bill-parents .your-first input', 'Test');
        $I->fillField('#bill-parents .your-last input', 'Student');
        $I->fillField('#bill-parents .your-email input', 'TestStudent@mailinator.com');
        $I->click('#bill-parents [value="#submit-contact"]');
        $I->wait(10);

        $I->wantTo('check mailinator for bill my parents email');
        $I->amOnPage('/cron');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Test has asked for your help with school');
        $I->click('//a[contains(.,"Test has asked for your help with school")]');
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
        $I->seeInCurrentUrl('/torchandlaurelparents');
        $I->test('tryPrepayParent');
        $I->seeInCurrentUrl('/torchandlaurelregister');
        $I->fillField('.password input', 'password');
        $I->click('Register');
        $I->wait(10);

        $I->seeInCurrentUrl('/profile/funnel');
        $I->test('tryStudyFunnel');
        $I->seeInCurrentUrl('/plan');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryPrepayParent(AcceptanceTester $I)
    {
        $I->wantTo('prepay for my student as a parent');
        $I->seeAmOnUrl('/parents');
        $I->wait(5);
        $I->seeLink('Tell your student');
        $I->click('Tell your student');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('#student-invite .first-name input', $last);
        $I->fillField('#student-invite .last-name input', 'test');
        $I->fillField('#student-invite .email input', $last . 'test' . '@mailinator.com');
        $I->fillField('#student-invite .your-first input', 'test');
        $I->fillField('#student-invite .your-last input', 'parent');
        $I->fillField('#student-invite .your-email input', 'testparent@mailinator.com');
        $I->click('#student-invite [value="#submit-contact"]');
        $I->wait(10);
        // previous invite will autofill checkout page otherwise it will fail
        $I->test('tryGuestCheckout');
        $I->seeInCurrentUrl('/thanks');
        $I->amOnPage('/cron');

        // check mailinator for emails
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('test has prepaid for your study plan');
        $I->click('//a[contains(.,"test has prepaid for your study plan")]');
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
            });

        $I->seeLink('Go to Study Sauce');
        $I->click('//a[contains(.,"Go to Study Sauce")]');
        $I->wait(5);
        $I->executeInSelenium(function (WebDriver $webdriver) {
                $handles=$webdriver->getWindowHandles();
                $last_window = end($handles);
                $webdriver->switchTo()->window($last_window);
            });
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryGuestCheckout(AcceptanceTester $I)
    {
        $I->wantTo('complete the checkout');
        $I->seeAmOnUrl('/checkout');
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
     * @param AcceptanceTester $I
     */
    public function tryStudyFunnel(AcceptanceTester $I)
    {
        $I->wantTo('finish the study assessment');
        $I->seeInCurrentUrl('/profile/funnel');
        $I->test('tryFunnelProfile');
        $I->seeInCurrentUrl('/schedule/funnel');
        $I->test('tryFunnelSchedule');
        $I->seeInCurrentUrl('/customization/funnel');
        $I->test('tryFunnelCustomization');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryFunnelProfile(AcceptanceTester $I)
    {
        $I->wantTo('fill out the study profile');
        $I->seeAmOnUrl('/profile/funnel');
        $I->checkOption('Nothing but As');
        $I->checkOption('Hit hard, keep weeks open');
        $I->checkOption('input[name="profile-11am"][value="2"]');
        $I->checkOption('input[name="profile-4pm"][value="3"]');
        $I->checkOption('input[name="profile-9pm"][value="4"]');
        $I->checkOption('input[name="profile-2am"][value="5"]');
        $I->click('#profile .highlighted-link button');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryFunnelSchedule(AcceptanceTester $I)
    {
        $I->wantTo('fill out my class schedule');
        $I->seeAmOnUrl('/schedule/funnel');
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('//span[contains(.,"Arizona State University")]');
        $I->fillField('.class-row:nth-child(1) .class-name input', 'PHIL 101');
        $I->click('.class-row:nth-child(1) input[value="M"] + i');
        $I->click('.class-row:nth-child(1) input[value="W"] + i');
        $I->click('.class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(1) .start-time input', '11');
        $I->fillField('.class-row:nth-child(1) .end-time input', '12');
        $I->fillField('.class-row:nth-child(1) .start-date input', (new \DateTime())->format('m/d/y'));
        $I->click('.class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryFunnelCustomization(AcceptanceTester $I)
    {
        $I->wantTo('customize my courses');
        $I->seeAmOnUrl('/customization/funnel');
        $I->checkOption('#customization input[value="memorization"]');
        $I->checkOption('#customization input[value="tough"]');
        $I->click('#customization .highlighted-link [value="#save-profile"]');
        $I->wait(10);
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
        $I->click('[value="#save-goal"]');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewSchedule(AcceptanceTester $I)
    {
        $I->wantTo('fill out my class schedule');
        $I->seeAmOnUrl('/schedule');
        $I->click('#schedule .selectize-control');
        $I->pressKey('#schedule .selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('#schedule .selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('//span[contains(.,"Arizona State University")]');
        $I->fillField('#schedule .class-row:nth-child(1) .class-name input', 'PHIL 101');
        $I->click('#schedule .class-row:nth-child(1) input[value="M"] + i');
        $I->click('#schedule .class-row:nth-child(1) input[value="W"] + i');
        $I->click('#schedule .class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('#schedule .class-row:nth-child(1) .start-time input', '11');
        $I->fillField('#schedule .class-row:nth-child(1) .end-time input', '12');
        $I->fillField('#schedule .class-row:nth-child(1) .start-date input', (new \DateTime())->format('m/d/y'));
        $I->click('#schedule .class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);
    }

    /**
     * @depends tryNewSchedule
     * @param AcceptanceTester $I
     */
    public function tryNewDeadlines(AcceptanceTester $I)
    {
        $I->wantTo('set up a deadline');
        $I->seeAmOnUrl('/deadlines');
        $I->selectOption('.deadline-row.edit .class-name select', 'PHIL 101');
        $I->fillField('.deadline-row.edit .assignment input', 'Exam 1');
        $I->checkOption('.deadline-row.edit input[value="86400"] + i');
        $I->checkOption('.deadline-row.edit input[value="172800"] + i');
        $I->checkOption('.deadline-row.edit input[value="345600"] + i');
        $I->checkOption('.deadline-row.edit input[value="604800"] + i');
        $I->click('.deadline-row.edit .due-date input');
        $d = date_add(new \DateTime(), new \DateInterval('P8D'))->format('j');
        if($d < 8) {
            $I->click('.ui-datepicker-next');
        }
        $I->click('//*[@id="ui-datepicker-div"]//td[not(@class="ui-datepicker-unselectable")]/a[contains(.,"' . $d . '")]');
        $I->fillField('.deadline-row.edit .percent input', '10');
        $I->click('#deadlines .highlighted-link [value="#save-deadline"]');
        $I->wait(10);
    }

    /**
     * @depends tryNewSchedule
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
     * @depends tryNewSchedule
     * @depends tryNewCheckin
     * @param AcceptanceTester $I
     */
    public function tryNewMetrics(AcceptanceTester $I) {
        $I->wantTo('enter a manually study session');
        $I->seeAmOnUrl('/metrics');
        $I->click('#metrics a[href="#add-study-hours"]');
        $I->selectOption('#add-study-hours .class-name select', 'PHIL 101');
        $I->click('#add-study-hours .date input');
        $I->click('.ui-datepicker-calendar td:not(.ui-datepicker-unselectable) a');
        $I->selectOption('#add-study-hours .time select', '45');
        $I->click('#add-study-hours [value="#submit-checkin"]');
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
        $I->click('[value="#partner-save"]');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     * @depends tryGuestCheckout
     */
    public function tryDetailedSchedule(AcceptanceTester $I)
    {
        $I->wantTo('invite a new accountability partner');
        $I->seeAmOnUrl('/schedule');
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('//span[contains(.,"Arizona State University")]');

        // add one class
        $I->fillField('.class-row:nth-child(1) .class-name input', 'PHIL 101');
        $I->click('.class-row:nth-child(1) input[value="M"] + i');
        $I->click('.class-row:nth-child(1) input[value="W"] + i');
        $I->click('.class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(1) .start-time input', '11');
        $I->fillField('.class-row:nth-child(1) .end-time input', '12');
        $I->fillField('.class-row:nth-child(1) .start-date input', (new \DateTime())->format('m/d/y'));
        $I->click('.class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');

        // add a second class
        $I->fillField('.class-row:nth-child(2) .class-name input', 'CALC 102');
        $I->click('.class-row:nth-child(2) input[value="M"] + i');
        $I->click('.class-row:nth-child(2) input[value="W"] + i');
        $I->click('.class-row:nth-child(2) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(2) .start-time input', '11');
        $I->fillField('.class-row:nth-child(2) .end-time input', '12');

        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->see('Cannot overlap'); // should fail if hidden
        // fix time
        $I->fillField('.class-row:nth-child(2) .start-time input', '9');
        $I->fillField('.class-row:nth-child(2) .end-time input', '10');
        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);

        // add a new term
        $I->click('#schedule a[href="#manage-terms"]');
        $I->selectOption('#manage-terms select', '1/2014');
        $I->click('#manage-terms a[href="#create-schedule"]');

        // enter new schedule
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('//span[contains(.,"Arizona State University")]');

        $I->fillField('.class-row:nth-child(1) .class-name input', 'MAT 202');
        $I->click('.class-row:nth-child(1) input[value="M"] + i');
        $I->click('.class-row:nth-child(1) input[value="W"] + i');
        $I->click('.class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(1) .start-time input', '11');
        $I->fillField('.class-row:nth-child(1) .end-time input', '12');
        $I->fillField('.class-row:nth-child(1) .start-date input', (new \DateTime())->format('m/d/y'));
        $I->click('.class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');

        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function trySignup(AcceptanceTester $I)
    {
        $I->wantTo('sign up for study sauce');
        $I->seeAmOnUrl('/signup');
        $I->fillField('input[name="organization"]', 'Study Sauce');
        $I->fillField('input[name="first-name"]', 'test');
        $I->fillField('input[name="title"]', 'Mr');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('input[name="email"]', 'test' . $last . '@mailinator.com');
        $I->fillField('input[name="phone"]', '4804660856');
        $I->fillField('input[name="street1"]', '6934 E sandra ter');
        $I->fillField('input[name="city"]', 'scottsdale');
        $I->fillField('input[name="zip"]', '85254');
        $I->selectOption('select[name="state"]', 'Arizona');
        $I->selectOption('select[name="country"]', 'United States');
        $I->fillField('input[name="students"]', '10');
        $I->selectOption('.payment select', 'Credit card');
        $I->seeLink('Save');
        $I->click('Save');
        $I->wait(10);

        $I->wantTo('visit mailinator and check for organization email');
        $I->amOnPage('/cron');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Contact Us');
        $I->click('//a[contains(.,"Contact Us")]');
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
        });
        $I->see('Organization:');
    }

    /**
     * @depends tryNewPartner
     * @param AcceptanceTester $I
     */
    public function tryPartnerEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/cron');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('needs your help with school');
        $I->click('//a[contains(.,"needs your help with school")]');
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
        });

    }

    /**
     * @depends tryNewDeadlines
     * @param AcceptanceTester $I
     */
    public function tryDeadlineEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/cron');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('notification');
        $I->click('//a[contains(.,"notification")]');
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
        });
        $I->see('Exam 1');
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function trySocialLogin(AcceptanceTester $I)
    {
        $I->amOnPage('/course/1/lesson/1/step');
        $I->click('a[href*="/google"]');
        $I->fillField('input[name="Email"]', 'brian@studysauce.com');
        $I->fillField('input[name="Passwd"]', 'Da1ddy23');
        $I->click('[type="submit"]');
        $I->seeAmOnUrl('/course/1/lesson/1/step');

        // log out and log back in using social login
        $I->click('a[href*="/logout"]');
        $I->click('a[href*="/login"]');
        $I->click('a[href*="/google"]');
        $I->seeInCurrentUrl('/profile/funnel');
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function tryStudyNotes(AcceptanceTester $I)
    {
        $I->amOnPage('/notes');
        $I->click('a[href*="/evernote"]');
        $I->fillField('input[name="username"]', 'brian@studysauce.com');
        $I->fillField('input[name="password"]', 'Da1ddy23');
        $I->click('[type="submit"]');
        $I->click('authorize');
        $I->seeInCurrentUrl('/notes');
        $I->click('#right-panel a[href="#expand"] span');
        $I->click('a[href="/schedule"]');
        $I->wait(5);
        $I->test('tryNewSchedule');
        $I->seeAmOnUrl('/notes');
        $I->click('study note');
        $I->selectOption('#notes select[name="notebook"]', 'PHIL 101');
        $I->fillField('#notes .input.title input', 'This is a new note ' . date('Y-m-d'));
        for($i = 0; $i < strlen('' . time()); $i++)
        {
            $key = constant('WebDriverKeys::NUMPAD' . substr('' . time(), $i, 1));
            $I->pressKey('#editor1', $key);
        }
        $I->click('#notes a[href="#save-note"]');
        $I->wait(15);
        $I->fillField('#notes [name="search"]', date('Y-m-d'));
        $I->wait(5);
        $I->see('This is a new note ' . date('Y-m-d'));
        $I->fillField('#notes [name="search"]', '');
        $I->click('This is a new note ' . date('Y-m-d'));
        $I->click('#notes a[href="#delete-note"]');
        $I->wait(5);
    }

}



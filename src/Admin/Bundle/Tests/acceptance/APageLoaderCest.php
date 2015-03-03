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
class APageLoaderCest
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
        $I->seeAmOnUrl('/premium');
        $I->wait(5);
        $I->seeLink('Bill my parents');
        $I->click('Bill my parents');
        $I->fillField('#bill-parents .first-name input', 'Test');
        $I->fillField('#bill-parents .last-name input', 'Parent');
        $I->fillField('#bill-parents .email input', 'TestParent@mailinator.com');
        $I->fillField('#bill-parents .your-first input', 'Test');
        $I->fillField('#bill-parents .your-last input', 'Student');
        $I->fillField('#bill-parents .your-email input', 'TestStudent@mailinator.com');
        $I->click('#bill-parents a[href="#submit-contact"]');
        $I->wait(10);
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'TestParent');
        $I->click('.input-append btn');
        $I->waitForText('about a minute ago', 60*5);
        $I->seeLink('test has asked for your help with school');
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
        $I->test('tryPrepayParent');
        $I->seeInCurrentUrl('/students');
        $I->test('tryStudentRegister');
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
        $I->click('#student-invite a[href="#submit-contact"]');
        $I->wait(10);
        // previous invite will autofill checkout page otherwise it will fail
        $I->test('tryGuestCheckout');
        $I->seeInCurrentUrl('/thanks');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', $last . 'test');
        $I->click('.input-append btn');
        $I->waitForText('about a minute ago', 60*5);
        $I->seeLink('test has prepaid for your study plan');
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
        $I->seeInCurrentUrl('/students');
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
    public function tryStudentRegister(AcceptanceTester $I)
    {
        $I->wantTo('register as a new student');
        $I->seeAmOnUrl('/students');
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
        $I->click('Register');
        $I->wait(10);
        $I->seeInCurrentUrl('/course/1/lesson/1/step');
    }

    /**
     * @depends tryGuestCheckout
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
     * @depends tryGuestCheckout
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
     * @depends tryGuestCheckout
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
        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);
    }

    /**
     * @depends tryGuestCheckout
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
        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewDeadlines(AcceptanceTester $I)
    {
        $I->wantTo('set up a deadline');
        $I->seeAmOnUrl('/deadlines');
        $I->selectOption('.deadline-row .class-name select', 'PHIL 101');
        $I->fillField('.deadline-row .assignment input', 'Exam 1');
        $I->checkOption('.deadline-row input[value="86400"]');
        $I->checkOption('.deadline-row input[value="172800"]');
        $I->checkOption('.deadline-row input[value="345600"]');
        $I->click('.deadline-row .due-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->fillField('.deadline-row .percent input', '10');
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
     * @param AcceptanceTester $I
     */
    public function tryNewPartner(AcceptanceTester $I)
    {
        $I->wantTo('invite a new accountability partner');
        $I->seeAmOnUrl('/partner');
        $I->fillField('#partner .first-name input', 'Test');
        $I->fillField('#partner .last-name input', 'Partner');
        $I->fillField('#partner .email input', 'TestPartner@mailinator.com');
        $I->click('[value="save-partner"]');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     * @depends tryStudentRegister
     */
    public function tryDetailedSchedule(AcceptanceTester $I)
    {
        $I->wantTo('invite a new accountability partner');
        $I->seeAmOnUrl('/schedule');
    }
}



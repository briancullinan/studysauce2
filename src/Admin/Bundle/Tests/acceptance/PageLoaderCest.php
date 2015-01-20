<?php
namespace StudySauce\Bundle\Tests;

use WebDriver;
use StudySauce\Bundle\Tests\AcceptanceTester;
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
        $I->wantTo('send a contact message');
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
     * @param AcceptanceTester $I
     */
    public function tryStudyFunnel(AcceptanceTester $I)
    {
        $this->tryGuestCheckout($I);
        $I->wantTo('finish the study assessment');
        $I->seeInCurrentUrl('/profile/funnel');
        $I->see('What kind of grades do you want?');
        $I->checkOption('Nothing but As');
        $I->checkOption('Hit hard, keep weeks open');
        $I->checkOption('input[name="profile-11am"][value="2"]');
        $I->checkOption('input[name="profile-4pm"][value="3"]');
        $I->checkOption('input[name="profile-9pm"][value="4"]');
        $I->checkOption('input[name="profile-2am"][value="5"]');
        $I->seeLink('Next');
        $I->click('Next');
        $I->wait(10);
        $I->wantTo('fill out my class schedule');
        $I->seeInCurrentUrl('/schedule/funnel');
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('div[data-value="Arizona State University"]');
        $I->fillField('.class-row:nth-child(1) .class-name input', 'PHIL 101');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryFreeCourse(AcceptanceTester $I)
    {
        $this->tryStudentRegister($I);
        $I->wantTo('complete all the free courses');
        $I->seeInCurrentUrl('/course/1/lesson/1/step');
        $I->seeLink('Launch');
        $I->wantTo('complete course 1');
        $I->click('Launch');
        $I->wait(10);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_introduction-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_introduction-step1 .highlighted-link a'))->click();
            });
        $I->wait(5);
        $I->checkOption('input[value="college-senior"]');
        $I->checkOption('input[value="born"]');
        $I->checkOption('input[value="cram"]');
        $I->checkOption('input[value="on"]');
        $I->checkOption('input[value="two"]');
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_introduction-step2 .highlighted-link a:first-child'))->click();
            });
        $I->wait(15);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_introduction-step2 .highlighted-link a:last-child'))->click();
            });
        $I->wait(5);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_introduction-step3 .highlighted-link a'))->click();
            });
        $I->wait(5);
        $I->fillField('#course1_introduction-step4 textarea', 'To get better grades!');
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_introduction-step4 .highlighted-link a'))->click();
            });
        $I->wait(15);



        $I->wantTo('complete course 2');
        $I->seeInCurrentUrl('/course/1/lesson/2/step');
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_setting_goals .highlighted-link a'))->click();
            });
        $I->wait(10);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('#course1_setting_goals-step1 iframe')));
            });
        $I->click('.ytp-thumbnail');
        $I->switchToIFrame();
        $I->wait(15);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_setting_goals-step1 .highlighted-link a'))->click();
            });
        $I->wait(5);
        $I->checkOption('input[value="60"]');
        $I->fillField('input[name="quiz-smart-acronym-S"]', 'specific');
        $I->fillField('input[name="quiz-smart-acronym-M"]', 'measurable');
        $I->fillField('input[name="quiz-smart-acronym-A"]', 'achievable');
        $I->fillField('input[name="quiz-smart-acronym-R"]', 'relevant');
        $I->fillField('input[name="quiz-smart-acronym-T"]', 'time-bound');
        $I->fillField('input[name="quiz-motivation-I"]', 'intrinsic');
        $I->fillField('input[name="quiz-motivation-E"]', 'extrinsic');
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_setting_goals-step2 .highlighted-link a:first-child'))->click();
            });
        $I->wait(15);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_setting_goals-step2 .highlighted-link a:last-child'))->click();
            });
        $I->wait(5);
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->findElement(WebDriverBy::cssSelector('#course1_setting_goals-step3 .highlighted-link a'))->click();
            });
        $I->wait(5);
        $I->seeLink('Set up my goals');
        $I->click('Set up my goals');
        $I->wait(15);



        $I->wantTo('complete goals');
        $I->seeInCurrentUrl('/goals');
        $I->fillField('input[name="quiz-smart-acronym-T"]', 'time-bound');
        $I->fillField('input[name="quiz-motivation-I"]', 'intrinsic');
        $I->fillField('input[name="quiz-motivation-E"]', 'extrinsic');
        $I->seeLink('Set up my goals');
        $I->click('Set up my goals');
        $I->wait(15);

    }
}



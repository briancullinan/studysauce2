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
class EmailCest
{
    /**
     * @param FunctionalTester $I
     */
    public function _before(FunctionalTester $I)
    {
    }

    /**
     * @param FunctionalTester $I
     */
    public function _after(FunctionalTester $I)
    {
    }

    // tests

    /**
     * @param FunctionalTester $I
     */
    public function tryAllEmailTemplates(FunctionalTester $I)
    {
        $I->test('tryWelcomeStudent');
        $I->test('tryContactMessage');
        $I->test('tryInvoice');
        $I->test('tryAdviserInvite');
        $I->test('tryWelcomeReminder');
        $I->test('tryResetPassword');
        $I->test('tryPrepay');
        $I->test('tryDeadlineReminder');
        $I->test('tryWelcomePartner');
        $I->test('tryAchievement');
        $I->test('tryPartnerReminder');
        $I->test('tryAdministrator');
        $I->test('tryGroupInvite');
        $I->test('tryStudentInvite');
        $I->test('tryPartnerInvite');
        $I->test('tryBlank');
        $I->test('tryCSAMyrna');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryWelcomeStudent(FunctionalTester $I)
    {
        $I->testEmail('welcome-student');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryContactMessage(FunctionalTester $I)
    {
        $I->testEmail('contact-message');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryInvoice(FunctionalTester $I)
    {
        $I->testEmail('invoice');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryAdviserInvite(FunctionalTester $I)
    {
        $I->testEmail('adviser-invite');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryWelcomeReminder(FunctionalTester $I)
    {
        $I->testEmail('welcome-reminder');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryResetPassword(FunctionalTester $I)
    {
        $I->testEmail('reset-password');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryPrepay(FunctionalTester $I)
    {
        $I->testEmail('prepay');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryDeadlineReminder(FunctionalTester $I)
    {
        $I->testEmail('deadline-reminder');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryWelcomePartner(FunctionalTester $I)
    {
        $I->testEmail('welcome-partner');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryAchievement(FunctionalTester $I)
    {
        $I->testEmail('achievement');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryPartnerReminder(FunctionalTester $I)
    {
        $I->testEmail('partner-reminder');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryAdministrator(FunctionalTester $I)
    {
        $I->testEmail('administrator');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryGroupInvite(FunctionalTester $I)
    {
        $I->testEmail('group-invite');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryStudentInvite(FunctionalTester $I)
    {
        $I->testEmail('student-invite');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryPartnerInvite(FunctionalTester $I)
    {
        $I->testEmail('partner-invite');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryBlank(FunctionalTester $I)
    {
        $I->testEmail('blank');
    }
    /**
     * @param FunctionalTester $I
     */
    public function tryCSAMyrna(FunctionalTester $I)
    {
        $I->testEmail('csa-myrna');
    }
}
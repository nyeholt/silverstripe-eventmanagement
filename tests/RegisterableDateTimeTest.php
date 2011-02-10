<?php
/**
 * Tests for the {@link RegisterableDateTime} class.
 *
 * @package    silverstripe-eventmanagement
 * @subpackage tests
 */
class RegisterableDateTimeTest extends SapphireTest {

	public static $fixture_file = 'eventmanagement/tests/RegisterableDateTimeTest.yml';

	/**
	 * @covers RegisterableDateTime::onBeforeWrite();
	 */
	public function testEventDetailsChangedNotificationEmail() {
		$event    = $this->objFromFixture('RegisterableEvent', 'event');
		$datetime = $this->objFromFixture('RegisterableDateTime', 'datetime');

		// First test that no emails are sent out for trivial changes.
		$datetime->StartTime = 0;
		$datetime->write();
		$this->assertNull($this->findEmail('test@example.com'));
		$this->assertNull($this->findEmail('canceled@example.com'));

		// Now do a non-emailed change and check they're still not send.
		$datetime->EndTime = '12:00:00';
		$datetime->write();
		$this->assertNull($this->findEmail('test@example.com'));
		$this->assertNull($this->findEmail('canceled@example.com'));

		// Now change a property that users are notified of a change in and
		// check an email is sent.
		$datetime->StartDate = '2011-01-02';
		$datetime->write();
		$this->assertEmailSent('test@example.com');
		$this->assertNull($this->findEmail('canceled@example.com'));
		$this->clearEmails();

		// Now disable notification and do the same and check no emails are
		// sent.
		$event->EmailNotifyChanges = false;
		$event->write();

		$datetime->StartDate = '2011-01-03';
		$datetime->flushCache();

		$datetime = DataObject::get_by_id('RegisterableDateTime', $datetime->ID);
		$datetime->write();

		$this->assertNull($this->findEmail('test@example.com'));
		$this->assertNull($this->findEmail('canceled@example.com'));
	}

}
<?php

/**
 * @file
 * Test case for testing the Page Load Progress module.
 */

namespace Drupal\page_load_progress\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests for the page_load_progress module.
 *
 * @group page_load_progress
 */
class PageLoadProgressAdminSettingsFormTest extends WebTestBase {
  /**
   * User account with page_load_progress permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $privilegedUser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('page_load_progress');

  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Page Load Progress settings form',
      'description' => 'Tests the Page Load Progress admin settings form.',
      'group' => 'Page Load Progress',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    // Privileged user should only have the page_load_progress permissions
    $this->privilegedUser = $this->drupalCreateUser(array('administer Page Load Progress settings'));
    $this->drupalLogin($this->privilegedUser);
  }

  /**
   * Test the page_load_progress settings form.
   */
  public function testPageLoadProgressSettings() {
    // Verify if we can successfully access the page_load_progress form.
    $this->drupalGet('admin/config/user-interface/page-load-progress');
    $this->assertResponse(200, 'The Page Load Progress settings page is available.');
    $this->assertTitle(t('Page Load Progress | Drupal'), 'The title on the page is "Page Load Progress".');

    // Verify every field exists.
    $this->assertField('edit-page-load-progress-time', 'edit-page-load-progress-time field exists');
    $this->assertField('edit-page-load-progress-elements', 'edit-page-load-progress-elements field exists');

    // Validate default form values.
    $this->assertFieldById('edit-page-load-progress-time', 10, 'The edit-page-load-progress-time field has the value "Show immediately".');
    $this->assertFieldById('edit-page-load-progress-elements', '.form-submit', 'The edit-page-load-progress-elements field has the value ".form-submit".');

    // Verify that there's no access bypass.
    $this->drupalLogout();
    $this->drupalGet('admin/config/user-interface/page-load-progress');
    $this->assertResponse(403, 'Access denied for anonymous user.');
  }

  /**
   * Test posting data to the page_load_progress settings form.
   */
  public function testPageLoadProgressSettingsPost() {
    // Post form with new values.
    $edit = array(
      'page_load_progress_time' => 5000,
      'page_load_progress_elements' => '.sample_submit',
    );
    $this->drupalPostForm('admin/config/user-interface/page-load-progress', $edit, t('Save configuration'));

    // Load settings form page and test for new values.
    $this->drupalGet('admin/config/user-interface/page-load-progress');
    $this->assertFieldById('edit-page-load-progress-time', $edit['page_load_progress_time'],
      format_string('The edit-page-load-progress-time field has the value %val.', array('%val' => $edit['page_load_progress_time'])));
    $this->assertFieldById('edit-page-load-progress-elements', $edit['page_load_progress_elements'],
      format_string('The edit-page-load-progress-elements field has the value %val.', array('%val' => $edit['page_load_progress_elements'])));
  }
}

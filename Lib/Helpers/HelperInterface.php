<?php
/**
 * Freyja CLI Helper Interface.
 *
 * @package Freyja\CLI\Helpers
 * @copyright 2016 SqueezyWeb
 * @since 0.1.0
 */

namespace Freyja\CLI\Helpers;

/**
 * Interface all helpers must implement.
 *
 * @package Freyja\CLI\Helpers
 * @author Mattia Migliorini <mattia@squeezyweb.com>
 * @since 0.1.0
 * @version 1.0.0
 */
interface HelperInterface {
  /**
   * Set helper set associated with this helper.
   *
   * @since 1.0.0
   * @access public
   *
   * @param HelperSet $helper_set Optional. HelperSet instance. Default null.
   */
  public function setHelperSet(HelperSet $helper_set = null);

  /**
   * Retrieve helper set associated with this helper.
   *
   * @since 1.0.0
   * @access public
   *
   * @return HelperSet HelperSet instance.
   */
  public function getHelperSet();

  /**
   * Retrieve canonical name of this helper.
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Canonical name.
   */
  public function getName();
}

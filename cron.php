<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * cron.php
 *
 * PHP version 5..5.
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unableå to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  MailHandler
 * @package   
 * @author    Josh Houghtelin <Josh@FindSomeHelp.com>
 * @copyright 2014 IMPACT INTERNATIONAL MARKETING Inc.
 * @license   (C)2014 IMPACT INTERNATIONAL MARKETING Inc.
 * @since     2014/02/26
 * @link      http://www.iimgroup.com
 */

/**
 * MAILHANDLER Config
 */
require_once 'config.php';

/**
 * MailHandler Class
 */
require_once 'MailHandler.php';

/**
 * Start the Party
 */
$MailHandler = new MailHandler($config['incommingMailFolder'],$config['processedMailFolder'],$config['logFile']);
$MailHandler->setLogLevel(3);
$MailHandler->checkNewMail();
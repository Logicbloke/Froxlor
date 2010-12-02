<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    Install
 * @version    $Id$
 */

if(isFroxlorVersion('0.9-r0'))
{
	showUpdateStep("Updating from 0.9-r0 to 0.9-r1", false);
	showUpdateStep("Performing database updates");
	/*
	 * add missing database-updates if necessary (old: update/update_database.php)
	 */
	if(isset($settings['system']['dbversion']) && (int)$settings['system']['dbversion'] < 1)
	{
		$db->query("ALTER TABLE `panel_databases` ADD `dbserver` INT( 11 ) UNSIGNED NOT NULL default '0';");
	}
	if(isset($settings['system']['dbversion']) && (int)$settings['system']['dbversion'] < 2)
	{
		$db->query("ALTER TABLE `panel_ipsandports` CHANGE `ssl_cert` `ssl_cert_file` VARCHAR( 255 ) NOT NULL,
						ADD `ssl_key_file` VARCHAR( 255 ) NOT NULL,
						ADD `ssl_ca_file` VARCHAR( 255 ) NOT NULL,
						ADD `default_vhostconf_domain` TEXT NOT NULL;");

		$db->query("INSERT INTO `panel_settings` SET `settinggroup` = 'system', `varname` = 'ssl_key_file', `value` = '';");
		$db->query("INSERT INTO `panel_settings` SET `settinggroup` = 'system', `varname` = 'ssl_ca_file', `value` = '';");
	}
	// eof(lostuff)

	/*
	 * remove billing tables in database
	 */
	define('TABLE_BILLING_INVOICES', 'billing_invoices');
	define('TABLE_BILLING_INVOICES_ADMINS', 'billing_invoices_admins');
	define('TABLE_BILLING_INVOICE_CHANGES', 'billing_invoice_changes');
	define('TABLE_BILLING_INVOICE_CHANGES_ADMINS', 'billing_invoice_changes_admins');
	define('TABLE_BILLING_SERVICE_CATEGORIES', 'billing_service_categories');
	define('TABLE_BILLING_SERVICE_CATEGORIES_ADMINS', 'billing_service_categories_admins');
	define('TABLE_BILLING_SERVICE_DOMAINS_TEMPLATES', 'billing_service_domains_templates');
	define('TABLE_BILLING_SERVICE_OTHER', 'billing_service_other');
	define('TABLE_BILLING_SERVICE_OTHER_TEMPLATES', 'billing_service_other_templates');
	define('TABLE_BILLING_TAXCLASSES', 'billing_taxclasses');
	define('TABLE_BILLING_TAXRATES', 'billing_taxrates');

	$db->query("DROP TABLE  `" . TABLE_BILLING_SERVICE_CATEGORIES . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_SERVICE_CATEGORIES_ADMINS . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_SERVICE_DOMAINS_TEMPLATES . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_SERVICE_OTHER . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_SERVICE_OTHER_TEMPLATES . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_TAXCLASSES . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_TAXRATES . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_INVOICES . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_INVOICES_ADMINS . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_INVOICE_CHANGES . "`;");
	$db->query("DROP TABLE  `" . TABLE_BILLING_INVOICE_CHANGES_ADMINS . "`;");

	/*
	 * update panel_domains, panel_customers, panel_admins
	 */
	$db->query("ALTER TABLE `" . TABLE_PANEL_ADMINS . "`
		  DROP `firstname`,
		  DROP `title`,
		  DROP `company`,
		  DROP `street`,
		  DROP `zipcode`,
		  DROP `city`,
		  DROP `country`,
		  DROP `phone`,
		  DROP `fax`,
		  DROP `taxid`,
		  DROP `contract_date`,
		  DROP `contract_number`,
		  DROP `contract_details`,
		  DROP `included_domains_qty`,
		  DROP `included_domains_tld`,
		  DROP `additional_traffic_fee`,
		  DROP `additional_traffic_unit`,
		  DROP `additional_diskspace_fee`,
		  DROP `additional_diskspace_unit`,
		  DROP `taxclass`,
		  DROP `setup_fee`,
		  DROP `interval_fee`,
		  DROP `interval_length`,
		  DROP `interval_type`,
		  DROP `interval_payment`,
		  DROP `calc_tax`,
		  DROP `term_of_payment`,
		  DROP `payment_every`,
		  DROP `payment_method`,
		  DROP `bankaccount_holder`,
		  DROP `bankaccount_number`,
		  DROP `bankaccount_blz`,
		  DROP `bankaccount_bank`,
		  DROP `service_active`,
		  DROP `servicestart_date`,
		  DROP `serviceend_date`,
		  DROP `lastinvoiced_date`,
		  DROP `lastinvoiced_date_traffic`,
		  DROP `lastinvoiced_date_diskspace`,
		  DROP `customer_categories_once`,
		  DROP `customer_categories_period`,
		  DROP `invoice_fee`,
		  DROP `invoice_fee_hosting`,
		  DROP `invoice_fee_hosting_customers`,
		  DROP `invoice_fee_domains`,
		  DROP `invoice_fee_traffic`,
		  DROP `invoice_fee_diskspace`,
		  DROP `invoice_fee_other`,
		  DROP `edit_billingdata`;");

	$db->query("ALTER TABLE `" . TABLE_PANEL_CUSTOMERS . "`
		  DROP `taxid`,
		  DROP `title`,
		  DROP `country`,
		  DROP `additional_service_description`,
		  DROP `contract_date`,
		  DROP `contract_number`,
		  DROP `contract_details`,
		  DROP `included_domains_qty`,
		  DROP `included_domains_tld`,
		  DROP `additional_traffic_fee`,
		  DROP `additional_traffic_unit`,
		  DROP `additional_diskspace_fee`,
		  DROP `additional_diskspace_unit`,
		  DROP `taxclass`,
		  DROP `setup_fee`,
		  DROP `interval_fee`,
		  DROP `interval_length`,
		  DROP `interval_type`,
		  DROP `interval_payment`,
		  DROP `calc_tax`,
		  DROP `term_of_payment`,
		  DROP `payment_every`,
		  DROP `payment_method`,
		  DROP `bankaccount_holder`,
		  DROP `bankaccount_number`,
		  DROP `bankaccount_blz`,
		  DROP `bankaccount_bank`,
		  DROP `service_active`,
		  DROP `servicestart_date`,
		  DROP `serviceend_date`,
		  DROP `lastinvoiced_date`,
		  DROP `lastinvoiced_date_traffic`,
		  DROP `lastinvoiced_date_diskspace`,
		  DROP `invoice_fee`,
		  DROP `invoice_fee_hosting`,
		  DROP `invoice_fee_domains`,
		  DROP `invoice_fee_traffic`,
		  DROP `invoice_fee_diskspace`,
		  DROP `invoice_fee_other`;");
	$db->query("ALTER TABLE `panel_domains`
		  DROP `taxclass`,
		  DROP `setup_fee`,
		  DROP `interval_fee`,
		  DROP `interval_length`,
		  DROP `interval_type`,
		  DROP `interval_payment`,
		  DROP `service_active`,
		  DROP `servicestart_date`,
		  DROP `serviceend_date`,
		  DROP `lastinvoiced_date`;");

	$db->query("DELETE FROM `" . TABLE_PANEL_SETTINGS . "`
		WHERE `settinggroup` = 'billing';");

	$db->query("ALTER TABLE `" . TABLE_PANEL_ADMINS . "`
		  MODIFY `traffic` BIGINT(30),
		  MODIFY `traffic_used` BIGINT(30)");

	lastStepStatus(0);

	updateToVersion('0.9-r1');
}

if(isFroxlorVersion('0.9-r1'))
{
	showUpdateStep("Updating from 0.9-r1 to 0.9-r2", false);
	showUpdateStep("Updating settings table");

	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('spf', 'use_spf', '0');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('spf', 'spf_entry', '@	IN	TXT	\"v=spf1 a mx -all\"');");
	$db->query("UPDATE `" . TABLE_PANEL_SETTINGS . "` SET `varname` = 'froxlor_graphic' WHERE `varname` = 'syscp_graphic'");
	if(isset($settings['admin']['syscp_graphic'])
		&& $settings['admin']['syscp_graphic'] != ''
	){
		$settings['admin']['froxlor_graphic'] = $settings['admin']['syscp_graphic'];
	}
	else
	{
		$settings['admin']['froxlor_graphic'] = 'images/header.gif';
	}

	lastStepStatus(0);

	updateToVersion('0.9-r2');
}

if(isFroxlorVersion('0.9-r2'))
{
	showUpdateStep("Updating from 0.9-r2 to 0.9-r3", false);
	showUpdateStep("Updating tables");

	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'debug_cron', '0');");
	$db->query("ALTER TABLE `" . TABLE_MAIL_AUTORESPONDER . "` ADD `date_from` int(15) NOT NULL default '-1' AFTER `enabled`");
	$db->query("ALTER TABLE `" . TABLE_MAIL_AUTORESPONDER . "` ADD `date_until` int(15) NOT NULL default '-1' AFTER `date_from`");

	lastStepStatus(0);

	updateToVersion('0.9-r3');
}

if(isFroxlorVersion('0.9-r3'))
{
	showUpdateStep("Updating from 0.9-r3 to 0.9-r4", false);
	showUpdateStep("Creating new table 'cronjobs_run'");

	$db->query("CREATE TABLE IF NOT EXISTS `cronjobs_run` (
				`id` bigint(20) NOT NULL auto_increment,
				`module` varchar(250) NOT NULL,
				`cronfile` varchar(250) NOT NULL,
				`lastrun` int(15) NOT NULL DEFAULT '0',
				`interval` varchar(100) NOT NULL DEFAULT '5 MINUTE',
				`isactive` tinyint(1) DEFAULT '1',
				`desc_lng_key` varchar(100) NOT NULL DEFAULT 'cron_unknown_desc',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM;");

	lastStepStatus(0);
	showUpdateStep("Inserting new values into table");

	// checking for active ticket-module
	$ticket_active = 0;
	if((int)$settings['ticket']['enabled'] == 1)
	{
		$ticket_active = 1;
	}

	// checking for active aps-module
	$aps_active = 0;
	if((int)$settings['aps']['aps_active'] == 1)
	{
		$aps_active = 1;
	}

	// checking for active autoresponder-module
	$ar_active = 0;
	if((int)$settings['autoresponder']['autoresponder_active'] == 1)
	{
		$ar_active = 1;
	}

	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/core', 'cron_tasks.php', '5 MINUTE', '1', 'cron_tasks');");
	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/core', 'cron_legacy.php', '5 MINUTE', '1', 'cron_legacy');");
	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/aps', 'cron_apsinstaller.php', '5 MINUTE', ".$aps_active.", 'cron_apsinstaller');");
	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/autoresponder', 'cron_autoresponder.php', '5 MINUTE', ".$ar_active.", 'cron_autoresponder');");
	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/aps', 'cron_apsupdater.php', '1 HOUR', ".$aps_active.", 'cron_apsupdater');");
	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/core', 'cron_traffic.php', '1 DAY', '1', 'cron_traffic');");
	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/ticket', 'cron_used_tickets_reset.php', '1 MONTH', '".$ticket_active."', 'cron_ticketsreset');");
	$db->query("INSERT INTO `cronjobs_run` (`module`, `cronfile`, `interval`, `isactive`, `desc_lng_key`) VALUES ('froxlor/ticket', 'cron_ticketarchive.php', '1 MONTH', '".$ticket_active."', 'cron_ticketarchive');");

	lastStepStatus(0);
	showUpdateStep("Updating old settings values");

	$db->query("UPDATE `" . TABLE_PANEL_SETTINGS . "` SET `value` = 'Froxlor Support' WHERE `settinggroup`='ticket' AND `varname`='noreply_name' AND `value`='SysCP Support'");

	lastStepStatus(0);
	updateToVersion('0.9-r4');
}

if(isFroxlorVersion('0.9-r4'))
{
	showUpdateStep("Updating from 0.9-r4 to 0.9 final");
	lastStepStatus(0);
	updateToVersion('0.9');
}

if(isFroxlorVersion('0.9'))
{
	showUpdateStep("Updating from 0.9 to 0.9.1", false);

	showUpdateStep("Updating settings values");
	$db->query("UPDATE `" . TABLE_PANEL_SETTINGS . "` SET `value` = 'images/header.gif' WHERE `varname` = 'froxlor_graphic' AND `value` = 'images/header.png'");

	lastStepStatus(0);
	updateToVersion('0.9.1');
}

if(isFroxlorVersion('0.9.1'))
{
	showUpdateStep("Updating from 0.9.1 to 0.9.2", false);

	showUpdateStep("Checking whether last-system-guid is sane");

	$result = $db->query_first("SELECT MAX(`guid`) as `latestguid` FROM `".TABLE_PANEL_CUSTOMERS."`");

	if (isset($result['latestguid'])
		&& (int)$result['latestguid'] > 0
		&& $result['latestguid'] != $settings['system']['lastguid']
	) {
		checkLastGuid();
		lastStepStatus(1, 'fixed');
	} else {
		lastStepStatus(0);
	}
	updateToVersion('0.9.2');
}

if(isFroxlorVersion('0.9.2'))
{
	showUpdateStep("Updating from 0.9.2 to 0.9.3");
	lastStepStatus(0);
	updateToVersion('0.9.3');
}

if(isFroxlorVersion('0.9.3'))
{
	showUpdateStep("Updating from 0.9.3 to 0.9.3-svn1", false);

	showUpdateStep("Updating tables");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('panel', 'password_min_length', '0');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'store_index_file_subs', '1');");
	lastStepStatus(0);

	updateToVersion('0.9.3-svn1');
}

if(isFroxlorVersion('0.9.3-svn1'))
{
	showUpdateStep("Updating from 0.9.3-svn1 to 0.9.3-svn2", false);

	showUpdateStep("Updating tables");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('panel', 'adminmail_defname', 'Froxlor Administrator');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('panel', 'adminmail_return', '');");
	lastStepStatus(0);

	updateToVersion('0.9.3-svn2');
}

if(isFroxlorVersion('0.9.3-svn2'))
{
	showUpdateStep("Updating from 0.9.3-svn2 to 0.9.3-svn3", false);

	showUpdateStep("Correcting cron start-times");
	// set specific times for some crons (traffic only at night, etc.)
	$ts = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
	$db->query("UPDATE `".TABLE_PANEL_CRONRUNS."` SET `lastrun` = '".$ts."' WHERE `cronfile` ='cron_traffic.php';");
	$ts = mktime(1, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
	$db->query("UPDATE `".TABLE_PANEL_CRONRUNS."` SET `lastrun` = '".$ts."' WHERE `cronfile` ='cron_used_tickets_reset.php';");
	$db->query("UPDATE `".TABLE_PANEL_CRONRUNS."` SET `lastrun` = '".$ts."' WHERE `cronfile` ='cron_ticketarchive.php';");
	lastStepStatus(0);

	showUpdateStep("Adding new language: Polish");
	$db->query("INSERT INTO `".TABLE_PANEL_LANGUAGE."` SET `language` = 'Polski', `file` = 'lng/polish.lng.php'");
	lastStepStatus(0);

	updateToVersion('0.9.3-svn3');
}

if(isFroxlorVersion('0.9.3-svn3'))
{
	showUpdateStep("Updating from 0.9.3-svn3 to 0.9.3-svn4", false);

	showUpdateStep("Adding new DKIM settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('dkim', 'dkim_algorithm', 'all');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('dkim', 'dkim_add_adsp', '1');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('dkim', 'dkim_keylength', '1024');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('dkim', 'dkim_servicetype', '0');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('dkim', 'dkim_add_adsppolicy', '1');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('dkim', 'dkim_notes', '');");
	lastStepStatus(0);

	updateToVersion('0.9.3-svn4');
}

if(isFroxlorVersion('0.9.3-svn4'))
{
	showUpdateStep("Updating from 0.9.3-svn4 to 0.9.3-svn5", false);

	showUpdateStep("Adding new settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'stdsubdomain', '');");
	lastStepStatus(0);

	updateToVersion('0.9.3-svn5');
}

if(isFroxlorVersion('0.9.3-svn5'))
{
	showUpdateStep("Updating from 0.9.3-svn5 to 0.9.4 final");
	lastStepStatus(0);
	updateToVersion('0.9.4');
}

if(isFroxlorVersion('0.9.4'))
{
	showUpdateStep("Updating from 0.9.4 to 0.9.4-svn1", false);

	/**
	 * some users might still have the setting in their database
	 * because we already had this back in older versions.
	 * To not confuse Froxlor, we just update old settings.
	 */
	if(isset($settings['system']['awstats_path'])
		&& $settings['system']['awstats_path'] != ''
	) {
		showUpdateStep("Updating awstats path setting");
		$db->query("UPDATE `" . TABLE_PANEL_SETTINGS . "` SET `value` = '/usr/bin/' WHERE `settinggroup` = 'system' AND `varname` = 'awstats_path';");
		lastStepStatus(0);
	}
	elseif(!isset($settings['system']['awstats_path']))
	{
		showUpdateStep("Adding new awstats path setting");
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'awstats_path', '/usr/bin/');");
		lastStepStatus(0);
	}

	if(isset($settings['system']['awstats_domain_file'])
		&& $settings['system']['awstats_domain_file'] != ''
	) {
		showUpdateStep("Updating awstats configuration path setting");
		$db->query("UPDATE `" . TABLE_PANEL_SETTINGS . "` SET `varname` = 'awstats_conf' WHERE `varname` = 'awstats_domain_file';");
	}
	else
	{
		showUpdateStep("Adding awstats configuration path settings");
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'awstats_conf', '/etc/awstats/');");
	}
	lastStepStatus(0);

	updateToVersion('0.9.4-svn1');
}

if(isFroxlorVersion('0.9.4-svn1'))
{
	showUpdateStep("Updating from 0.9.4-svn1 to 0.9.4-svn2", false);

	$update_domains = isset($_POST['update_domainwildcardentry']) ? intval($_POST['update_domainwildcardentry']) : 0;

	if($update_domains != 1)
	{
		$update_domains = 0;
	}

	if($update_domains == 1)
	{
		showUpdateStep("Updating domains with iswildcarddomain=yes");
		$query = "SELECT `d`.`id` FROM `".TABLE_PANEL_DOMAINS."` `d`, `".TABLE_PANEL_CUSTOMERS."` `c` ";
		$query.= "WHERE `parentdomainid`='0' AND `email_only` = '0' AND `d`.`customerid` = `c`.`customerid` AND `d`.`id` <> `c`.`standardsubdomain`";
		$result = $db->query($query);
		$updated_domains = 0;
		while($domain = $db->fetch_array($result))
		{
			$db->query("UPDATE `".TABLE_PANEL_DOMAINS."` SET `iswildcarddomain` = '1' WHERE `id` ='".(int)$domain['id']."'");
			$updated_domains++;
		}
		lastStepStatus(0, 'Updated '.$updated_domains.' domain(s)');
	} else {
		showUpdateStep("Won't update domains with iswildcarddomain=yes as requested");
		lastStepStatus(1);
	}

	showUpdateStep("Updating database table definition for panel_domains");
	$db->query("ALTER TABLE `" . TABLE_PANEL_DOMAINS . "` MODIFY `iswildcarddomain` tinyint(1) NOT NULL default '1';");
	lastStepStatus(0);

	updateToVersion('0.9.4-svn2');
}

if(isFroxlorVersion('0.9.4-svn2'))
{
	showUpdateStep("Updating from 0.9.4-svn2 to 0.9.5 final");
	lastStepStatus(0);
	updateToVersion('0.9.5');
}

if(isFroxlorVersion('0.9.5'))
{
	showUpdateStep("Updating from 0.9.5 to 0.9.6-svn1", false);

	showUpdateStep("Adding time-to-live configuration setting");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'defaultttl', '604800');");
	lastStepStatus(0);

	showUpdateStep("Updating database table structure for panel_ticket_categories");
	$db->query("ALTER TABLE `" . TABLE_PANEL_TICKET_CATS . "` ADD `logicalorder` int(3) NOT NULL default '1' AFTER `adminid`;");
	lastStepStatus(0);

	updateToVersion('0.9.6-svn1');
}

if(isFroxlorVersion('0.9.6-svn1'))
{
	showUpdateStep("Updating from 0.9.6-svn1 to 0.9.6-svn2", false);

	$update_adminmail = isset($_POST['update_adminmail']) ? validate($_POST['update_adminmail'], 'update_adminmail') : false;
	$do_update = true;

	if($update_adminmail !== false)
	{
		showUpdateStep("Checking newly entered admin-mail");
		if(!PHPMailer::ValidateAddress($update_adminmail))
		{
			$do_update = false;
			lastStepStatus(2, 'E-Mail still not valid, go back and try again');
		}
		else
		{
			$db->query("UPDATE `" . TABLE_PANEL_SETTINGS . "` SET `value` = '".$db->escape($update_adminmail)."' WHERE `settinggroup` = 'panel' AND `varname` = 'adminmail';");
			lastStepStatus(0);
		}
	}

	if($do_update)
	{
		updateToVersion('0.9.6-svn2');
	}
}

if(isFroxlorVersion('0.9.6-svn2'))
{
	showUpdateStep("Updating from 0.9.6-svn2 to 0.9.6-svn3", false);

	$update_deferr_enable = isset($_POST['update_deferr_enable']) ? true : false;

	$err500 = false;
	$err401 = false;
	$err403 = false;
	$err404 = false;

	showUpdateStep("Adding new webserver configurations to database");
	if($update_deferr_enable == true)
	{
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'enabled', '1');");

		if(isset($_POST['update_deferr_500'])
			&& trim($_POST['update_deferr_500']) != ''
		) {
			$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err500', '".$db->escape($_POST['update_deferr_500'])."');");
			$err500 = true;
		}

		if(isset($_POST['update_deferr_401'])
			&& trim($_POST['update_deferr_401']) != ''
		) {
			$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err401', '".$db->escape($_POST['update_deferr_401'])."');");
			$err401 = true;
		}

		if(isset($_POST['update_deferr_403'])
			&& trim($_POST['update_deferr_403']) != ''
		) {
			$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err403', '".$db->escape($_POST['update_deferr_403'])."');");
			$err403 = true;
		}

		if(isset($_POST['update_deferr_404'])
			&& trim($_POST['update_deferr_404']) != ''
		) {
			$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err404', '".$db->escape($_POST['update_deferr_404'])."');");
			$err404 = true;
		}
	}

	if(!$update_deferr_enable) {
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'enabled', '0');");
	}
	if(!$err401) {
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err401', '');");
	}
	if(!$err403) {
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err403', '');");
	}
	if(!$err404) {
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err404', '');");
	}
	if(!$err500) {
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err500', '');");
	}

	lastStepStatus(0);

	updateToVersion('0.9.6-svn3');
}

if(isFroxlorVersion('0.9.6-svn3'))
{
	showUpdateStep("Updating from 0.9.6-svn3 to 0.9.6-svn4", false);

	$update_deftic_priority = isset($_POST['update_deftic_priority']) ? intval($_POST['update_deftic_priority']) : 2;

	showUpdateStep("Setting default support-ticket priority");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('ticket', 'default_priority', '".(int)$update_deftic_priority."');");
	lastStepStatus(0);

	updateToVersion('0.9.6-svn4');
}

if(isFroxlorVersion('0.9.6-svn4'))
{
	showUpdateStep("Updating from 0.9.6-svn4 to 0.9.6-svn5", false);

	$update_defsys_phpconfig = isset($_POST['update_defsys_phpconfig']) ? intval($_POST['update_defsys_phpconfig']) : 1;

	if($update_defsys_phpconfig != 1) {
		showUpdateStep("Setting default php-configuration to user defined config #".$update_defsys_phpconfig);
	} else {
		showUpdateStep("Adding default php-configuration setting to the database");
	}

	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'mod_fcgid_defaultini', '".(int)$update_defsys_phpconfig."');");
	lastStepStatus(0);

	updateToVersion('0.9.6-svn5');
}

if(isFroxlorVersion('0.9.6-svn5'))
{
	showUpdateStep("Updating from 0.9.6-svn5 to 0.9.6-svn6", false);

	showUpdateStep("Adding new FTP-quota settings");

	$update_defsys_ftpserver = isset($_POST['update_defsys_ftpserver']) ? intval($_POST['update_defsys_ftpserver']) : 'proftpd';

	// add ftp server setting
	$db->query("INSERT INTO `panel_settings` SET `settinggroup` = 'system', `varname` = 'ftpserver', `value` = '".$db->escape($update_defsys_ftpserver)."';");

	// add proftpd quota
	$db->query("CREATE TABLE `ftp_quotalimits` (`name` varchar(30) default NULL, `quota_type` enum('user','group','class','all') NOT NULL default 'user', `per_session` enum('false','true') NOT NULL default 'false', `limit_type` enum('soft','hard') NOT NULL default 'hard', `bytes_in_avail` float NOT NULL, `bytes_out_avail` float NOT NULL, `bytes_xfer_avail` float NOT NULL, `files_in_avail` int(10) unsigned NOT NULL, `files_out_avail` int(10) unsigned NOT NULL, `files_xfer_avail` int(10) unsigned NOT NULL) ENGINE=MyISAM;");

	$db->query("INSERT INTO `ftp_quotalimits` (`name`, `quota_type`, `per_session`, `limit_type`, `bytes_in_avail`, `bytes_out_avail`, `bytes_xfer_avail`, `files_in_avail`, `files_out_avail`, `files_xfer_avail`) VALUES ('froxlor', 'user', 'false', 'hard', 0, 0, 0, 0, 0, 0);");

	$db->query("CREATE TABLE `ftp_quotatallies` (`name` varchar(30) NOT NULL, `quota_type` enum('user','group','class','all') NOT NULL, `bytes_in_used` float NOT NULL, `bytes_out_used` float NOT NULL, `bytes_xfer_used` float NOT NULL, `files_in_used` int(10) unsigned NOT NULL, `files_out_used` int(10) unsigned NOT NULL, `files_xfer_used` int(10) unsigned NOT NULL ) ENGINE=MyISAM;");

	// fill quota tallies
	$result_ftp_users = $db->query("SELECT username FROM `" . TABLE_FTP_USERS . "` WHERE 1;");

	while($row_ftp_users = $db->fetch_array($result_ftp_users))
	{
		$result_ftp_quota = $db->query("SELECT diskspace_used FROM `" . TABLE_PANEL_CUSTOMERS . "` WHERE loginname = SUBSTRING_INDEX('" . $row_ftp_users['username'] . "', '" . $settings['customer']['ftpprefix'] . "', 1);");
		$row_ftp_quota = mysql_fetch_row($result_ftp_quota);
		$db->query("INSERT INTO `ftp_quotatallies` (`name`, `quota_type`, `bytes_in_used`, `bytes_out_used`, `bytes_xfer_used`, `files_in_used`, `files_out_used`, `files_xfer_used`) VALUES ('" . $row_ftp_users['username'] . "', 'user', '" . $row_ftp_quota[0] . "'*1024, '0', '0', '0', '0', '0');");
	}

	lastStepStatus(0);

	updateToVersion('0.9.6-svn6');
}

if(isFroxlorVersion('0.9.6-svn6'))
{
	showUpdateStep("Updating from 0.9.6-svn6 to 0.9.6 final");
	lastStepStatus(0);
	updateToVersion('0.9.6');
}

if(isFroxlorVersion('0.9.6'))
{
	showUpdateStep("Updating from 0.9.6 to 0.9.7-svn1", false);

	$update_customredirect_enable = isset($_POST['update_customredirect_enable']) ? 1 : 0;
	$update_customredirect_default = isset($_POST['update_customredirect_default']) ? (int)$_POST['update_customredirect_default'] : 1;

	showUpdateStep("Adding new tables to database");
	$db->query("CREATE TABLE IF NOT EXISTS `redirect_codes` (
  `id` int(5) NOT NULL auto_increment,
  `code` varchar(3) NOT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;");

	$db->query("CREATE TABLE IF NOT EXISTS `domain_redirect_codes` (
  `rid` int(5) NOT NULL,
  `did` int(11) unsigned NOT NULL,
  UNIQUE KEY `rc` (`rid`, `did`)
) ENGINE=MyISAM;");
	lastStepStatus(0);

	showUpdateStep("Filling new tables with default data");
	$db->query("INSERT INTO `redirect_codes` (`id`, `code`, `enabled`) VALUES (1, '---', 1);");
	$db->query("INSERT INTO `redirect_codes` (`id`, `code`, `enabled`) VALUES (2, '301', 1);");
	$db->query("INSERT INTO `redirect_codes` (`id`, `code`, `enabled`) VALUES (3, '302', 1);");
	$db->query("INSERT INTO `redirect_codes` (`id`, `code`, `enabled`) VALUES (4, '303', 1);");
	$db->query("INSERT INTO `redirect_codes` (`id`, `code`, `enabled`) VALUES (5, '307', 1);");
	lastStepStatus(0);

	showUpdateStep("Updating domains");
	$res = $db->query("SELECT `id` FROM `".TABLE_PANEL_DOMAINS."` ORDER BY `id` ASC");
	$updated_domains = 0;
	while($d = $db->fetch_array($res))
	{
		$db->query("INSERT INTO `domain_redirect_codes` (`rid`, `did`) VALUES ('".(int)$update_customredirect_default."', '".(int)$d['id']."');");
		$updated_domains++;
	}
	lastStepStatus(0, 'Updated '.$updated_domains.' domain(s)');

	showUpdateStep("Adding new settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('customredirect', 'enabled', '".(int)$update_customredirect_enable."');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('customredirect', 'default', '".(int)$update_customredirect_default."');");
	lastStepStatus(0);

	// need to fix default-error-copy-and-paste-shizzle
	showUpdateStep("Checking if anything is ok with the default-error-handler");
	if(!isset($settings['defaultwebsrverrhandler']['err404']))
	{
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err404', '');");
	}
	if(!isset($settings['defaultwebsrverrhandler']['err403']))
	{
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err403', '');");
	}
	if(!isset($settings['defaultwebsrverrhandler']['err401']))
	{
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('defaultwebsrverrhandler', 'err401', '');");
	}
	lastStepStatus(0);

	updateToVersion('0.9.7-svn1');
}

if(isFroxlorVersion('0.9.7-svn1'))
{
	showUpdateStep("Updating from 0.9.7-svn1 to 0.9.7-svn2", false);

	showUpdateStep("Updating open_basedir due to security - issue");
	$result = $db->query("SELECT `id` FROM `" . TABLE_PANEL_DOMAINS . "` WHERE `documentroot` LIKE '%:%' AND `documentroot` NOT LIKE 'http://%' AND `openbasedir_path` = '0' AND `openbasedir` = '1'");
	while($row = $db->fetch_array($result))
	{
		$db->query("UPDATE `".TABLE_PANEL_DOMAINS."` SET `openbasedir_path` = '1' WHERE `id` = " . $row['id']);
	}
	lastStepStatus(0);

	updateToVersion('0.9.7-svn2');
}

if(isFroxlorVersion('0.9.7-svn2'))
{
	showUpdateStep("Updating from 0.9.7-svn2 to 0.9.7-svn3", false);

	showUpdateStep("Updating database tables");
	$db->query("ALTER TABLE `redirect_codes` ADD `desc` varchar(200) NOT NULL AFTER `code`;");
	lastStepStatus(0);
	
	showUpdateStep("Updating field-values");
	$db->query("UPDATE `redirect_codes` SET `desc` = 'rc_default' WHERE `code` = '---';");
	$db->query("UPDATE `redirect_codes` SET `desc` = 'rc_movedperm' WHERE `code` = '301';");
	$db->query("UPDATE `redirect_codes` SET `desc` = 'rc_found' WHERE `code` = '302';");
	$db->query("UPDATE `redirect_codes` SET `desc` = 'rc_seeother' WHERE `code` = '303';");
	$db->query("UPDATE `redirect_codes` SET `desc` = 'rc_tempred' WHERE `code` = '307';");
	lastStepStatus(0);

	updateToVersion('0.9.7-svn3');
}

if(isFroxlorVersion('0.9.7-svn3'))
{
	showUpdateStep("Updating from 0.9.7-svn3 to 0.9.7 final");
	lastStepStatus(0);
	updateToVersion('0.9.7');
}

if(isFroxlorVersion('0.9.7'))
{
	showUpdateStep("Updating from 0.9.7 to 0.9.8 final");
	lastStepStatus(0);
	updateToVersion('0.9.8');
}

if(isFroxlorVersion('0.9.8'))
{
	showUpdateStep("Updating from 0.9.8 to 0.9.9-svn1", false);

	$update_defdns_mailentry = isset($_POST['update_defdns_mailentry']) ? '1' : '0';

	showUpdateStep("Adding new settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'dns_createmailentry', '".(int)$update_defdns_mailentry."');");
	lastStepStatus(0);

	updateToVersion('0.9.9-svn1');
}

if(isFroxlorVersion('0.9.9-svn1'))
{
	showUpdateStep("Updating from 0.9.9-svn1 to 0.9.9 final");
	lastStepStatus(0);
	updateToVersion('0.9.9');
}

if(isFroxlorVersion('0.9.9'))
{
	showUpdateStep("Updating from 0.9.9 to 0.9.10-svn1", false);
	
	showUpdateStep("Checking whether you are missing any settings", false);
	$nonefound = true;
	
	$update_httpuser = isset($_POST['update_httpuser']) ? $_POST['update_httpuser'] : false;
	$update_httpgroup = isset($_POST['update_httpgroup']) ? $_POST['update_httpgroup'] : false;

	if($update_httpuser !== false)
	{
		$nonefound = false;
		showUpdateStep("Adding missing setting 'httpuser'");
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'httpuser', '".$update_httpuser."');");
		lastStepStatus(0);
	}

	if($update_httpgroup !== false)
	{
		$nonefound = false;
		showUpdateStep("Adding missing setting 'httpgroup'");
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'httpgroup', '".$update_httpgroup."');");
		lastStepStatus(0);
	}

	$result = $db->query_first("SELECT * FROM `" . TABLE_PANEL_SETTINGS . "` WHERE `settinggroup` = 'system' AND `varname` = 'debug_cron'");
	if(!isset($result) || !isset($result['value']))
	{
		$nonefound = false;
		showUpdateStep("Adding missing setting 'debug_cron'");
		$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'debug_cron', '0');");
		lastStepStatus(0);
	}
	
	if($nonefound) {
		showUpdateStep("No missing settings found");
		lastStepStatus(0);
	}

	updateToVersion('0.9.10-svn1');
}

if(isFroxlorVersion('0.9.10-svn1'))
{
	showUpdateStep("Updating from 0.9.10-svn1 to 0.9.10-svn2", false);
	
	showUpdateStep("Updating database table definition for panel_databases");
	$db->query("ALTER TABLE `" . TABLE_PANEL_DATABASES . "` ADD `apsdb` tinyint(1) NOT NULL default '0' AFTER `dbserver`;");
	lastStepStatus(0);

	showUpdateStep("Adding APS databases to customers overview");
	$count_dbupdates = 0;
	$db_root = null;
	openRootDB();
	$result = $db_root->query("SHOW DATABASES;");
	while($row = $db_root->fetch_array($result))
	{	
		if(preg_match('/^web([0-9]+)aps([0-9]+)$/', $row['Database'], $matches))
		{
			$cid = $matches[1];
			$databasedescription = 'APS DB';
			$result = $db->query('INSERT INTO `' . TABLE_PANEL_DATABASES . '` (`customerid`, `databasename`, `description`, `dbserver`, `apsdb`) VALUES ("' . (int)$cid . '", "' . $db->escape($row['Database']) . '", "' . $db->escape($databasedescription) . '", "0", "1")');
			$result = $db->query('UPDATE `' . TABLE_PANEL_CUSTOMERS . '` SET `mysqls_used`=`mysqls_used`+1 WHERE `customerid`="' . (int)$cid . '"');
			$count_dbupdates++;
		}
	}
	closeRootDB();
	if($count_dbupdates > 0) {
		lastStepStatus(0, "Found ".$count_dbupdates." customer APS databases");
	} else {
		lastStepStatus(0, "None found");
	}

	updateToVersion('0.9.10-svn2');
}

if(isFroxlorVersion('0.9.10-svn2'))
{
	showUpdateStep("Updating from 0.9.10-svn2 to 0.9.10", false);

	$update_directlyviahostname = isset($_POST['update_directlyviahostname']) ? (int)$_POST['update_directlyviahostname'] : '0';

	showUpdateStep("Adding new settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'froxlordirectlyviahostname', '".(int)$update_directlyviahostname."');");
	lastStepStatus(0);

	updateToVersion('0.9.10');
}

if(isFroxlorVersion('0.9.10'))
{
	showUpdateStep("Updating from 0.9.10 to 0.9.11-svn1", false);

	$update_pwdregex = isset($_POST['update_pwdregex']) ? $_POST['update_pwdregex'] : '';

	showUpdateStep("Adding new settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('panel', 'password_regex', '".$db->escape($update_pwdregex)."');");
	lastStepStatus(0);

	updateToVersion('0.9.11-svn1');
}

if(isFroxlorVersion('0.9.11-svn1'))
{
	showUpdateStep("Updating from 0.9.11-svn1 to 0.9.11-svn2", false);

	showUpdateStep("Adding perl/CGI directory fields");
	$db->query("ALTER TABLE `".TABLE_PANEL_HTACCESS."` ADD `options_cgi` tinyint(1) NOT NULL default '0' AFTER `error401path`;");
	$db->query("ALTER TABLE `".TABLE_PANEL_CUSTOMERS."` ADD `perlenabled` tinyint(1) NOT NULL default '0' AFTER `aps_packages_used`;");
	lastStepStatus(0);

	updateToVersion('0.9.11-svn2');
}

if(isFroxlorVersion('0.9.11-svn2'))
{
	showUpdateStep("Updating from 0.9.11-svn2 to 0.9.11-svn3", false);

	$update_perlpath = isset($_POST['update_perlpath']) ? $_POST['update_perlpath'] : '/usr/bin/perl';

	showUpdateStep("Adding new settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'perl_path', '".$db->escape($update_perlpath)."');");
	lastStepStatus(0);

	updateToVersion('0.9.11-svn3');
}

if(isFroxlorVersion('0.9.11-svn3'))
{
	showUpdateStep("Updating from 0.9.11-svn3 to 0.9.11 final");
	lastStepStatus(0);

	updateToVersion('0.9.11');
}

if(isFroxlorVersion('0.9.11'))
{
	showUpdateStep("Updating from 0.9.11 to 0.9.12-svn1", false);

	$update_fcgid_ownvhost = isset($_POST['update_fcgid_ownvhost']) ? (int)$_POST['update_fcgid_ownvhost'] : '0';
	$update_fcgid_httpuser = isset($_POST['update_fcgid_httpuser']) ? $_POST['update_fcgid_httpuser'] : 'froxlorlocal';
	$update_fcgid_httpgroup = isset($_POST['update_fcgid_ownvhost']) ? $_POST['update_fcgid_ownvhost'] : 'froxlorlocal';

	if($update_fcgid_httpuser == '') {
		$update_fcgid_httpuser = 'froxlorlocal';
	}
	if($update_fcgid_httpgroup == '') {
		$update_fcgid_httpgroup = 'froxlorlocal';
	}
	
	showUpdateStep("Adding new settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'mod_fcgid_ownvhost', '".$db->escape($update_fcgid_ownvhost)."');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'mod_fcgid_httpuser', '".$db->escape($update_fcgid_httpuser)."');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'mod_fcgid_httpgroup', '".$db->escape($update_fcgid_httpgroup)."');");
	lastStepStatus(0);

	updateToVersion('0.9.12-svn1');
}

if(isFroxlorVersion('0.9.12-svn1'))
{
	showUpdateStep("Updating from 0.9.12-svn1 to 0.9.12-svn2", false);

	$update_perl_suexecworkaround = isset($_POST['update_perl_suexecworkaround']) ? (int)$_POST['update_perl_suexecworkaround'] : '0';
	$update_perl_suexecpath = isset($_POST['update_perl_suexecpath']) ? makeCorrectDir($_POST['update_perl_suexecpath']) : '/var/www/cgi-bin/';

	if($update_perl_suexecpath == '') {
		$update_perl_suexecpath = '/var/www/cgi-bin/';
	}
	
	showUpdateStep("Adding new settings for perl/CGI");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('perl', 'suexecworkaround', '".$db->escape($update_perl_suexecworkaround)."');");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('perl', 'suexecpath', '".$db->escape($update_perl_suexecpath)."');");
	lastStepStatus(0);

	updateToVersion('0.9.12-svn2');
}

if(isFroxlorVersion('0.9.12-svn2'))
{
	showUpdateStep("Updating from 0.9.12-svn2 to 0.9.12-svn3", false);

	showUpdateStep("Adding new field to domain table");
	$db->query("ALTER TABLE `".TABLE_PANEL_DOMAINS."` ADD `ismainbutsubto` int(11) unsigned NOT NULL default '0' AFTER `mod_fcgid_maxrequests`;");
	lastStepStatus(0);

	updateToVersion('0.9.12-svn3');
}

if(isFroxlorVersion('0.9.12-svn3'))
{
	showUpdateStep("Updating from 0.9.12-svn3 to 0.9.12-svn4", false);
	
	$update_awstats_awstatspath = isset($_POST['update_awstats_awstatspath']) ? makeCorrectDir($_POST['update_awstats_awstatspath']) : $settings['system']['awstats_path'];	

	showUpdateStep("Adding new settings for awstats");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'awstats_awstatspath', '".$db->escape($update_awstats_awstatspath)."');");
	lastStepStatus(0);

	updateToVersion('0.9.12-svn4');
}

if(isFroxlorVersion('0.9.12-svn4'))
{
	showUpdateStep("Updating from 0.9.12-svn4 to 0.9.12-svn5", false);	

	showUpdateStep("Setting ticket-usage-reset cronjob interval to 1 day");
	$db->query("UPDATE `cronjobs_run` SET `interval`='1 DAY' WHERE `cronfile`='cron_used_tickets_reset.php';");
	lastStepStatus(0);

	updateToVersion('0.9.12-svn5');
}

if(isFroxlorVersion('0.9.12-svn5'))
{
	showUpdateStep("Updating from 0.9.12-svn5 to 0.9.12-svn6", false);	

	showUpdateStep("Adding new field to table 'panel_htpasswds'");
	$db->query("ALTER TABLE `".TABLE_PANEL_HTPASSWDS."` ADD `authname` varchar(255) NOT NULL default 'Restricted Area' AFTER `password`;");
	lastStepStatus(0);

	updateToVersion('0.9.12-svn6');
}

if(isFroxlorVersion('0.9.12-svn6'))
{
	showUpdateStep("Updating from 0.9.12-svn6 to 0.9.12 final");
	lastStepStatus(0);

	updateToVersion('0.9.12');
}

if(isFroxlorVersion('0.9.12'))
{
	showUpdateStep("Updating from 0.9.12 to 0.9.13-svn1", false);

	showUpdateStep("Adding new fields to admin-table");
	$db->query("ALTER TABLE `".TABLE_PANEL_ADMINS."` ADD `email_autoresponder` int(5) NOT NULL default '0' AFTER `aps_packages_used`;");
	$db->query("ALTER TABLE `".TABLE_PANEL_ADMINS."` ADD `email_autoresponder_used` int(5) NOT NULL default '0' AFTER `email_autoresponder`;");
	lastStepStatus(0);
	
	showUpdateStep("Adding new fields to customer-table");
	$db->query("ALTER TABLE `".TABLE_PANEL_CUSTOMERS."` ADD `email_autoresponder` int(5) NOT NULL default '0' AFTER `perlenabled`;");
	$db->query("ALTER TABLE `".TABLE_PANEL_CUSTOMERS."` ADD `email_autoresponder_used` int(5) NOT NULL default '0' AFTER `email_autoresponder`;");
	lastStepStatus(0);
	
	if((int)$settings['autoresponder']['autoresponder_active'] == 1)
	{
		$update_autoresponder_default = isset($_POST['update_autoresponder_default']) ? intval_ressource($_POST['update_autoresponder_default']) : 0;

		if(isset($_POST['update_autoresponder_default_ul'])) {
			$update_autoresponder_default = -1;
		}
	}
	else
	{
		$update_autoresponder_default = 0;
	}

	showUpdateStep("Setting default amount of autoresponders");
	// admin gets unlimited
	$db->query("UPDATE `".TABLE_PANEL_ADMINS."` SET `email_autoresponder`='-1' WHERE `adminid` = '".(int)$userinfo['adminid']."'");
	// customers
	$db->query("UPDATE `".TABLE_PANEL_CUSTOMERS."` SET `email_autoresponder`='".(int)$update_autoresponder_default."' WHERE `deactivated` = '0'");
	lastStepStatus(0);

	updateToVersion('0.9.13-svn1');
}

if(isFroxlorVersion('0.9.13-svn1'))
{
	showUpdateStep("Updating from 0.9.13-svn1 to 0.9.13 final");
	lastStepStatus(0);

	updateToVersion('0.9.13');
}

if(isFroxlorVersion('0.9.13'))
{
	showUpdateStep("Updating from 0.9.13 to 0.9.13.1 final", false);

	$update_defaultini_ownvhost = isset($_POST['update_defaultini_ownvhost']) ? (int)$_POST['update_defaultini_ownvhost'] : 1;	

	showUpdateStep("Adding settings for Froxlor-vhost's PHP-configuration");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'mod_fcgid_defaultini_ownvhost', '".(int)$update_defaultini_ownvhost."');");
	lastStepStatus(0);

	updateToVersion('0.9.13.1');
}

/**
 * be compatible with the few who already use 0.9.14-svn1
 */
if(isFroxlorVersion('0.9.14-svn1'))
{
	showUpdateStep("Resetting version 0.9.14-svn1 to 0.9.13.1");
	lastStepStatus(0);

	updateToVersion('0.9.13.1');
}

if(isFroxlorVersion('0.9.13.1'))
{
	showUpdateStep("Updating from 0.9.13.1 to 0.9.14-svn2", false);
	
	if($settings['ticket']['enabled'] == '1')
	{
		showUpdateStep("Setting INTERVAL for used-tickets cronjob");
		setCycleOfCronjob(null, null, $settings['ticket']['reset_cycle'], null);
		lastStepStatus(0);
	}
	updateToVersion('0.9.14-svn2');
}

if(isFroxlorVersion('0.9.14-svn2'))
{
	showUpdateStep("Updating from 0.9.14-svn2 to 0.9.14-svn3", false);

	$update_awstats_icons = isset($_POST['update_awstats_icons']) ? makeCorrectDir($_POST['update_awstats_icons']) : $settings['system']['awstats_icons'];	

	showUpdateStep("Adding AWStats icons path to the settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'awstats_icons', '".$db->escape($update_awstats_icons)."');");
	lastStepStatus(0);

	updateToVersion('0.9.14-svn3');
}

if(isFroxlorVersion('0.9.14-svn3'))
{
	showUpdateStep("Updating from 0.9.14-svn3 to 0.9.14-svn4", false);

	$update_ssl_cert_chainfile = isset($_POST['update_ssl_cert_chainfile']) ? $_POST['update_ssl_cert_chainfile'] : '';	

	if($update_ssl_cert_chainfile != '')
	{
		$update_ssl_cert_chainfile = makeCorrectFile($update_ssl_cert_chainfile);
	}

	showUpdateStep("Adding SSLCertificateChainFile to the settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('system', 'ssl_cert_chainfile', '".$db->escape($update_ssl_cert_chainfile)."');");
	lastStepStatus(0);

	showUpdateStep("Adding new field to IPs and ports for SSLCertificateChainFile");
	$db->query("ALTER TABLE `".TABLE_PANEL_IPSANDPORTS."` ADD `ssl_cert_chainfile` varchar(255) NOT NULL AFTER `default_vhostconf_domain`;");
	lastStepStatus(0);

	updateToVersion('0.9.14-svn4');
}

if(isFroxlorVersion('0.9.14-svn4'))
{
	showUpdateStep("Updating from 0.9.14-svn4 to 0.9.14-svn5", false);

	showUpdateStep("Adding docroot-field to IPs and ports for custom-docroot settings");
	$db->query("ALTER TABLE `".TABLE_PANEL_IPSANDPORTS."` ADD `docroot` varchar(255) NOT NULL default '' AFTER `ssl_cert_chainfile`;");
	lastStepStatus(0);

	updateToVersion('0.9.14-svn5');
}

if(isFroxlorVersion('0.9.14-svn5'))
{
	showUpdateStep("Updating from 0.9.14-svn5 to 0.9.14-svn6", false);

	$update_allow_domain_login = isset($_POST['update_allow_domain_login']) ? (int)$_POST['update_allow_domain_login'] : '0';

	showUpdateStep("Adding domain-login switch to the settings");
	$db->query("INSERT INTO `" . TABLE_PANEL_SETTINGS . "` (`settinggroup`, `varname`, `value`) VALUES ('login', 'domain_login', '".(int)$update_allow_domain_login."');");
	lastStepStatus(0);

	updateToVersion('0.9.14-svn6');
}

if(isFroxlorVersion('0.9.14-svn6'))
{
	showUpdateStep("Updating from 0.9.14-svn6 to 0.9.14-svn10", false);

	// remove deprecated realtime-feature
	showUpdateStep("Removing realtime-feature (deprecated)");
	$db->query("DELETE FROM `" . TABLE_PANEL_SETTINGS . "` WHERE `settinggroup` = 'system' AND `varname` = 'realtime_port';");
	lastStepStatus(0);

	// remove deprecated panel_navigation
	showUpdateStep("Removing table `panel_navigation` (deprecated)");
	$db->query("DROP TABLE IF EXISTS `panel_navigation`;");
	lastStepStatus(0);

	// remove deprecated panel_cronscript
	showUpdateStep("Removing table `panel_cronscript` (deprecated)");
	$db->query("DROP TABLE IF EXISTS `panel_cronscript`;");
	lastStepStatus(0);

	// make ticket-system ipv6 compatible
	showUpdateStep("Altering IP field in panel_tickets (IPv6 compatibility)");
	$db->query("ALTER TABLE `" . TABLE_PANEL_TICKETS . "` MODIFY `ip` varchar(39) NOT NULL default '';");
	lastStepStatus(0);

	showUpdateStep("Removing deprecated legacy-cronjob from database");
	$db->query("DELETE FROM `".TABLE_PANEL_CRONRUNS."` WHERE `cronfile` ='cron_legacy.php';");
	lastStepStatus(0);

	updateToVersion('0.9.14-svn10');
}

/*
 * revert database changes we did for multiserver-support
 * before branching - sorry guys :/
 */
if(isFroxlorVersion('0.9.14-svn9'))
{
	showUpdateStep("Reverting multiserver-patches (svn)", false);

	$update_allow_domain_login = isset($_POST['update_allow_domain_login']) ? (int)$_POST['update_allow_domain_login'] : '0';

	showUpdateStep("Reverting database table-changes");
	$db->query("ALTER TABLE `".TABLE_PANEL_SETTINGS."` DROP `sid`;");

	showUpdateStep(".");
	$db->query("ALTER TABLE `".TABLE_PANEL_CUSTOMERS."` DROP `sid`;");

	showUpdateStep(".");
	$db->query("ALTER TABLE `".TABLE_MAIL_VIRTUAL."` DROP `sid`;");

	showUpdateStep(".");
	$db->query("ALTER TABLE `".TABLE_FTP_USERS."` DROP `sid`;");

	showUpdateStep(".");
	$db->query("ALTER TABLE `".TABLE_PANEL_TASKS."` DROP `sid`;");

	showUpdateStep(".");
	$db->query("ALTER TABLE `".TABLE_APS_TASKS."` DROP `sid`;");

	showUpdateStep(".");
	$db->query("ALTER TABLE `".TABLE_PANEL_LOG."` DROP `sid`;");

	showUpdateStep(".");
	$db->query("ALTER TABLE `".TABLE_PANEL_PHPCONFIGS."` DROP `sid`;");
	lastStepStatus(0);

	showUpdateStep("Removing froxlor-clients table");
	$db->query("DROP TABLE IF EXISTS `froxlor_clients`");
	lastStepStatus(0);

	updateToVersion('0.9.14-svn10');
}

if(isFroxlorVersion('0.9.14-svn10'))
{
	showUpdateStep("Updating from 0.9.14-svn10 to 0.9.14 final");
	lastStepStatus(0);

	updateToVersion('0.9.14');
}

if(isFroxlorVersion('0.9.14'))
{
	showUpdateStep("Updating from 0.9.14 to 0.9.15-svn1", false);
	
	showUpdateStep("Adding new settings for Nginx support");
	$db->query("INSERT INTO `".TABLE_PANEL_SETTINGS."` (`settinggroup`, `varname`, `value`) VALUES ('system', 'nginx_php_backend', '127.0.0.1:8888')");
	$db->query("INSERT INTO `".TABLE_PANEL_SETTINGS."` (`settinggroup`, `varname`, `value`) VALUES ('system', 'perl_server', 'unix:/var/run/nginx/cgiwrap-dispatch.sock')");
	$db->query("INSERT INTO `".TABLE_PANEL_SETTINGS."` (`settinggroup`, `varname`, `value`) VALUES ('system', 'phpreload_command', '')");

	updateToVersion('0.9.15-svn1');
}

if(isFroxlorVersion('0.9.15-svn1'))
{
	showUpdateStep("Updating from 0.9.15-svn1 to 0.9.15 final");
	lastStepStatus(0);

	updateToVersion('0.9.15');
}
<?php
namespace Froxlor\UI;

use Froxlor\Settings;

class Form
{

	private static $fields = null;

	public static function buildFormEx($form, $part = '')
	{
		self::$fields = array();

		if (\Froxlor\Validate\Form::validateFormDefinition($form)) {
			foreach ($form['groups'] as $groupname => $groupdetails) {
				// show overview
				if ($part == '') {
					if (isset($groupdetails['title']) && $groupdetails['title'] != '') {
						self::getFormOverviewGroupOutput($groupname, $groupdetails);
					}
				} elseif ($part != '' && ($groupname == $part || $part == 'all')) {
					// only show one section
					/**
					 * this part checks for the 'websrv_avail' entry in the settings-array
					 * if found, we check if the current webserver is in the array.
					 * If this
					 * is not the case, we change the setting type to "hidden", #502
					 */
					$do_show = true;
					if (isset($groupdetails['websrv_avail']) && is_array($groupdetails['websrv_avail'])) {
						$websrv = Settings::Get('system.webserver');
						if (! in_array($websrv, $groupdetails['websrv_avail'])) {
							$do_show = false;
						}
					}

					// visible = Settings::Get('phpfpm.enabled') for example would result in false if not enabled
					// and therefore not shown as intended. Only check if do_show is still true as it might
					// be false due to websrv_avail
					if (isset($groupdetails['visible']) && $do_show) {
						$do_show = $groupdetails['visible'];
					}

					// if ($do_show) {
					if (isset($groupdetails['title']) && $groupdetails['title'] != '') {
						self::getFormGroupOutput($groupname, $groupdetails);
						if (! isset(self::$fields['sections'])) {
							self::$fields['sections'] = array();
						}
						self::$fields['sections'][$groupname] = array(
							'title' => $groupdetails['title'],
							'nobuttons' => false,
							'elements' => array()
						);
					}

					if (\Froxlor\Validate\Form::validateFieldDefinition($groupdetails)) {
						// Prefetch form fields
						foreach ($groupdetails['fields'] as $fieldname => $fielddetails) {
							$groupdetails['fields'][$fieldname] = self::arrayMergePrefix($fielddetails, $fielddetails['type'], self::prefetchFormFieldData($fieldname, $fielddetails));
							// $form['groups'][$groupname]['fields'][$fieldname] = $groupdetails['fields'][$fieldname];
						}

						// Collect form field output
						foreach ($groupdetails['fields'] as $fieldname => $fielddetails) {
							self::getFormFieldOutput($groupname, $fieldname, $fielddetails);
						}
					}
					// }
				}
			}
		}

		return self::$fields;
	}

	public static function processFormEx(&$form, &$input, $url_params = array(), $part = null, $settings_all = array(), $settings_part = null, $only_enabledisable = false)
	{
		if (\Froxlor\Validate\Form::validateFormDefinition($form)) {
			$submitted_fields = array();
			$changed_fields = array();
			$saved_fields = array();

			foreach ($form['groups'] as $groupname => $groupdetails) {
				if (($settings_part && $part == $groupname) || $settings_all || $only_enabledisable) {
					if (\Froxlor\Validate\Form::validateFieldDefinition($groupdetails)) {
						// Prefetch form fields
						foreach ($groupdetails['fields'] as $fieldname => $fielddetails) {
							if (! $only_enabledisable || ($only_enabledisable && isset($fielddetails['overview_option']))) {
								$groupdetails['fields'][$fieldname] = self::arrayMergePrefix($fielddetails, $fielddetails['type'], self::prefetchFormFieldData($fieldname, $fielddetails));
								$form['groups'][$groupname]['fields'][$fieldname] = $groupdetails['fields'][$fieldname];
							}
						}
					}
				}
			}

			foreach ($form['groups'] as $groupname => $groupdetails) {
				if (($settings_part && $part == $groupname) || $settings_all || $only_enabledisable) {
					if (\Froxlor\Validate\Form::validateFieldDefinition($groupdetails)) {
						// Validate fields
						foreach ($groupdetails['fields'] as $fieldname => $fielddetails) {
							if (! $only_enabledisable || ($only_enabledisable && isset($fielddetails['overview_option']))) {
								$newfieldvalue = self::getFormFieldData($fieldname, $fielddetails, $input);
								if ($newfieldvalue != $fielddetails['value']) {
									if (($error = \Froxlor\Validate\Form::validateFormField($fieldname, $fielddetails, $newfieldvalue)) !== true) {
										\Froxlor\UI\Response::standard_error($error, $fieldname);
									} else {
										$changed_fields[$fieldname] = $newfieldvalue;
									}
								}

								$submitted_fields[$fieldname] = $newfieldvalue;
							}
						}
					}
				}
			}

			foreach ($form['groups'] as $groupname => $groupdetails) {
				if (($settings_part && $part == $groupname) || $settings_all || $only_enabledisable) {
					if (\Froxlor\Validate\Form::validateFieldDefinition($groupdetails)) {
						// Check fields for plausibility
						foreach ($groupdetails['fields'] as $fieldname => $fielddetails) {
							if (! $only_enabledisable || ($only_enabledisable && isset($fielddetails['overview_option']))) {
								if (($plausibility_check = self::checkPlausibilityFormField($fieldname, $fielddetails, $submitted_fields[$fieldname], $submitted_fields)) !== false) {
									if (is_array($plausibility_check) && isset($plausibility_check[0])) {
										if ($plausibility_check[0] == \Froxlor\Validate\Check::FORMFIELDS_PLAUSIBILITY_CHECK_OK) {
											// Nothing to do here, everything's okay
										} elseif ($plausibility_check[0] == \Froxlor\Validate\Check::FORMFIELDS_PLAUSIBILITY_CHECK_ERROR) {
											unset($plausibility_check[0]);
											$error = $plausibility_check[1];
											unset($plausibility_check[1]);
											$targetname = implode(' ', $plausibility_check);
											\Froxlor\UI\Response::standard_error($error, $targetname);
										} elseif ($plausibility_check[0] == \Froxlor\Validate\Check::FORMFIELDS_PLAUSIBILITY_CHECK_QUESTION) {
											unset($plausibility_check[0]);
											$question = $plausibility_check[1];
											unset($plausibility_check[1]);
											$targetname = implode(' ', $plausibility_check);
											if (! isset($input[$question])) {
												if (is_array($url_params) && isset($url_params['filename'])) {
													$filename = $url_params['filename'];
													unset($url_params['filename']);
												} else {
													$filename = '';
												}
												\Froxlor\UI\HTML::askYesNo($question, $filename, array_merge($url_params, $submitted_fields, array(
													$question => $question
												)), $targetname);
											}
										} else {
											\Froxlor\UI\Response::standard_error('plausibilitychecknotunderstood');
										}
									}
								}
							}
						}
					}
				}
			}

			foreach ($form['groups'] as $groupname => $groupdetails) {
				if (($settings_part && $part == $groupname) || $settings_all || $only_enabledisable) {
					if (\Froxlor\Validate\Form::validateFieldDefinition($groupdetails)) {
						// Save fields
						foreach ($groupdetails['fields'] as $fieldname => $fielddetails) {
							if (! $only_enabledisable || ($only_enabledisable && isset($fielddetails['overview_option']))) {
								if (isset($changed_fields[$fieldname])) {
									if (($saved_field = self::saveFormField($fieldname, $fielddetails, self::manipulateFormFieldData($fieldname, $fielddetails, $changed_fields[$fieldname]))) !== false) {
										$saved_fields = array_merge($saved_fields, $saved_field);
									} else {
										\Froxlor\UI\Response::standard_error('errorwhensaving', $fieldname);
									}
								}
							}
						}
					}
				}
			}

			// Save form
			return self::saveForm($form, $saved_fields);
		}
	}

	public static function saveForm($fielddata, $newfieldvalue)
	{
		$returnvalue = '';
		if (is_array($fielddata) && isset($fielddata['save_method']) && $fielddata['save_method'] != '') {
			$returnvalue = call_user_func(array(
				'\\Froxlor\\Settings\\Store',
				$fielddata['save_method']
			), $fielddata, $newfieldvalue);
		} elseif (is_array($fielddata) && ! isset($fielddata['save_method'])) {
			$returnvalue = true;
		} else {
			$returnvalue = false;
		}
		return $returnvalue;
	}

	public static function saveFormField($fieldname, $fielddata, $newfieldvalue)
	{
		$returnvalue = '';
		if (is_array($fielddata) && isset($fielddata['save_method']) && $fielddata['save_method'] != '') {
			$returnvalue = call_user_func(array(
				'\\Froxlor\\Settings\\Store',
				$fielddata['save_method']
			), $fieldname, $fielddata, $newfieldvalue);
		} elseif (is_array($fielddata) && ! isset($fielddata['save_method'])) {
			$returnvalue = array();
		} else {
			$returnvalue = false;
		}
		return $returnvalue;
	}

	/**
	 *
	 * @deprecated
	 */
	public static function getFormGroupOutput($groupname, $groupdetails)
	{}

	public static function getFormOverviewGroupOutput($groupname, $groupdetails)
	{
		if (! isset(self::$fields['sections'])) {
			self::$fields['sections'] = array();
		}
		self::$fields['sections'][$groupname] = array(
			'title' => $groupdetails['title'],
			'nobuttons' => false,
			'activated' => true,
			'elements' => array()
		);

		if (isset($groupdetails['fields'])) {
			foreach ($groupdetails['fields'] as $fieldname => $fielddetails) {
				if (isset($fielddetails['overview_option']) && $fielddetails['overview_option'] == true) {
					if ($fielddetails['type'] != 'option' && $fielddetails['type'] != 'bool') {
						\Froxlor\UI\Response::standard_error('overviewsettingoptionisnotavalidfield');
					}

					if ($fielddetails['type'] == 'option') {
						$options_array = $fielddetails['option_options'];
						$options = '';
						foreach ($options_array as $value => $vtitle) {
							$options .= \Froxlor\UI\HTML::makeoption($vtitle, $value, Settings::Get($fielddetails['settinggroup'] . '.' . $fielddetails['varname']));
						}
						self::$fields['sections'][$groupname]['elements'][] = array(
							'title' => $fielddetails['label'],
							'type' => 'select',
							'select_var' => $options
						);
					} else {
						self::$fields['sections'][$groupname]['elements'][] = array(
							'title' => \Froxlor\Frontend\UI::getLng('admin.activated'),
							'type' => 'yesno',
							'select' => \Froxlor\UI\HTML::makeyesno($fieldname, '1', '0', Settings::Get($fielddetails['settinggroup'] . '.' . $fielddetails['varname']), false, ' form-control-sm')
						);
						self::$fields['sections'][$groupname]['activated'] = (int) Settings::Get($fielddetails['settinggroup'] . '.' . $fielddetails['varname']);
					}
				}
			}
		}

		/**
		 * this part checks for the 'websrv_avail' entry in the settings
		 * if found, we check if the current webserver is in the array.
		 * If this
		 * is not the case, we change the setting type to "hidden", #502
		 */
		if (isset($groupdetails['websrv_avail']) && is_array($groupdetails['websrv_avail'])) {
			$websrv = Settings::Get('system.webserver');
			if (! in_array($websrv, $groupdetails['websrv_avail'])) {
				self::$fields['sections'][$groupname]['title'] .= sprintf(\Froxlor\Frontend\UI::getLng('serversettings.option_unavailable_websrv'), implode(", ", $groupdetails['websrv_avail']));
				self::$fields['sections'][$groupname]['disabled'] = true;
			}
		}
	}

	public static function getFormFieldOutput($groupname, $fieldname, $fielddata)
	{
		if (is_array($fielddata) && isset($fielddata['type']) && $fielddata['type'] != '' && method_exists('\\Froxlor\\UI\\Fields', 'getFormFieldOutput' . ucfirst($fielddata['type']))) {
			$fielddata['desc'] = '';
			if (isset($fielddata['label']) && is_array($fielddata['label'])) {
				if (isset($fielddata['label']['title']) && isset($fielddata['label']['description'])) {
					$fielddata['desc'] = $fielddata['label']['description'];
					$fielddata['label'] = $fielddata['label']['title'];
				} else {
					$fielddata['label'] = implode(' ', $fielddata['label']);
				}
			}

			if (! isset($fielddata['value'])) {
				if (isset($fielddata['default'])) {
					$fielddata['value'] = $fielddata['default'];
				} else {
					$fielddata['value'] = null;
				}
			}

			/**
			 * this part checks for the 'websrv_avail' entry in the settings-array
			 * if found, we check if the current webserver is in the array.
			 * If this
			 * is not the case, we change the setting type to "hidden", #502
			 */
			$do_show = true;
			if (isset($fielddata['websrv_avail']) && is_array($fielddata['websrv_avail'])) {
				$websrv = Settings::Get('system.webserver');
				if (! in_array($websrv, $fielddata['websrv_avail'])) {
					$do_show = false;
					$fielddata['desc'] .= sprintf(\Froxlor\Frontend\UI::getLng('serversettings.option_unavailable_websrv'), implode(", ", $fielddata['websrv_avail']));
				}
			}

			// visible = Settings::Get('phpfpm.enabled') for example would result in false if not enabled
			// and therefore not shown as intended. Only check if do_show is still true as it might
			// be false due to websrv_avail
			if (isset($fielddata['visible']) && $do_show) {
				$do_show = $fielddata['visible'];
				if (! $do_show) {
					$fielddata['desc'] .= \Froxlor\Frontend\UI::getLng('serversettings.option_unavailable');
				}
			}
			// if ($do_show) {

			$fielddata['data_field'] = call_user_func(array(
				'\\Froxlor\\UI\\Fields',
				'getFormFieldOutput' . ucfirst($fielddata['type'])
			), $fieldname, $fielddata, $do_show);
			// }
			self::$fields['sections'][$groupname]['elements'][$fieldname] = $fielddata;
		}
	}

	public static function prefetchFormFieldData($fieldname, $fielddata)
	{
		$returnvalue = array();
		if (is_array($fielddata) && isset($fielddata['type']) && $fielddata['type'] != '' && method_exists('\\Froxlor\\UI\\Fields', 'prefetchFormFieldData' . ucfirst($fielddata['type']))) {
			$returnvalue = call_user_func(array(
				'\\Froxlor\\UI\\Fields',
				'prefetchFormFieldData' . ucfirst($fielddata['type'])
			), $fieldname, $fielddata);
		}
		return $returnvalue;
	}

	public static function getFormFieldData($fieldname, $fielddata, &$input)
	{
		if (is_array($fielddata) && isset($fielddata['type']) && $fielddata['type'] != '' && method_exists('\\Froxlor\\UI\\Data', 'getFormFieldData' . ucfirst($fielddata['type']))) {
			$newfieldvalue = call_user_func(array(
				'\\Froxlor\\UI\\Data',
				'getFormFieldData' . ucfirst($fielddata['type'])
			), $fieldname, $fielddata, $input);
		} else {
			if (isset($input[$fieldname])) {
				$newfieldvalue = $input[$fieldname];
			} elseif (isset($fielddata['default'])) {
				$newfieldvalue = $fielddata['default'];
			} else {
				$newfieldvalue = false;
			}
		}

		return trim($newfieldvalue);
	}

	public static function manipulateFormFieldData($fieldname, $fielddata, $newfieldvalue)
	{
		if (is_array($fielddata) && isset($fielddata['type']) && $fielddata['type'] != '' && method_exists('\\Froxlor\\UI\\Data', 'manipulateFormFieldData' . ucfirst($fielddata['type']))) {
			$newfieldvalue = call_user_func(array(
				'\\Froxlor\\UI\\Data',
				'manipulateFormFieldData' . ucfirst($fielddata['type'])
			), $fieldname, $fielddata, $newfieldvalue);
		}

		return $newfieldvalue;
	}

	public static function checkPlausibilityFormField($fieldname, $fielddata, $newfieldvalue, $allnewfieldvalues)
	{
		$returnvalue = '';
		if (is_array($fielddata) && isset($fielddata['plausibility_check_method']) && $fielddata['plausibility_check_method'] != '' && method_exists($fielddata['plausibility_check_method'][0], $fielddata['plausibility_check_method'][1])) {
			$returnvalue = call_user_func($fielddata['plausibility_check_method'], $fieldname, $fielddata, $newfieldvalue, $allnewfieldvalues);
		} else {
			$returnvalue = false;
		}
		return $returnvalue;
	}

	private static function arrayMergePrefix($array1, $key_prefix, $array2)
	{
		if (is_array($array1) && is_array($array2)) {
			if ($key_prefix != '') {
				foreach ($array2 as $key => $value) {
					$array1[$key_prefix . '_' . $key] = $value;
					unset($array2[$key]);
				}
				unset($array2);
				return $array1;
			} else {
				return array_merge($array1, $array2);
			}
		} else {
			return $array1;
		}
	}
}
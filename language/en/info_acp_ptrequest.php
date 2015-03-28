<?php
/**
*
* info_acp_ptrequest [English]
*
* @package Request Pattern
* @copyright (c) 2015 rxu
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_REQUEST_PATTERN'					=> 'Request template',
	'ACP_REQUEST_PATTERN_MANAGE'			=> 'Manage request template',
	'ACP_REQUEST_PATTERN_CONFIG'			=> 'Configuration',
	'ACP_REQUEST_PATTERN_EXPLAIN'			=> 'Here you can set, edit, reorder and delete questions for request template.',
	'ACP_REQUEST_PATTERN_CONFIG_EXPLAIN'	=> 'Here you can specify the forums in which the &laquo;Request template&raquo; <strong>will be not available</strong>.',

	'QUESTION_NAME'				=> 'Question',
	'EXPLAIN'					=> 'Explanation',
	'DELETE_MARKED_SUCESS'		=> 'Selected questions was successfully deleted',
	'DELETE_SUCESS'				=> 'All questions was successfully deleted',
	'UPDATE_SUCCESS'			=> 'Selected questions was successfully changed',
	'UPDATE_CONFIG_SUCCESS'		=> 'Settings was changed successfully',
	'UPDATE_FAIL'				=> 'No items have been selected',
	'EMPTY_QUESTION'			=> 'You did not enter a question',
	'ADD_SUCCESS'				=> 'Question was added successfully',
	'CHANGE_SELECTED'			=> 'Change selected questions',
	'EXCLUDE_FORUMS_EXPLAIN'	=> 'To select multiple forums, use appropriate for your computer and browser combination of mouse and keyboard. Excluded forums are displayed on a blue background.',
));

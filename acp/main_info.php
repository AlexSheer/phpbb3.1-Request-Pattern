<?php
/**
*
* @package phpBB Extension - Request Pattern
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\ptrequest\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\sheer\ptrequest\acp\main_module',
			'version'	=> '1.0.0',
			'title' => 'ACP_REQUEST_PATTERN',
			'modes'		=> array(
				'settings'	=> array(
					'title' => 'ACP_REQUEST_PATTERN_MANAGE',
					'auth' => 'ext_sheer/ptrequest && acl_a_board',
					'cat' => array('ACP_REQUEST_PATTERN')
				),
				'config'	=> array(
					'title' => 'ACP_REQUEST_PATTERN_CONFIG',
					'auth' => 'ext_sheer/ptrequest && acl_a_board',
					'cat' => array('ACP_REQUEST_PATTERN')
				),
			),
		);
	}
}

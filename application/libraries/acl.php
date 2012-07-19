<?php

if ( ! defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Simple config file based ACL
 *
 * @author Kevin Phillips <kevin@kevinphillips.co.nz>
 */
class Acl {

	private $_CI;
	private $acl;

	function __construct() {

		$this->_CI = & get_instance();
		$this->_CI->load->config( 'acl', TRUE );
		$this->acl = $this->_CI->config->item( 'permission', 'acl' );
	}

	/**
	 * function that checks that the user has the required permissions
	 *
	 * @param string $controller
	 * @param array $required_permissions
	 * @param integer $author_uid
	 * @return boolean
	 */
	public function has_permission( $controller, $required_permissions = array( 'delete all' ), $author_uid = NULL ) {

		/* make sure that the required permissions is an array */

		if ( ! is_array( $required_permissions ) )
			$required_permissions = explode( ',', $required_permissions );

		/* Get the vars from ci_session */
		$uid = $this->_CI->session->userdata( 'uid' );
		$user_roles = $this->_CI->session->userdata( 'roles' );

		/* Shouldn't happen but if we stick to belt and braces we should be OK */
		if ( ! $uid || ! $user_roles )
			return FALSE;

		/* set empty array */
		$permissions = array( );

		/* Load the permissions config */

		foreach ( $this->acl[ $controller ] as $actions => $roles ) {
			foreach ( $user_roles as $user_role ) {
				if ( in_array( $user_role, $roles ) )
					$permissions[ $actions ] = $roles;
			}
		}

		foreach ( $permissions as $action => $role ) {

			if ( in_array( $action, $required_permissions ) ) {
				if ( ($action == 'edit own' || $action == 'delete own' ) && ( ! isset( $author_uid ) || $author_uid != $uid ) ) {
					return FALSE;
				}

				return TRUE;
			}
		}
	}

}

/* End of application/libraries/acl.php */
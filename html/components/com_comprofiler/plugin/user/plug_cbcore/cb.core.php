<?php
/**
* Core plugin with tab classes for: Portrait and Contact Tabs for handling the core CB tab api
* @version $Id: cb.core.php 1768 2012-02-20 14:35:13Z beat $
* @package Community Builder
* @subpackage Page Title, Portrait, Contact tabs CB core plugin
* @author Beat and JoomlaJoe
* @copyright (C) Beat, JoomlaJoe, www.joomlapolis.com and various
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerFunction( 'onBeforeDeleteUser', 'onBeforeDeleteUser', 'CBfield_image' );
$_PLUGINS->registerUserFieldTypes( array( 	'checkbox'		=> 'CBfield_checkbox',
											'multicheckbox'	=> 'CBfield_select_multi_radio',
											'date'			=> 'CBfield_date',
												'datetime'			=> 'CBfield_date',
											'select'		=> 'CBfield_select_multi_radio',
											'multiselect'	=> 'CBfield_select_multi_radio',
											'emailaddress'	=> 'CBfield_email',
											'primaryemailaddress'	=> 'CBfield_email',
											'editorta'		=> 'CBfield_editorta',
											'textarea'		=> 'CBfield_textarea',
											'text'			=> 'CBfield_text',
											'integer'		=> 'CBfield_integer',
											'radio'			=> 'CBfield_select_multi_radio',
											'webaddress'	=> 'CBfield_webaddress',
											'pm'					=> 'CBfield_pm',
											'image'					=> 'CBfield_image',
											'status'				=> 'CBfield_status',
											'formatname'			=> 'CBfield_formatname',
											'predefined'			=> 'CBfield_predefined',
											'counter'			=> 'CBfield_counter',
											'connections'		=> 'CBfield_connections',
											'password'		=> 'CBfield_password',
											'hidden'		=> 'CBfield_text',
											'delimiter'		=> 'CBfield_delimiter',
											'userparams'	=> 'CBfield_userparams' ) );	// reserved, used now: 'other_types'
																							// future reserved: 'all_types'
$_PLUGINS->registerUserFieldParams();

class CBfield_text extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework;

		if ( ( $output == 'htmledit' ) && ( $reason != 'search' ) && defined( '_CB_VALIDATE_NEW' ) ) {
			if ( $field->params->get( 'fieldValidateInBrowser', 1 ) == 1 ) {
				$pregExp				=	$this->_getRegexp( $field );
				if ( $pregExp ) {
					$validationId		=	'cbvalidatefield_' . $field->name;
					$pregExpError		=	CBTxt::P( $field->params->get( 'pregexperror', 'Not a valid input' ), array( '[FIELDNAME]' => $field->title ) );				// CBTxt::T('Not a valid input')
					$_CB_framework->outputCbJQuery( 'jQuery.validator.addMethod("' . addslashes( $validationId ) . '", function(value, element) {'
					.	'	return this.optional(element) || ' . $pregExp . '.test(value);'
					.	'}, "' . addslashes( $pregExpError ) . '"); ');
	
					$value				=	$user->get( $field->name );
					return $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '', null, true, array( $this->getMetaClass( $field, array( $validationId . ':true' ) ) ) );
				}
			}
		}
		return parent::getField( $field, $user, $output, $reason, $list_compare_types );
	}
	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string                $columnName  Column to validate
	 * @param  string                $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason ) {
		$validated						=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		if ( $validated && ( $value !== '' ) && ( $value !== null ) ) {		// empty values (e.g. non-mandatory) are treated in the parent validation.
			$pregExp					=	$this->_getRegexp( $field );
			if ( $pregExp ) {
				$validated				=	preg_match( $pregExp, $value );
				if ( ! $validated ) {
					$pregExpError		=	CBTxt::P( $field->params->get( 'pregexperror', 'Not a valid input'), array( '[FIELDNAME]' => $field->title ) );				// CBTxt::T('Not a valid input')
					$this->_setValidationError( $field, $user, $reason, $pregExpError );
				}
			}
		}
		return $validated;
	}
	function _getRegexp( $field ) {
		$fieldValidateExpression		=	$field->params->get( 'fieldValidateExpression', '' );
		if ( $fieldValidateExpression != '' ) {
			$possibilities				=	array(	'singleword'		=>	'/^[a-z]*$/i',
											'multiplewords'		=>	'/^([a-z]+ *)*$/i',
											'singleaznum'		=>	'/^[a-z]+[a-z0-9_]*$/i',
											'atleastoneofeach'	=>	'/^(?=.*\d)(?=.*(\W|_))(?=.*[a-z])(?=.*[A-Z]).{6,255}$/'
										 );
			if ( isset( $possibilities[$fieldValidateExpression] ) ) {
				$pregExp				=	$possibilities[$fieldValidateExpression];
			} elseif ( $fieldValidateExpression == 'customregex' ) {
				$pregExp				=	$field->params->get( 'pregexp', '/^.*$/' );
			}
			if ( ! preg_match( "#^/(?:\\\\/|[^/\\n\\r])+/[a-z]*\$#", $pregExp ) ) {
				// it's not a valid regexp: do not use it!: 
				$pregExp				=	null;
			}
		} else {
			$pregExp					=	null;
		}
		return $pregExp;
	}
}
class CBfield_textarea extends CBfield_text {
	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		switch ( $output ) {
			case 'html':
			case 'rss':
				return str_replace( "\n", '<br />', parent::getField( $field, $user, $output, $reason, $list_compare_types ) );
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
	}
	/**
	 * converts to HTML
	 * Override to change the field type from textarea to text in case of searches.
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  string                $tag         <tag
	 * @param  string                $type        type="$type"
	 * @param  string                $value       value="$value"
	 * @param  string                $additional  'xxxx="xxx" yy="y"'  WARNING: No classes in here, use $classes
	 * @param  string                $allValues   
	 * @param  boolean               $displayFieldIcons
	 * @param  array                 $classes     CSS classes
	 * @return string                            HTML: <tag type="$type" value="$value" xxxx="xxx" yy="y" />
	 */
	function _fieldEditToHtml( &$field, &$user, $reason, $tag, $type, $value, $additional, $allValues = null, $displayFieldIcons = true, $classes = null ) {
		if ( $reason == 'search' ) {
			$type			=	'text';
		}
		return parent::_fieldEditToHtml( $field, $user, $reason, $tag, $type, $value, $additional, $allValues, $displayFieldIcons, $classes );
	}
}

class CBfield_predefined extends CBfield_text {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig;

		$value								=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( ( $field->type == 'predefined' ) && ( $ueConfig['allow_profilelink'] == 1 ) && ( $reason != 'profile' ) && ( $reason != 'edit' ) ) {
					$profileURL				=	$_CB_framework->userProfileUrl( $user->id, true );
					return '<a href="' . $profileURL . '">' . htmlspecialchars( $value ) . '</a>';
				} else {
					return htmlspecialchars( $value ); 
				}
				break;

			case 'htmledit':
				if ( $field->name == 'username' ) {
//					if ( ( $ueConfig["usernameedit"] == 1 ) || ( $user->username == '' ) || ( $_CB_framework->getUi() == 2 ) ) {
					if ( ! ( ( $ueConfig['usernameedit'] == 0 ) && ( $reason == 'edit' ) && ( $_CB_framework->getUi() == 1 ) ) ) {
						$onProfile			=	$field->profile;
						$field->profile		=	1;		// username is always "on profile" (e.g. SEF solutions in url).

						if ( defined( '_CB_VALIDATE_NEW' ) && ( $reason != 'search' ) ) {
							$version = checkJversion();
							if ($version >= 1) {
								// Joomla 1.5 and 1.6:
								$regexp		=	'[\\<|\\>|\\"|\\\'|\\%|\\;|\\(|\\)|\\&]';
							} elseif ( $version == -1 ) {
								// Mambo 4.6+:
								$regexp		=	'[^A-Za-z0-9]';
							} else {
								// Joomla 1.0 and Mambo 4.5:
								$regexp		=	'[\\<|\\>|\\"|\\\'|\\%|\\;|\\(|\\)|\\&|\\+|\\-]';
							}
							$_CB_framework->outputCbJQuery( 'jQuery.validator.addMethod("cbusername", function(value, element) {'
							.	'	return this.optional(element) || ! /' . $regexp . '/i.test(value);'
							.	'}, "' . addslashes( sprintf( CBTxt::html_entity_decode(_VALID_AZ09), CBTxt::html_entity_decode(_PROMPT_UNAME), 2 ) ) . '"); ');
	
							if ( ( $ueConfig['reg_username_checker'] == 1 ) || ( $_CB_framework->getUi() == 2 ) ) {
								$html		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '', null, true, array( 'cbusername', $this->ajaxCheckField( $field, $user, $reason, array( 'cbusername:true' ) ) ) );
							} else {
								$html		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '', null, true, array( 'cbusername', $this->getMetaClass( $field, array( 'cbusername:true' ) ) ) );
							}
						} else {
							$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '' );
							if ( ( ( $ueConfig['reg_username_checker'] == 1 ) || ( $_CB_framework->getUi() == 2 ) )
								&& ( $reason != 'search' ) )
							{
								$this->ajaxCheckField( $field, $user, $reason );
							}
						}
						$field->profile		=	$onProfile;
					} else {
						$html				=	htmlspecialchars( $value )
											.	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'hidden', $value, '' );
					}
				} else {
					$html					=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '' );
				}
				if ( $reason == 'search' ) {
					$html					=	$this->_fieldSearchModeHtml( $field, $user, $html, 'text', $list_compare_types );
				}
				return $html;
				break;

			default:
				return $this->_formatFieldOutput( $field->name, $value, $output );
				break;
		}
	}
	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check
	 * that the logged-in user has rights to edit that $user.
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  array                 $postdata
	 * @param  string                $reason     'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string                            Expected output.
	 */
	function fieldClass( &$field, &$user, &$postdata, $reason ) {
		global $_CB_framework, $_CB_database, $ueConfig, $_GET;

		parent::fieldClass( $field, $user, $postdata, $reason );		// performs spoofcheck.

		$html					=	null;
		$function				=	cbGetParam( $_GET, 'function', '' );
		if ( $function == 'checkvalue' ) {
			$username			=	stripslashes( cbGetParam( $postdata, 'value', '' ) );
			$usernameISO		=	CBTxt::utf8ToISO( $username );			// ajax sends in utf8, we need to convert back to the site's encoding.
	
			$function			=	'testnotexists';
			if ( ( ( $ueConfig['reg_username_checker'] == 1 ) || ( $_CB_framework->getUi() == 2 ) )
				&& ( ( $reason == 'edit' ) || ( $reason == 'register' ) ) )
			{
				if ( ( ! $user ) || ( $usernameISO != $user->username ) ) {

					if ( ! $this->validate( $field, $user, 'username', $usernameISO, $postdata, $reason ) ) {
						global $_PLUGINS;
						$html			=	'<span class="cb_result_error">' . $_PLUGINS->getErrorMSG( '<br />' ) . '</span>';
					} else {
						if ( $_CB_database->isDbCollationCaseInsensitive() ) {
							$query	=	"SELECT COUNT(*) AS result FROM #__users WHERE username = " . $_CB_database->Quote( ( trim( $usernameISO ) ) );
						} else {
							$query	=	"SELECT COUNT(*) AS result FROM #__users WHERE LOWER(username) = " . $_CB_database->Quote( ( strtolower( trim( $usernameISO ) ) ) );
						}
						$_CB_database->setQuery($query);
						$dataObj			=	null;
						if ( $_CB_database->loadObject( $dataObj ) ) {
							if ( $dataObj->result ) {
								// funily, the output does not need to be UTF8 again:
								if ( $function == 'testexists' ) {
									$html	=	( '<span class="cb_result_ok">' . sprintf( ISOtoUtf8( _UE_USERNAME_EXISTS_ON_SITE ), htmlspecialchars( $username ) ) . '</span>' );
								} else {
									$html	=	( '<span class="cb_result_error">' . sprintf( ISOtoUtf8( _UE_USERNAME_ALREADY_EXISTS ), htmlspecialchars( $username ) ) . '</span>' );
								}
							} else {
								if ( $function == 'testexists' ) {
									$html	=	( '<span class="cb_result_error">' . sprintf( ISOtoUtf8( _UE_USERNAME_DOES_NOT_EXISTS_ON_SITE ), htmlspecialchars( $username ) ) . '</span>' );
								} else {
									if ( $reason == 'register' ) {
										$html	=	( '<span class="cb_result_ok">' . sprintf( ISOtoUtf8( _UE_USERNAME_DOESNT_EXISTS ), htmlspecialchars( $username ) ) . '</span>' );
									} else {
										$html	=	( '<span class="cb_result_ok">' . sprintf( ISOtoUtf8( _UE_USERNAME_FREE_OK_TO_PROCEED ), htmlspecialchars( $username ) ) . '</span>' );
									}
								}
							}
						} else {
							$html			=	( '<span class="cb_result_error">' . ISOtoUtf8( _UE_SEARCH_ERROR ) . ' !' . '</span>' );
						}
					}
				} else {
					if ( $user && ( $user->id == $_CB_framework->myId() ) ) {
						$html			=	( '<span class="cb_result_ok">' . sprintf( ISOtoUtf8( _UE_THIS_IS_YOUR_USERNAME ), htmlspecialchars( $username ) ) . '</span>' );
					} else {
						$html			=	( '<span class="cb_result_ok">' . sprintf( ISOtoUtf8( _UE_THIS_IS_USERS_USERNAME ), htmlspecialchars( $username ) ) . '</span>' );
					}
				}
			} else {
				$html					=	ISOtoUtf8( _UE_NOT_AUTHORIZED );
			}
		}
		return $html;
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $_CB_framework, $ueConfig;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		switch ( $field->name ) {
			case 'username':
				if ( ! ( ( $ueConfig['usernameedit'] == 0 ) && ( $reason == 'edit' ) && ( $_CB_framework->getUi() == 1 ) ) ) {
					$username				=	stripslashes( cbGetParam( $postdata, 'username', null ) );
					$fieldMinLength			=	$this->getMinLength( $field );
					if ( cbIsoUtf_strlen( $username ) < $fieldMinLength ) {
						$this->_setValidationError( $field, $user, $reason, sprintf( _UE_VALID_UNAME, _UE_UNAME, $fieldMinLength ) );
					} else {
						if ( $this->validate( $field, $user, $field->name, $username, $postdata, $reason ) ) {
							if ( ( $username !== null ) && ( $username !== $user->username ) ) {
								$this->_logFieldUpdate( $field, $user, $reason, $user->username, $username );
							}
						}
					}
					if ( $username !== null ) {
						$user->username		=	$username;
					}
				}
				break;

			case 'name':
			case 'firstname':
			case 'middlename':
			case 'lastname':
				$value							=	stripslashes( cbGetParam( $postdata, $field->name ) );
				$col							=	$field->name;
				if ( $value !== null ) {
					// Form name from first/middle/last name if needed:
					if ( $field->name !== 'name' ) {
						$nameArr				=	array();
						if ( $ueConfig['name_style'] >= 2 ) {
							$firstname		=	stripslashes( cbGetParam( $postdata, 'firstname' ) );
							if ( $firstname ) {
								$nameArr[]	=	 $firstname;
							}
							if ( $ueConfig['name_style'] == 3 ) {
								$middlename	=	stripslashes( cbGetParam( $postdata, 'middlename' ) );
								if ( $middlename ) {
									$nameArr[]	=	$middlename;
								}
							}
							$lastname		=	stripslashes( cbGetParam( $postdata, 'lastname' ) );
							if ( $lastname ) {
								$nameArr[]	=	$lastname;
							}
						}
						if ( count( $nameArr ) > 0 ) {
							$user->name			=	implode( ' ', $nameArr );
						}
					}
				}
				if ( ( $value == '' ) && $field->required ) {
					/* $nameTitles			=	array(	'name'			=> _UE_YOUR_NAME,
													'firstname'		=> _UE_YOUR_FNAME,
													'middlename'	=> _UE_YOUR_MNAME,
													'lastname'		=> _UE_YOUR_LNAME );
					$this->_setValidationError( $nameTitles[$field->name] . ' : '. cbUnHtmlspecialchars( _UE_REQUIRED_ERROR ) );
					*/
					$this->_setValidationError( $field, $user, $reason, cbUnHtmlspecialchars( _UE_REQUIRED_ERROR ) );
				} else {
					if ( $this->validate( $field, $user, $field->name, $value, $postdata, $reason ) ) {
						if ( ( (string) $user->$col ) !== (string) $value ) {
							$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
						}
					}
				}
				if ( $value !== null ) {
					$user->$col					=	$value;
				}
				break;

			default:
				$this->_setValidationError( $field, $user, $reason, sprintf(CBTxt::T("Unknown field %s"), $field->name) );
				break;
		}
	}
	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string                $columnName  Column to validate
	 * @param  string                $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason ) {
		$validated			=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		if ( $validated ) {
			if ( $field->name == 'username' ) {
				$version = checkJversion();
				if ($version >= 1) {
					// "^[a-zA-Z](([\.\-a-zA-Z0-9@])?[a-zA-Z0-9]*)*$", "i");
					// $regex		=	'/^[\\<|\\>|"|\'|\\%|\\;|\\(|\\)|\\&|\\+|\\-]*$/i';
					$regex		=	'/^[\\<|\\>|"|\\\'|\\%|\\;|\\(|\\)|\\&]*$/i';
				} elseif ( $version == -1 ) {
					$regex		=	"[^A-Za-z0-9]";
				} else {
					$regex		=	'/[\\<|\\>|"|\'|\\%|\\;|\\(|\\)|\\&|\\+|\\-]/i';
				}
				$validated		=	! preg_match( $regex, $value );
				if ( ! $validated ) {
					$this->_setValidationError( $field, $user, $reason, sprintf( CBTxt::html_entity_decode(_VALID_AZ09), CBTxt::html_entity_decode(_PROMPT_UNAME), 2 ) );
				}
			}
		}
		return $validated;
	}
	/**
	 * Returns the minimum field length as set
	 * 
	 * @param  moscomprofilerFields  $field
	 * @return int
	 */
	function getMinLength( $field ) {
		$min						=	parent::getMinLength( $field );
		if ( ( ! $min ) && ( $field->name == 'username' ) ) {
			$min					=	3;
		}
		return $min;
	}
	/**
	 * Returns the maximum field length as set
	 * 
	 * @param  moscomprofilerFields  $field
	 * @return int
	 */
	function getMaxLength( $field ) {
		$maxLen						=	parent::getMaxLength( $field );
		if ( $maxLen ) {
			return $maxLen;
		}
		if ( $field->name == 'username' ) {
			return 150;
		} else {
			return 100;
		}
	}
}
class CBfield_password extends CBfield_text {
	/**
	 * Returns a PASSWORD field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output      'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $formatting  'table', 'td', 'span', 'div', 'none'
	 * @param  string                $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types ) {
		global $ueConfig, $_CB_OneTwoRowsStyleToggle;

		$results								=	null;

		if ( $output == 'htmledit' ) {

			if ( ( $field->name != 'password' ) || ( $reason != 'register' ) || ! ( isset( $ueConfig['emailpass'] ) && ( $ueConfig['emailpass'] == "1" ) ) ) {

				$verifyField					=	new moscomprofilerFields( $field->_db );
				foreach ( array_keys( get_object_vars( $verifyField ) ) as $k ) {
					$verifyField->$k			=	$field->$k;
				}
				$verifyField->name				=	$field->name . '__verify';
				$verifyField->fieldid			=	$field->fieldid . '__verify';
				if ( $field->name == 'password' ) {
					$verifyField->title			=	_UE_VPASS;
				} else {
					$verifyField->title			=	sprintf( getLangDefinition( $field->params->get( 'verifyPassTitle', '_UE_VERIFY_SOMETHING' ) ), getLangDefinition( $field->title ) );	// cbReplaceVars to be done only once later
				}
				$verifyField->_identicalTo		=	$field->name;
	
				$toggleState					=	$_CB_OneTwoRowsStyleToggle;
				$results						=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
				$_CB_OneTwoRowsStyleToggle		=	$toggleState;		// appear as in same row
				$results						.=	parent::getFieldRow( $verifyField, $user, $output, $formatting, $reason, $list_compare_types );
	
				unset( $verifyField );

			} else {
				// case of "sending password by email" at registration time for main password field:
				$results						=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
			}
		} else {
			$results							=	parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}
		return $results;
	}
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $ueConfig;

		$value									=	'';			// passwords are never sent back to forms.

		switch ( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					return null;
				}

			if ( ( $field->name != 'password' ) || ( $reason != 'register' ) || ! ( isset( $ueConfig['emailpass'] ) && ( $ueConfig['emailpass'] == "1" ) ) ) {

					$req							=	$field->required;
					if ( ( $reason == 'edit' ) && in_array( $field->name, array( 'password', 'password__verify' ) ) ) {
						$field->required			=	0;
					}

					$html							=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $field->type, $value, '', null, true, array( $this->getMetaClass( $field ) ) );
					$field->required				=	$req;

				} else {
					// case of "sending password by email" at registration time for main password field:
					$html							=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'html', _SENDING_PASSWORD, '' );
				}
				return $html;
				break;

			case 'html':
				return CBTxt::T('********');
				break;
			default:
				return null;
				break;
		}
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $_CB_framework, $ueConfig;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		// For CB main password don't save if it's on registration and passwords are auto-generated.
		if ( ( $reason == 'register' ) && ( $field->name == 'password' ) ) {
			if ( isset( $ueConfig['emailpass'] ) && ( $ueConfig['emailpass'] == "1" ) ) {
				return;
			}
		}

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	stripslashes( cbGetParam( $postdata, $col,				'', _CB_ALLOWRAW ) );
			$valueVerify			=	stripslashes( cbGetParam( $postdata, $col . '__verify',	'', _CB_ALLOWRAW ) );

			if ( ( ( $reason == 'edit' ) && ( $user->id != 0 ) && ( $user->$col || ( $field->name == 'password' ) ) ) || ( $_CB_framework->getUi() == 2 ) ) {
				$fieldRequired		=	$field->required;
				$field->required	=	0;
			}
			$this->validate( $field, $user, $col, $value, $postdata, $reason );

			if ( ( ( $reason == 'edit' ) && ( $user->id != 0 ) && ( $user->$col || ( $field->name == 'password' ) ) ) || ( $_CB_framework->getUi() == 2 ) ) {
				$field->required	=	$fieldRequired;
			}

			$fieldMinLength			=	$this->getMinLength( $field );

			$user->$col				=	null;		// don't update unchanged (hashed) passwords unless typed-in and all validates:
			if ( $value ) {
				if ( cbIsoUtf_strlen( $value ) < $fieldMinLength ) {
					$this->_setValidationError( $field, $user, $reason, sprintf( _UE_VALID_PASS_CHARS, _UE_PASS, $fieldMinLength ) );
				} elseif ( $value != $valueVerify ) {
					$this->_setValidationError( $field, $user, $reason, _UE_REGWARN_VPASS2 );
				} else {
					// There is no event for password changes on purpose here !
					$user->$col		=	$value;			// store only if validated
				}
			}
		}
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason ) {
		return array();
	}
	/**
	 * Returns the minimum field length as set
	 * 
	 * @param  moscomprofilerFields  $field
	 * @return int
	 */
	function getMinLength( $field ) {
		$defaultMin					=	6;
		return $field->params->get( 'fieldMinLength', $defaultMin );
	}
}
class CBfield_select_multi_radio extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		$value					=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( $value != '' ) {
					$chosen			=	$this->_explodeCBvalues( $value );
				} else {
					$chosen			=	array();
				}
				$class				=	trim( $field->params->get( 'field_display_class' ) );
				$displayStyle		=	$field->params->get( 'field_display_style' );
				$listType			=	( $displayStyle == 1 ? 'ul' : ( $displayStyle == 2 ? 'ol' : ', ' ) );
				for( $i = 0, $n = count( $chosen ); $i < $n; $i++ ) {
	   				$chosen[$i]		=	getLangDefinition( $chosen[$i] );
				}
				return $this->_arrayToFormat( $field, $chosen, $output, $listType, $class );
				break;

			case 'htmledit':
				global $_CB_database;

				$_CB_database->setQuery( "SELECT fieldtitle AS `value`, fieldtitle AS `text`, concat('cbf',fieldvalueid) AS id FROM #__comprofiler_field_values"		// id needed for the labels
										. "\n WHERE fieldid = " . (int) $field->fieldid
										. "\n ORDER BY ordering" );
				$allValues		=	$_CB_database->loadObjectList();
/*
				if ( $reason == 'search' ) {
					array_unshift( $allValues, $this->_valueDoesntMatter( $field, $reason, ( $field->type == 'multicheckbox' ) ) );
					if ( ( $field->type == 'multicheckbox' ) && ( $value === null ) ) {
						$value	=	array( null );			// so that "None" is really not checked if not checked...
					}
				}
*/
				if ( $reason == 'search' ) {
//					$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'multicheckbox', $value, '', $allValues );
					$displayType	=	$field->type;
					switch ( $field->type ) {
						case 'radio':
							if ( in_array( $list_compare_types, array( 0, 2 ) ) || ( is_array( $value ) && ( count( $value ) > 1 ) ) ) {
								$displayType	=	'multicheckbox';
							}
							$jqueryclass		=	'cb__js_' . $field->type;
							break;
					
						case 'select':
							$jqueryclass		=	'cb__js_' . $field->type;
							break;
					
						default:
							$jqueryclass		=	'';
							break;
					}
					if ( in_array( $list_compare_types, array( 0, 2 ) ) && ( $displayType != 'multicheckbox' ) ) {
						array_unshift( $allValues, moscomprofilerHTML::makeOption( '', _UE_NO_PREFERENCE ) );
					}
					$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $displayType, $value, '', $allValues );
					$html			=	$this->_fieldSearchModeHtml( $field, $user, $html, ( strpos( $displayType, 'multi' ) === 0 ? 'multiplechoice' : 'singlechoice' ), $list_compare_types, $jqueryclass );
				} else {
					$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $field->type, $value, '', $allValues );
				}
				return $html;
				break;

			case 'xml':
			case 'json':
			case 'php':
				if ( substr( $reason, -11 ) == ':translated' ) {
					// Translated version in case reason finishes by :translated: (will be used later):
					if ( in_array( $field->type, array( 'radio', 'select' ) ) ) {
						$chosen			=	getLangDefinition( $value );
					} else {
						// multiselect, multicheckbox:
						$chosen			=	$this->_explodeCBvalues( $value );
						for( $i = 0, $n = count( $chosen ); $i < $n; $i++ ) {
			   				$chosen[$i]	=	getLangDefinition( $chosen[$i] );
						}
					}
					return $this->_arrayToFormat( $field, $chosen, $output );
					break;
				}
				// else: fall-through on purpose here (fixes bug #2960):
			case 'csv':
				if ( in_array( $field->type, array( 'radio', 'select' ) ) ) {
					$chosen			=	$value;
				} else {
					// multiselect, multicheckbox:
					$chosen			=	$this->_explodeCBvalues( $value );
				}
				return $this->_arrayToFormat( $field, $chosen, $output );
				break;

			case 'csvheader':
			case 'fieldslist':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return '*' . CBTxt::T('Unknown Output Format') . '*';
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $_CB_database;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value						=	cbGetParam( $postdata, $col, null, _CB_ALLOWRAW );
//			if ( $value === null ) {
//				$value				=	array();
//			} elseif ( $field->type == 'radio' ) {
//				$value				=	array( $value );
//			}

			if ( is_array( $value ) ) {
				if ( count( $value ) > 0 ) {
	
					$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
											. "\n WHERE fieldid = " . (int) $field->fieldid
											. "\n ORDER BY ordering" );
					$authorizedValues	=	$_CB_database->loadResultArray();

					$okVals				=	array();
					foreach ( $value as $k => $v ) {
						// revert escaping of cbGetParam:
						$v				=	stripslashes( $v );
						// check authorized values:
						if ( in_array( $v, $authorizedValues, true ) && ! in_array( $v, $okVals, true ) ) {		// in case a value appears multiple times in a multi-field !
							$okVals[$k]	=	$v;
						}
					}
					$value				=	$this->_implodeCBvalues( $okVals );
				} else {
					$value				=	'';
				}
			} elseif ( ( $value === null ) || ( $value === '' ) ) {
				$value					=	'';
			} else {
				$value					=	stripslashes( $value );	// compensate for cbGetParam.
				$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
											. "\n WHERE fieldid = " . (int) $field->fieldid
											. "\n AND fieldtitle = " . $_CB_database->Quote( $value ) );
				$authorizedValues	=	$_CB_database->loadResultArray();
				if ( ! in_array( $value, $authorizedValues, true ) ) {
					$value			=	null;
				}
			}
			if ( $this->validate( $field, $user, $col, $value, $postdata, $reason ) ) {
				if ( isset( $user->$col ) && ( (string) $user->$col ) !== (string) $value ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
			}
			$user->$col				=	$value;
		}
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		global $_CB_database;

		$displayType						=	$field->type;
		if ( ( $field->type == 'radio' ) && in_array( $list_compare_types, array( 0, 2 ) ) ) {
			$displayType	=	'multicheckbox';
		}

		$query								=	array();
		$searchMode							=	$this->_bindSearchMode( $field, $searchVals, $postdata, ( strpos( $displayType, 'multi' ) === 0 ? 'multiplechoice' : 'singlechoice' ), $list_compare_types );
		if ( $searchMode ) {
			foreach ( $field->getTableColumns() as $col ) {
				$value						=	cbGetParam( $postdata, $col );
				if ( is_array( $value ) ) {
					if ( count( $value ) > 0 ) {
						$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
												. "\n WHERE fieldid = " . (int) $field->fieldid
												. "\n ORDER BY ordering" );
						$authorizedValues	=	$_CB_database->loadResultArray();
		
						foreach ( $value as $k => $v ) {
							if ( ( count( $value ) == 1 ) && ( $v === '' ) ) {
								if ( $list_compare_types == 1 ) {
									$value		=	'';		// Advanced search: "None": checked: search for nothing selected
								} else {
									$value		=	null;	// Type 0 and 2 : Simple search: "Do not care" checked: do not search
								}
								break;
							}
							// revert escaping of cbGetParam:
							$v				=	stripslashes( $v );
							// check authorized values:
							if ( in_array( $v, $authorizedValues ) ) {
								$value[$k]	=	$v;
							} else {
								unset( $value[$k] );
							}
						}
		
					} else {
						$value				=	null;
					}
					if ( ( $value !== null ) && ( $value !== '' ) && in_array( $searchMode, array( 'is', 'isnot' ) ) ) {		// keep $value array if search is not strict
						$value				=	stripslashes( $this->_implodeCBvalues( $value ) );	// compensate for cbGetParam.
					}
				} else {
					if ( ( $value !== null ) && ( $value !== '' ) ) {
						$value					=	stripslashes( $value );	// compensate for cbGetParam.
						$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
													. "\n WHERE fieldid = " . (int) $field->fieldid
													. "\n AND fieldtitle = " . $_CB_database->Quote( $value ) );
						$authorizedValues	=	$_CB_database->loadResultArray();
						if ( ! in_array( $value, $authorizedValues ) ) {
							$value			=	null;
						}
					} else {
						if ( ( $list_compare_types == 1 ) && in_array( $searchMode, array( 'is', 'isnot' ) ) ) {
							$value			=	'';
						} else {
	//					if ( ( $field->type == 'multicheckbox' ) && ( $value === null ) ) {
							$value			=	null;				// 'none' is not checked and no other is checked: search for DON'T CARE
						}
					}
				}
				if ( $value !== null ) {
					$searchVals->$col		=	$value;
					// $this->validate( $field, $user, $col, $value, $postdata, $reason );
					$sql					=	new cbSqlQueryPart();
					$sql->tag				=	'column';
					$sql->name				=	$col;
					$sql->table				=	$field->table;
					$sql->type				=	'sql:field';
					$sql->operator			=	'=';
					$sql->value				=	$value;
					$sql->valuetype			=	'const:string';
					$sql->searchmode		=	$searchMode;
					$query[]				=	$sql;
				}
			}
		}
		return $query;
	}
}
class CBfield_checkbox extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		$value			=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( $value == 1 ) {
					return _UE_YES; 
				} elseif ( $value == 0 ) {
					return _UE_NO;
				} else {
					return null;
				}
				break;

			case 'htmledit':
				if ( $reason == 'search' ) {
					$choices	=	array();
					$choices[]	=	moscomprofilerHTML::makeOption( '', _UE_NO_PREFERENCE );
					$choices[]	=	moscomprofilerHTML::makeOption( '1', _UE_YES );
					$choices[]	=	moscomprofilerHTML::makeOption( '0', _UE_NO );
					$html		=	'<div class="cbSingleCntrl">' . $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '', $choices ) . '</div>';
					$html		=	$this->_fieldSearchModeHtml( $field, $user, $html, 'singlechoice', $list_compare_types );
					return $html;
				} else {
					$checked		=	'';
					if ( $value == 1 ) {
						$checked	=	' checked="checked"';
					}
					return '<div class="cbSingleCntrl">' . $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'checkbox', '1', $checked ) . '</div>';
				}
				break;

			case 'json':
				return "'" . $field->name . "' : " . (int) $value;
				break;

			case 'php':
				return array( $field->name => (int) $value );
				break;

			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return '*' . CBTxt::T('Unknown Output Format') . '*';
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	stripslashes( cbGetParam( $postdata, $col ) );

			if ( $value == '' ) {
				$value				=	0;
			} elseif ( $value == '1' ) {
				$value				=	1;
			}
			$validated				=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
			if ( ( $value === 0 ) || ( $value === 1 ) ) {
				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
			}
			$user->$col				=	$value;
		}
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		$query							=	array();
		$searchMode						=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );
		if ( $searchMode ) {
			foreach ( $field->getTableColumns() as $col ) {
				$value						=	stripslashes( cbGetParam( $postdata, $col ) );
				if ( $value === '0' ) {
					$value				=	0;
				} elseif ( $value == '1' ) {
					$value				=	1;
				} else {
					continue;
				}
				$searchVals->$col		=	$value;
				// $this->validate( $field, $user, $col, $value, $postdata, $reason );
				$sql					=	new cbSqlQueryPart();
				$sql->tag				=	'column';
				$sql->name				=	$col;
				$sql->table				=	$field->table;
				$sql->type				=	'sql:field';
				$sql->operator			=	'=';
				$sql->value				=	$value;
				$sql->valuetype			=	'const:int';
				$sql->searchmode		=	$searchMode;
				$query[]				=	$sql;
			}
		}
		return $query;
	}
}
/**
 * Basic CB integer field extender.
 */
class CBfield_integer extends CBfield_text {
	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerField  $field
	 * @param  moscomprofilerUser   $user
	 * @param  string               $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string               $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                  $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		$value						=	$user->get( $field->name );

		switch ( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					$minNam			=	$field->name . '__minval';
					$maxNam			=	$field->name . '__maxval';

					$minVal			=	$user->get( $minNam );
					$maxVal			=	$user->get( $maxNam );

					$fieldNameSave	=	$field->name;
					$field->name	=	$minNam;
					$minHtml		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $minVal, '' );
					$field->name	=	$maxNam;
					$maxHtml		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $maxVal, '' );
					$field->name	=	$fieldNameSave;
					$ret			=	$this->_fieldSearchRangeModeHtml( $field, $user, $output, $reason, $value, $minHtml, $maxHtml, $list_compare_types );

				} else {
					$ret			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '' );
				}
				break;
			case 'html':
			case 'rss':
			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				$ret				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return $ret;
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	cbGetParam( $postdata, $col );
			if ( ! is_array( $value ) ) {
				$value				=	stripslashes( $value );
				$validated			=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
				if ( $value === '' ) {
					$value			=	null;
				} else {
					$value			=	(int) $value;		// int conversion to sanitize input.
				}
				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
				$user->$col		=	$value;
			}
		}
	}

	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string                $columnName  Column to validate
	 * @param  string                $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason ) {
		$validated					=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		if ( $validated && ( $value !== '' ) && ( $value !== null ) ) {		// empty values (e.g. non-mandatory) are treated in the parent validation.
			$validated				=	preg_match( '/^[-0-9]*$/', $value );
			if ( $validated ) {
				// check range:
				$min				=	(int) $field->params->get( 'integer_minimum', '0' );
				$max				=	(int) $field->params->get( 'integer_maximum', '1000000' );
				if ( $max < $min ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T('Min setting > Max setting !') );
					$validated		=	false;
				}
				if ( ( ( (int) $value ) < $min ) || ( ( (int) $value ) > $max ) ) {
					$this->_setValidationError( $field, $user, $reason, sprintf( _UE_YEAR_NOT_IN_RANGE, (int) $value, (int) $min, (int) $max ) );		// using that year string, as we don't have a general one.
					$validated		=	false;
				}
				if ( $validated ) {
					// check for forbidden values as integers:
					$forbiddenContent			=	$field->params->get( 'fieldValidateForbiddenList_' . $reason, '' );
					if ( $forbiddenContent != '' ) {
						$forbiddenContent		=	explode( ',', $forbiddenContent );
						if ( in_array( (string) ( (int) $value ), $forbiddenContent ) ) {
							$this->_setValidationError( $field, $user, $reason, _UE_INPUT_VALUE_NOT_ALLOWED );
							$validated			=	false;
						}
					}
				}
			} else {
				$this->_setValidationError( $field, $user, $reason, CBTxt::T('Not an integer') );
			}
		}
		return $validated;
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		$query							=	array();
		foreach ( $field->getTableColumns() as $col ) {
			$minNam						=	$col . '__minval';
			$maxNam						=	$col . '__maxval';
			$searchMode					=	$this->_bindSearchRangeMode( $field, $searchVals, $postdata, $minNam, $maxNam, $list_compare_types );
			if ( $searchMode ) {
				$minVal					=	(int) cbGetParam( $postdata, $minNam, 0 );
				$maxVal					=	(int) cbGetParam( $postdata, $maxNam, 0 );

				if ( $minVal && ( cbGetParam( $postdata, $minNam, '' ) !== '' ) ) {
					$searchVals->$minNam =	$minVal;
					$query[]			=	$this->_intToSql( $field, $col, $minVal, '>=', $searchMode );
				}
				if ( $maxVal && ( cbGetParam( $postdata, $maxNam, '' ) !== '' ) ) {
					$searchVals->$maxNam =	$maxVal;
					$query[]			=	$this->_intToSql( $field, $col, $maxVal, '<=', $searchMode );
				}
			}
		}
		return $query;
	}
	function _intToSql( &$field, $col, $value, $operator, $searchMode ) {
		$value							=	(int) $value;
		// $this->validate( $field, $user, $col, $value, $postdata, $reason );
		$sql							=	new cbSqlQueryPart();
		$sql->tag						=	'column';
		$sql->name						=	$col;
		$sql->table						=	$field->table;
		$sql->type						=	'sql:field';
		$sql->operator					=	$operator;
		$sql->value						=	$value;
		$sql->valuetype					=	'const:int';
		$sql->searchmode				=	$searchMode;
		return $sql;
	}
}

class CBfield_date extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		$value								=	$user->get( $field->name );
		
		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( ( $value != '' ) && ( $value != '0000-00-00 00:00:00' ) && ( $value != '0000-00-00' ) ) {
					$display_by				=	$field->params->get( 'field_display_by', 0 );
					if ( $display_by == 1 ) {

						// display by years:
						list($yb, $cb, $db)	=	sscanf($value, '%d-%d-%d');
						list($yn, $cn, $dn)	=	sscanf(date('Y-m-d'), '%d-%d-%d');
						$age				=	(int) ( $yn - $yb );
						if ( ( $cb > $cn ) || ( ( $cb == $cn ) && ( $db > $dn ) ) ) {
							$age			-=	1;
						}
						if ( $age < 0 ) {
							$age			=	null;
						}
						return ( $field->params->get( 'field_display_years_text', 1 ) ? sprintf( _UE_AGE_YEARS, $age ) : $age );

					} elseif ( $display_by == 2 ) {

						// display by ago:
						return $this->_ago( $value, $field->params->get( 'field_display_ago_text', 1 ), true );

					} elseif ( $display_by == 3 ) {

						// display birthday without the year:
						global $ueConfig;

						if ( ( $value != '' ) && ( $value != null ) && ( $value != '0000-00-00 00:00:00' ) && ( $value != '0000-00-00' ) ) {
							if ( strlen( $value ) > 10 ) {
								$value = _old_cbFormatDate( $value, "%m-%d" );		// offsets datetime with server offset setting
							}
							$value			=	substr( $value, 5, 5 );

							$month			=	substr( $value, 0, 2 );
							if ( defined( '_UE_MONTHS_' . (int) $month ) ) {
								$value		=	'MM' . substr( $value, 2 );
							}
							$convert		=	array(	'Y/m/d' => 'm/d',
														'd/m/y' => 'd/m',
														'y/m/d' => 'm/d',
														'd/m/Y' => 'd/m',
														'm/d/y' => 'm/d',
														'm/d/Y' => 'm/d',
														'Y-m-d' => 'm/d',
														'd-m-y' => 'd/m',
														'y-m-d' => 'm/d',
														'd-m-Y' => 'd/m',
														'm-d-y' => 'm/d',
														'm-d-Y' => 'm/d',
														'Y.m.d' => 'm/d',
														'd.m.y' => 'd/m',
														'y.m.d' => 'm/d',
														'd.m.Y' => 'd/m',
														'm.d.y' => 'm/d',
														'm.d.Y' => 'm/d' );
							if ( isset( $convert[$ueConfig['date_format']] ) ) {
								$format		=	$convert[$ueConfig['date_format']];
							} else {
								$format		=	'm/d';
							}
							$value			=	dateConverter( $value, 'm-d', $format );
							if ( defined( '_UE_MONTHS_' . (int) $month ) ) {
								$value		=	str_replace( array( 'MM', '/' ), array( constant( '_UE_MONTHS_' . (int) $month ), ' ' ), $value );
							}
							return $value;
						} else {
							return '';
						}
					} else {
						$date_time    		= $field->params->get( 'show_date_time', 0 );
						return htmlspecialchars( cbFormatDate( $value, 1, ( $date_time == 1 ? false : true ) ) );
					}
				} else {
					return '';
				}
				break;

			case 'htmledit':
				global $_CB_framework;
				$calendars					=	new cbCalendars( $_CB_framework->getUi() );
				if ( $reason == 'search' ) {
					$minNam					=	$field->name . '__minval';
					$maxNam					=	$field->name . '__maxval';

					$minVal					=	$user->get( $minNam );
					$maxVal					=	$user->get( $maxNam );

					$search_by				=	$field->params->get( 'field_search_by', 0 );

					list( $yMin, $yMax )	=	$this->_yearsRange( $field, $search_by );

					if ( $search_by == 1 ) {
						// Search by age range:
						$choices			=	array();
						$choices			=	array();
						for ( $i = $yMin ; $i <= $yMax ; $i++ ) {
							$choices[]		=	moscomprofilerHTML::makeOption( $i, $i );
						}
						if ( $minVal === null ) {
							$minVal				=	$yMin;
						}
						if ( $maxVal === null ) {
							$maxVal				=	$yMax;
						}
						$additional			=	' class="inputbox"';
						$minHtml	=	moscomprofilerHTML::selectList( $choices, $minNam, $additional, 'text', 'value', $minVal, 2 );
						$maxHtml	=	moscomprofilerHTML::selectList( $choices, $maxNam, $additional, 'text', 'value', $maxVal, 2 );
					} else {
						if ( $minVal !== null ) {
							$minVal	=	date( 'Y-m-d', strtotime( $minVal ) );
						}
						if ( $maxVal !== null ) {
							$maxVal	=	date( 'Y-m-d', strtotime( $maxVal ) );
						}

						// Search by date range:
						$minHtml	=	$calendars->cbAddCalendar( $minNam, _UE_SEARCH_FROM . ' ' . $this->getFieldTitle( $field, $user, 'text', $reason ), false, $minVal, false, false, $yMin, $yMax );
						$maxHtml	=	$calendars->cbAddCalendar( $maxNam, _UE_SEARCH_TO . ' ' . $this->getFieldTitle( $field, $user, 'text', $reason ), false, $maxVal, false, false, $yMin, $yMax );
					}
					$html	=	$this->_fieldSearchRangeModeHtml( $field, $user, $output, $reason, $value, $minHtml, $maxHtml, $list_compare_types );
					return $html;

				} elseif ( ( ! in_array( $field->name, array( 'registerDate', 'lastvisitDate', 'lastupdatedate' ) ) ) ) {
					list( $yMin, $yMax )	=	$this->_yearsRange( $field, 0 );
					$html		=	$calendars->cbAddCalendar( $field->name, $this->getFieldTitle( $field, $user, 'text', $reason ), $this->_isRequired( $field, $user, $reason ), $value, $this->_isReadOnly( $field, $user, $reason ), false, $yMin, $yMax )
								.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, $field->type, $value, 'input', null, true, $this->_isRequired( $field, $user, $reason ) && ! $this->_isReadOnly( $field, $user, $reason ) );
				} else {
					$html		=	null;
				}
				return $html;
				break;

			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return '*' . CBTxt::T('Unknown Output Format') . '*';
	}
	function _yearsRange( &$field, $outputMode ) {
		$yMin					=	$this->_yearSetting( $field->params->get( 'year_min', '-110' ), $outputMode );
		$yMax					=	$this->_yearSetting( $field->params->get( 'year_max', '+25' ), $outputMode );
		if ( $outputMode == 1 ) {
			// Age is the other way around, older is bigger age...
			$temp				=	$yMin;
			$yMin				=	$yMax;
			$yMax				=	$temp;
		}
		if ( ( ( $yMax - $yMin ) > 1000 ) || ( $yMax < $yMin ) ) {
			$yMax				=	$yMin + 1000;
		}
		return array( $yMin, $yMax + 1 );			// quick fix for bug #214 : add 1 year to be sure to have a full year room
	}
	function _yearSetting( $setParam, $outputMode ) {
		global $_CB_framework;

		$yearSetting				=	trim( $setParam );
		if ( strlen( $yearSetting ) == 0 ) {
			$offset					=	0;
		} else {
			$sign					=	$yearSetting[0];
			if ( $sign == '+' ) {
				$offset				=	(int) substr( $yearSetting, 1 );
			} elseif ( $sign == '-' ) {
				$offset				=	- (int) substr( $yearSetting, 1 );
			} else {
				$offset				=	null;
				$fullYear			=	(int) $yearSetting;
			}
		}

		if ( $outputMode == 1 ) {
			if ( $offset === null ) {
				$offset				=	$fullYear - date( 'Y', $_CB_framework->now() +  $_CB_framework->getCfg( 'offset' ) );
			}
			return -$offset;
		} else {
			if ( $offset !== null ) {
				$fullYear			=	date( 'Y', $_CB_framework->now() +  $_CB_framework->getCfg( 'offset' ) ) + $offset;
			}
			return $fullYear;
		}
	}
	/**
	 * Convert a SQL date or datetime into a string that tells how long ago that date was.
	 * eg: 2 days ago, 3 minutes ago, 2 years ago.
	 *
	 * @param  string   $d                 SQL date
	 * @param  boolean  $displayAgo
	 * @param  boolean  $serverTimeOffset  if it's server time and needs to be offseted 
	 * @return string      time ago
	 */
	function _ago( $d, $displayAgo, $serverTimeOffset = true ) {
		if ( $d && ( $d != '0000-00-00' ) && ( $d != '0000-00-00 00:00:00' ) ) {
			global $_CB_framework;
			$c						=	getdate( $_CB_framework->now() - ( $serverTimeOffset ? ( 3600 * $_CB_framework->getCfg( 'offset' ) ) : 0 ) );
			$p						=	array( 'year', 'mon', 'mday', 'hours', 'minutes', 'seconds' );
			$display				=	array( _UE_YEARS, _UE_MONTHS, _UE_DAYS, _UE_HOURS, _UE_MINUTES, _UE_SECONDS );
			$factor					=	array( 0, 12, 30, 24, 60, 60 );
			$d						=	$this->_datetoarr( $d );
			for ( $w = 0 ; $w < 6 ; $w++ ) {
				if ( $w > 0 ) {
					$c[$p[$w]]		+=	$c[$p[$w-1]] * $factor[$w];
					$d[$p[$w]]		+=	$d[$p[$w-1]] * $factor[$w];
				}
				$durationAgo		=	$c[$p[$w]] - $d[$p[$w]];
				if ( $durationAgo > 1 ) {
					if ( ( ! $d['hastime'] ) && ( $w > 2 ) ) {
						return _UE_TODAY;
					}
					if ( $displayAgo ) {
						return sprintf( _UE_ANYTHING_AGO, $durationAgo . ' ' . $display[$w] );
					} else {
						return $durationAgo . ' ' . $display[$w];
					}
				}
			}
			return _UE_NOW;
		} else {
			return _UE_NEVER;
		}
	}
	/**
	 * Converts SQL date or datetime into getdate()-type array
	 *
	 * @param  string  $d  SQL date
	 * @return array
	 */
	function _datetoarr( $d ) {
		$matches			=	array();
		if ( preg_match( "/([0-9]{4})(\\-)([0-9]{2})(\\-)([0-9]{2}) ([0-9]{2})(\\:)([0-9]{2})(\\:)([0-9]{2})/", $d, $matches ) ) {
		    return array( 
				'seconds'	=> $matches[10], 
				'minutes'	=> $matches[8], 
				'hours'		=> $matches[6],  
				'mday'		=> $matches[5], 
				'mon'		=> $matches[3],  
				'year'		=> $matches[1],
				'hastime'	=> true
			);
		} elseif ( preg_match( "/([0-9]{4})(\\-)([0-9]{2})(\\-)([0-9]{2})/", $d, $matches ) ) {
		    return array( 
				'seconds'	=> 0, 
				'minutes'	=> 0, 
				'hours'		=> 0,  
				'mday'		=> $matches[5], 
				'mon'		=> $matches[3],  
				'year'		=> $matches[1],
				'hastime'	=> false
			);
		} else {
		    return array( 
				'seconds'	=> 0, 
				'minutes'	=> 0, 
				'hours'		=> 0,  
				'mday'		=> 0, 
				'mon'		=> 0,  
				'year'		=> 0,
				'hastime'	=> false
			);
		}
	}
	/**
	 * Labeller for title:
	 * Returns a field title
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'text' or: 'html', 'htmledit', (later 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist')
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @return string
	 */
	function getFieldTitle( &$field, &$user, $output, $reason ) {
		$title					=	'';
		$byAge					=	( ( ( $output == 'html' ) || ( $output == 'rss' ) ) && ( $field->params->get( 'field_display_by', 0 ) > 0 ) )
								||	( ( $reason == 'search' ) && ( $field->params->get( 'field_search_by', 0 ) == 1 ) )
								;
		if ( $byAge ) {
			$title				=	$field->params->get( 'duration_title' );
		}
		if ( $title != '' ) {
			if ( $output === 'text' ) {
				return strip_tags( cbReplaceVars( $title, $user ) );
			} else {
				return cbReplaceVars( $title, $user );
			}
		} else {
			return parent::getFieldTitle( $field, $user, $output, $reason );
		}
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		if ( ( ! in_array( $field->name, array( 'registerDate', 'lastvisitDate', 'lastupdatedate' ) ) ) ) {
			foreach ( $field->getTableColumns() as $col ) {
				$value					=	stripslashes( cbGetParam( $postdata, $col ) );
				$value					=	$this->_displayDateToSql( $value );
				$validated				=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
				if ( $value !== null ) {
					if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) && ! ( ( ( $user->$col === '0000-00-00' ) || ( $user->$col === '0000-00-00 00:00:00' ) ) && ( $value == '' ) ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
					}
					$user->$col			=	$value;
				}
			}
		}
	}
	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string                $columnName  Column to validate
	 * @param  string                $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason ) {
		$validate	=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		if ( $validate && ( $value !== null ) ) {
			$year						=	substr( $value, 0, 4 );
			if ( ( $year == '' ) || ( $year == '0000' ) ) {
				if ( $this->_isRequired( $field, $user, $reason ) ) {
					$this->_setValidationError( $field, $user, $reason, cbUnHtmlspecialchars(_UE_REQUIRED_ERROR) );
					return false;
				}
			} else {
				// check range:
				list( $yMin, $yMax )		=	$this->_yearsRange( $field, 0 );
				if ( ( $year < $yMin ) || ( $year > $yMax ) ) {
					$this->_setValidationError( $field, $user, $reason, sprintf( _UE_YEAR_NOT_IN_RANGE, (int) $year, (int) $yMin, (int) $yMax ) );
					$validate				=	false;
				}
			}
		}
		return $validate;
	}
	/**
	 * Internal function to convert CB-formatted date from field into SQL date.
	 * @access private
	 *
	 * @param  string  $value
	 * @return string
	 */
	function _displayDateToSql( $value ) {
		global $ueConfig;

		if ( $value !== null ) {
			$sqlFormat					=	'Y-m-d';
			$fieldForm					=	str_replace( 'y', 'Y', $ueConfig['date_format'] );
			$value						=	dateConverter( stripslashes( $value ), $fieldForm, $sqlFormat );
			if ( ! preg_match( '/[0-9]{4}-[01][0-9]-[0-3][0-9]/', $value ) ) {
				$value					=	'';
			}
		}
		return $value;
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		$search_by								=	$field->params->get( 'field_search_by', 0 );

		list( $yMinMin, $yMaxMax )				=	$this->_yearsRange( $field, $search_by );

		$query									=	array();

		foreach ( $field->getTableColumns() as $col ) {
			$minNam								=	$col . '__minval';
			$maxNam								=	$col . '__maxval';
			$searchMode							=	$this->_bindSearchRangeMode( $field, $searchVals, $postdata, $minNam, $maxNam, $list_compare_types );

			if ( $searchMode ) {
				if ( $search_by == 1 ) {
					// search by years:
					// list( $y, $c, $d, $h, $m, $s )	=	sscanf( date( 'Y-m-d H:i:s' ), '%d-%d-%d %d:%d:%d' );
					list( $y, $c, $d )			=	sscanf( date( 'Y-m-d' ), '%d-%d-%d' );

					$minValIn					=	(int) cbGetParam( $postdata, $minNam, 0 );
					$maxValIn					=	(int) cbGetParam( $postdata, $maxNam, 0 );

					if ( ( $maxValIn && ( $maxValIn <= $yMaxMax ) ) && ( $minValIn && ( $minValIn > $yMinMin ) ) ) {
						$yMax					=	$y - $minValIn;
						$maxVal					=	sprintf( '%04d-%02d-%02d', $yMax, $c, $d );
					} else {
						$maxVal					=	null;
					}

					if ( ( $minValIn && ( $minValIn >= $yMinMin ) ) && ( $maxValIn && ( $maxValIn < $yMaxMax ) ) ) {
						$yMin					=	$y - $maxValIn -1;	// yes, crossed: the more years back, the smaller the date...	add 1 year for searches from 24 to 24 (INCLUDED)
						$minVal					=	sprintf( '%04d-%02d-%02d', $yMin, $c, $d );
					} else {
						$minVal					=	null;
					}
				} else {
					$minVal						=	$this->_displayDateToSql( stripslashes( cbGetParam( $postdata, $minNam ) ) );
					$maxVal						=	$this->_displayDateToSql( stripslashes( cbGetParam( $postdata, $maxNam ) ) );
					$minValIn					=	cbFormatDate( $minVal );
					$maxValIn					=	cbFormatDate( $maxVal );
				}

				$min_search						=	( $minVal && ( $minVal !== '0000-00-00' ) );
				$max_search						=	( $maxVal && ( $maxVal !== '0000-00-00' ) );
				$force_min						=	( ( ! $min_search ) && $max_search && ( ! in_array( $field->name, array( 'lastupdatedate', 'lastvisitDate' ) ) ) );

				if ( $min_search || $force_min ) {
					$min						=	new cbSqlQueryPart();
					$min->tag					=	'column';
					$min->name					=	$col;
					$min->table					=	$field->table;
					$min->type					=	'sql:field';
					$min->operator				=	( ! $force_min ? ( $searchMode == 'isnot' ? '<=' : '>=' ) : '>' );
					$min->value					=	( ! $force_min ? $minVal : '0000-00-00' );
					$min->valuetype				=	'const:date';
					$min->searchmode			=	$searchMode;

					if ( ! $force_min ) {
						if ( ( ! $maxVal ) && $maxValIn ) {
							$searchVals->$maxNam=	$maxValIn;
						}

						$searchVals->$minNam	=	$minValIn;
					}
				}

				if ( $max_search ) {
					$max						=	new cbSqlQueryPart();
					$max->tag					=	'column';
					$max->name					=	$col;
					$max->table					=	$field->table;
					$max->type					=	'sql:field';
					$max->operator				=	( $searchMode == 'isnot' ? '>=' : '<=' );
					$max->value					=	$maxVal;
					$max->valuetype				=	'const:date';
					$max->searchmode			=	$searchMode;

					if ( ( ! $minVal ) && $minValIn ) {
						$searchVals->$minNam	=	$minValIn;
					}

					$searchVals->$maxNam		=	$maxValIn;
				}

				if ( isset( $min ) && isset( $max ) ) {
					$sql						=	new cbSqlQueryPart();
					$sql->tag					=	'column';
					$sql->name					=	$col;
					$sql->table					=	$field->table;
					$sql->type					=	'sql:operator';
					$sql->operator				=	( $searchMode == 'isnot' ? 'OR' : 'AND' );
					$sql->searchmode			=	$searchMode;

					$sql->addChildren( array( $min, $max ) );

					$query[]					=	$sql;
				} elseif ( isset( $min ) ) {
					$query[]					=	$min;
				} elseif ( isset( $max ) ) {
					$query[]					=	$max;
				}
			}
		}
		return $query;
	}
}
class CBfield_editorta extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig;

		$value							=	$user->get( implode( '', $field->getTableColumns() ) );

		switch ( $output ) {
			case 'html':
			case 'rss':
				$cbFields				=	new cbFields();
				$badHtmlFilter			=&	$cbFields->getInputFilter( array (), array (), 1, 1 );
				if ( isset( $ueConfig['html_filter_allowed_tags'] ) && $ueConfig['html_filter_allowed_tags'] ) {
					$badHtmlFilter->tagBlacklist	=	array_diff( $badHtmlFilter->tagBlacklist, explode(" ", $ueConfig['html_filter_allowed_tags']) );
				}
				$html					=	$cbFields->clean( $badHtmlFilter, $value );
				unset( $cbFields );
				break;
			case 'htmledit':
				if ( $reason == 'search' ) {
					$fsize				=	$field->size;
					if ( $field->size > 120 ) {
						$field->size	=	null;
					}
					$html				=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '' );
					$field->size		=	$fsize;
					$html				=	$this->_fieldSearchModeHtml( $field, $user, $html, 'text', $list_compare_types );
				} elseif ( ! ( $this->_isReadOnly( $field, $user, $reason ) ) ) {
					$cbFields			=	new cbFields();
					$badHtmlFilter		=&	$cbFields->getInputFilter( array (), array (), 1, 1 );
					if ( isset( $ueConfig['html_filter_allowed_tags'] ) && $ueConfig['html_filter_allowed_tags'] ) {
						$badHtmlFilter->tagBlacklist	=	array_diff( $badHtmlFilter->tagBlacklist, explode(" ", $ueConfig['html_filter_allowed_tags']) );
					}
					$value				=	$cbFields->clean( $badHtmlFilter, $value );
					unset( $cbFields );

					$html				=	$_CB_framework->displayCmsEditor( $field->name, $value, 600, 350, $field->cols, $field->rows )
										.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, $field->type, $value, 'input', null, true, ( $this->_isRequired( $field, $user, $reason ) && ( ! $this->_isReadOnly( $field, $user, $reason ) ) ) );
					$this->_addSaveAndValidateCode( $field, $user, $reason );
				} else {
					$html				=	null;
				}
				break;

			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return $html;
	}
	function _addSaveAndValidateCode( $field, $user, $reason ) {
		global $_CB_framework;
		$jsSaveCode			=	$_CB_framework->saveCmsEditorJS( $field->name );
		if ( $jsSaveCode ) {
			$_CB_framework->outputCbJQuery( "$('#adminForm').submit( function() { " . $jsSaveCode . " return true; } );" );
		}
		if ( $this->_isRequired( $field, $user, $reason ) ) {
			// jQuery handles the onReady aspects very well... :
			if ( defined( '_CB_VALIDATE_NEW' ) ) {
				$jsSaveCode			=	$_CB_framework->saveCmsEditorJS( $field->name, 0, false );
				cbimport( 'cb.validator' );
				cbValidator::addMethod( 'cbeditorareaRequired' . $field->name, '
jQuery.validator.addMethod("cbeditorareaRequired' . $field->name . '", function(value, element, param) {
			' . $jsSaveCode . ';
			return $(element).hasClass(\'requiredDisabled\') || $(element).closest(\'.fieldCell,.cb_field\').hasClass(\'requiredDisabled\') || $.trim($(element).val()).length > 0;
});
$(\'form[name="adminForm"] textarea[name="' . $field->name . '"]\').addClass("required").rules( "add", { cbeditorareaRequired' . $field->name . ': true } );' );
			} else {
				$_CB_framework->outputCbJQuery(
								'$(\'form[name="adminForm"] textarea[name="' . $field->name . '"]\').addClass("required");'
							.		'document.adminForm.' . $field->name . ".setAttribute('mosReq','1');"
							.		'document.adminForm.' . $field->name . ".setAttribute('mosLabel','" . addslashes( $this->getFieldTitle( $field, $user, 'text', $reason ) ) . "');" );
			}
		}

	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $ueConfig;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value						=	stripslashes( cbGetParam( $postdata, $col, '', _CB_ALLOWRAW ) );
			if ( $value !== null ) {
				$badHtmlFilter			=	new CBInputFilter( array (), array (), 1, 1, 1 );
	
				if ( isset( $ueConfig['html_filter_allowed_tags'] ) && $ueConfig['html_filter_allowed_tags'] ) {
					$badHtmlFilter->tagBlacklist	=	array_diff( $badHtmlFilter->tagBlacklist, explode(" ", $ueConfig['html_filter_allowed_tags']) );
				}
	
				$value					=	$badHtmlFilter->process( $value );
			}
			$validated					=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
			if ( $value !== null ) {
				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
				$user->$col				=	$value;
			}
		}
	}
}
class CBfield_email extends CBfield_text {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig;

		$value								=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( $field->type == 'primaryemailaddress' ) {

					$imgMode					=	0;

					if ( ( $ueConfig['allow_email_display'] == 3 ) || ( $imgMode != 0 ) ) {
						$oValueText				=	_UE_SENDEMAIL;
					} else {
						$oValueText				=	htmlspecialchars( $value );
					}
					$emailIMG					=	'<img src="' . $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/images/email.gif" border="0" alt="' . _UE_SENDEMAIL . '" title="' . _UE_SENDEMAIL . '" />';
					switch ( $imgMode ) {
						case 0:
							$linkItemImg		=	null;
							$linkItemSep		=	null;
							$linkItemTxt		=	$oValueText;
						break;
						case 1:
							$linkItemImg		=	$emailIMG;
							$linkItemSep		=	null;
							$linkItemTxt		=	null;
						break;
						case 2:
							$linkItemImg		=	$emailIMG;
							$linkItemSep		=	' ';
							$linkItemTxt		=	$oValueText;
						break;
					}
					$oReturn					=	'';
					//if no email or 4 (do not display email) then return empty string
					if ( ( $value == null ) || ( $ueConfig['allow_email_display'] == 4 ) || ( ( $imgMode != 0 ) && ( $ueConfig['allow_email_display'] == 1 ) ) ) {
						// $oReturn				=	'';
					} else {
						switch ( $ueConfig['allow_email_display'] ) {
							case 1: //display email only
								$oReturn		=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 0 );
								break;
							case 2: //mailTo link
								// cloacking doesn't cloack the text of the hyperlink, if that text does contain email addresses		//TODO: fix it.
								if ( ! $linkItemImg && $linkItemTxt == htmlspecialchars( $value ) ) {
									$oReturn	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, '', 0 );
								} elseif ( $linkItemImg && $linkItemTxt != htmlspecialchars( $value ) ) {
									$oReturn	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, $linkItemImg . $linkItemSep . $linkItemTxt, 0 );
								} elseif ( $linkItemImg && $linkItemTxt == htmlspecialchars( $value ) ) {
									$oReturn 	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, $linkItemImg, 0 ) . $linkItemSep;
									$oReturn	.=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, '', 0 );
								} elseif ( ! $linkItemImg && $linkItemTxt != htmlspecialchars( $value ) ) {
									$oReturn	=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, $linkItemTxt, 0 );
								}
								break;
							case 3: //email Form (with cloacked email address if visible)
								$oReturn		=	"<a href=\""
												.	cbSef("index.php?option=com_comprofiler&amp;task=emailUser&amp;uid=" . $user->id . getCBprofileItemid(true))
												.	"\" title=\"" . _UE_MENU_SENDUSEREMAIL_DESC . "\">" . $linkItemImg . $linkItemSep;
								if ( $linkItemTxt && ( $linkItemTxt != _UE_SENDEMAIL ) ) {
									$oReturn	.=	moscomprofilerHTML::emailCloaking( $linkItemTxt, 0 );
								} else {
									$oReturn	.=	$linkItemTxt;
								}
								$oReturn		.=	"</a>";
								break;
						}
					}

				} else {

					// emailaddress:
					if ( $value == null ) {
						$oReturn				=	'';
					} else {
						if ( $ueConfig['allow_email'] == 1 ) {
							$oReturn			=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 1, "", 0 );
						} else {
							$oReturn			=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $value ), 0 );
						}
					}

				}
				break;

			case 'htmledit':
				$ajaxCheck					=	( ( ( ( $field->type == 'primaryemailaddress' ) && ( isset( $ueConfig['reg_email_checker'] ) && ( $ueConfig['reg_email_checker'] > 0 ) ) )
													||	$field->params->get( 'field_check_email', 0 )
													||  ( $_CB_framework->getUi() == 2 ) )
												&& ( $reason != 'search' ) );
				if ( defined( '_CB_VALIDATE_NEW' ) ) {
					if ( $ajaxCheck && ( $reason != 'search' ) ) {
						$oReturn			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '', null, true, array( 'email', $this->ajaxCheckField( $field, $user, $reason, array( 'email:true' ) ) ) );
						// $this->ajaxCheckField( $field, $user, $reason );
					} else {
						$oReturn			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '', null, true, ( ( $reason != 'search' ) ? array( 'email' ) : array() ) );
						if ( $reason == 'search' ) {
							$oReturn		=	$this->_fieldSearchModeHtml( $field, $user, $oReturn, 'text', $list_compare_types );
						}
					}
				} else {
					$oReturn				=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '', null, true, ( ( $reason != 'search' ) ? array( 'email' ) : array() ) );
					if ( $reason == 'search' ) {
						$oReturn			=	$this->_fieldSearchModeHtml( $field, $user, $oReturn, 'text', $list_compare_types );
					} elseif ( $ajaxCheck ) {
						$this->ajaxCheckField( $field, $user, $reason );
					}
				}
				break;

			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				$oReturn				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return $oReturn;
	}
	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check
	 * that the logged-in user has rights to edit that $user.
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  array                 $postdata
	 * @param  string                $reason     'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string                            Expected output.
	 */
	function fieldClass( &$field, &$user, &$postdata, $reason ) {
		global $_CB_framework, $_CB_database, $ueConfig, $_GET;

		parent::fieldClass( $field, $user, $postdata, $reason );		// performs spoofcheck.

		if ( ( ( ( $field->type == 'primaryemailaddress' ) && ( isset( $ueConfig['reg_email_checker'] ) && ( $ueConfig['reg_email_checker'] > 0 ) ) )
				||	$field->params->get( 'field_check_email', 0 )
				||  ( $_CB_framework->getUi() == 2 ) )
			&& ( ( $reason == 'edit' ) || ( $reason == 'register' ) ) )
		{
			$function			=	cbGetParam( $_GET, 'function', '' );
			if ( $function == 'checkvalue' ) {
				$email			=	stripslashes( cbGetParam( $postdata, 'value', '' ) );
				$emailISO 		=	CBTxt::utf8ToISO( $email );			// ajax sends in utf8, we need to convert back to the site's encoding.
			
				if ( ( $field->type == 'primaryemailaddress' )
				&& ( ( ( isset( $ueConfig['reg_email_checker'] ) ? ( $ueConfig['reg_email_checker'] > 1 ) : false )
						&& ( ( $reason == 'register' ) || ( ( $reason == 'edit') && $user && ( $emailISO != $user->email ) ) ) )
					||  ( $_CB_framework->getUi() == 2 ) ) )
				{
					if ( $_CB_database->isDbCollationCaseInsensitive() ) {
						$query	=	"SELECT COUNT(*) AS result FROM #__users WHERE email = " . $_CB_database->Quote( ( trim( $emailISO ) ) );
					} else {
						$query	=	"SELECT COUNT(*) AS result FROM #__users WHERE LOWER(email) = " . $_CB_database->Quote( ( strtolower( trim( $emailISO ) ) ) );
					}
					$_CB_database->setQuery($query);
					$dataObj	=	null;
					if ( $_CB_database->loadObject( $dataObj ) ) {
						if ( $function == 'testexists' ) {
							if ( $dataObj->result ) {
								return '<span class="cb_result_ok">' . sprintf( ISOtoUtf8( _UE_EMAIL_EXISTS_ON_SITE ), htmlspecialchars( $email ) ) . "</span>";
							} else {
								return '<span class="cb_result_error">' . sprintf( ISOtoUtf8( _UE_EMAIL_DOES_NOT_EXISTS_ON_SITE ), htmlspecialchars( $email ) ) . "</span>";
							}
						} else {
							if ( $dataObj->result ) {
								return '<span class="cb_result_error">' . sprintf( ISOtoUtf8( _UE_EMAIL_ALREADY_REGISTERED ), htmlspecialchars( $email ) ) . "</span>";
							}
						}
					}
				}
				if ( $function == 'testexists' ) {
					return ISOtoUtf8( _UE_NOT_AUTHORIZED );
				} else {
					if ( ( $reason == 'register' ) || ( ( $reason == 'edit') && $user && ( $emailISO != $user->email ) ) || ( $_CB_framework->getUi() == 2 ) ) {
						$checkResult	=	cbCheckMail( $_CB_framework->getCfg( 'mailfrom' ), $emailISO );
					} else {
						return '<span class="cb_result_info">' . sprintf( ISOtoUtf8( CBTxt::T("No changes.") ), htmlspecialchars( $email ) ) . "</span>";
					}
				}
				switch ( $checkResult ) {
					case -2:
						return '<span class="cb_result_error">' . sprintf( ISOtoUtf8( _UE_EMAIL_NOVALID ), htmlspecialchars( $email ) ) . "</span>";
						break;
					case -1:
						return '<span class="cb_result_warning">' . sprintf( ISOtoUtf8( _UE_EMAIL_COULD_NOT_CHECK ), htmlspecialchars( $email ) ) . "</span>";
						break;
					case 0:
						if ( $ueConfig['reg_confirmation'] == 0 ) {
							return '<span class="cb_result_error">' . sprintf( ISOtoUtf8( _UE_EMAIL_INCORRECT_CHECK ), htmlspecialchars( $email ) ) . "</span>";
						} else {
							return '<span class="cb_result_error">' . sprintf( ISOtoUtf8( _UE_EMAIL_INCORRECT_CHECK_NEEDED ), htmlspecialchars( $email ) ) . "</span>";
						}
						break;
					case 1:
						return '<span class="cb_result_ok">' . sprintf( ISOtoUtf8( _UE_EMAIL_VERIFIED ), htmlspecialchars( $email ) ) . "</span>";
						break;
					default:
						return '<span class="cb_result_error">' . sprintf(CBTxt::T('Unexpected cbCheckMail result: %s'), $checkResult) . '.</span>';
						break;
				}
			}
			return null;
		} else {
			return ISOtoUtf8( _UE_NOT_AUTHORIZED );
		}
	}

	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	stripslashes( cbGetParam( $postdata, $col ) );
			if ( $value !== null ) {
				$value				=	str_replace( array( 'mailto:', 'http://', 'https://' ), '', $value );
			}
			$validated				=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
			if ( $value !== null ) {
				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
				$user->$col			=	$value;
			}
		}
	}
	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string                $columnName  Column to validate
	 * @param  string                $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason ) {
		$validate	=	parent::validate( $field, $user, $columnName, $value, $postdata, $reason );
		if ( $validate && ( $value != null ) ) {
			if ( ! cbIsValidEmail( $value ) ) {
				$this->_setValidationError( $field, $user, $reason, sprintf( _UE_EMAIL_NOVALID, htmlspecialchars( $value ) ) );
				$validate				=	false;
			}
		}
		return $validate;
	}
}
class CBfield_webaddress extends CBfield_text {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $ueConfig;

		$value						=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( $value == null ) {
					return '';
				} elseif ( $ueConfig['allow_website'] == 1 ) {
					$oReturn		=	$this->_explodeCBvalues( $value );
					if ( count( $oReturn ) < 2) {
						$oReturn[1]	=	$oReturn[0];
					}
					return '<a href="http://' . htmlspecialchars( $oReturn[0] ) . '" target="_blank" rel="nofollow">' . htmlspecialchars( $oReturn[1] ) . '</a>';
				} else {
					return htmlspecialchars( $value );
				}
				break;

			case 'htmledit':
				if ( $field->rows != 2 ) {
					$oReturn	=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, '' );
				} else {
					$oValuesArr			=	$this->_explodeCBvalues( $value );
					if ( count( $oValuesArr ) < 2 ) {
						$oValuesArr[1]	=	'';
					}
					$oReturn	=	'<span class="webUrlSpan">'
								.	'<span class="subTitleSpan">'._UE_WEBURL.':</span>'
								.	'<span class="subFieldSpan">'
								.	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $oValuesArr[0], '' )
								.	'</span></span>'
								;
					$saveFieldName		=	$field->name;
					$saveFieldTitle		=	$field->title;
					$field->name		=	$saveFieldName . 'Text';
					$field->title		=	$field->title . ': ' . _UE_WEBTEXT;
					$oReturn	.=	'<span class="webTextSpan">'
								.	'<span class="subTitleSpan">'._UE_WEBTEXT.':</span>'
								.	'<span class="subFieldSpan">'
								.	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $oValuesArr[1], '' )
								.	'</span></span>'
								;
					$field->name		=	$saveFieldName;
					$field->title		=	$saveFieldTitle;
				}
				if ( $reason == 'search' ) {
					$oReturn			=	$this->_fieldSearchModeHtml( $field, $user, $oReturn, 'text', $list_compare_types );
				}
				return $oReturn;
				break;

			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
		return '*' . CBTxt::T('Unknown Output Format') . '*';
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value						=	stripslashes( cbGetParam( $postdata, $col, '' ) );
			$valueText					=	stripslashes( cbGetParam( $postdata, $col . 'Text', '' ) );

			if ( $value !== null ) {
				$value					=	str_replace( array( 'mailto:', 'http://', 'https://' ), '', $value );
	
				if ( $valueText ) {
					$oValuesArr			=	array();
					$oValuesArr[0]		=	$value;
					$oValuesArr[1]		=	str_replace( array( 'mailto:', 'http://', 'https://' ),'', $valueText );
					$value				=	$this->_implodeCBvalues( $oValuesArr );
				}
			}
			$validated					=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
			if ( $value !== null ) {
				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
				$user->$col				=	$value;
			}
		}
	}
}
class CBfield_pm extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $_CB_PMS;

		$oReturn					=	'';

		$resultArray		=	$_CB_PMS->getPMSlinks( $user->id, $_CB_framework->myId(), '', '', 1) ;	// toid,fromid,subject,message,1: link to compose new PMS message for $toid user.
		if ( count( $resultArray ) > 0) {
			switch ( $output ) {
				case 'html':
				case 'rss':
					$pmIMG				=	'<img src="' . $_CB_framework->getCfg( 'live_site' ) . '/components/com_comprofiler/images/pm.gif" border="0" alt="' . _UE_PM_USER . '" title="' . _UE_PM_USER . '" />';
					foreach ( $resultArray as $res ) {
					 	if ( is_array( $res ) ) {
					 		$imgMode			=	0;			//TBD later: make this a field parameter.
							switch ( $imgMode ) {
								case 0:
									$linkItem	=	getLangDefinition( $res["caption"] );
								break;
								case 1:
									$linkItem	=	$pmIMG;
								break;
								case 2:
									$linkItem	=	$pmIMG . ' ' . getLangDefinition( $res["caption"] );
								break;
							}
							$oReturn			.=	'<a href="' . cbSef( $res["url"] ) . '" title="' . getLangDefinition( $res["tooltip"] ) . '">' . $linkItem . '</a>';
					 	}
					}
					break;
	
				case 'htmledit':
					$oReturn					=	null;
					break;
				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$retArray					=	array();
					foreach ( $resultArray as $res ) {
					 	if ( is_array( $res ) ) {
							$title				=	cbReplaceVars( $res["caption"], $user );
							$url				=	cbSef( $res["url"] );
							$description		=	cbReplaceVars( $res["tooltip"], $user );
	 						$retArray[]			=	array( 'title' => $title, 'url' => $url, 'link' => $description );
					 	}
					}
					$oReturn					=	$this->_linksArrayToFormat( $retArray, $output );
					break;
			}
		}
		return $oReturn;
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );
		// on purpose don't log field update
		// nothing to do, PM fields don't save :-)
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason ) {
		return array();
	}
}
/**
 * Avatar
 */
class CBfield_image extends cbFieldHandler {
	function _getImageFieldParam( &$field, $name ) {
		global $ueConfig;

		$paramValue						=	$field->params->get( $name, '' );
		if ( $paramValue == '' ) {
			$paramValue					=	$ueConfig[$name];
		}
		return $paramValue;
	}
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig;

		$oReturn						=	'';
		if ( ( $ueConfig['allowAvatar'] == '1' ) || ( $field->name != 'avatar' ) ) {
			switch ( $output ) {
				case 'html':
				case 'rss':
					$thumbnail			=	( $reason != 'profile' );
					$oReturn			=	$this->_avatarHtml( $field, $user, $reason, $thumbnail, 2 );

					$name				=	$field->name;
					$nameapproved		=	$field->name . 'approved';
					if ( ( $reason == 'profile' ) && ( $user->$name != '' ) && ( $user->$nameapproved == 0 ) && ( isModerator( $_CB_framework->myId() ) ) ) {
						$oReturn		.=	' ' . $this->_avatarHtml( $field, $user, $reason, false, 10 );
					}
					break;

				case 'htmledit':
					if ( $reason == 'search' ) {
						$choices		=	array();
						$choices[]		=	moscomprofilerHTML::makeOption( '', _UE_NO_PREFERENCE );
						$choices[]		=	moscomprofilerHTML::makeOption( '1', _UE_HAS_PROFILE_IMAGE );
						$choices[]		=	moscomprofilerHTML::makeOption( '0', _UE_HAS_NO_PROFILE_IMAGE );
						$col			=	$field->name;
						$value			=	$user->$col;
						$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '', $choices );
						$html			=	$this->_fieldSearchModeHtml( $field, $user, $html, 'singlechoice', $list_compare_types );		//TBD: Has avatarapproved...
					} else {
						$html			=	$this->_htmlEditForm( $field, $user, $reason );
					}
					return $html;
				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$thumbnail			=	( $reason != 'profile' );
					$imgUrl				=	$this->_avatarLivePath( $field, $user, $thumbnail );
					$oReturn			=	$this->_formatFieldOutput( $field->name, $imgUrl, $output );
					break;
			}
		}
		return $oReturn;
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $_CB_framework, $ueConfig, $_PLUGINS, $_FILES;

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		$col										=	$field->name;
		$colapproved								=	$col . 'approved';
		$col_choice									=	$col . '__choice';
		$col_file									=	$col . '__file';
		$col_gallery								=	$col . '__gallery';

		$choice										=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value								=	( isset( $_FILES[$col_file] ) ? $_FILES[$col_file] : null );
		
				// Image is uploaded in the commit, but lets validate it here as well:
				$this->validate( $field, $user, $choice, $value, $postdata, $reason );
				break;
			case 'gallery':
				$newAvatar							=	stripslashes( cbGetParam( $postdata, $col_gallery ) );
		
				if ( $this->validate( $field, $user, $choice, $newAvatar, $postdata, $reason ) ) {
					$value							=	'gallery/' . $newAvatar;

					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
					}
					
					deleteAvatar( $user->$col ); // delete old avatar
	
					$user->$col							=	$value;
					$user->$colapproved					=	1;
				}
				break;
			case 'delete':
				if ( $user->id && ( $user->$col != null ) && ( $user->$col != '' ) ) {
					global $_CB_database;

					if ( isset( $user->$col ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, '' );
					}
					
					deleteAvatar( $user->$col ); // delete old avatar

					$user->$col						=	null; // this will not update, so we do query below:
					$user->$colapproved				=	1;

					$query							=	'UPDATE ' . $_CB_database->NameQuote( $field->table )
													.	"\n SET " . $_CB_database->NameQuote( $col )			  . ' = NULL'
													.	', '	  . $_CB_database->NameQuote( $col . 'approved' ) . ' = 1'
													.	', '	  . $_CB_database->NameQuote( 'lastupdatedate' )  . ' = ' . $_CB_database->Quote( $_CB_framework->dateDbOfNow() )
													.	"\n WHERE " . $_CB_database->NameQuote( 'id' )			  . ' = ' . (int) $user->id;
					$_CB_database->setQuery( $query );
					$_CB_database->query();
				}
				break;
			case 'approve':
				if ( isset( $user->$col ) && ( $_CB_framework->getUi() == 2 ) && $user->id && ( $user->$col != null ) && ( $user->$colapproved == 0 ) ) {
					$this->_logFieldUpdate( $field, $user, $reason, '', $user->$col );	// here we are missing the old value, so can't give it...

					$user->$colapproved				=	1;
					$user->lastupdatedate			=	$_CB_framework->dateDbOfNow();

					$cbNotification					=	new cbNotification();
					$cbNotification->sendFromSystem( $user, _UE_IMAGEAPPROVED_SUB, _UE_IMAGEAPPROVED_MSG );
				}
				break;
			case '':
			default:
				$this->validate( $field, $user, $choice, $newAvatar, $postdata, $reason );
				break;
		}
	}

	/**
	 * Mutator:
	 * Prepares field data commit
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save user edit, 'register' for save registration
	 */
	function commitFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $_CB_framework, $ueConfig, $_PLUGINS, $_FILES;

		$col										=	$field->name;
		$colapproved								=	$col . 'approved';
		$col_choice									=	$col . '__choice';
		$col_file									=	$col . '__file';

		$choice										=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value								=	( isset( $_FILES[$col_file] ) ? $_FILES[$col_file] : null );

				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					$_PLUGINS->loadPluginGroup( 'user' );

					$isModerator					=	isModerator( $_CB_framework->myId() );

					$_PLUGINS->trigger( 'onBeforeUserAvatarUpdate', array( &$user, &$user, $isModerator, &$value['tmp_name'] ) );
					if ( $_PLUGINS->is_errors() ) {
						$this->_setErrorMSG( $_PLUGINS->getErrorMSG() );
					}
					$imgToolBox						=	new imgToolBox();
					$imgToolBox->_conversiontype	=	$ueConfig['conversiontype'];
					$imgToolBox->_IM_path			=	$ueConfig['im_path'];
					$imgToolBox->_NETPBM_path		=	$ueConfig['netpbm_path'];
					$imgToolBox->_maxsize			=	$this->_getImageFieldParam( $field, 'avatarSize' );
					$imgToolBox->_maxwidth			=	$this->_getImageFieldParam( $field, 'avatarWidth' );
					$imgToolBox->_maxheight			=	$this->_getImageFieldParam( $field, 'avatarHeight' );
					$imgToolBox->_thumbwidth		=	$this->_getImageFieldParam( $field, 'thumbWidth' );
					$imgToolBox->_thumbheight		=	$this->_getImageFieldParam( $field, 'thumbHeight' );
					$imgToolBox->_debug				=	0;

					$allwaysResize					=	( isset( $ueConfig['avatarResizeAlways'] ) ? $ueConfig['avatarResizeAlways'] : 1 );

					$fileNameInDir					=	( $col == 'avatar' ? '' : $col . '_' ) . uniqid( $user->id . '_' );
					$newFileName					=	$imgToolBox->processImage( $value, $fileNameInDir, $_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/', 0, 0, 1, $allwaysResize );
					if ( ! $newFileName ) {
						$this->_setValidationError( $field, $user, $reason, $imgToolBox->_errMSG ); // needed if uploaded file fails in imgToolbox
						//BB: maybe this needed instead ? :
						// $this->_setErrorMSG( $imgToolBox->_errMSG );
						return;
					}

					if ( isset( $user->$col ) && ! ( ( $col == 'avatar' ) && ( $ueConfig['avatarUploadApproval'] == 1 ) && ( $isModerator == 0 ) ) ) {
						// if auto-approved:				//TBD: else need to log update on image approval !
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $newFileName );
					}

					if ( isset( $user->$col ) && ( $user->$col != '' ) ) {
						deleteAvatar( $user->$col );
					}

					if ( ( $col == 'avatar' ) && ( $ueConfig['avatarUploadApproval'] == 1 ) && ( $isModerator == 0 ) ) {
						$cbNotification				=	new cbNotification();
						$cbNotification->sendToModerators( _UE_IMAGE_ADMIN_SUB, _UE_IMAGE_ADMIN_MSG );
			
						$user->$col					=	$newFileName;
						$user->$colapproved			=	0;
					} else {
						$user->$col					=	$newFileName;
						$user->$colapproved			=	1;
					}

					$_PLUGINS->trigger( 'onAfterUserAvatarUpdate', array( &$user, &$user, $isModerator, $newFileName ) );
				}
				break;
		}
	}

	/**
	 * Mutator:
	 * Prepares field data rollback
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save user edit, 'register' for save registration
	 */
	function rollbackFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $_CB_framework, $ueConfig, $_PLUGINS, $_FILES;

		$col										=	$field->name;
		$colapproved								=	$col . 'approved';
		$col_choice									=	$col . '__choice';
		$col_file									=	$col . '__file';

		$choice										=	stripslashes( cbGetParam( $postdata, $col_choice ) );

		switch ( $choice ) {
			case 'upload':
				$value								=	( isset( $_FILES[$col_file] ) ? $_FILES[$col_file] : null );
		
				if ( $this->validate( $field, $user, $choice, $value, $postdata, $reason ) ) {
					deleteAvatar( $user->$col );
				}
				break;
		}
	}

	/**	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string                $columnName  Column to validate
	 * @param  string                $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason      'edit' for save user edit, 'register' for save registration
	 * @return boolean                            True if validate, $this->_setErrorMSG if False
	 */
	function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason ) {
		global $_CB_framework, $ueConfig, $_FILES;

		$isRequired		=	$this->_isRequired( $field, $user, $reason );

		switch ( $columnName ) {
			case 'upload':
				if ( ( $field->name == 'avatar' ) && ! $ueConfig['allowAvatarUpload'] ) {
					$this->_setValidationError( $field, $user, $reason, _UE_NOT_AUTHORIZED );
					return false;
				}

				if ( ! isset( $value['tmp_name'] ) || empty( $value['tmp_name'] ) || ( $value['error'] != 0 ) || ! is_uploaded_file( $value['tmp_name'] ) ) {
					if ( $isRequired ) {
						$this->_setValidationError( $field, $user, $reason, _UE_UPLOAD_ERROR_EMPTY );
					}

					return false;
				}
				break;
			case 'gallery':
				if ( ( $field->name == 'avatar' ) && ! $ueConfig['allowAvatarGallery'] ) {
					$this->_setValidationError( $field, $user, $reason, _UE_NOT_AUTHORIZED );
					return false;
				}

				if ( ( $value == '' ) || preg_match( '/[^-_a-zA-Z0-9.]/', $value ) || ( strpos( $value, '..' ) !== false ) ) {
					$this->_setValidationError( $field, $user, $reason, _UE_UPLOAD_ERROR_CHOOSE . $value );
					return false;
				}
				break;
			case '':
			default:
				$valCol			=	$field->name;
				if ( $isRequired && ( ( ! $user ) || ( ! isset( $user->$valCol ) ) || ( ! $user->$valCol ) ) ) {
					if ( ! $value ) {
						$this->_setValidationError( $field, $user, $reason, _UE_FIELDREQUIRED );
						return false;
					}
				}
				break;
		}

		return true;
	}

	/**	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		$query								=	array();
		$searchMode							=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );
		$col								=	$field->name;
		$colapproved						=	$col . 'approved';
		$value								=	cbGetParam( $postdata, $col );
		if ( $value === '0' ) {
			$value							=	0;
		} elseif ( $value == '1' ) {
			$value							=	1;
		} else {
			$value							=	null;
		}
		if ( $value !== null ) {
			$searchVals->$col				=	$value;
			// $this->validate( $field, $user, $col, $value, $postdata, $reason );
			$sql							=	new cbSqlQueryPart();
			$sql->tag						=	'column';
			$sql->name						=	$colapproved;
			$sql->table						=	$field->table;
			$sql->type						=	'sql:operator';
			$sql->operator					=	$value ? 'AND' : 'OR';
			$sql->searchmode				=	$searchMode;
	
			$sqlpict						=	new cbSqlQueryPart();
			$sqlpict->tag					=	'column';
			$sqlpict->name					=	$col;
			$sqlpict->table					=	$field->table;
			$sqlpict->type					=	'sql:field';
			$sqlpict->operator				=	$value ? 'IS NOT' : 'IS';
			$sqlpict->value					=	'NULL';
			$sqlpict->valuetype				=	'const:null';
			$sqlpict->searchmode			=	$searchMode;
	
			$sqlapproved					=	new cbSqlQueryPart();
			$sqlapproved->tag				=	'column';
			$sqlapproved->name				=	$colapproved;
			$sqlapproved->table				=	$field->table;
			$sqlapproved->type				=	'sql:field';
			$sqlapproved->operator			=	$value ? '>' : '=';
			$sqlapproved->value				=	0;
			$sqlapproved->valuetype			=	'const:int';
			$sqlapproved->searchmode		=	$searchMode;
	
			$sql->addChildren( array( $sqlpict, $sqlapproved ) );
			$query[]						=	$sql;
		}
		return $query;
	}
	/**
	 * Returns full URL of fullsize of avatar
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @param  boolean               $thumbnail TRUE: return Thumbnail (tn) image, FALSE: return full-size image.
	 * @param  int                   $show_avatar
	 * @return string                URL
	 */
	function _avatarHtml( &$field, &$user, $reason, $thumbnail = true, $show_avatar = 2 ) {
		global $_CB_framework, $ueConfig;

		// $cbMyIsModerator				=	isModerator( $_CB_framework->myId() );

		if ( $field->name == 'avatar' ) {
			$name						=	getNameFormat( $user->name,$user->username,$ueConfig['name_format'] );
		} else {
			$name						=	cbReplaceVars( $field->title, $user );		// does htmlspecialchars()
		}
		
		$imgUrl							=	$this->_avatarLivePath( $field, $user, $thumbnail, $show_avatar );

		$allow_link						=	( $ueConfig['allow_profilelink'] == 1 ) && ( $reason != 'profile' );

		$class							=	( $thumbnail ? 'cbThumbPict' : 'cbFullPict' );

		if ( $allow_link ) {
			$profileURL					=	$_CB_framework->userProfileUrl( $user->id, true, ( $field->name == 'avatar' ? null : $field->tabid ) );
			$aTag						=	'<a href="' . $profileURL . '">';
			$naTag						=	'</a>';
		} else {
			$aTag						=	null;
			$naTag						=	null;
		}

		$return							=	$aTag
										.	'<img src="' . $imgUrl . '" alt="' . htmlspecialchars( $name ) . '" title="' . htmlspecialchars( $name ) . '" class="' . $class . '" />'
										.	$naTag;
		return $return;

	}
	/**
	 * Returns full URL of thumbnail of avatar
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser  $user
	 * @param  int                 $show_avatar
	 * @return string              URL
	 */
	function _avatarLivePath( &$field, &$user, $thumbnail = true, $show_avatar = 2 ) {
		global $_CB_framework;

		$oValue							=	null;
		$col							=	$field->name;
		$colapproved					=	$col . 'approved';
		if ( $user && $user->id ) {
			$avatar						=	$user->$col;
			$avatarapproved				=	$user->$colapproved;
			$live_site					=	$_CB_framework->getCfg( 'live_site' );
			$absolute_path				=	$_CB_framework->getCfg( 'absolute_path' );
			$tn							=	$thumbnail ? 'tn' : '';

			$oValue						=	null;
			if ( ( $avatar != '' ) && ( ( $avatarapproved > 0 ) || ( $show_avatar == 10 ) ) ) {
				if ( strpos( $avatar, 'gallery/' ) === false ) {
					$oValue				=	'images/comprofiler/' . $tn . $avatar;
				} else {
					$oValue				=	'images/comprofiler/' . $avatar;
				}
				if ( ! is_file( $absolute_path . '/' . $oValue ) ) {
					$oValue				=	null;
				}
			}
			if ( ( $oValue === null ) && ( $show_avatar == 2 ) ) {
				if ( $avatarapproved == 0 ) {
					$icon				=	'pending_n.png';
				} else {
					$icon				=	'nophoto_n.png';
				}
				return selectTemplate() . 'images/avatar/' . $tn . $icon;
			}
		}
		if ( $oValue ) {
			$oValue						=	$live_site . '/' . $oValue;
		}
		return $oValue;
	}
/*
	function _avatarLivePath( &$field, &$user, $thumbnail = true, $show_avatar = 2 ) {
		global $_CB_framework;

		$oValue							=	null;
		if ( $user && $user->id ) {
			$avatar						=	$user->avatar;
			$avatarapproved				=	$user->avatarapproved;
			$live_site					=	$_CB_framework->getCfg( 'live_site' );
			$absolute_path				=	$_CB_framework->getCfg( 'absolute_path' );
			$tn							=	$thumbnail ? 'tn' : '';

			$oValue						=	null;
			if ( ( $avatar != '' ) && ( $avatarapproved > 0 ) ) {
				if ( strpos( $avatar, 'gallery/' ) === false ) {
					$oValue				=	'images/comprofiler/' . $tn . $avatar;
				} else {
					$oValue				=	'images/comprofiler/' . $avatar;
				}
				if ( ! is_file( $absolute_path . '/' . $oValue ) ) {
					$oValue				=	null;
				}
			}
			if ( ( $oValue === null ) && ( $show_avatar == 2 ) ) {
				if ( $avatarapproved == 0 ) {
					$icon				=	'pendphoto.jpg';
				} else {
					$icon				=	'nophoto.jpg';
				}

				$lang					=	$_CB_framework->getCfg( 'lang' );
				if ( ! is_readable( $absolute_path . '/components/com_comprofiler/plugin/language/' . $lang . '/images/' . $tn . $icon ) ) {
					$lang				=	'default_language';
				}
				$oValue					=	'components/com_comprofiler/plugin/language/' . $lang . '/images/' . $tn . $icon;
			}
		}
		if ( $oValue ) {
			$oValue						=	$live_site . '/' . $oValue;
		}
		return $oValue;
	}
*/
	/**
	 * 
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  boolean               $displayFieldIcons
	 * @return string                            HTML: <tag type="$type" value="$value" xxxx="xxx" yy="y" />
	 */
	function _htmlEditForm( &$field, &$user, $reason, $displayFieldIcons = true ) {
		global $_CB_framework, $_CB_database, $ueConfig;

		if ( ( $field->name == 'avatar' ) && ! ( $ueConfig['allowAvatarUpload'] || $ueConfig['allowAvatarGallery'] ) ) {
			return null;
		}

		$name							=	$field->name;
		$nameapproved					=	$field->name . 'approved';
		$required						=	$this->_isRequired( $field, $user, $reason );

		$existingAvatar					=	false;
		if ( $user && $user->id ) {
			// we can not trust the following, as if another field did error out, it's wrong:
			// $existingAvatar			=	( $user->$name != null );
			// so load from database:
			$realDatabaseUser			=	new moscomprofilerUser( $_CB_database );
			if ( $realDatabaseUser->load( (int) $user->id ) ) {
				$existingAvatar			=	( $realDatabaseUser->$name != null );
			}
		}

		$html							=	'<div>';

		$choices						=	array();
		if ( ( $reason == 'register' ) || ( ( $reason == 'edit' ) && ( $user->id == 0 ) ) ) {
			if ( $required == 0 ) {
				$choices[]				=	moscomprofilerHTML::makeOption( '', _UE_AVATAR_NONE );
			}
		} else {
			if ( $existingAvatar || ( $required == 0 ) ) {
				$choices[]				=	moscomprofilerHTML::makeOption( '', _UE_AVATAR_NO_CHANGE );
			}
		}
		if ( ( $name != 'avatar' ) || $ueConfig['allowAvatarUpload'] ) {
			$choices[]					=	moscomprofilerHTML::makeOption( 'upload', ( $existingAvatar ? _UE_AVATAR_UPLOAD_NEW : _UE_AVATAR_UPLOAD ) );
		}
		if ( ( $name == 'avatar' ) && $ueConfig['allowAvatarGallery'] ) {
			$choices[]					=	moscomprofilerHTML::makeOption( 'gallery', _UE_AVATAR_SELECT );
		}
		if ( ( $_CB_framework->getUi() == 2 ) && $existingAvatar && ( $user->$nameapproved == 0 ) ) {
			$choices[]					=	moscomprofilerHTML::makeOption( 'approve', _UE_APPROVE_IMAGE );
		}
		if ( $existingAvatar && ( $required == 0 ) ) {
			$choices[]					=	moscomprofilerHTML::makeOption( 'delete', _UE_DELETE_AVATAR );
		}
		$html							.=	'<div>';
		if ( ( $reason != 'register' ) && ( $user->id != 0 ) && $existingAvatar ) {
			$html						.=	$this->_avatarHtml( $field, $user, $reason ) . ' ';
		}
		if ( ( $reason == 'edit' ) && $existingAvatar && ( $user->$nameapproved == 0 ) && ( isModerator( $_CB_framework->myId() ) ) ) {
			$html						.=	$this->_avatarHtml( $field, $user, $reason, false, 10 ) . ' ';
		}
		if ( count( $choices ) > 1 ) {
			$additional					=	' class="inputbox"';

			if ( ( $_CB_framework->getUi() == 1 ) && ( $reason == 'edit' ) && $field->readonly ) {
				$additional				.=	' disabled="disabled"';
			}

			$html						.=	moscomprofilerHTML::selectList( $choices, $name . '__choice', $additional, 'value', 'text', '', $required, true, false );
/*
			$js							=	"	$('#cbimg_upload_" . $name . ",#cbimg_gallery_" . $name . "').hide();"
										.	"\n	$('#" . $name . "__choice').click( function() {"
										.	"\n		var choice = $(this).val();"
										.	"\n		if ( choice == '' ) {"
										.	"\n			$('#cbimg_upload_" . $name . "').slideUp('slow');"
										.	"\n			$('#cbimg_gallery_" . $name . "').slideUp('slow');"
										.	"\n		} else if ( choice == 'upload' ) {"
										.	"\n			$('#cbimg_upload_" . $name . "').slideDown('slow');"
										.	"\n			$('#cbimg_gallery_" . $name . "').slideUp('slow');"
										.	"\n		} else if ( choice == 'gallery' ) {"
										.	"\n			$('#cbimg_upload_" . $name . "').slideUp('slow');"
										.	"\n			$('#cbimg_gallery_" . $name . "').slideDown('slow');"
										.	"\n		}"
										.	"\n	} ).click();"
										;
*/
			static $functOut			=	false;
			if ( ! $functOut ) {
				$js						=	"function cbslideImage(choice,uplodid,galleryid) {"
										.	"\n	if ( ( choice == '' ) || ( choice == 'approve' ) || ( choice == 'delete' ) ) {"
										.	"\n		$(uplodid).slideUp('slow');"
										.	"\n		$(galleryid).slideUp('slow');"
										.	"\n	} else if ( choice == 'upload' ) {"
										.	"\n		$(uplodid).slideDown('slow');"
										.	"\n		$(galleryid).slideUp('slow');"
										.	"\n	} else if ( choice == 'gallery' ) {"
										.	"\n		$(uplodid).slideUp('slow');"
										.	"\n		$(galleryid).slideDown('slow');"
										.	"\n	}"
										.	"\n}"
										;
				$_CB_framework->outputCbJQuery( $js );
				$functOut				=	true;
			}
			$js							=	"$('#cbimg_upload_" . $name . ",#cbimg_gallery_" . $name . "').hide();"
										.	"\n	{"
										.	"\n	  $('#" . $name . "__choice').click( function() {"
										.	"\n		cbslideImage( $(this).val(), '#cbimg_upload_" . $name . "', '#cbimg_gallery_" . $name . "' );"
										.	"\n	  } ).click();"
										.	"\n	  $('#" . $name . "__choice').change( function() {"
										.	"\n		cbslideImage( $(this).val(), '#cbimg_upload_" . $name . "', '#cbimg_gallery_" . $name . "' );"
										.	"\n	  } );"
										.	"\n	}"
										;
				$_CB_framework->outputCbJQuery( $js );
		} else {
			$html						.=	'<input type="hidden" name="' . $name . '__choice" value="' . $choices[0]->value . '" />';
		}
		$html							.=	$this->_fieldIconsHtml( $field, $user, 'htmledit', $reason, 'select', '', null, '', array(), $displayFieldIcons, $required );
		$html							.=	'</div>';



		if ( ( $name != 'avatar' ) || $ueConfig['allowAvatarUpload'] ) {
			$button						=	( $reason == 'register' ? _UE_REGISTER : ( $_CB_framework->getUi() == 2 ? _UE_SAVE : _UE_UPDATE ) );
			$saveFieldName				=	$field->name;
			$saveFieldRequired			=	$field->required;
			$field->name				.=	'__file';
			if ( $field->required && $user && isset( $user->$saveFieldName ) && $user->$saveFieldName ) {
				$field->required		=	0;
			}
			
			$html	.=	'<div id="cbimg_upload_' . $name . '">'
					.		'<p>' . sprintf( _UE_UPLOAD_DIMENSIONS_AVATAR, $this->_getImageFieldParam( $field, 'avatarWidth' ), $this->_getImageFieldParam( $field, 'avatarHeight' ), $this->_getImageFieldParam( $field, 'avatarSize' ) ) . '</p>'
					.		'<div>' . _UE_UPLOAD_SELECT_FILE . ' '
					.			'<input type="file" name="' . $name . '__file" value="" class="inputbox" />'
//STILL MORE TODO in js					.			$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'file', null, null )
					.		'</div>'
					.		'<p>' . ( $ueConfig['reg_enable_toc'] ? sprintf( _UE_AVATAR_DISCLAIMER_TERMS, $button, "<a href='".cbSef(htmlspecialchars($ueConfig['reg_toc_url']))."' target='_BLANK'> " . _UE_AVATAR_TOC_LINK . "</a>" ) : sprintf( _UE_AVATAR_DISCLAIMER, $button ) ) . '</p>'
					.	'</div>'
					;
			$field->name				=	$saveFieldName;
			$field->required			=	$saveFieldRequired;
		}

		if ( ( $name == 'avatar' ) && $ueConfig['allowAvatarGallery'] ) {
			$live_site					=	$_CB_framework->getCfg( 'live_site' );
			$avatar_gallery_path		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/gallery';
			$avatar_images				=	array();
			$avatar_images				=	display_avatar_gallery( $avatar_gallery_path );

			$html	.=	'<div id="cbimg_gallery_' . $name . '">'
					.		"\n\t<table width='100%' border='0' cellpadding='4' cellspacing='2'>"
					.	"\n\t\t<tr align='center' valign='middle'>"
					;
			for ( $i = 0 ; $i < count($avatar_images) ; $i++ ) {
				$j						=	$i + 1;
				$avatar_name			=	ucfirst( str_replace( '_', ' ', preg_replace( '/^(.*)\..*$/', '\1', $avatar_images[$i] ) ) );
				$html	.=	"\n\t\t\t<td>"
						.		'<input type="radio" name="' . $name . '__gallery" id="' . $name . '__gallery_' . $i . '" value="' . $avatar_images[$i] . '" />'
						.		'<label for="' . $name . '__gallery_' . $i . '">'
						.			'<img src="' . $live_site . '/images/comprofiler/gallery/'. $avatar_images[$i] . '" alt="' . $avatar_name . '" title="' . $avatar_name . '" />'
						.		'</label>'
						.	'</td>'
						;
				if ( function_exists( 'fmod' ) ) {
					if ( ! fmod( $j, 5 ) ) {
						$html	.=	"</tr>\n\t\t<tr align=\"center\" valign=\"middle\">";
					}
				} else {
					if ( ! fmodReplace( $j, 5 ) ) {			// PHP < 4.2.0...
						$html	.=	"</tr>\n\t\t<tr align=\"center\" valign=\"middle\">";
					}
				}

			}
			$html	.=	"\n\t\t</tr>\n\t\t"
					.	"\n\t</table>"
					.	'</div>'
					;
		}
		$html		.=	'</div>';
		return $html;
	}
	/**
	 * This event-driven method is temporary until we get another API for deleting each field:
	 * 
	 * @param $user
	 */
	function onBeforeDeleteUser( $user ) {
		global $_CB_framework, $_CB_database;
	
		$query				=	'SELECT ' . $_CB_database->NameQuote( 'name' )
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'type' ). " = " . $_CB_database->Quote( 'image' )
							.	"\n AND " . $_CB_database->NameQuote( 'name' ). " != " . $_CB_database->Quote( 'avatar' );
		$_CB_database->setQuery( $query );
		$image_fields		=	$_CB_database->loadResultArray();
	
		if ( $image_fields ) {
			$image_path		=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/';
	
			foreach ( $image_fields as $image_field ) {
				if ( isset( $user->$image_field ) && ( $user->$image_field != '' ) ) {
					if ( file_exists( $image_path . $user->$image_field ) ) {
						@unlink( $image_path . $user->$image_field );
	
						if ( file_exists( $image_path . 'tn' . $user->$image_field ) ) {
							@unlink( $image_path . 'tn' . $user->$image_field );
						}
					}
				}
			}
		}
	}
}
class CBfield_status extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig;

		$oReturn						=	'';

		if ( ( $ueConfig['allow_onlinestatus'] == 1 ) && isset( $user ) && $user->id ) {
			$lastTime					=	$_CB_framework->userOnlineLastTime( $user->id );
			$isonline					=	( $lastTime != null );

			switch ( $output ) {
				case 'html':
				case 'rss':
					if( $isonline > 0 ) {
						$oValue			=	_UE_ISONLINE;
						$img			=	'online.png';
						$class			=	'cb_online';
					} else { 
						$oValue			=	_UE_ISOFFLINE;
						$img			=	'offline.png';
						$class			=	'cb_offline';
					}
					$onlineIMG			=	'<img src="' . $_CB_framework->getCfg('live_site') . '/components/com_comprofiler/images/' . $img . '" border="0" alt="' . $oValue . '" title="' . $oValue . '" width="15" height="15" />';

					$imgMode			=	2;				//TBD: unhardcode eventually

					switch ( $imgMode ) {
						CASE 0:
							$oReturn	=	$oValue;
						break;
						CASE 1:
							$oReturn	=	$onlineIMG;
						break;
						CASE 2:
							$oReturn	=	'<span class="' . $class . '"><span>' . htmlspecialchars( $oValue ) . '</span></span>';
						break;
					}
					break;
	
				case 'htmledit':
					$oReturn			=	null;
					if ( $reason == 'search' ) {
						$oReturn		=	$this->_fieldSearchModeHtml( $field, $user, $oReturn, 'none', $list_compare_types );		//TBD: is online or not...
					}
					break;

				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$isOnlineBoolean	=	( $isonline > 0 ? 'true' : 'false' );
					$oReturn			=	$this->_formatFieldOutputIntBoolFloat( $field->name, $isOnlineBoolean, $output );;
					break;
			}
		}
		return $oReturn;
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );
		// nothing to do, Status fields don't save :-)
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason ) {
		return array();
	}
}
class CBfield_counter extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		$oReturn							=	'';

		if ( is_object( $user ) ) {
			$values							=	array();
			foreach ( $field->getTableColumns() as $col ) {
				$values[]					=	(int) $user->$col;
			}
			$value							=	implode( ', ', $values );

			switch ( $output ) {
				case 'html':
				case 'rss':
					$oReturn				=	$value;
					break;
	
				case 'htmledit':
					$oReturn				=	null;
					if ( $reason == 'search' ) {
						$minNam				=	$field->name . '__minval';
						$maxNam				=	$field->name . '__maxval';
	
						$minVal				=	$user->get( $minNam );
						$maxVal				=	$user->get( $maxNam );
						if ( $maxVal === null ) {
							$maxVal			=	99999;
						}
						$choices			=	array();
						for ( $i = 0 ; $i <= 10000 ; ( $i < 5 ? $i += 1 : ( $i < 30 ? $i += 5 : ( $i < 100 ? $i += 10 : ( $i < 1000 ? $i += 100 : $i += 1000 ) ) ) ) ) {
							$choices[]		=	moscomprofilerHTML::makeOption( $i, $i );
						}
						$additional			=	' class="inputbox"';
						$html				=	'<div>'
											.	'<span class="cbSearchFromTo cbSearchFrom">'
											.	_UE_SEARCH_FROM
											.	'</span> <span class="cbSearchFromVal">'
											.	moscomprofilerHTML::selectList( $choices, $minNam, $additional, 'value', 'text', $minVal, 2 )
											.	'</span>'
											.	' <span class="cbSearchFromTo cbSearchTo">'
											.	_UE_SEARCH_TO
											;
						$choices[]			=	moscomprofilerHTML::makeOption( '99999', _UE_ANY );
						$html				.=	'</span> <span class="cbSearchToVal">'
											.	moscomprofilerHTML::selectList( $choices, $maxNam, $additional, 'value', 'text', $maxVal, 2 )
											.	'</span>'
											.	' <span class="cbSearchFromTo cbSearchTo">'
											.	$this->getFieldTitle( $field, $user, $output, $reason )
											.	'</span> '
											.	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, $field->type, $value, 'input', null, true, false )
											.	'</div>'
											;
	
						$oReturn			=	$this->_fieldSearchModeHtml( $field, $user, $html, 'isisnot', $list_compare_types );

					}
					break;

				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$oReturn				=	$this->_formatFieldOutputIntBoolFloat( $field->name, $value, $output );;
					break;
			}
		}
		return $oReturn;
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );
		// nothing to do, counter Status fields don't save :-)
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		$searchMode						=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'isisnot', $list_compare_types );
		$query							=	array();
		if ( $searchMode ) {
			foreach ( $field->getTableColumns() as $col ) {
				$minNam					=	$col . '__minval';
				$maxNam					=	$col . '__maxval';
				$minVal					=	(int) cbGetParam( $postdata, $minNam, 0 );
				$maxVal					=	(int) cbGetParam( $postdata, $maxNam, 0 );
				if ( $minVal != 0 ) {
					$searchVals->$minNam =	$minVal;
					$query[]			=	$this->_intToSql( $field, $col, $minVal, '>=', $searchMode );
				}
				if ( $maxVal != 99999 ) {
					$searchVals->$maxNam =	$maxVal;
					$query[]			=	$this->_intToSql( $field, $col, $maxVal, '<=', $searchMode );
				}
			}
		}
		return $query;
	}
	/**
	 * Internal function to build SQL request
	 * @access private
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  string                $col
	 * @param  int                   $value
	 * @param  string                $operator
	 * @param  string                $searchMode
	 * @return cbSqlQueryPart
	 */
	function _intToSql( &$field, $col, $value, $operator, $searchMode ) {
		$value							=	(int) $value;
		// $this->validate( $field, $user, $col, $value, $postdata, $reason );
		$sql							=	new cbSqlQueryPart();
		$sql->tag						=	'column';
		$sql->name						=	$col;
		$sql->table						=	$field->table;
		$sql->type						=	'sql:field';
		$sql->operator					=	$operator;
		$sql->value						=	$value;
		$sql->valuetype					=	'const:int';
		$sql->searchmode				=	$searchMode;
		return $sql;
	}
}

class CBfield_connections extends CBfield_counter {
	/**
	 * Formatter:
	 * Returns a field row in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output      'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $formatting  'tr', 'td', 'div', 'span', 'none',   'table'??
	 * @param  string                $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types ) {
		global $ueConfig;

		if ( $ueConfig['allowConnections'] ) {
			return parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
		}
		return null;
	}
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig;

		$oReturn							=	null;

		if ( $ueConfig['allowConnections'] && is_object( $user ) ) {
			$cbCon							=	new cbConnection( $_CB_framework->myId() );
			$value							=	$cbCon->getConnectionsCount( $user->id );

			switch ( $output ) {
				case 'html':
				case 'rss':
					$oReturn				=	$value;
					break;
	
				case 'htmledit':
					// $oReturn				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
					$oReturn				=	null;		//TBD for now no searches...not optimal in SQL anyway.
					break;

				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$oReturn				=	$this->_formatFieldOutputIntBoolFloat( $field->name, $value, $output );;
					break;
			}
		}
		return $oReturn;
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		global $ueConfig;

		$searchMode						=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );
		$query							=	array();
		if ( $ueConfig['allowConnections'] && $searchMode ) {
			$col						=	$field->name;
			$minNam						=	$col . '__minval';
			$maxNam						=	$col . '__maxval';
			$minVal						=	(int) cbGetParam( $postdata, $minNam, 0 );
			$maxVal						=	(int) cbGetParam( $postdata, $maxNam, 0 );
			if ( $minVal && ( $minVal != 0 ) ) {
				$searchVals->$minNam	=	$minVal;
				$query[]				=	$this->_intToSql( $field, $col, $minVal, '>=', $searchMode );
			}
			if ( $maxVal && ( $maxVal != 0 ) ) {
				$searchVals->$maxNam	=	$maxVal;
				$query[]				=	$this->_intToSql( $field, $col, $maxVal, '<=', $searchMode );
			}
		}
		return $query;
	}
	/**
	 * Internal function to build SQL request
	 * @access private
		<data name="change_logs" type="sql:count" distinct="id"  table="#__cpay_history" class="cbpaidHistory">
			<joinkeys dogroupby="true">
				<column name="table_name"   operator="=" value="#__cpay_payment_baskets" type="sql:field" valuetype="const:string" />
				<column name="table_key_id" operator="=" value="id" type="sql:field" valuetype="sql:field" />
			</joinkeys>
		</data>

		<where>
			<column name="id"     operator="=" value="plan_id" type="int"       valuetype="sql:formula">
				<data name="plan_id" type="sql:field" table="#__cpay_payment_items" class="cbpaidPayementItem" key="plan_id" value="id" valuetype="sql:field">
					<data name="basket_id" type="sql:field" table="#__cpay_payment_baskets" class="cbpaidPayementBasket" key="id" value="payment_basket_id" valuetype="sql:field">
						<where>
							<column name="payment_status" operator="=" value="Completed" type="sql:field" valuetype="const:string" />
						</where>
					</data>
				</data>
		    </column>
		</where>
	
		<column name="id"     operator="=" value="plan_id" type="int"       valuetype="sql:formula">
			<data name="plan_id" type="sql:field" table="#__cpay_payment_items" class="cbpaidPayementItem" key="plan_id" value="id" valuetype="sql:field">
				<data name="basket_id" type="sql:field" table="#__cpay_payment_baskets" class="cbpaidPayementBasket" key="id" value="payment_basket_id" valuetype="sql:field">
					<where>
						<column name="payment_status" operator="=" value="Completed" type="sql:field" valuetype="const:string" />
					</where>
				</data>
			</data>
	    </column>
	    
	 * @param  moscomprofilerFields  $field
	 * @param  string                $col
	 * @param  int                   $value
	 * @param  string                $operator
	 * @param  string                $searchMode
	 * @return cbSqlQueryPart
	 */
	function _intToSql( &$field, $col, $value, $operator, $searchMode ) {
		$value							=	(int) $value;
		// $this->validate( $field, $user, $col, $value, $postdata, $reason );
		$sql							=	new cbSqlQueryPart();
		$sql->tag						=	'column';
		$sql->name						=	$field->name;
		$sql->table						=	'#__comprofiler_members';
		$sql->type						=	'sql:count';
		$sql->distinct					=	'memberid';
		$sql->operator					=	$operator;
		$sql->value						=	$value;
		$sql->valuetype					=	'const:int';
		$sql->searchmode				=	$searchMode;
		$sql->key						=	'id';
		$sql->keyvalue					=	'referenceid';
		return $sql;
	}
}

class CBfield_formatname extends cbFieldHandler {
	/**
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework, $ueConfig;

		$oReturn						=	'';
		if ( isset( $user ) && $user->id ) {

		$value							=	getNameFormat( $user->name, $user->username, $ueConfig['name_format'] );

			switch ( $output ) {
				case 'html':
				case 'rss':
					$allow_link			=	( $ueConfig['allow_profilelink'] == 1 ) && ( $reason != 'profile');
					if ( $allow_link ) {
						$profileURL		=	$_CB_framework->userProfileUrl( $user->id, true );
						$oReturn		=	'<a href="' . $profileURL . '">' . $value . '</a>';
					} else {
						$oReturn		=	$value;
					}
					break;
	
				case 'htmledit':
					$oReturn			=	null;
					break;

				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$oReturn			=	$this->_formatFieldOutput( $field->name, $value, $output );;
					break;
			}
		}
		return $oReturn;
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );
		// nothing to do, Formatted names fields don't save :-)
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason ) {
		return array();
	}
}
class CBfield_delimiter extends cbFieldHandler {
	/**
	 * Returns a DELIMITER field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		$value		=	cbReplaceVars( getLangDefinition( cbUnHtmlspecialchars( $field->description ) ), $user );	//TBD: unhtml is kept for backwards database compatibility until CB 2.0
		return $this->_formatFieldOutput( $field->name, $value, $output, false );
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );
		// nothing to do, Delimiter fields don't save :-)
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return array of cbSqlQueryPart
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason ) {
		return array();
	}
}

class CBfield_userparams extends cbFieldHandler {
	/**
	 * Initializer:
	 * Puts the default value of $field into $user (for registration or new user in backend)
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 */
	function initFieldToDefault( &$field, &$user, $reason ) {
	}
	/**
	 * Returns a USERPARAMS field in specified format
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output      'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $formatting  'table', 'td', 'span', 'div', 'none'
	 * @param  string                $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types ) {
		global $_CB_framework, $_CB_database, $ueConfig;

		$results							=	null;

		if ( class_exists( 'JFactory' ) ) {						// Joomla 1.5 :
				$lang						=&	JFactory::getLanguage();
				$lang->load( 'com_users' );
		}

		$pseudoFields						=	array();

		//Implementing Joomla's new user parameters such as editor
		$ui									=	$_CB_framework->getUi();

		$userParams							=	$this->_getUserParams( $ui, $user );		

		if ( is_array( $userParams ) && ( count( $userParams ) > 0 )
			&& ( ( $ui == 2 ) || ( ( isset( $ueConfig['frontend_userparams'] ) ) ? ( $ueConfig['frontend_userparams'] == 1 ) : in_array( $_CB_framework->getCfg( "frontend_userparams" ), array( '1', null) ) ) ) )
		{
			//Loop through each parameter and prepare rendering appropriately.
			foreach ( $userParams AS $k => $userParam ) {
				$paramField					=	new moscomprofilerFields( $_CB_database );
				$paramField->title			=	$userParam[0];
				$paramField->_html			=	$userParam[1];
				$paramField->description	=	( isset( $userParam[2] ) && class_exists("JText") ? JText::_( $userParam[2] ) : null );
				$paramField->name			=	( isset( $userParam[3] ) && class_exists("JText") ? JText::_( $userParam[3] ) : null );		// very probably wrong!
				$paramField->fieldid		=	'userparam_' . $k;
				$paramField->displaytitle	=	substr( $userParam[0], 0, 6 ) == '<label' ? -1 : 1;		// don't redisplay <label for> markup
				$paramField->type			=	'param';		// this is for cb_ftparam class to be correct.
				$pseudoFields[]				=	$paramField;
			}
		}

		if( $_CB_framework->getUi() == 2 ) {
			if ( checkJversion() < 2 ) {
				$myGids						=	CBuser::getMyInstance()->getUserData()->gids;
				$cms_mod					=	$_CB_framework->acl->mapGroupNamesToValues( 'Administrator' );
				$cms_admin					=	$_CB_framework->acl->mapGroupNamesToValues( 'Superadministrator' );
			}
			$i_am_super_admin				=	$_CB_framework->acl->amIaSuperAdmin();
			//1.6+ only: $user_is_super_admin			=	$_CB_framework->acl->checkActionForGroups( $this->gids, 'core.admin', null );

			if ( checkJversion() == 2 ) {
				$cms_admin_title			=	'Super Users';
			} else {
				$cms_admin_title			=	'Super Administrator';
			}
			$canBlockUser					=	CBuser::getMyInstance()->authoriseAction( 'core.edit.state', 'com_users' );
			$canEmailEvents					=	   ( ( $user->id == 0 ) && ( checkJversion() >= 2 ? $canBlockUser : ( count( array_intersect( $myGids, array( $cms_mod, $cms_admin ) ) ) ) ) )
												|| CBuser::getInstance( $user->id )->authoriseAction( 'core.edit.state', 'com_users' )
												|| array_intersect( $user->gids, getParentGIDS( $ueConfig['imageApproverGid'] ) );	// allow also CB isModerator

			$lists							=	array();

			if ( $canBlockUser ) {

				// ensure user can't add group higher than themselves
				$gtree						=	$_CB_framework->acl->get_groups_below_me();

				if ( checkJversion() == 2 ) {
					if ( ( ! $i_am_super_admin ) && $user->id && CBuser::getInstance( $user->id )->authoriseAction( 'core.manage', 'com_users' ) && ( CBuser::getInstance( $user->id )->authoriseAction( 'core.edit', 'com_users' ) ||  CBuser::getInstance( $user->id )->authoriseAction( 'core.edit.state', 'com_users' ) ) ) {
						$disabled			=	' disabled="disabled"';
					} else {
						$disabled			=	'';
					}
					$strgids				=	array_map( 'strval', $user->gids );
					$lists['gid']			=	moscomprofilerHTML::selectList( $gtree, 'gid[]', 'class="inputbox" size="11" multiple="multiple"' . $disabled, 'value', 'text', $strgids, 2, false );
				} else {
					if ( ( ! $i_am_super_admin ) && ( ( in_array( $cms_admin, $user->gids ) && in_array( $cms_admin, $myGids ) ) || ( $user->id == $_CB_framework->myId() && in_array( $cms_admin, $myGids ) ) ) ) {
						$lists['gid']		=	"<input type=\"hidden\" name=\"gid\" value=\"$user->gid\" /><strong>$cms_admin_title</strong>";
					} else if ( ( ! $i_am_super_admin ) && ( in_array( $cms_mod, $myGids ) && ( ! in_array( $cms_admin, $myGids ) ) && in_array( $cms_mod, $user->gids ) ) ) {
						$lists['gid']		=	"<input type=\"hidden\" name=\"gid\" value=\"$user->gid\" /><strong>Administrator</strong>";
					} else {
						$lists['gid']		=	moscomprofilerHTML::selectList( $gtree, 'gid', 'class="inputbox" size="11"', 'value', 'text', $user->gid, 2, false );
					}
				}
	
				// build the html select list
				$lists['block']					=	moscomprofilerHTML::yesnoSelectList( 'block', 'class="inputbox" size="1"', $user->block );
	
				$list_approved					=	array();
				$list_approved[]				=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'Unapproved' ) );
				$list_approved[]				=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Approved' ) );
				$list_approved[]				=	moscomprofilerHTML::makeOption( '2', CBTxt::T( 'Disapproved' ) );
				$lists['approved']				=	moscomprofilerHTML::selectList( $list_approved, 'approved', 'class="inputbox" size="1"', 'value', 'text', $user->approved, 2, false );
	
				$lists['confirmed']				=	moscomprofilerHTML::yesnoSelectList( 'confirmed', 'class="inputbox" size="1"', $user->confirmed );
				// build the html select list
				$lists['sendEmail']				=	moscomprofilerHTML::yesnoSelectList( 'sendEmail', 'class="inputbox" size="1"', $user->sendEmail );


				$paramField					=	new moscomprofilerFields( $_CB_database );
				$paramField->title			=	CBTxt::T( 'Group' );
				$paramField->_html			=	$lists['gid'];
				$paramField->description	=	'';
				$paramField->name			=	'gid';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new moscomprofilerFields( $_CB_database );
				$paramField->title			=	CBTxt::T( 'Block User' );
				$paramField->_html			=	$lists['block'];
				$paramField->description	=	'';
				$paramField->name			=	'block';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new moscomprofilerFields( $_CB_database );
				$paramField->title			=	CBTxt::T( 'Approve User' );
				$paramField->_html			=	$lists['approved'];
				$paramField->description	=	'';
				$paramField->name			=	'approved';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new moscomprofilerFields( $_CB_database );
				$paramField->title			=	CBTxt::T( 'Confirm User' );
				$paramField->_html			=	$lists['confirmed'];
				$paramField->description	=	'';
				$paramField->name			=	'confirmed';
				$pseudoFields[]				=	$paramField;

				$paramField						=	new moscomprofilerFields( $_CB_database );
				$paramField->title				=	CBTxt::T( 'Receive Moderator Emails' );
				if ($canEmailEvents || $user->sendEmail) {
					$paramField->_html			=	$lists['sendEmail'];
				} else {
					$paramField->_html			=	CBTxt::T('No (User\'s group-level doesn\'t allow this)')
												.	'<input type="hidden" name="sendEmail" value="0" />';
				}
				$paramField->description		=	'';
				$paramField->name				=	'sendEmail';
				$pseudoFields[]					=	$paramField;
			}

			if( $user->id) {
				$paramField					=	new moscomprofilerFields( $_CB_database );
				$paramField->title			=	CBTxt::T( 'Register Date' );
				$paramField->_html			=	cbFormatDate( $user->registerDate );
				$paramField->description	=	'';
				$paramField->name			=	'registerDate';
				$pseudoFields[]				=	$paramField;

				$paramField					=	new moscomprofilerFields( $_CB_database );
				$paramField->title			=	CBTxt::T( 'Last Visit Date' );
				$paramField->_html			=	cbFormatDate( $user->lastvisitDate );
				$paramField->description	=	'';
				$paramField->name			=	'lastvisitDate';
				$pseudoFields[]				=	$paramField;
			}
		}

		switch ( $output ) {
			case 'htmledit':
				foreach ( $pseudoFields as $paramField ) {
					$paramField->required	=	$this->_isRequired( $field, $user, $reason );
					$paramField->profile	=	$field->profile;
					$results				.=	parent::getFieldRow( $paramField, $user, $output, $formatting, $reason, $list_compare_types );
				}
				unset( $pseudoFields );
				return $results;
				break;

			default:
				return null;
				break;
		}
	}
	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  moscomprofilerField   $field
	 * @param  moscomprofilerUser    $user
	 * @param  string                $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string                $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int                   $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed                
	 */
	function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		switch ( $output ) {
			case 'htmledit':
				return $field->_html . $this->_fieldIconsHtml( $field, $user, $output, $reason, 'input', 'text', $field->_html, '', null, true, $this->_isRequired( $field, $user, $reason ) && ! $this->_isReadOnly( $field, $user, $reason ) );
				break;

			default:
				return null;
				break;
		}
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  moscomprofilerFields  $field
	 * @param  moscomprofilerUser    $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array                 $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string                $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		global $_CB_framework, $ueConfig;

		// Nb. frontend registration setting of usertype, gid, block, sendEmail, confirmed, approved
		// are handled in moscomprofilerUser::bindSafely() so they are available to other plugins.

		// this is (for now) handled in the core of CB... except params and block/email/approved/confirmed:

		if ( $_CB_framework->getUi() == 2 ) {
			$canBlockUser					=	CBuser::getMyInstance()->authoriseAction( 'core.edit.state', 'com_users' );
			if ( $canBlockUser ) {
				if ( checkJversion() == 2 ) {
					$user->gids				=	cbGetParam( $postdata, 'gid', array( 0 ) );
					$user->gid				=	(int) $_CB_framework->acl->getBackwardsCompatibleGid( $user->gids );
				} else {
					$user->gid				=	cbGetParam( $postdata, 'gid', 0 );
					$user->gids				=	array( $user->gid );
				}
				$user->block				=	cbGetParam( $postdata, 'block', 0 );
				$user->approved				=	cbGetParam( $postdata, 'approved', 0 );
				$user->confirmed			=	cbGetParam( $postdata, 'confirmed', 0 );

				$user->sendEmail			=	cbGetParam( $postdata, 'sendEmail', 0 );
			}
		}
 
		if (	( $_CB_framework->getUi() == 2 )
			||	( ( isset( $ueConfig['frontend_userparams'] ) ) ? ( $ueConfig['frontend_userparams'] == 1 ) : in_array( $_CB_framework->getCfg( "frontend_userparams" ), array( '1', null) ) ) )
		{
			// save user params
			$params							=	cbGetParam( $_POST, 'params', null );			//TBD: verify if stripslashes is needed here: it might be needed...leaving as is for now.
			if ( $params != null ) {
				if ( is_array( $params ) ) {
					if ( checkJversion() == 2 ) {
						$registry			=	new JRegistry( $params );
						$value				=	$registry->toArray();
					} else {
						$txt				=	array();
						foreach ( $params as $k => $v) {
								$txt[]			=	$k . '=' . $v;
						}
						$value				=	implode( "\n", $txt );
					}
					if ( ( (string) $user->params ) !== (string) $value ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->params, $value );
					}
					$user->params			=	$value;
				}
			}
		}
	}
	/**
	* Retrieve joomla standard user parameters so that they can be displayed in user edit mode.
	* @param  int                 $ui        1 for front-end, 2 for back-end
	* @param  moscomprofilerUser  $user      the user being displayed
	* @param  string              $name      Name of variable
	* @return array of user parameter attributes (title,value)
	*/
	function _getUserParams( $ui, $user,$name = "params" ) {
		global $_CB_framework;

		$result = array();	// in case not Joomla

		if (class_exists("JUser")) {						// Joomla 1.5 and 1.6:
			if ( $user->id ) {
				$juser		=&	JUser::getInstance( $user->id );
			} else {
				$juser		=&	JUser::getInstance();
			}
			if ( checkJversion() == 2 ) {
				// Joomla 1.6:
				$result				=	array();

				jimport( 'joomla.form.form' );

				JForm::addFormPath( JPATH_ADMINISTRATOR . '/components/com_users/models/forms' );

				$form				=	JForm::getInstance( 'com_users.params', 'user', array( 'load_data' => true ) );
				$params				=	$juser->getParameters( true )->toArray();

				if ( $params ) foreach ( $params as $k => $v ) {
					$form->setValue( $k, 'params', $v );
				}

				$fields				=	$form->getFieldset( 'settings' );

				if ( $fields ) foreach ( $fields as $field ) {
					$admin_field	=	( strpos( $field->name, 'admin' ) || strpos( $field->name, 'help' ) );

					if ( ( $admin_field && ( $juser->authorise( 'canManageUsers' ) || ( ! $user->id ) ) ) || ( ! $admin_field ) ) {
						$result[]	=	array( $field->label, $field->input, $field->description, $field->name );
					}
				}
			} else {
				// Joomla 1.5:
				$params =& $juser->getParameters( true );
				// $result = $params->render( 'params' );
				if (is_callable(array($params,"getParams"))) {
					$result = $params->getParams( $name );	//BBB new API submited to Jinx 17.4.2006.
				} else {
					foreach ($params->_xml->param as $param) {	//BBB still needs core help... accessing private variable _xml .
						$result[] = $params->renderParam( $param, $name );
					}
				}
			}
		} else {							
			if(file_exists($_CB_framework->getCfg('absolute_path') .'/administrator/components/com_users/users.class.php')){
				require_once( $_CB_framework->getCfg('absolute_path') .'/administrator/components/com_users/users.class.php' );		
			}
			if (class_exists('mosUserParameters')) {		// Joomla 1.0 :
				global $mainframe;
				$file 	= $mainframe->getPath( 'com_xml', 'com_users' );
	
				$userParams		=	new mosUserParameters( $user->params, $file, 'component' );
	
				if (isset($userParams->_path) && $userParams->_path) {						// Joomla 1.0
					if (!is_object( $userParams->_xmlElem )) {
						require_once( $_CB_framework->getCfg('absolute_path') . '/includes/domit/xml_domit_lite_include.php' );
		
						$xmlDoc = new DOMIT_Lite_Document();
						$xmlDoc->resolveErrors( true );
						if ($xmlDoc->loadXML( $userParams->_path, false, true )) {
							$root =& $xmlDoc->documentElement;
		
							$tagName = $root->getTagName();
							$isParamsFile = ($tagName == 'mosinstall' || $tagName == 'mosparams');
							if ($isParamsFile && $root->getAttribute( 'type' ) == $userParams->_type) {
								$params = &$root->getElementsByPath( 'params', 1 );
								if ($params ) {
									$userParams->_xmlElem =& $params;
								}
							}
						}
					}
				}
				$result=array();
				
				if (isset($userParams->_xmlElem) && is_object( $userParams->_xmlElem )) {    // Joomla 1.0
					$element =& $userParams->_xmlElem;
					//$params = mosParseParams( $row->params );
					$userParams->_methods = get_class_methods( "mosUserParameters" );
					foreach ($element->childNodes as $param) {
						$result[] = $userParams->renderParam( $param, $name );
					}
				}
			}
		}
		return $result;
	}
}



/**
* Tab Class for User Profile Page title display
* @package Community Builder : cb.core pluing
* @subpackage Page Title tab CB core module
* @author JoomlaJoe and Beat
*/
class getPageTitleTab  extends cbTabHandler {
	/**
	* Constructor
	*/
	function getPageTitleTab() {
		$this->cbTabHandler();
	}
	/**
	* Generates the HTML to display the user profile tab
	* @param  moscomprofilerTab   $tab       the tab database entry
	* @param  moscomprofilerUser  $user      the user being displayed
	* @param  int                 $ui        1 for front-end, 2 for back-end
	* @return mixed                          either string HTML for tab content, or false if ErrorMSG generated
	*/
	function getDisplayTab($tab,$user,$ui) {
		global $ueConfig;
		// Display user's name + "Profile Page"
		$params	=	$this->params;
		$title	=	cbReplaceVars( $params->get( 'title', '_UE_PROFILE_TITLE_TEXT' ), $user );
		$name	=	getNameFormat( $user->name, $user->username, $ueConfig['name_format'] );
		$return	=	'<div class="contentheading" id="cbProfileTitle">' . sprintf( $title, $name ) . "</div>\n";
		
		$return	.=	$this->_writeTabDescription( $tab, $user );
		
		return $return;
	}
}	// end class getPageTitleTab

/**
* Tab Class for User Profile Portrait/Avatar display
* @package Community Builder : cb.core pluing
* @subpackage Portrait tab CB core module
* @author JoomlaJoe and Beat
*/
class getPortraitTab  extends cbTabHandler {
	/**
	* Generates the HTML to display the user profile tab
	* @param  moscomprofilerTab   $tab       the tab database entry
	* @param  moscomprofilerUser  $user      the user being displayed
	* @param  int                 $ui        1 for front-end, 2 for back-end
	* @return mixed                          either string HTML for tab content, or false if ErrorMSG generated
	*/
	function getDisplayTab($tab,$user,$ui) {
		$return		=	$this->_writeTabDescription( $tab, $user, 'cbPortraitDescription' );
		return $return;
	}
}	// end class getPortraitTab

/**
* Tab Class for User Profile EDIT Contacts special fields display
* @package Community Builder : cb.core pluing
* @subpackage Contact tab CB core module
* @author JoomlaJoe and Beat
*/
class getContactTab extends cbTabHandler {
	/**
	* Generates the HTML to display the user edit tab
	* @param  moscomprofilerTab   $tab       the tab database entry
	* @param  moscomprofilerUser  $user      the user being displayed
	* @param  int                 $ui        1 for front-end, 2 for back-end
	* @return mixed                          either string HTML for tab content, or false if ErrorMSG generated
	*/
	function getEditTab($tab,$user,$ui) {
		$return		=	$this->_writeTabDescription( $tab, $user );
		return $return;
	}
}	// end class getContactTab

?>
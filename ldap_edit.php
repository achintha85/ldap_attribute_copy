<?php

/**
 * This script gets a copy of users with a attribute conifugred in the configuration called ATTRIBUTE_1
 * Then copies the ATTRIBUTE_1 attribute value to configured attribute called ATTRIBUTE_2
 * Please modify the configuration below to meet your needs
 */
 
/**********************
 * Script Configuration
 **********************/ 
$ldap_password = 'PASSWORD';
$ldap_username = 'cn=USER,dc=DC1,dc=DC2,dc=DC3';
$ldap_host = 'HOSTNAME';
$ldap_base_dn = 'ou=users,dc=DC1,dc=DC2,dc=DC3';
$attribute_to_copy_from = 'ATTRIBUTE_1';
$attribute_to_copy_to = 'ATTRIBUTE_2';
/**********************/

$ldap_connection = ldap_connect('$ldap_host');

if (FALSE === $ldap_connection) {

	echo "Uh-oh, something is wrong...";
}

// We have to set this option for the version of Active Directory we are using.
ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

if (TRUE === ldap_bind($ldap_connection, $ldap_username, $ldap_password)){

	$search_filter = '(&)';
	$attributes = array();
	$attributes[] = "uid";
	$attributes[] = "displayname";
	$attributes[] = $attribute_to_copy_to;
	$attributes[] = $attribute_to_copy_from;
	$result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter, $attributes);

	if(FALSE !== $result) {

		$entries = ldap_get_entries($ldap_connection, $result);

		for($x=0; $x<$entries['count']; $x++) {
	      
			if(!empty($entries[$x][$attribute_to_copy_from][0])) {
				
				$entry = array();
		
				$dn = strtolower(trim($entries[$x]['dn']));
				$copy_from_value = strtolower(trim($entries[$x][$attribute_to_copy_from][0]));
				$entry[$attribute_to_copy_to][] = $copy_from_value;
				$result = ldap_mod_replace($ldap_connection, $dn, $entry);	

				$ad_users[] = array(
					'uid' => strtolower(trim($entries[$x]['uid'][0])),
					'displayname' => strtolower(trim($entries[$x]['displayname'][0])),
					$attribute_to_copy_to => strtolower(trim($entries[$x][$attribute_to_copy_to][0])),
					$attribute_to_copy_from => $copy_from_value,
					'dn' => $dn,
					'successfully_modified' => $result
				);
			}
		}
	}

	ldap_unbind($ldap_connection); // Clean up after ourselves.
}

print_r($ad_users);
echo "\n\n";
echo "Retrieved ". count($ad_users) ." Active Directory users\n";

?>
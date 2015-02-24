<?php

/**
 * This script gets a copy of users with a attribute called new_mail
 * Then copies the new_mail attribute value to mail attribte
 * You'll be able to modiy it to perform similar functions
 */
$ldap_password = 'PASSWORD';
$ldap_username = 'cn=USER,dc=DC1,dc=DC2,dc=DC3';
$ldap_connection = ldap_connect('HOSTNAME');

if (FALSE === $ldap_connection) {

	echo "Uh-oh, something is wrong...";
}

// We have to set this option for the version of Active Directory we are using.
ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

if (TRUE === ldap_bind($ldap_connection, $ldap_username, $ldap_password)){

	$ldap_base_dn = 'ou=users,dc=DC1,dc=DC2,dc=DC3';
	$search_filter = '(&)';
	$attributes = array();
	$attributes[] = "uid";
	$attributes[] = "displayname";
	$attributes[] = "mail";
	$attributes[] = "new_mail";
	$result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter, $attributes);

	if(FALSE !== $result) {

		$entries = ldap_get_entries($ldap_connection, $result);

		for($x=0; $x<$entries['count']; $x++) {
	      
			if(!empty($entries[$x]['new_mail'][0])) {
				
				$entry = array();
		
				$dn = strtolower(trim($entries[$x]['dn']));
				$new_email = strtolower(trim($entries[$x]['new_mail'][0]));
				$entry['mail'][] = $new_email;
				$result = ldap_mod_replace($ldap_connection, $dn, $entry);	

				$ad_users[] = array(
					'uid' => strtolower(trim($entries[$x]['uid'][0])),
					'displayname' => strtolower(trim($entries[$x]['displayname'][0])),
					'mail' => strtolower(trim($entries[$x]['mail'][0])),
					'new_email' => $new_email,
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

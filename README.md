#ReadMe

This script gets a copy of users with a attribute called new_mail
Then copies the new_mail attribute value to mail attribte
You'll be able to modiy it to perform similar functions

##Running the Script

Configure the following items in the script.

```php
$ldap_password = 'PASSWORD';
$ldap_username = 'cn=USER,dc=DC1,dc=DC2,dc=DC3';
$ldap_host = 'HOSTNAME';
$ldap_base_dn = 'ou=users,dc=DC1,dc=DC2,dc=DC3';
$attribute_to_copy_from = 'ATTRIBUTE_1';
$attribute_to_copy_to = 'ATTRIBUTE_2';
```

Run the script from the commandline.

```bash
> php ldap_attribute_copy.php
```

# SB-Synchronization

At the moment, synchronization is implemented server group => forum group.<br>
Management is located in "Groups & Permissions" if the administrator has the right to manage groups.

#### Form:
* <b>Has a group</b><br>Here you need to specify which group the administrator should have.
* <b>On server(s)</b><br>Specify on which servers the user should have an admin access (must be on all these servers).
* <b>Give a group(s)</b><br>Which groups to assign to the user on the platform.

#### Requirements:
* XenForo 2.2.8+
* BlackTea/SteamAuth 1.7.9+

#### Installation:
1. Add to `src/config.php` connection credentials:
```php
$config['sbintegration'] = [
    'host' => '',
    'port' => 3306,
    'username' => '',
    'password' => '',
    'dbname' => ''
];
```
2. Install addon via CP or CLI.
3. In the options (admin.php?options/groups/pbySBSync/), specify the table prefix and the number of users to synchronize (per synchronization).

<?php
/* * ********************************************************** */
/**
 * Backups
 */
$_LANG['backups']['index']['panel_title'] = 'Manage your backups';
$_LANG['backups']['index']['name'] = 'Name';
$_LANG['backups']['index']['status'] = 'Status';
$_LANG['backups']['index']['size'] = 'Size';
$_LANG['backups']['index']['desc'] = 'Description';
$_LANG['backups']['index']['actions'] = 'Actions';
$_LANG['backups']['index']['confirm'] = 'Are you sure you want to restore?';
$_LANG['backups']['index']['restore'] = 'Restore';
$_LANG['backups']['index']['vm_not_found'] = 'VM not found!';
$_LANG['backups']['index']['not_found'] = 'List is empty';
/**/
$_LANG['backups']['restore']['success'] = 'Restoring started!';
/**/
$_LANG['backups']['other']['not_available'] = 'Functionality not available in your package!';
/**
 * DNS
 */
$_LANG['dns']['index']['panel_title'] = 'DNS Manager';
$_LANG['dns']['index']['add'] = 'Add New';
$_LANG['dns']['index']['domain'] = 'Domain';
$_LANG['dns']['index']['created'] = 'Created';
$_LANG['dns']['index']['actions'] = 'Actions';
$_LANG['dns']['index']['manage'] = 'Manage';
$_LANG['dns']['index']['confirm'] = 'Are you sure you want to delete?';
$_LANG['dns']['index']['delete'] = 'Delete';
$_LANG['dns']['index']['not_found'] = 'List is empty';
$_LANG['dns']['index']['dns_title'] = 'Name Servers';
$_LANG['dns']['index']['dns1'] = 'ns1.vultr.com';
$_LANG['dns']['index']['dns2'] = 'ns2.vultr.com';
/**/
$_LANG['dns']['create']['panel_title'] = 'DNS Manager - Add Domain';
$_LANG['dns']['create']['select_domain'] = 'Select domain from list:';
$_LANG['dns']['create']['input_domain'] = 'or enter other domain name';
$_LANG['dns']['create']['input_domain2'] = 'Enter domain name';
$_LANG['dns']['create']['ip'] = 'Server IP';
$_LANG['dns']['create']['create'] = 'Add DNS Zone';
$_LANG['dns']['create']['vm_not_found'] = 'Please create VM first!';
$_LANG['dns']['create']['connection_error'] = 'Connection error!';
$_LANG['dns']['create']['add_domain'] = 'Domain has been successfully added';
/**/
$_LANG['dns']['delete']['delete_success'] = 'Domain has been successfully removed!';
$_LANG['dns']['delete']['delete_error'] = 'Error when delete domain! Please try again';
/**/
$_LANG['dns']['manage']['panel_title'] = 'DNS Manager';
$_LANG['dns']['manage']['delete'] = 'Delete';
$_LANG['dns']['manage']['type'] = 'Type';
$_LANG['dns']['manage']['name'] = 'Name';
$_LANG['dns']['manage']['data'] = 'Data';
$_LANG['dns']['manage']['priority'] = 'Priority';
$_LANG['dns']['manage']['update'] = 'Update Domain';
$_LANG['dns']['manage']['add_new'] = 'Add New Record';
$_LANG['dns']['manage']['ttl'] = 'TTL (seconds)';
$_LANG['dns']['manage']['example'] = 'Example:';
$_LANG['dns']['manage']['add'] = 'Add';
$_LANG['dns']['manage']['delete_success'] = 'Record %var% has been deleted!';
$_LANG['dns']['manage']['update_success'] = 'Record %var% has been updated!';
$_LANG['dns']['manage']['delete_success_multi'] = 'Records: %var% have been deleted!';
$_LANG['dns']['manage']['update_success_multi'] = 'Records: %var% have been updated!';
$_LANG['dns']['manage']['add_success'] = 'Record has been successfully added!';
$_LANG['dns']['manage']['error'] = 'You can not manage this domain!';
$_LANG['dns']['manage']['not_found'] = 'List is empty';
$_LANG['dns']['manage']['record'] = 'Record ID';

/**
 * Graphs
 */
$_LANG['graphs']['index']['panel_title_incoming'] = 'Inbound Bandwidth';
$_LANG['graphs']['index']['panel_title_outgoing'] = 'Outbound Bandwidth';
$_LANG['graphs']['index']['empty_data'] = 'Usage graphs are not yet ready. Please come back later.';
/**
 * Main
 */
$_LANG['main']['index']['control_panel'] = 'Control Panel';
$_LANG['main']['index']['boot'] = 'Start';
$_LANG['main']['index']['reboot'] = 'Restart';
$_LANG['main']['index']['stop'] = 'Stop';
$_LANG['main']['index']['reinstall'] = 'Reinstall';
$_LANG['main']['index']['console'] = 'Console';
$_LANG['main']['index']['vps_name'] = 'VM Name';
$_LANG['main']['index']['cpus'] = 'CPU Count';
$_LANG['main']['index']['memory'] = 'Memory';
$_LANG['main']['index']['confirm_reinstall'] = 'Are you sure you want to reinstall your server? Any data on your server will be permanently lost!';
$_LANG['main']['index']['hdd'] = 'Storage';
$_LANG['main']['index']['template'] = 'OS Template';
$_LANG['main']['index']['templateISO'] = 'ISO Template';
$_LANG['main']['index']['ipaddress'] = 'IP Address';
$_LANG['main']['index']['root_password'] = 'Root Password';
$_LANG['main']['index']['root_pass_show'] = 'Show';
$_LANG['main']['index']['root_pass_hide'] = 'Hide';
$_LANG['main']['index']['stats'] = 'Stats';
$_LANG['main']['index']['bandwidth'] = 'Bandwidth';
$_LANG['main']['index']['usage_graphs'] = 'Usage Graphs';
$_LANG['main']['index']['details'] = 'Details';
$_LANG['main']['index']['app_info'] = 'Application Information';
$_LANG['main']['index']['status'] = 'Status';
$_LANG['main']['index']['vm_not_found'] = 'VM not found!';
$_LANG['main']['index']['vm_status_is'] = 'VM status is: ';
$_LANG['main']['index']['change_os'] = 'Change OS';
$_LANG['main']['index']['change_iso'] = 'Change ISO';
$_LANG['main']['index']['detach_iso'] = 'Detach ISO';
$_LANG['main']['index']['power_status'] = 'Power Status';
$_LANG['main']['index']['server_status'] = 'Server State';
$_LANG['main']['index']['main_ip'] = 'Main IP:';
$_LANG['main']['index']['internal_ip'] = 'Internal IP:';
$_LANG['main']['index']['networks'] = 'Networks';
$_LANG['main']['index']['netmask'] = 'Netmask';
$_LANG['main']['index']['address'] = 'Address';
$_LANG['main']['index']['gateway'] = 'Gateway';
$_LANG['main']['index']['vm_status'] = 'VM Status';
$_LANG['main']['index']['pass_undefined'] = 'Unknown. Password was set via snapshot restore.';
$_LANG['main']['index']['reverse_dns'] = 'Reverse DNS';
$_LANG['main']['index']['rev_dns_success'] = 'Reverse DNS updated. Changes may take 6-12 hours to become active';
$_LANG['main']['index']['detach_iso_success'] = 'ISO file was successfully detached';
$_LANG['main']['index']['default_ip'] = 'Default IP';
$_LANG['main']['index']['network'] = 'Network';
$_LANG['main']['index']['cidr'] = 'CIDR';
$_LANG['main']['index']['ip'] = 'IP';
$_LANG['main']['index']['reverse'] = 'Reverse';
$_LANG['main']['index']['actions'] = 'Actions';
$_LANG['main']['index']['confirm'] = 'Are you sure you want to remove this record?';
$_LANG['main']['index']['delete'] = 'Delete';
$_LANG['main']['index']['edit'] = 'Edit';
$_LANG['main']['index']['add'] = 'Add';
$_LANG['main']['index']['ipv6rev_set_success'] = 'Reverse DNS updated';
$_LANG['main']['index']['ipv6rev_bad_ip'] = 'Not valid IPv6 address!';
$_LANG['main']['index']['connection_error'] = 'Connection error!';
$_LANG['main']['index']['ipv6rev_delete_success'] = 'Reverse record removed';
$_LANG['main']['index']['label_change'] = 'Change';
$_LANG['main']['index']['save'] = 'Save';
$_LANG['main']['index']['label_change_cancel'] = 'Cancel';
$_LANG['main']['index']['label_success'] = 'Label changed!';
$_LANG['main']['index']['label_error'] = 'Please enter a different label';
$_LANG['main']['index']['change_app'] = 'Change APP';
$_LANG['main']['index']['change_os'] = 'Change OS';

$_LANG['main']['create']['hostname_empty'] = 'Please enter hostname!';
$_LANG['main']['create']['hostname_not_valid'] = 'Hostname is not valid!';
/**/
$_LANG['main']['create']['panel_title'] = 'Setup Your Server';
$_LANG['main']['create']['server_label'] = 'Server Label:';
$_LANG['main']['create']['server_label_placeholder'] = 'Enter your server label';
$_LANG['main']['create']['server_hostname'] = 'Hostname:';
$_LANG['main']['create']['server_hostname_placeholder'] = 'Enter your server hostname';
$_LANG['main']['create']['create'] = 'Create';
$_LANG['main']['create']['yes'] = 'Yes';
$_LANG['main']['create']['no'] = 'No';
$_LANG['main']['create']['notify_mail'] = 'Notify after creating ( mail ):';
$_LANG['main']['create']['backups'] = 'Auto backups:';
$_LANG['main']['create']['ssh_cert'] = 'Select SSH certificate:';
$_LANG['main']['create']['ssh_install'] = 'Add SSH Key:';
$_LANG['main']['create']['priv_net'] = 'Turn on/off private network:';
$_LANG['main']['create']['ipv6'] = 'Turn on/off IPv6:';
$_LANG['main']['create']['script'] = 'Select startup script:';
$_LANG['main']['create']['script_install'] = 'Startup script:';
$_LANG['main']['create']['manage'] = 'Manage';
$_LANG['main']['create']['ipxe'] = 'iPXE Custom Script';
$_LANG['main']['create']['ipxe_url'] = 'iPXE Chain URL';
$_LANG['main']['create']['iso'] = 'ISO';
$_LANG['main']['create']['app'] = 'Select APP:';
$_LANG['main']['create']['snapshot'] = 'Select snapshot:';
$_LANG['main']['create']['system'] = 'Select OS type:';
$_LANG['main']['create']['location'] = 'Select location:';
$_LANG['main']['create']['ipxe_not_found'] = 'IPXE Url not found!';
$_LANG['main']['create']['pxe_script_error'] = 'IPXE script must start with #!ipxe';
$_LANG['main']['create']['iso_not_found'] = 'ISO not selected!';
$_LANG['main']['create']['pxe_script_error'] = 'IPXE script must start with #!ipxe';
$_LANG['main']['create']['ipxe_script_not_found'] = 'IPXE script not selected!';
$_LANG['main']['create']['boot_script_not_found'] = 'Boot script not selected!';
$_LANG['main']['create']['iso_type_not_found'] = 'Undefined OS type';
$_LANG['main']['create']['created_success'] = 'Creating VPS has started. Please wait a moment';
$_LANG['main']['create']['no_script_found'] = 'No boot scripts found. Please create it first';
$_LANG['main']['create']['no_ssh_found'] = 'No SSH keys found. Please create it first';
$_LANG['main']['create']['reload_info'] = 'When VM is ready, page will reload automatically.';
/**
 * SSHKeys
 */
$_LANG['sshkeys']['index']['panel_title'] = 'Manage SSH Keys';
$_LANG['sshkeys']['index']['id'] = 'ID';
$_LANG['sshkeys']['index']['name'] = 'Name';
$_LANG['sshkeys']['index']['created'] = 'Created';
$_LANG['sshkeys']['index']['actions'] = 'Actions';
$_LANG['sshkeys']['index']['add'] = 'Add New';
$_LANG['sshkeys']['index']['delete'] = 'Delete';
$_LANG['sshkeys']['index']['confirm'] = 'Are you sure you want to delete?';
$_LANG['sshkeys']['index']['not_found'] = 'List is empty';
/**/
$_LANG['sshkeys']['delete']['delete_success'] = 'SSH key has been successfully deleted!';
$_LANG['sshkeys']['delete']['delete_error'] = 'Error when deleted ssh key! Please try again';
/**/
$_LANG['sshkeys']['add']['name'] = 'Name';
$_LANG['sshkeys']['add']['ssh_key'] = 'SSH Key';
$_LANG['sshkeys']['add']['create'] = 'Create';
$_LANG['sshkeys']['add']['script_name'] = 'SSH key name';
$_LANG['sshkeys']['add']['panel_title'] = 'Manage SSH keys - create new';
$_LANG['sshkeys']['add']['add_success'] = 'SSH key has been successfully created!';
$_LANG['sshkeys']['add']['add_error'] = 'Error when create SSH key!';
/**
 * Scripts
 */
$_LANG['scripts']['index']['panel_title'] = 'Startup Scripts';
$_LANG['scripts']['index']['add'] = 'Create New';
$_LANG['scripts']['index']['id'] = 'ID';
$_LANG['scripts']['index']['name'] = 'Name';
$_LANG['scripts']['index']['type'] = 'Type';
$_LANG['scripts']['index']['created'] = 'Created';
$_LANG['scripts']['index']['actions'] = 'Actions';
$_LANG['scripts']['index']['confirm'] = 'Are you sure you want to delete?';
$_LANG['scripts']['index']['delete'] = 'Delete';
$_LANG['scripts']['index']['undefined'] = 'undefined';
$_LANG['scripts']['index']['show'] = 'Show';
$_LANG['scripts']['index']['hide'] = 'Hide';
$_LANG['scripts']['index']['not_found'] = 'List is empty';
/**/
$_LANG['scripts']['add']['panel_title'] = 'Create new startup script';
$_LANG['scripts']['add']['name'] = 'Name';
$_LANG['scripts']['add']['type'] = 'Type';
$_LANG['scripts']['add']['script'] = 'Script';
$_LANG['scripts']['add']['create'] = 'Create';
$_LANG['scripts']['add']['boot_script_error'] = 'Boot script must start with #!/bin/sh';
$_LANG['scripts']['add']['pxe_script_error'] = 'IPXE script must start with #!ipxe';
$_LANG['scripts']['add']['undefined_script_type'] = 'Undefined script type';
$_LANG['scripts']['add']['success_add'] = 'Startup script has been successfully created';
$_LANG['scripts']['add']['error_add'] = 'Error when create script! Please try again';
/**/
$_LANG['scripts']['delete']['success_delete'] = 'Startup script has been successfully deleted!';
$_LANG['scripts']['delete']['error_delete'] = 'Error when deleted script! Please try again';
/**/
$_LANG['scripts']['core']['connection_error'] = 'Connection error! Please try again later.';
/**
 * Snapshots
 */
$_LANG['snapshots']['index']['panel_title'] = 'Snapshots (used %use% from %available% available)';
$_LANG['snapshots']['index']['create_new'] = 'Create New';
$_LANG['snapshots']['index']['id'] = 'ID';
$_LANG['snapshots']['index']['description'] = 'Description';
$_LANG['snapshots']['index']['size'] = 'Size';
$_LANG['snapshots']['index']['status'] = 'Status';
$_LANG['snapshots']['index']['created'] = 'Created';
$_LANG['snapshots']['index']['actions'] = 'Actions';
$_LANG['snapshots']['index']['confirm'] = 'Are you sure you want to delete?';
$_LANG['snapshots']['index']['confirmrestore'] = 'Are you sure you want to restore?';
$_LANG['snapshots']['index']['delete'] = 'Delete';
$_LANG['snapshots']['index']['restore'] = 'Restore';
$_LANG['snapshots']['index']['not_found'] = 'List is empty';
$_LANG['snapshots']['index']['pending'] = '(snapshot in progress)';
/**/
$_LANG['snapshots']['other']['not_available'] = 'Functionality not available in your package!';
/**/
$_LANG['snapshots']['add']['panel_title'] = 'Create new snapshot';
$_LANG['snapshots']['add']['description'] = 'Description';
$_LANG['snapshots']['add']['create'] = 'Create snapshot';
$_LANG['snapshots']['add']['snapshot_limit'] = 'You have reached snapshots limit!';
$_LANG['snapshots']['add']['created'] = 'Snapshot has been successfully created';
$_LANG['snapshots']['add']['delete_error'] = 'Error when create snapshot! Please try again';
/**/
$_LANG['snapshots']['delete']['success'] = 'Snapshot has been successfully deleted!';
$_LANG['snapshots']['delete']['error'] = 'Error when deleted snapshot! Please try again';
/**/
$_LANG['snapshots']['restore']['success'] = 'Restoring snapshot has been started. It can take a while.';
$_LANG['snapshots']['restore']['error'] = 'Error when restored snapshot! Please try again';
/**
 * OS Change
 */
$_LANG['oschange']['index']['panel_title'] = 'Change OS';
$_LANG['oschange']['index']['panel_title_app'] = 'Change Application';
$_LANG['oschange']['index']['label'] = 'Select New OS';
$_LANG['oschange']['index']['label_app'] = 'Select New Application';
$_LANG['oschange']['index']['change'] = 'Change';
$_LANG['oschange']['index']['confirm_os'] = 'Are you sure you want to change this server to a different operating system? All data will be lost in the process!';
$_LANG['oschange']['index']['confirm_app'] = 'Are you sure you want to change this server to a different application? All data will be lost in the process!';
$_LANG['oschange']['index']['current'] = 'Current OS is ';
$_LANG['oschange']['index']['success'] = 'OS successfully changed!';
$_LANG['oschange']['index']['success_app'] = 'Application successfully changed!';
$_LANG['oschange']['index']['no_available_oses'] = 'There are no OS available to change!';
$_LANG['oschange']['index']['no_available_app'] = 'There are no application available to change!';
$_LANG['oschange']['index']['vm_not_found'] = 'VM not found!';
$_LANG['oschange']['index']['back'] = 'Back';
/**
 * ISO Change
 */
$_LANG['isochange']['index']['panel_title'] = 'Change ISO';
$_LANG['isochange']['index']['label'] = 'Select New ISO File';
$_LANG['isochange']['index']['current'] = 'Current ISO is ';
$_LANG['isochange']['index']['success'] = 'ISO successfully changed!';
$_LANG['isochange']['index']['no_available_isos'] = 'There are no ISO file available to change!';
/**
 * Others
 */
$_LANG['elements']['buttons']['main_page'] = 'Main Page';
$_LANG['elements']['buttons']['snapshots'] = 'Snapshots';
$_LANG['elements']['buttons']['scripts'] = 'Startup Scripts';
$_LANG['elements']['buttons']['ssh_keys'] = 'SSH Keys';
$_LANG['elements']['buttons']['dns'] = 'DNS';
$_LANG['elements']['buttons']['backups'] = 'Backups';
/**
 * 
 */
$_LANG['core']['client']['create_vm_first'] = 'Please create VM first!';
$_LANG['core']['client']['action_not_found'] = 'Action Not Found (404)';
$_LANG['core']['client']['controller_not_found'] = 'Module Not Found (404)';
$_LANG['core']['client']['api_connection_error'] = 'Can not connect to Vultr!';
/**
 * AJAX
 */
$_LANG['core']['ajax']['start_success'] = 'Server is running!';
$_LANG['core']['ajax']['reboot_success'] = 'Restart started!';
$_LANG['core']['ajax']['stop_success'] = 'Server has been stopped!';
$_LANG['core']['ajax']['reinstall_success'] = 'Reinstall OS has been started!';
$_LANG['core']['ajax']['checkStatus'] = 'Server status is: ';
$_LANG['core']['ajax']['start_success'] = 'Server is running!';
$_LANG['core']['ajax']['service_not_active'] = 'Service is not active!';
/**
 * Hooks
 */
$_LANG['core']['hook']['upgrade_empty'] = 'No options available to upgrade the product!';
$_LANG['core']['hook']['connection_error'] = 'Please check your API key! Connection error!';
$_LANG['core']['hook']['save_key'] = 'Please first save your API key!';
$_LANG['core']['hook']['configurable_options_success'] = 'Vultr Configurable Options successfully created! Please configure pricing.';
$_LANG['core']['hook']['custom_field_success'] = 'Custom fields have been created successfully!';
$_LANG['core']['hook']['custom_field_exist'] = 'Custom fields already exist!';
/**
 * Other
 */
$_LANG['core']['action']['action_not_supported'] = 'Action not supported!';
$_LANG['core']['action']['not_found_subid'] = 'Not found VPS ID';
$_LANG['core']['action']['no_upgrades_available'] = 'There are currently no upgrades available for this package.';
$_LANG['core']['action']['cant_upgrade'] = 'You can not upgrades to this package.';
$_LANG['core']['action']['template_not_found'] = 'Template file not found';
$_LANG['vps_ajax']['unknown'] = '';
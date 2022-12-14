<?php

use Illuminate\Database\Capsule\Manager as Capsule;

if (!class_exists('VultrHelper')) {

    class VultrHelper {

        public static function parseRegions($data) {

            $regions = '';
            foreach ($data as $key => $value) {
                $regions.=',' . $value['name'] . ' (' . $value['country'] . ')';
            }

            return ltrim($regions, ',');
        }

        public static function parsePlans($data) {
            $plans = array();
            foreach ($data as $key => $value) {
                //if ($value['plan_type'] != 'SSD') {
                //    unset($data[$key]);
                //    continue;
                //}
                if (empty($value['available_locations'])) {
                    unset($data[$key]);
                    continue;
                } else {
                    $plans[$value['VPSPLANID']] = $value['VPSPLANID'] . '|' . $value['name'] . ' (' . $value['plan_type'] . ') - ' . $value['price_per_month'] . '$';
                }
            }
            return $plans;
        }

        public static function parseOS($data) {
            $os = '';
            foreach ($data as $key => $value) {
                $os.=',' . $value['name'];
            }

            return ltrim($os, ',');
        }

        public static function parseAPPS($data) {
            $apps = '';
            foreach ($data as $key => $value) {
                $apps.=',' . $value['deploy_name'];
            }

            return ltrim($apps, ',');
        }

        public static function getOsFamilyList($data) {
            $oses = array();
            foreach ($data as $key => $value) {
                $oses[current(explode(' ', $value['name']))] = current(explode(' ', $value['name']));
            }
            return $oses;
        }

        public static function getPayAppList($data) {
            $apps = array();
            foreach ($data as $key => $value) {
                if ($value['surcharge'] > 0) {
                    $apps[$value['deploy_name']] = $value['deploy_name'];
                }
            }
            return $apps;
        }

        public static function getMountedIsoFileName($vultrApi, $subid) {
            //Get attached ISO file name
            $mountedIsoId = $vultrApi->iso_status($subid)["ISOID"];
            $apiIsosList = $vultrApi->iso_list();
            $mountedIsoName = "";
            foreach ($apiIsosList as $value) {
                if ($value['ISOID'] == $mountedIsoId)
                    $mountedIsoName = $value['filename'];
            }
            return $mountedIsoName;
        }

        public static function addCustomFields($productID) {
            $result = Capsule::table('tblcustomfields')->where('type', 'product')->where('relid', $productID)->where('fieldname', 'subid|Virtual machine ID')->get();
            if (!$result) {
                Capsule::table('tblcustomfields')->insert(array('type' => 'product', 'relid' => $productID, 'fieldname' => 'subid|Virtual machine ID', 'fieldtype' => 'text', 'adminonly' => 'on'));
            }
        }

        public static function configurableOptions($productID) {

            logActivity('in configurableOptions logging...', 0);
            logModuleCall('Vultr', 'configurableOptions', 'start', '1', '2', '3');
            $productApiKey = self::getProductConfigOptions($productID, 'configoption1');
            if ($productApiKey) {
                $vultrAPI = new \VultrAPI($productApiKey);
                logActivity('in configurableOptions logging... API Key: '.$productApiKey, 0);
                if ($vultrAPI->checkConnection()) {
                    return self::prepareConfigurableOptions($vultrAPI, $productID);
                } else {
                    return array('status' => FALSE, 'message' => LangHelper::T('core.hook.connection_error'));
                }
            } else {
                return array('status' => FALSE, 'message' => LangHelper::T('core.hook.save_key'));
            }
        }

        public static function customFields($productID) {
            $result = Capsule::table('tblcustomfields')->where('fieldname', 'subid|Virtual machine ID')->where('type', 'product')->where('relid', $productID)->select('id')->get();
            if (!$result) {
                Capsule::table('tblcustomfields')->insert(array('type' => 'product', 'relid' => $productID, 'fieldname' => 'subid|Virtual machine ID', 'fieldtype' => 'text', 'adminonly' => 'on'));
                return array('status' => true, 'reload' => true, 'message' => LangHelper::T('core.hook.custom_field_success'));
            }
            return array('status' => true, 'message' => LangHelper::T('core.hook.custom_field_exist'));
        }

        public static function getProductConfigOptions($productID, $field = 'all', $default = '') {
            $result = Capsule::table('tblproducts')->where('id', $productID)->first();
            if ($field == 'all') {
                return $result;
            } else {
                if (isset($result->{$field})) {
                    return $result->{$field};
                } else {
                    return $default;
                }
            }
        }

        private static function prepareConfigurableOptions($vultrAPI, $productID) {

            $checkIsset = Capsule::table('tblproductconfiglinks')->where('pid', $productID)->get();

            if ($checkIsset) {
                return array('status' => false, 'message' => 'Product configurable options already exist!');
            }
            $productConfigGroupID = Capsule::table('tblproductconfiggroups')->insertGetId(array('name' => 'Vultr', 'description' => 'Autogenerated Vultr #' . $productID . ' Configurable Options'));
            Capsule::table('tblproductconfiglinks')->insert(array('gid' => $productConfigGroupID, 'pid' => $productID));
            $autoBackupID = Capsule::table('tblproductconfigoptions')->insertGetId(array('gid' => $productConfigGroupID, 'optionname' => 'auto_backups|Auto backups', 'optiontype' => '3'));
            Capsule::table('tblproductconfigoptionssub')->insert(array('configid' => $autoBackupID, 'optionname' => 'Yes'));
            $snapshotID = Capsule::table('tblproductconfigoptions')->insertGetId(array('gid' => $productConfigGroupID, 'optionname' => 'snapshots|Snapshots limit', 'optiontype' => '4', 'qtyminimum' => '0', 'qtymaximum' => '10'));
            Capsule::table('tblproductconfigoptionssub')->insert(array('configid' => $snapshotID, 'optionname' => '1'));
            $osID = Capsule::table('tblproductconfigoptions')->insertGetId(array('gid' => $productConfigGroupID, 'optionname' => 'os_type|OS Type', 'optiontype' => '1'));

            foreach ($vultrAPI->os_list() as $value) {
                if ($value['name'] == "Custom")
                    $value['name'] = "ISO";
                Capsule::table('tblproductconfigoptionssub')->insert(array('configid' => $osID, 'optionname' => $value['OSID'] . '|' . $value['name']));
            }
            $appID = Capsule::table('tblproductconfigoptions')->insertGetId(array('gid' => $productConfigGroupID, 'optionname' => 'application|Application', 'optiontype' => '1'));
            Capsule::table('tblproductconfigoptionssub')->insert(array('configid' => $appID, 'optionname' => '0|None'));
            foreach ($vultrAPI->app_list() as $value) {
                Capsule::table('tblproductconfigoptionssub')->insert(array('configid' => $appID, 'optionname' => $value['APPID'] . '|' . $value['deploy_name']));
            }

            $snapshotSelectID = Capsule::table('tblproductconfigoptions')->insertGetId(array('gid' => $productConfigGroupID, 'optionname' => 'snapshot_select|Snapshot', 'optiontype' => '1'));
            Capsule::table('tblproductconfigoptionssub')->insert(array('configid' => $snapshotSelectID, 'optionname' => '0|None'));
            foreach ($vultrAPI->snapshot_list() as $value) {
                Capsule::table('tblproductconfigoptionssub')->insert(array('configid' => $snapshotSelectID, 'optionname' => $value['SNAPSHOTID'] . '|' . $value['description']));
            }

            return array('status' => true, 'reload' => true, 'message' => LangHelper::T('core.hook.configurable_options_success'));
        }

        public static function checkUserOSisApp($vultrAPI, $product) {
            $apps = $vultrAPI->app_list();
            foreach ($apps as $value) {
                if ($value['deploy_name'] == $product)
                    return $value['APPID'];
            }
            return false;
        }

        public static function checkUserOSisWin($vultrAPI, $product) {
            $oses = $vultrAPI->os_list();
            foreach ($oses as $value) {
                if ($value['name'] == $product && $value['windows'])
                    return $value['APPID'];
            }
            return false;
        }

        public static function prepareRegionList($regions, $plans, $plan) {
            $availableRegions = $plans[$plan]["available_locations"];
            foreach ($regions as $value) {
                if (!in_array($value["DCID"], $availableRegions)) {
                    unset($regions[$value["DCID"]]);
                }
            }
            return $regions;
        }

        public static function prepareSnapshotList($snapshots) {
            return $snapshots;
        }

        public static function getVMParams($params) {
            $service = Capsule::table('tblhosting')
                            ->where('id', $params['serviceid'])->first();
            $product = Capsule::table('tblproducts')
                            ->where('id', $params['pid'])->first();
            $customField = Capsule::table('tblcustomfields')
                            ->where('type', 'product')
                            ->where('relid', $params['pid'])
                            ->where('fieldname', 'LIKE', 'subid|%')->first();
            if ($customField) {
                $customFieldValue = Capsule::table('tblcustomfieldsvalues')
                                ->where('fieldid', $customField->id)
                                ->where('relid', $params['serviceid'])->first();
                return array('service' => $service, 'product' => $product, 'customField' => $customField, 'customFieldValue' => $customFieldValue);
            }
            return FALSE;
        }

        public static function startVMAction($params) {
            $vmParams = self::getVMParams($params);
            if ($vmParams["service"]->domainstatus != 'Active') {
                return array('status' => FALSE, 'message' => LangHelper::T('core.ajax.service_not_active'));
            }
            if ($vmParams) {
                $vultrAPI = new VultrAPI($vmParams['product']->configoption1);
                $code = $vultrAPI->start($vmParams['customFieldValue']->value);
                if ($code == '200') {
                    return array('status' => TRUE, 'message' => LangHelper::T('core.ajax.start_success'));
                } else {
                    return array('status' => FALSE, 'message' => $code);
                }
            }
            return array('status' => FALSE);
        }

        public static function rebootVMAction($params) {
            $vmParams = self::getVMParams($params);
            if ($vmParams["service"]->domainstatus != 'Active') {
                return array('status' => FALSE, 'message' => LangHelper::T('core.ajax.service_not_active'));
            }
            if ($vmParams) {
                $vultrAPI = new VultrAPI($vmParams['product']->configoption1);
                $code = $vultrAPI->reboot($vmParams['customFieldValue']->value);
                if ($code == '200') {
                    return array('status' => TRUE, 'message' => LangHelper::T('core.ajax.reboot_success'));
                } else {
                    return array('status' => FALSE, 'message' => $code);
                }
            }
            return array('status' => FALSE);
        }

        public static function stopVMAction($params) {            
            $vmParams = self::getVMParams($params);
            if ($vmParams["service"]->domainstatus != 'Active') {
                return array('status' => FALSE, 'message' => LangHelper::T('core.ajax.service_not_active'));
            }
            if ($vmParams) {
                $vultrAPI = new VultrAPI($vmParams['product']->configoption1);
                $code = $vultrAPI->halt($vmParams['customFieldValue']->value);
                if ($code == '200') {
                    return array('status' => TRUE, 'message' => LangHelper::T('core.ajax.stop_success'));
                } else {
                    return array('status' => FALSE, 'message' => $code);
                }
            }
            return array('status' => FALSE);
        }

        public static function reinstallVMAction($params) {
            $vmParams = self::getVMParams($params);
            $snapshotId = filter_input(INPUT_POST, 'snapshotId');
//            logModuleCall('Vultr Provision', 'vultr.helper.php', 'snapshotId', '1', $snapshotId);
            if ($vmParams["service"]->domainstatus != 'Active') {
                return array('status' => FALSE, 'message' => LangHelper::T('core.ajax.service_not_active'));
            }
            if ($vmParams) {
                $vultrAPI = new VultrAPI($vmParams['product']->configoption1);
                $code = $vultrAPI->restore_snapshot($vmParams['customFieldValue']->value, $snapshotId);
                if ($code == '200') {
                    return array('status' => TRUE, 'message' => LangHelper::T('core.ajax.reinstall_success'));
                } else {
                    return array('status' => FALSE, 'message' => $code);
                }
            }
            return array('status' => FALSE);
        }

        public static function checkStatusVMAction($params) {
            $vmParams = self::getVMParams($params);
            if ($vmParams) {
                $vultrAPI = new VultrAPI($vmParams['product']->configoption1, 1);
                $servers = $vultrAPI->server_list();
                if (isset($servers[$vmParams['customFieldValue']->value])) {
                    $status = $servers[$vmParams['customFieldValue']->value]['status'];
                    if ($status == 'pending') {
                        $status = 'installing';
                    }
                    return array('status' => TRUE, 'vm_status' => $status, 'message' => LangHelper::T('core.ajax.checkStatus') . $servers[$vmParams['customFieldValue']->value]['power_status'], 'server_state' => $servers[$vmParams['customFieldValue']->value]['server_state'], 'power_status' => $servers[$vmParams['customFieldValue']->value]['power_status']);
                } else {
                    return array('status' => FALSE);
                }
            }
            return array('status' => FALSE);
        }

        public static function getUserServiceSnapshots($snapshots, $clientID, $serviceID = false) {
            $listQuery = Capsule::table('vultr_snapshots')
                    ->select('SNAPSHOTID')
                    ->where('client_id', $clientID);
            if ($serviceID) {
                $listQuery = $listQuery->where('service_id', $serviceID);
            }
            $listQuery = $listQuery->get();
            $list = array();
            foreach ($listQuery as $value) {
                $list[$value->SNAPSHOTID] = $value->SNAPSHOTID;
            }
            foreach ($snapshots as $key => $value) {
                if (!in_array($value['SNAPSHOTID'], $list)) {
                    unset($snapshots[$key]);
                } else {
                    $snapshots[$key]['size'] = VultrHelper::recalcSize($snapshots[$key]['size']);
                }
            }
            return $snapshots;
        }

        /*
         * Get global snapshots
         */

        public static function getGlobalSnapshots($snapshotList) {
            $globalSnapshots = Capsule::table('tbladdonmodules')->select('value')->where([
                        ['module', '=', 'vultr'],
                        ['setting', '=', 'snapshotsSettings'],
                    ])->first();
            $globalSnapshots = unserialize($globalSnapshots->value);
            $globalList = [];
            foreach ($snapshotList as $key => $value) {
                if (array_key_exists($key, $globalSnapshots)) {
                    $value['size'] = self::recalcSize($value['size']);
                    $globalList[$key] = $value;
                }
            }
            return $globalList;
        }

        public static function removeUncompleteSnapshots($snapshotList = []) {
            $allSnapshots = [];
            foreach ($snapshotList as $key => $value) {
                if ($value['status'] == "complete") {
                    $allSnapshots[$key] = $value;
                }
            }
            return $allSnapshots;
        }

        public static function getAllSnapshots($snapshotList, $userId) {
            $userSnapshotsList = self::getUserServiceSnapshots($snapshotList, $userId);
            $globalSnapshotsList = self::getGlobalSnapshots($snapshotList);
            $allSnapshots = array_merge($userSnapshotsList, $globalSnapshotsList);
            return self::removeUncompleteSnapshots($allSnapshots);
        }

        public static function getAvailableRegion($regionList) {
            $serverLocationSettings = Capsule::table('tbladdonmodules')->select('value')->where([
                        ['module', '=', 'vultr'],
                        ['setting', '=', 'locationSettings'],
                    ])->first();
            $locationSettings = unserialize($serverLocationSettings->value);
            if (!empty($locationSettings)) {
                foreach ($regionList as $key => $value) {
                    if (array_key_exists($value['DCID'], $locationSettings)) {
                        unset($regionList[$key]);
                    }
                }
            }
            return $regionList;
        }

        public static function getAvailableIsos($isosList) {

            $isoSettings = Capsule::table('tbladdonmodules')->select('value')->where([
                        ['module', '=', 'vultr'],
                        ['setting', '=', 'isoSettings'],
                    ])->first();
            $isoSettingsUnserialized = unserialize($isoSettings->value);

            if (!empty($isoSettingsUnserialized)) {
                foreach ($isosList as $key => $value) {
                    if (!array_key_exists($value['ISOID'], $isoSettingsUnserialized)) {
                        unset($isosList[$key]);
                    }
                }
            } else
                $isosList = [];
            return $isosList;
        }

        /*
         * End global snapshots
         */

        public static function getUserScripts($clientID, $scripts) {
            $allowScripts = Capsule::table('vultr_scripts')->where('client_id', $clientID)->get();
            if (empty($allowScripts)) {
                return array();
            } else {
                $allows = array();
                foreach ($allowScripts as $key => $value) {
                    $allows[$key] = $value->SCRIPTID;
                }

                foreach ($scripts as $key => $value) {
                    $index = array_search($value['SCRIPTID'], $allows);
                    if (is_int($index)) {
                        $scripts[$key]['type'] = $allowScripts[$index]->type;
                    } else {
                        unset($scripts[$key]);
                    }
                }
            }
            return $scripts;
        }

        public static function getUserSSHKeys($clientID, $keys) {
            $allowKeys = Capsule::table('vultr_sshkeys')->where('client_id', $clientID)->get();
            if (empty($allowKeys)) {
                return array();
            } else {
                $allows = array();
                foreach ($allowKeys as $key => $value) {
                    $allows[] = $value->SSHKEYID;
                }
                foreach ($keys as $key => $value) {
                    if (!in_array($value['SSHKEYID'], $allows)) {
                        unset($keys[$key]);
                    }
                }
                return $keys;
            }
        }

        public static function getUserBackups($backups, $servers, $clientID) {
            $vms = Capsule::table('tblcustomfieldsvalues')
                    ->join('tblcustomfields', 'tblcustomfieldsvalues.fieldid', '=', 'tblcustomfields.id')
                    ->join('tblhosting', 'tblcustomfieldsvalues.relid', '=', 'tblhosting.id')
                    ->where('tblhosting.userid', $clientID)
                    ->where('tblcustomfields.fieldname', 'LIKE', 'subid|%')
                    ->where('tblcustomfieldsvalues.value', '!=', '')
                    ->select('tblcustomfieldsvalues.value')
                    ->get();
            $vmList = array();
            foreach ($vms as $vm) {
                $vmList[] = $vm->value;
            }
            $ips = array();
            foreach ($servers as $vm) {
                if (in_array($vm['SUBID'], $vmList)) {
                    $ips[] = $vm['main_ip'];
                }
            }
            $returnBackups = array();
            foreach ($backups as $k => $vm) {
                foreach ($ips as $ip) {
                    if (strpos($vm['description'], $ip) !== false) {
                        $returnBackups[$k] = $vm;
                        $returnBackups[$k]['size'] = self::recalcSize($returnBackups[$k]['size']);
                    }
                }
            }
            return $returnBackups;
        }

        public static function cleanServiceParams($serviceID) {
            $service = Capsule::table('tblhosting')
                            ->where('id', $serviceID)->first();
            $customField = Capsule::table('tblcustomfields')
                            ->where('type', 'product')
                            ->where('relid', $service->packageid)
                            ->where('fieldname', 'LIKE', 'subid|%')->first();
            if ($customField) {
                Capsule::table('tblcustomfieldsvalues')
                        ->where('fieldid', $customField->id)
                        ->where('relid', $serviceID)->update(array('value' => ''));
            }
            Capsule::table('vultr_snapshots')
                    ->where('service_id', $serviceID)->delete();
            Capsule::table('vultr_dns')
                    ->where('service_id', $serviceID)->delete();
        }

        public static function recalcSize($size) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $power = max(0, (int) $size) > 0 ? floor(log($size, 1024)) : 0;
            return number_format($size / pow(1024, $power), 2, '.', ',') . $units[$power];
        }

        public static function getAllOSAndAppCustomFields() {
            $fields = Capsule::table('tblproductconfigoptions')
                            ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                            ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                            ->join('tblproductgroups', 'tblproductgroups.id', '=', 'tblproducts.gid')
                            ->where('tblproducts.servertype', '=', 'vultr')
                            ->where('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                            ->orWhere('tblproductconfigoptions.optionname', 'like', 'application|%')
                            ->select('tblproductconfigoptions.gid', 'tblproductconfigoptions.id', 'optionname')->get();
            $return = array();
            foreach ($fields as $value) {
                $name = explode('|', $value->optionname);
                if ($name[0] == 'os_type') {
                    $return[$value->gid]['appID'] = Capsule::table('tblproductconfigoptionssub')->select('id')->where('optionname', '186|Application')->where('configid', $value->id)->first();
                    $return[$value->gid]['id'] = $value->id;
                } else {
                    $return[$value->gid]['app'] = $value->id;
                }
            }
            return $return;
        }

        public static function getAllowProductUpgradeOptions() {
            $fields = Capsule::table('tblproductconfigoptions')
                            ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                            ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                            ->join('tblproductgroups', 'tblproductgroups.id', '=', 'tblproducts.gid')
                            ->where('tblproducts.servertype', '=', 'vultr')
                            ->where('tblproductconfigoptions.optionname', 'like', 'auto_backups|%')
                            ->orWhere('tblproductconfigoptions.optionname', 'like', 'application|%')
                            ->select('tblproductconfigoptions.id')->get();
            $return = array();
            foreach ($fields as $value) {
                $return[$value->id] = $value->id;
            }
            return $return;
        }

        public static function getAllowProductUpgrades($sid) {
            $serviceInfo = Capsule::table('tblhosting')->where('id', $sid)->first();
            $pInfo = Capsule::table('tblproducts')->where('id', $serviceInfo->packageid)->first();
            $vultrAPI = new VultrAPI($pInfo->configoption1, 1);
            if ($vultrAPI->checkConnection()) {
                $vmInfo = self::getVMParams(array('serviceid' => $sid, 'pid' => $serviceInfo->packageid));
                if (isset($vmInfo['customFieldValue']->value)) {
                    return $vultrAPI->upgrade_plan_list($vmInfo['customFieldValue']->value);
                }
            }
            return array();
        }

        public static function getAllowOSChange($params) {
            $serviceInfo = Capsule::table('tblhosting')->where('id', $params['id'])->first();
            $pInfo = Capsule::table('tblproducts')->where('id', $serviceInfo->packageid)->first();
            if ($pInfo->servertype == 'vultr') {
                $vultrAPI = new VultrAPI($pInfo->configoption1, 1);
                if ($vultrAPI->checkConnection()) {
                    $vmInfo = self::getVMParams(array('serviceid' => $params['id'], 'pid' => $serviceInfo->packageid));
                    if (isset($vmInfo['customFieldValue']->value)) {
                        $list = $vultrAPI->os_change_list($vmInfo['customFieldValue']->value);
                        $return = array();
                        foreach ($list as $k => $v) {
                            $return[$k] = $v['name'];
                        }
                        return $return;
                    }
                }
            }
            return array();
        }

        public static function getAllowAPPsChange($params) {
            $serviceInfo = Capsule::table('tblhosting')->where('id', $params['id'])->first();
            $pInfo = Capsule::table('tblproducts')->where('id', $serviceInfo->packageid)->first();
            if ($pInfo->servertype == 'vultr') {
                $vultrAPI = new VultrAPI($pInfo->configoption1, 1);
                if ($vultrAPI->checkConnection()) {
                    $vmInfo = self::getVMParams(array('serviceid' => $params['id'], 'pid' => $serviceInfo->packageid));
                    if (isset($vmInfo['customFieldValue']->value)) {
                        $list = $vultrAPI->app_change_list($vmInfo['customFieldValue']->value);
                        $return = array();
                        foreach ($list as $k => $v) {
                            $return[$k] = $v['deploy_name'];
                        }
                        return $return;
                    }
                }
            }
            return array();
        }

        public static function getAllowOSChangeIDS($sid, $oses) {
            $fields = Capsule::table('tblproductconfigoptions')
                            ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                            ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                            ->join('tblhosting', 'tblhosting.packageid', '=', 'tblproducts.id')
                            ->join('tblproductgroups', 'tblproductgroups.id', '=', 'tblproducts.gid')
                            ->where('tblproducts.servertype', '=', 'vultr')
                            ->where('tblhosting.id', $sid)
                            ->where('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                            ->select('tblproductconfigoptions.id')->first();
            if ($fields) {
                return $fields->id;
            } else {
                return FALSE;
            }
        }

        public static function parseAllowOSChangeIDS($params, $optionID, $allowOSChange, $currentOS) {
            $allowIDS = array();
            foreach ($allowOSChange as $value) {
                $field = Capsule::table('tblproductconfigoptionssub')->where('configid', $optionID)->where('optionname', 'like', '_' . $value['OSID'] . '|%')->first();
                if ($field) {
                    $allowIDS[$field->id] = $field->id;
                }
            }
            foreach ($params['configoptions'] as $key => $value) {
                if ($value['id'] == $optionID) {
                    foreach ($params['configoptions'][$key]['options'] as $key2 => $value2) {
                        if (!in_array($value2['id'], $allowIDS) && $value2['id'] != $currentOS) {
                            unset($params['configoptions'][$key]['options'][$key2]);
                        }
                    }
                }
            }
            return $params;
        }

        public static function compareOS($sid, $los, $ros) {
            $fields = Capsule::table('tblproductconfigoptions')
                            ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                            ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                            ->join('tblhosting', 'tblhosting.packageid', '=', 'tblproducts.id')
                            ->join('tblproductgroups', 'tblproductgroups.id', '=', 'tblproducts.gid')
                            ->where('tblproducts.servertype', '=', 'vultr')
                            ->where('tblhosting.id', $sid)
                            ->where('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                            ->select('tblproductconfigoptions.id')->first();

            if ($fields) {
                $field = Capsule::table('tblproductconfigoptionssub')->where('configid', $fields->id)->where('optionname', 'like', $los . '|%')->first();
                if ($field) {
                    $name = explode('|', $field->optionname);
                    if (isset($name[1]) && $name[1] == $ros) {
                        return true;
                    }
                }
            }
            return false;
        }

        public static function compareAPP($sid, $los, $ros) {
            $fields = Capsule::table('tblproductconfigoptions')
                            ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                            ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                            ->join('tblhosting', 'tblhosting.packageid', '=', 'tblproducts.id')
                            ->join('tblproductgroups', 'tblproductgroups.id', '=', 'tblproducts.gid')
                            ->where('tblproducts.servertype', '=', 'vultr')
                            ->where('tblhosting.id', $sid)
                            ->where('tblproductconfigoptions.optionname', 'like', 'application|%')
                            ->select('tblproductconfigoptions.id')->first();

            if ($fields) {
                $field = Capsule::table('tblproductconfigoptionssub')->where('configid', $fields->id)->where('optionname', 'like', $los . '|%')->first();
                if ($field) {
                    $name = explode('|', $field->optionname);
                    if (isset($name[1]) && $name[1] == $ros) {
                        return true;
                    }
                }
            }
            return false;
        }

        public static function updateAutoBackupsStatus($serviceID, $status) {
            $fields = Capsule::table('tblhostingconfigoptions')
                    ->select('tblhostingconfigoptions.*')
                    ->join('tblproductconfigoptions', 'tblhostingconfigoptions.configid', '=', 'tblproductconfigoptions.id')
                    ->where('tblhostingconfigoptions.relid', $serviceID)
                    ->where('tblproductconfigoptions.optionname', 'like', 'auto_backups|%')
                    ->get();
            foreach ($fields as $value) {
                Capsule::table('tblhostingconfigoptions')->where('id', $value->id)->update(array('qty' => $status));
            }
        }

        public static function changeOSTypeToApp($serviceID) {
            $fields = Capsule::table('tblhostingconfigoptions')
                    ->select('tblhostingconfigoptions.*')
                    ->join('tblproductconfigoptions', 'tblhostingconfigoptions.configid', '=', 'tblproductconfigoptions.id')
                    ->where('tblhostingconfigoptions.relid', $serviceID)
                    ->where('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                    ->get();
            foreach ($fields as $value) {
                $field = Capsule::table('tblproductconfigoptionssub')->where('configid', $value->configid)->where('optionname', 'like', '186|%')->first();
                Capsule::table('tblhostingconfigoptions')->where('id', $value->id)->update(array('optionid' => $field->id));
            }
        }

        public static function changeOSTypeToNoApp($serviceID) {
            $fields = Capsule::table('tblhostingconfigoptions')
                    ->select('tblhostingconfigoptions.*')
                    ->join('tblproductconfigoptions', 'tblhostingconfigoptions.configid', '=', 'tblproductconfigoptions.id')
                    ->where('tblhostingconfigoptions.relid', $serviceID)
                    ->where('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                    ->get();
            foreach ($fields as $value) {
                $field = Capsule::table('tblproductconfigoptionssub')->where('configid', $value->configid)->where('optionname', 'not like', '186|%')->first();
                Capsule::table('tblhostingconfigoptions')->where('id', $value->id)->update(array('optionid' => $field->id));
            }
        }

        public static function checkIsVultrUpgrade($hostingId) {
            $fields = Capsule::table('tblhosting')
                            ->join('tblproducts', 'tblproducts.id', '=', 'tblhosting.packageid')
                            ->where('tblhosting.id', $hostingId)
                            ->where('tblproducts.servertype', '=', 'vultr')->first();
            if ($fields) {
                return true;
            } else {
                return FALSE;
            }
        }

        public static function checkIsVMCreated($hostingId) {
            $params = self::buildServiceParams($hostingId);
            if (!isset($params['customfields']['subid'])) {
                return false;
            }
            if (empty($params['customfields']['subid'])) {
                return false;
            }
            return true;
        }

        public static function buildServiceParams($hostingId) {
            $result = Capsule::table('tblhosting')->select('packageid', 'server')->where('id', $hostingId)->first();
            $packageId = $result->packageid;
            $params['pid'] = $packageId;
            $serverId = $result->server;
            $result = Capsule::table('tblcustomfields')->select('id')->where('relid', $packageId)->where('fieldname', 'like', 'subid|%')->first();
            $fieldId = $result->id;
            $result = Capsule::table('tblcustomfieldsvalues')->select('value')->where('fieldid', $fieldId)->where('relid', $hostingId)->first();
            $vmName = $result->value;
            $params['customfields']['subid'] = $vmName;
            $result = Capsule::table('tblhostingconfigoptions')->where('relid', $hostingId)->get();
            foreach ($result as $k => $v) {
                $c = Capsule::table('tblproductconfigoptions')->where('id', $v->configid)->first();
                if ($c) {
                    $name = explode('|', $c->optionname);
                    if ($c->optiontype == 4) {
                        $params['configoptions'][$name[0]] = $v->qty;
                    } else {
                        $o = Capsule::table('tblproductconfigoptionssub')->where('id', $v->optionid)->first();
                        $value = explode('|', $o->optionname);
                        $params['configoptions'][$name[0]] = $value[0];
                    }
                }
            }
            $result = Capsule::table('tblservers')->select('hostname', 'ipaddress', 'username', 'password')->where('id', $serverId)->first();
            $params['serverhostname'] = $result['hostname'];
            $params['serverip'] = $result['ipaddress'];
            $params['serverusername'] = $result['username'];
            $params['serverpassword'] = decrypt($result['password']);
            return $params;
        }

        public static function getUpgradeConfigurableOptions($hostingId) {
            $fields = Capsule::table('tblproductconfigoptions')
                    ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                    ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                    ->join('tblhosting', 'tblproducts.id', '=', 'tblhosting.packageid')
                    ->where('tblproducts.servertype', '=', 'vultr')
                    ->where('tblhosting.id', $hostingId)
                    ->select('tblproductconfigoptions.*')
                    ->get();
            foreach ($fields as $key => $value) {
                $fields[$key]->options = Capsule::table('tblproductconfigoptionssub')->where('configid', $value->id)->get();
            }
            return $fields;
        }

        public static function getOsTypeAppId($hostingId) {
            $fields = Capsule::table('tblproductconfigoptions')
                    ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                    ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                    ->join('tblhosting', 'tblproducts.id', '=', 'tblhosting.packageid')
                    ->where('tblproducts.servertype', '=', 'vultr')
                    ->where('tblhosting.id', $hostingId)
                    ->where('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                    ->select('tblproductconfigoptions.*')
                    ->first();
            if ($fields) {
                $field = Capsule::table('tblproductconfigoptionssub')->where('configid', $fields->id)->where('optionname', 'like', '186|%')->first();
                if ($field) {
                    return $field->id;
                }
            }
            return FALSE;
        }

        public static function getFieldId($hostingId, $field) {
            $fields = Capsule::table('tblproductconfigoptions')
                    ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                    ->join('tblproducts', 'tblproductconfiglinks.pid', '=', 'tblproducts.id')
                    ->join('tblhosting', 'tblproducts.id', '=', 'tblhosting.packageid')
                    ->where('tblproducts.servertype', '=', 'vultr')
                    ->where('tblhosting.id', $hostingId)
                    ->where('tblproductconfigoptions.optionname', 'like', $field . '|%')
                    ->select('tblproductconfigoptions.*')
                    ->first();
            if ($fields) {
                return $fields->id;
            }
            return FALSE;
        }

        public static function getCurrentOSValue($hostingId) {
            $osFieldID = self::getFieldId($hostingId, 'os_type');
            $fields = Capsule::table('tblhostingconfigoptions')->where('relid', $hostingId)->select('optionid')->where('configid', $osFieldID)->first();
            if ($fields) {
                return $fields->optionid;
            }
            return FALSE;
        }

        public static function cleanString($string) {
            $string = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
            $string = strip_tags($string);
            $string = str_replace(' ', '-', $string);
            return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        }

        public static function updateApplicationStatus($serviceID, $appID) {
            $hco = Capsule::table('tblhostingconfigoptions')
                            ->join('tblproductconfigoptions', 'tblproductconfigoptions.id', '=', 'tblhostingconfigoptions.configid')
                            ->select('tblhostingconfigoptions.configid')
                            ->addSelect('tblhostingconfigoptions.id')
                            ->where('optionname', 'like', 'application|%')
                            ->where('relid', $serviceID)->get();
            foreach ($hco as $value) {
                $app = Capsule::table('tblproductconfigoptionssub')
                        ->where('configid', $value->configid)
                        ->where('optionname', 'like', $appID . '|%')
                        ->first();
                if ($app) {
                    Capsule::table('tblhostingconfigoptions')->where('id', $value->id)->update(array(
                        'optionid' => $app->id
                    ));
                }
            }
        }

        public static function updateOSStatus($serviceID, $osID) {
            $hco = Capsule::table('tblhostingconfigoptions')
                            ->join('tblproductconfigoptions', 'tblproductconfigoptions.id', '=', 'tblhostingconfigoptions.configid')
                            ->select('tblhostingconfigoptions.configid')
                            ->addSelect('tblhostingconfigoptions.id')
                            ->where('optionname', 'like', 'os_type|%')
                            ->where('relid', $serviceID)->get();
            foreach ($hco as $value) {
                $os = Capsule::table('tblproductconfigoptionssub')
                        ->where('configid', $value->configid)
                        ->where('optionname', 'like', $osID . '|%')
                        ->first();
                if ($os) {
                    Capsule::table('tblhostingconfigoptions')->where('id', $value->id)->update(array(
                        'optionid' => $os->id
                    ));
                }
            }
        }

        public static function checkRevDNSUpdated($serviceID) {
            $updated = Capsule::table('vultr_revdns')->where('service_id', $serviceID)->first();
            if ($updated) {
                return true;
            } else {
                return false;
            }
        }

        public function setRevDNSUpdated($serviceID, $clientID, $revnds, $updated) {
            Capsule::table('vultr_revdns')->insert(array(
                'service_id' => $serviceID,
                'client_id'  => $clientID,
                'updated'    => $updated,
                'reverse'    => $revnds
            ));
        }
        
        public function updateRevDNSRecord($serviceID, $revnds, $updated) {
            Capsule::table('vultr_revdns')->where([
                    ['service_id', '=', $serviceID]
                ])->update([
                    'updated' => $updated,
                    'reverse' => $revnds,
            ]);
        }

        public static function moveProductConfigOptionsOnUpgrade($params) {
            $count = Capsule::table('tblhostingconfigoptions')
                    ->join('tblproductconfigoptions', 'tblproductconfigoptions.id', '=', 'tblhostingconfigoptions.configid')
                    ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                    ->where('tblproductconfiglinks.pid', $params['packageid'])
                    ->where('relid', $params['serviceid'])
                    ->where(function ($query) {
                        $query->where('tblproductconfigoptions.optionname', 'like', 'auto_backups|%')
                        ->orWhere('tblproductconfigoptions.optionname', 'like', 'snapshots|%')
                        ->orWhere('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                        ->orWhere('tblproductconfigoptions.optionname', 'like', 'application|%');
                    })
                    ->select('tblhostingconfigoptions.id')
                    ->get();
            $list = array();
            foreach ($count as $key => $value) {
                $list[$value->id] = $value->id;
            }

            $toRemove = Capsule::table('tblhostingconfigoptions')
                    ->join('tblproductconfigoptions', 'tblproductconfigoptions.id', '=', 'tblhostingconfigoptions.configid')
                    ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                    ->where('relid', $params['serviceid'])
                    ->whereNotIn('tblhostingconfigoptions.id', $list)
                    ->where(function ($query) {
                        $query->where('tblproductconfigoptions.optionname', 'like', 'auto_backups|%')
                        ->orWhere('tblproductconfigoptions.optionname', 'like', 'snapshots|%')
                        ->orWhere('tblproductconfigoptions.optionname', 'like', 'os_type|%')
                        ->orWhere('tblproductconfigoptions.optionname', 'like', 'application|%');
                    })
                    ->select('tblhostingconfigoptions.id')
                    ->addSelect('tblhostingconfigoptions.qty')
                    ->addSelect('tblhostingconfigoptions.optionid')
                    ->addSelect('tblproductconfigoptions.optionname')
                    ->get();
            foreach ($toRemove as $key => $value) {
                $name = explode('|', $value->optionname);
                $field = Capsule::table('tblhostingconfigoptions')
                        ->join('tblproductconfigoptions', 'tblproductconfigoptions.id', '=', 'tblhostingconfigoptions.configid')
                        ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                        ->where('tblproductconfiglinks.pid', $params['packageid'])
                        ->where('relid', $params['serviceid'])
                        ->where('tblproductconfigoptions.optionname', 'like', $name[0] . '|%')
                        ->select('tblhostingconfigoptions.id')
                        ->first();
                $option = Capsule::table('tblproductconfigoptionssub')->where('id', $value->optionid)->first();
                $config = Capsule::table('tblproductconfigoptions')->where('id', $option->configid)->first();
                $configName = explode('|', $config->optionname);
                $configNew = Capsule::table('tblproductconfigoptions')
                        ->join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=', 'tblproductconfigoptions.gid')
                        ->where('tblproductconfiglinks.pid', $params['packageid'])
                        ->where('tblproductconfigoptions.optionname', 'like', $configName[0] . '|%')
                        ->first();
                if ($configNew) {
                    $optionNew = Capsule::table('tblproductconfigoptionssub')
                            ->where('configid', $configNew->id)
                            ->where('optionname', $option->optionname)
                            ->first();
                    if ($field) {
                        Capsule::table('tblhostingconfigoptions')->where('id', $field->id)->update(array('configid' => $configNew->id, 'optionid' => $optionNew->id, 'qty' => $value->qty));
                    } else {
                        Capsule::table('tblhostingconfigoptions')->insert(array('relid' => $params['serviceid'], 'configid' => $configNew->id, 'optionid' => $optionNew->id, 'qty' => $value->qty));
                    }
                }
                Capsule::table('tblhostingconfigoptions')->where('id', $value->id)->delete();
            }
        }

    }

}

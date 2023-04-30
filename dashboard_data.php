<?php
require_once('includes/load.php');
$sistema = new system_functions();
$intersect = ['network_vendor_model', 'router_device_model', 'network_mac_address', 'network_hardware_version',
    'network_software_version', 'network_firmware_name', 'network_serial_number', 'network_upstream_power',
    'network_upstream_frequencies', 'network_downstream_frequencies', 'network_downstream_power', 'network_downstream_snr'];
$all_devices = $sistema->getDevices();
$date = $sistema->make_date();
$sdate = $sistema->last_week();
?>
<div class="row">
    <?php
    foreach ($all_devices as $device):
        $uuid = $device['uuid'];
        $data = $sistema->getDeviceLatestRecord($uuid);
        /*GET TEST RESULTS*/
        $test_id = $sistema->getTestId($uuid);
        //echo "1st test id: $test_id\n";
        $last_Testid = $sistema->getLastTestId($uuid);
        //echo "Last test id: $last_Testid\n";

        $test_type = $sistema->getMode($uuid);
        //echo "mode: $test_type\n";
        $all_cycles = $sistema->getCycleStartsId($test_id,$uuid);
        //if($test_type=="auto"){
        $lasts_cycles = $sistema->getCycleStartsId($last_Testid,$uuid);
        //}else{
        // $lasts_cycles = $sistema->getManualTestResults($last_Testid,$uuid);
        //}
        $allproms = $sistema->getPromData($all_cycles,$uuid);
        $last_proms = $sistema->getPromData($lasts_cycles,$uuid);

        $accumulated_results = $sistema->compareTestData($allproms);
        $last_test_results = $sistema->compareTestData($last_proms);
        /**********************************************************/
        $datos = $sistema->getDeviceAdvancedRecords($uuid);
        $test_info = $sistema->getTestInfo($uuid);
        ?>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="device_configuration.php?device=<?= $device['uuid'] ?>"><strong>
                            <i class="fas fa-server"></i>
                            <?php echo $device['name'];?>
                            <?php if ($device['last_stress_type']) {
                                if ($device['last_type_network'] == 1) echo " Eth"; else {
                                    echo " WiFi ";
                                    if ($device['last_type_wi_fi'] == 1) echo "5Ghz"; else echo "2.4Ghz";
                                }
                            } else echo '';
                            ?>
                        </strong></a>
                    <br>
                    <a href="" onclick="desplegarVentana('<?php echo $device['uuid'];?>')"><strong>CM:<?= !empty($device['mac_address']) ? $device['mac_address'] : '' ?></strong></a>
                    <?php echo "<script>
                        function desplegarVentana(ID){
                        var yourValue = ID;
                        window.open('modem_info.php?device=' + yourValue, 'Modem Info', 'width=600,height=400');
                        }
                        </script>"?>
                        <?php if (!empty($device['network_device_model'])): ?>
                            <strong><?= $device['network_device_model'] ?></strong>
                        <?php endif; ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form action="proccess_test.php" method="GET" class="col-md-12 text-center">
                            <?php if($test_type=='auto'): ?>
                                <input type="radio" id="auto"  value="auto" name="test_type" checked>
                                <label class="h6" for="auto">AUTO</label>&nbsp
                                <input type="radio" id="manual"  value="manual" name="test_type">
                                <label class="h6" for="manual">MANUAL</label>&nbsp &nbsp
                            <?php elseif($test_type=='manual'): ?>
                                <input type="radio" id="auto"  value="auto" name="test_type">
                                <label class="h6" for="auto">AUTO</label>&nbsp
                                <input type="radio" id="manual"  value="manual" name="test_type" checked>
                                <label class="h6" for="manual">MANUAL</label>&nbsp &nbsp
                            <?php elseif($test_type==''): ?>
                                <input type="radio" id="auto"  value="auto" name="test_type">
                                <label class="h6" for="auto">AUTO</label>&nbsp
                                <input type="radio" id="manual"  value="manual" name="test_type">
                                <label class="h6" for="manual">MANUAL</label>&nbsp &nbsp
                            <?php endif;?>
                            <input type="hidden" name="uuid" value="<?php echo $uuid;?>">
                            <button type="submit" class="btn btn-primary btn-xs">Submit</button>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if($test_info['test_status'] =="running"): ?>
                            <div class="container bg-info col-md-12">
                                <h6><span class="glyphicon glyphicon-refresh spinning"></span><strong> Running test:</strong><span><?php echo $test_info['test_name'];?></span></h6>
                                <h6 class="text-info"><?php echo $test_info['test_details'];?></h6>
                                <h6 class="text-info"><?php echo $test_results;?></h6>
                            </div>
                            <?php elseif ($test_info['test_status'] =="success" and $last_test_results['failed']==0): ?>
                                <div class="container bg-success col-md-12">
                                    <h6 class="text-success"><strong>TEST: </strong><span><?php echo $test_info['test_name'];?></span>  COMPLETED</h6>
                                    <h6 class="text-success"><?php echo $test_info['test_details'];?></h6>
                                    <h6 class="text-success"><?php echo "Last test ok ".$last_test_results['success'];?></h6>
                                    <h6 class="text-danger"><?php echo "Last test failed ".$last_test_results['failed'];?></h6>
                                </div>
                            <?php elseif ($test_info['test_status']== "error" or $last_test_results['failed']>0): ?>
                                <div class="container bg-danger col-md-12">
                                    <h5 class="text-danger"><strong>TEST: </strong><?php echo $test_info['test_name'];?></span></h5>
                                    <h6 class="text-danger"><?php echo $test_info['test_details'];?></h6>
                                    <h6 class="text-success"><?php echo "Last test ok ".$last_test_results['success'];?></h6>
                                    <h6 class="text-danger"><?php echo "Last test failed ".$last_test_results['failed'];?></h6>
                                </div>
                            <?php elseif ($test_info['test_status']== ""): ?>
                            <div class="container bg-secondary col-md-12">
                                <h5 class="text-secondary"><strong>TEST: </strong><?php echo $test_info['test_name'];?></span></h5>
                                <h6 class="text-secondary"><?php echo $test_info['test_details'];?></h6>
                                <h6 class="text-success"><?php echo "Accumulated tests ok ".$last_test_results['success'];?></h6>
                                <h6 class="text-danger"><?php echo "Accumulated tests failed ".$last_test_results['failed'];?></h6>
                            </div>
                            <?php endif;?>
                            <div class="container bg-secondary col-md-12">
                                <h6 class="text-success"><?php echo "Accumulated tests ok ".$accumulated_results['success'];?></h6>
                                <h6 class="text-danger"><?php echo "Accumulated tests failed ".$accumulated_results['failed'];?></h6>
                            </div>

                        </div>
                    </div>
                    <br>
                    <!---Network--->
                    <div class="row">
                        <!--<div class="col-md-4">
                    <?php //if ($device['last_type_network'] == 1) echo "Eth"; /*else echo "WiFi ";*/ if ($device['last_type_wi_fi'] == 1) echo "5Ghz"; else echo "2.4Ghz";
                        ?>
                    </div>-->
                        <div class="col-md-4">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=1"
                               data-toggle="tooltip" data-placement="bottom" title="Ping">
                                <i class="fas fa-wifi">Ping</i>
                            </a>
                            <span><?php echo $data['ping']['ping'] ? (int)$data["ping"]["ping"] . ' ms' : "N/A"; ?></span>
                        </div>
                        <div class="col-md-4">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=2"
                               data-toggle="tooltip" data-placement="bottom" title="Download">
                                <i class="fas fa-arrow-down">Dwld</i>
                            </a>
                            <span><?php $datadw = abs(round($data['download']['kBps'] / 1024, 2));
                                /*echo $datadw ? $datadw . 'Mbps' : "N/A";*/echo $datadw ? $datadw . 'Mbps' : $datadw . 'Mbps'; ?></span>
                        </div>
                        <div class="col-md-4">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=3"
                               data-toggle="tooltip" data-placement="bottom" title="Upload">
                                <i class="fas fa-arrow-up">Upld</i>
                            </a>
                            <span><?php $dataup = abs(round($data['upload']['kBps'] / 1024, 2));
                                /*echo $dataup ? $dataup . 'Mbps' : "N/A";*/ echo $dataup ? $dataup . 'Mbps' : $dataup . 'Mbps'; ?></span>
                        </div>
                    </div>
                    <!---CPU--->
                    <div class="row">
                        <div class="col-md-4">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=4"
                               data-toggle="tooltip" data-placement="bottom" title="CPU">
                                <i class="fas fa-microchip">CPU</i>
                            </a>
                            <span><?php echo $data['cpu']['percentage'] ? (int)$data["cpu"]["percentage"] . ' %' : "N/A"; ?></span>
                        </div>
                        <div class="col-md-8">
                            <a href="#" data-toggle="tooltip" data-placement="bottom" title="Temperature">
                                <i class="fas fa-thermometer-half">Temp</i>
                            </a>
                            <span><?php echo $data['cpu']['temperature'] ? $data["cpu"]["temperature"] . ' °C' : "N/A"; ?></span>
                        </div>
                    </div>
                    <!---RAM--->
                    <div class="row">
                        <div class="col-md-4">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=5"
                               data-toggle="tooltip" data-placement="bottom" title="RAM">
                                <i class="fas fa-memory">RAM</i>
                            </a>
                            <span><?php echo $data['ram']['percentage'] ? (int)$data["ram"]["percentage"] . ' %' : "N/A"; ?></span>
                        </div>
                        <div class="col-md-8">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=5"
                               data-toggle="tooltip" data-placement="bottom" title="Used/Total">
                                <i class="fad fa-memory"></i>
                            </a>
                            <span><?php echo $data['ram']['used'] ? $data["ram"]["used"] . ' GB' : "N/A"; ?>/<?php echo $data['ram']['total'] ? $data["ram"]["total"] . ' GB' : "N/A"; ?></span>
                        </div>
                    </div>
                    <!---Disk--->
                    <div class="row">
                        <div class="col-md-4">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=6"
                               data-toggle="tooltip" data-placement="bottom" title="Disk">
                                <i class="fas fa-hdd">Disk</i>
                            </a>
                            <span><?php echo $data['disk']['percentage'] ? (int)$data["disk"]["percentage"] . ' %' : "N/A"; ?></span>
                        </div>
                        <div class="col-md-8">
                            <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=6"
                               data-toggle="tooltip" data-placement="bottom" title="Used/Total">
                                <i class="fad fa-hdd"></i>
                            </a>
                            <span><?php echo $data['disk']['used'] ? $data["disk"]["used"] . ' GB' : "N/A"; ?>/<?php echo $data['disk']['total'] ? $data["disk"]["total"] . ' GB' : "N/A"; ?></span>
                        </div>
                    </div>
                     <!--MODEL-->
                    <div class="row">
                        <div class="col-md-4">
                        <span><?php echo $device['router_device_model'] ? $device['router_device_model'] : "N/A"; ?></span>
                        </div>
                        <!--END MODEL-->
                        <!--UPS POWER-->
                        <div class="col-md-6">
                        <a href="graph.php?start=<?php echo $sdate; ?>&end=<?php echo $date; ?>&uuid=<?php echo $device['uuid']; ?>&type=7" 
                        data-toggle="tooltip" data-placement="bottom" title="UP POWER"">UPpwr</a>
                        <span><?php $deviceupst_power0 = explode(",",$device["upst_power"]);echo $device['upst_power'] ? $deviceupst_power0[0] .' dbmv': "N/A"; ?></span>
                        </div>
                    </div>    
                    <!--END POWER-->
                    <br/>
                    
                    
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <span><strong>Last seen:</strong> <?php echo $device['last_seen']; ?></span>
                            </center>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <div class="btn-group">
                                    <a href="ssh.php?device=<?php echo $device["uuid"]; ?>"
                                       class="btn btn-xs btn-primary">SSH</a>
                                    <a href="packets.php?device=<?php echo $device["uuid"]; ?>"
                                       class="btn btn-xs btn-primary">Packets</a>
                                    <a href="scripts.php?device=<?php echo $device["uuid"]; ?>"
                                       class="btn btn-xs btn-primary">Scripts</a>
                                    <?php if ($device['name'] == "A1") $portvnc = 5901;
                                    if ($device['name'] == "A2") $portvnc = 5902; ?>
                                    <a href="http://172.24.10.12/vnc/vnc.html?host=172.24.10.12&port=<?php echo $portvnc; ?>&path=172.24.10.12&password=docsiste&autoconnect=true"
                                       class="btn btn-xs btn-primary">Vnc</a>
                                </div>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<style>
    .glyphicon.spinning {
        animation: spin 1s infinite linear;
        -webkit-animation: spin2 1s infinite linear;
    }

    @keyframes spin {
        from { transform: scale(1) rotate(0deg); }
        to { transform: scale(1) rotate(360deg); }
    }

    @-webkit-keyframes spin2 {
        from { -webkit-transform: rotate(0deg); }
        to { -webkit-transform: rotate(360deg); }
    }
</style>




<?php
class system_functions
{
    private $conn;
    private $debug=0;

    // constructor
    function __construct()
    {
        require_once 'database_connect.php';
        // connecting to database
        $db = new database_connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct()
    {
    }

    public function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    function make_datetime()
    {
        date_default_timezone_set('America/Guatemala');
        return strftime("%Y-%m-%d %H:%M:%S", time());
    }

    function make_date()
    {
        date_default_timezone_set('America/Guatemala');
        return strftime("%Y-%m-%d", time());
    }

    function last_week()
    {
        date_default_timezone_set('America/Guatemala');
        return strftime("%Y-%m-%d", (time() - 604800));
    }

    function logActivity($user, $action)
    {
        $timestamp = $this->make_datetime();
        $stmt = $this->conn->prepare("INSERT INTO activity_log(user, action, timestamp) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $user, $action, $timestamp);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function hashFunction($password)
    {
        $salt = hash("sha256", rand());
        $salt = substr($salt, 0, 15);
        $encrypted = base64_encode(hash("sha256", $password . $salt) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    public function checkHash($salt, $password)
    {
        $hash = base64_encode(hash("sha256", $password . $salt) . $salt);
        return $hash;
    }

    public function registerUser($name, $username, $user_level, $password)
    {
        $hash = $this->hashFunction($password);
        $passwordsha256 = $hash["encrypted"];
        $salt = $hash["salt"];

        $stmt = $this->conn->prepare("INSERT INTO users(name, username, password, salt, user_level, status) VALUES(?, ?, ?, ?, ?, '1')");
        $stmt->bind_param("sssss", $name, $username, $passwordsha256, $salt, $user_level);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT name FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($token3);

            while ($stmt->fetch()) {
                $user["name"] = $token3;
            }
            $stmt->close();
            return $user;
        } else {
            return false;
        }
    }

    public function ChangePassword($user_id, $new_password)
    {
        $hash = $this->hashFunction($new_password);
        $passwordsha256 = $hash["encrypted"];
        $salt = $hash["salt"];

        $stmt = $this->conn->prepare("UPDATE users SET password = ?, salt = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param("sss", $passwordsha256, $salt, $user_id);

        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function checkExistingUser($username)
    {
        $stmt = $this->conn->prepare("SELECT id from users WHERE username = ?");

        $stmt->bind_param("s", $id);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // El usuario existe
            $stmt->close();
            return true;
        } else {
            // El usuario no existe
            $stmt->close();
            return false;
        }
    }

    function updateLastLogIn($user_id)
    {
        global $db;
        $date = $this->make_datetime();
        $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
        $result = $db->query($sql);
        return ($result && $db->affected_rows() === 1 ? true : false);
    }

    public function verifyAuthentication($username, $postpassword)
    {

        $stmt = $this->conn->prepare("SELECT id, name, password, salt FROM users WHERE username = ?");

        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $stmt->bind_result($token, $token2, $token4, $token5);

            while ($stmt->fetch()) {
                $user["id"] = $token;
                $user["name"] = $token2;
                $user["password"] = $token4;
                $user["salt"] = $token5;
            }

            $stmt->close();

            // verifying user password
            $salt = $token5;
            $password = $token4;
            $hash = $this->checkHash($salt, $postpassword);
            // check for password equality
            if ($user["password"] == $hash) {
                // user authentication details are correct
                return $user["id"];
            }
        } else {
            return NULL;
        }
    }

    public function getUserData($user_id)
    {
        $stmt = $this->conn->prepare("SELECT name, username, user_level, status, image FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("s", $user_id);

        if ($stmt->execute()) {
            $stmt->bind_result($name, $username, $user_level, $status, $position, $image);

            while ($stmt->fetch()) {
                $user["name"] = $name;
                $user["username"] = $username;
                $user["level"] = $user_level;
                $user["status"] = $status;
                $user["photo"] = $image;
            }
            return $user;
        } else {
            return false;
        }
    }

    // Registro de dispositivo
    public function registerDevice($name, $location = NULL, $ssh_user = NULL, $ssh_password = NULL, $ssh_port = 22, $ssh_hostname = 'raspberrypi', $scripts_path = '/home/pi/scripts/')
    {
        $first_setup = $this->make_datetime();
        $device_uuid = $this->generateUUID();

        $stmt = $this->conn->prepare("INSERT INTO devices (uuid, name, location, first_setup, last_seen, ssh_port, ssh_user, ssh_password, ssh_hostname, scripts_path) VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssss', $device_uuid, $name, $location, $first_setup, $ssh_port, $ssh_user, $ssh_password, $ssh_hostname, $scripts_path);

        if ($stmt->execute()) {
            $stmt->close();

            $new_device['uuid'] = $device_uuid;
            $new_device['name'] = $name;
            $new_device['first_setup'] = $first_setup;

            mkdir(ABSOLUTE_ROOT . '/uploads/packets/' . $device_uuid);
            mkdir(ABSOLUTE_ROOT . '/uploads/stress/' . $device_uuid);

            return $new_device;
        } else {
            $stmt->close();
            return false;
        }
    }

    private function updateLastSeen($device_uuid, $timestamp)
    {
        $stmt = $this->conn->prepare("UPDATE devices SET last_seen = ? WHERE uuid = ? LIMIT 1");
        $stmt->bind_param('ss', $timestamp, $device_uuid);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function deviceExists($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT name FROM devices WHERE uuid = ? LIMIT 1");
        $stmt->bind_param('s', $device_uuid);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($device_name);

            while ($stmt->fetch()) {
                $device["name"] = $device_name;
            }

            $stmt->close();

            if ($rows > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getDevices()
    {
        //$stmt = $this->conn->prepare("SELECT uuid, name, location, first_setup, last_seen, two_ssid,five_ssid,two_password,five_password,last_type_network,last_type_wi_fi,last_stress_type,mac_address,network_device_model,network_vendor_model, router_device_model, network_mac_address, network_hardware_version,network_software_version, network_firmware_name, network_serial_number, network_upstream_power,network_upstream_frequencies, network_downstream_frequencies, network_downstream_power, network_downstream_snr FROM devices");
        $stmt = $this->conn->prepare("SELECT uuid, name, location, first_setup, last_seen, two_ssid,five_ssid,two_password,five_password,last_type_network,last_type_wi_fi,last_stress_type,mac_address,router_device_model,upst_power FROM devices");

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            //$stmt->bind_result($uuid, $name, $location, $first_setup, $last_seen, $two_ssid, $five_ssid, $two_password,$five_password, $last_type_network, $last_type_wi_fi, $last_stress_type, $mac_address, $network_device_model,$network_model_vendor,$router_device_model,$network_mac_address,$network_hardware_version,$network_software_version,$network_firmware_name,$network_serial_number,$network_upstream_power,$network_upstream_frequencies,$network_downstream_frequencies,$network_downstream_power,$network_downstream_snr);
            $stmt->bind_result($uuid, $name, $location, $first_setup, $last_seen, $two_ssid, $five_ssid, $two_password, $five_password, $last_type_network, $last_type_wi_fi, $last_stress_type, $mac_address, $router_device_model, $upst_power);

            while ($stmt->fetch()) {
                $devices[$uuid]["uuid"] = $uuid;
                $devices[$uuid]["name"] = $name;
                $devices[$uuid]["location"] = $location;
                $devices[$uuid]["first_setup"] = $first_setup;
                $devices[$uuid]["last_seen"] = $last_seen;
                $devices[$uuid]["two_ssid"] = $two_ssid;
                $devices[$uuid]["five_ssid"] = $five_ssid;
                $devices[$uuid]["two_password"] = $two_password;
                $devices[$uuid]["five_password"] = $five_password;
                $devices[$uuid]["last_type_network"] = $last_type_network;
                $devices[$uuid]["last_type_wi_fi"] = $last_type_wi_fi;
                $devices[$uuid]["last_stress_type"] = $last_stress_type;
                $devices[$uuid]["mac_address"] = $mac_address;
                $devices[$uuid]["router_device_model"] = $router_device_model;
                $devices[$uuid]["upst_power"] = $upst_power;
                //$devices[$uuid]["router_device_model"] = $router_device_model;
                //$devices[$uuid]["network_mac_address"] = $network_mac_address;
                //$devices[$uuid]["network_hardware_version"] = $network_hardware_version;
                //$devices[$uuid]["network_software_version"] = $network_software_version;
                //$devices[$uuid]["network_firmware_name"] = $network_firmware_name;
                //$devices[$uuid]["network_serial_number"] = $network_serial_number;
                //$devices[$uuid]["network_upstream_power"] = $network_upstream_power;
                //$devices[$uuid]["network_upstream_frequencies"] = $network_upstream_frequencies;
                //$devices[$uuid]["network_downstream_frequencies"] = $network_downstream_frequencies;
                //$devices[$uuid]["network_downstream_power"] = $network_downstream_power;
                //$devices[$uuid]["network_downstream_snr"] = $network_downstream_snr;
            }

            $stmt->close();

            if ($rows > 0) {
                return $devices;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getDevice($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT name, location, first_setup, last_seen FROM devices WHERE uuid = ? LIMIT 1");
        $stmt->bind_param('s', $device_uuid);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($name, $location, $first_setup, $last_seen);

            while ($stmt->fetch()) {
                $device["uuid"] = $device_uuid;
                $device["name"] = $name;
                $device["location"] = $location;
                $device["first_setup"] = $first_setup;
                $device["last_seen"] = $last_seen;
            }

            $stmt->close();

            if ($rows > 0) {
                return $device;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getDeviceSshData($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT last_ip_address, ssh_port, ssh_user, ssh_password, ssh_hostname, scripts_path FROM devices WHERE uuid = ? LIMIT 1");
        $stmt->bind_param("s", $device_uuid);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($last_ip_address, $ssh_port, $ssh_user, $ssh_password, $ssh_hostname, $scripts_path);

            while ($stmt->fetch()) {
                $device["last_ip_address"] = $last_ip_address;
                $device["ssh_port"] = $ssh_port;
                $device["ssh_user"] = $ssh_user;
                $device["ssh_password"] = $ssh_password;
                $device["ssh_hostname"] = $ssh_hostname;
                $device["scripts_path"] = $scripts_path;
            }

            $stmt->close();

            if ($rows > 0) {
                return $device;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Monitoreo de hardware
    public function saveHardwareMonData($device_uuid, $cpu_data = NULL, $ram_data = NULL, $disk_data = NULL, $ip_address = NULL)
    {
        $timestamp = $this->make_datetime();

        if ($this->deviceExists($device_uuid)) {
            $stmt = $this->conn->prepare("INSERT INTO hardware_monitor(device, cpu, ram, disk, timestamp) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $device_uuid, $cpu_data, $ram_data, $disk_data, $timestamp);

            if ($stmt->execute()) {
                $stmt->close();
                $this->updateLastSeen($device_uuid, $timestamp);
                if ($ip_address) {
                    $this->updateIPAddress($device_uuid, $ip_address);
                }
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectCpuData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT cpu, timestamp FROM hardware_monitor WHERE device = ? AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($cpu_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = json_decode($cpu_data, true);

                $data[$i]["percentage"] = (float)$current_record["percentage"];
                $data[$i]["temperature"] = (float)$current_record["temperature"];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectRamData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT ram, timestamp FROM hardware_monitor WHERE device = ? AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($ram_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = json_decode($ram_data, true);

                $data[$i]["percentage"] = (float)$current_record["percentage"];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectDiskData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT disk, timestamp FROM hardware_monitor WHERE device = ? AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($disk_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = json_decode($disk_data, true);

                $data[$i]["percentage"] = (float)$current_record["percentage"];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Monitoreo de conexión
    public function saveConnectionMonData($device_uuid, $ping = NULL, $download = NULL, $upload = NULL, $ip_address = NULL)
    {
        $timestamp = $this->make_datetime();

        if ($this->deviceExists($device_uuid)) {
            $stmt = $this->conn->prepare("INSERT INTO connection_monitor(device, ping, download, upload, timestamp) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $device_uuid, $ping, $download, $upload, $timestamp);

            if ($stmt->execute()) {
                $stmt->close();
                $this->updateLastSeen($device_uuid, $timestamp);
                if ($ip_address) {
                    $this->updateIPAddress($device_uuid, $ip_address);
                }
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectPingData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT ping, timestamp FROM connection_monitor WHERE device = ? and ping is not NULL AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($ping_data, $timestamp);

            $i = 0;

            $data["targets"] = "";

            while ($stmt->fetch()) {
                $current_record = json_decode($ping_data, true);

                if (strpos($data["targets"], $current_record["target"]) === false) {
                    if ($data["targets"]) {
                        $data["targets"] .= ", ";
                    }

                    $data["targets"] .= $current_record["target"];
                }

                $data[$i]["ping"] = (int)$current_record["ping"];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectDownloadData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT download, timestamp FROM connection_monitor WHERE device = ? AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($download_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = json_decode($download_data, true);

                $data[$i]["kbps"] = (float)$current_record["kBps"];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectUploadData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT upload, timestamp FROM connection_monitor WHERE device = ? AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($upload_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = json_decode($upload_data, true);

                $data[$i]["kbps"] = (float)$current_record["kBps"];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Obtener últimos registros básicos dado un UUID
    public function getDeviceLatestRecord($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT ping, download, upload, timestamp FROM connection_monitor WHERE device = ? and download is not NULL ORDER BY id DESC LIMIT 1");
        $stmt->bind_param('s', $device_uuid);

        if ($stmt->execute()) {
            $stmt->bind_result($ping, $download, $upload, $conn_timestamp);

            while ($stmt->fetch()) {
                $record['ping'] = json_decode($ping, true);
                $record['download'] = json_decode($download, true);
                $record['upload'] = json_decode($upload, true);
                $record['conn_timestamp'] = $conn_timestamp;
            }
        } else {
            $record['ping'] = NULL;
            $record['download'] = NULL;
            $record['upload'] = NULL;
            $record['conn_timestamp'] = NULL;
        }

        $stmt->close();

        $stmt = $this->conn->prepare("SELECT cpu, ram, disk, timestamp FROM hardware_monitor WHERE device = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param('s', $device_uuid);

        if ($stmt->execute()) {
            $stmt->bind_result($cpu, $ram, $disk, $hw_timestamp);

            while ($stmt->fetch()) {
                $record['cpu'] = json_decode($cpu, true);
                $record['ram'] = json_decode($ram, true);
                $record['disk'] = json_decode($disk, true);
                $record['hw_timestamp'] = $hw_timestamp;
            }
        } else {
            $record['cpu'] = NULL;
            $record['ram'] = NULL;
            $record['disk'] = NULL;
            $record['hw_timestamp'] = NULL;
        }

        $stmt->close();

        if ($record['hw_timestamp'] == NULL && $record['conn_timestamp'] == NULL) {
            return false;
        } else {
            return $record;
        }
    }


    // Obtener últimos registros AVANZADAS UPST POWER, UPST FREQ, DOWNST POWER dado un UUID
    public function getDeviceAdvancedRecords($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT upst_power, upst_freq, dwnst_power,dwnst_snr, timestamp FROM connection_monitor WHERE upst_power is not NULL and device = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param('s', $device_uuid);

        if ($stmt->execute()) {
            $stmt->bind_result($upst_power, $upst_freq, $dwnst_power, $dwnst_snr, $conn_timestamp);

            while ($stmt->fetch()) {
                //$record['upst_power'] = json_decode($upst_power, true);
                $record['upst_power'] = $upst_power;
                $record['upst_freq'] = json_decode($upst_freq, true);
                $record['dwnst_power'] = json_decode($dwnst_power, true);
                $record['dwnst_snr'] = json_decode($dwnst_snr, true);
                $record['conn_timestamp'] = $conn_timestamp;
                }
        } else {
            $record['upst_power'] = NULL;
            $record['upst_freq'] = NULL;
            $record['dwnst_power'] = NULL;
            $record['dwnst_snr'] = NULL;
            $record['conn_timestamp'] = NULL;
            }

        $stmt->close();

        if ($record['conn_timestamp'] == NULL) {
            return false;
        } else {
            return $record;
        }
    }

    // Scripts del servidor
    public function getServerScripts()
    {
        $stmt = $this->conn->prepare("SELECT id, description, filename, relative_path, is_service, service_name, has_timer, timer_name, version, author, uploaded_at, updated_at FROM server_scripts");

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($id, $description, $filename, $relative_path, $is_service, $service_name, $has_timer, $timer_name, $version, $author, $uploaded_at, $updated_at);

            while ($stmt->fetch()) {
                $scripts[$id]["id"] = (int)$id;
                $scripts[$id]["description"] = $description;
                $scripts[$id]["filename"] = $filename;
                $scripts[$id]["relative_path"] = $relative_path;
                $scripts[$id]["is_service"] = (bool)$is_service;
                $scripts[$id]["service_name"] = $service_name;
                $scripts[$id]["has_timer"] = (bool)$has_timer;
                $scripts[$id]["timer_name"] = $timer_name;
                $scripts[$id]["version"] = (int)$version;
                $scripts[$id]["author"] = $this->getUserData($author);
                $scripts[$id]["uploaded_at"] = $uploaded_at;
                $scripts[$id]["updated_at"] = $updated_at;
            }

            $stmt->close();

            if ($rows > 0) {
                return $scripts;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function scriptExists($script_id)
    {
        $stmt = $this->conn->prepare("SELECT filename FROM server_scripts WHERE id = ? LIMIT 1");
        $stmt->bind_param('s', $script_id);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($script_filename);

            while ($stmt->fetch()) {
                $script["filename"] = $script_filename;
            }

            $stmt->close();

            if ($rows > 0) {
                $var = substr(strrchr($script['filename'], '.'), 1);
                return $var;

            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function saveToScriptsFolder($upload_name)
    {
        if (isset($_FILES[$upload_name]) && $_FILES[$upload_name]['error'] === UPLOAD_ERR_OK) {
            $file_temporary_path = $_FILES[$upload_name]['tmp_name'];
            $file_name = $_FILES[$upload_name]['name'];
            $file_size = $_FILES[$upload_name]['size'];
            $file_type = $_FILES[$upload_name]['type'];
            $file_name_cmps = explode(".", $file_name);
            $file_extension = strtolower(end($file_name_cmps));

            $allowed_types = array('py', 'sh', 'service', 'timer');
            if (in_array($file_extension, $allowed_types)) {
                $upload_directory = 'uploads/scripts/';
                $destination_path = $upload_directory . $file_name;

                if (move_uploaded_file($file_temporary_path, $destination_path)) {
                    return $file_name;
                } else {
                    return false;
                }
            } else {
                return false;
            }

        }
    }

    public function saveToPacketsFolder($device_uuid, $upload_name)
    {
        if (isset($_FILES[$upload_name]) && $_FILES[$upload_name]['error'] === UPLOAD_ERR_OK && $this->deviceExists($device_uuid)) {
            $file_temporary_path = $_FILES[$upload_name]['tmp_name'];
            $file_name = $_FILES[$upload_name]['name'];
            $file_size = $_FILES[$upload_name]['size'];
            $file_type = $_FILES[$upload_name]['type'];
            $file_name_cmps = explode(".", $file_name);
            $file_extension = strtolower(end($file_name_cmps));

            $allowed_types = array('pcap');
            if (in_array($file_extension, $allowed_types)) {
                $upload_directory = '../uploads/packets/' . $device_uuid . '/';
                $destination_path = $upload_directory . $file_name;

                if (move_uploaded_file($file_temporary_path, $destination_path)) {
                    return $file_name;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function saveToStressFolder($device_uuid, $upload_name)
    {
        if (isset($_FILES[$upload_name]) && $_FILES[$upload_name]['error'] === UPLOAD_ERR_OK && $this->deviceExists($device_uuid)) {
            $file_temporary_path = $_FILES[$upload_name]['tmp_name'];
            $file_name = $_FILES[$upload_name]['name'];
            $file_size = $_FILES[$upload_name]['size'];
            $file_type = $_FILES[$upload_name]['type'];
            $file_name_cmps = explode(".", $file_name);
            $file_extension = strtolower(end($file_name_cmps));

            $allowed_types = array('test');
            if (in_array($file_extension, $allowed_types)) {
                $upload_directory = '../uploads/stress/' . $device_uuid . '/';
                $destination_path = $upload_directory . $file_name;

                if (move_uploaded_file($file_temporary_path, $destination_path)) {
                    return $file_name;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function previousVersionExists($filename)
    {
        $stmt = $this->conn->prepare("SELECT id FROM server_scripts WHERE filename = ? LIMIT 1");
        $stmt->bind_param('s', $filename);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($id);

            while ($stmt->fetch()) {
                $script["id"] = $id;
            }

            $stmt->close();

            if ($rows > 0) {
                return $script['id'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function registerServerScript($description, $filename, $relative_path, $is_service, $service_name, $has_timer, $timer_name, $version, $author)
    {
        $timestamp = $this->make_datetime();
        $previous_version = $this->previousVersionExists($filename);

        if (!$previous_version) {
            $stmt = $this->conn->prepare("INSERT INTO server_scripts(description, filename, relative_path, is_service, service_name, has_timer, timer_name, version, author, uploaded_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssisisisss', $description, $filename, $relative_path, $is_service, $service_name, $has_timer, $timer_name, $version, $author, $timestamp, $timestamp);
        } else {
            $stmt = $this->conn->prepare("UPDATE server_scripts SET description = ?, filename = ?, relative_path = ?, is_service = ?, service_name = ?, has_timer = ?, timer_name = ?, version = ?, author = ?, uploaded_at = ?, updated_at = ? WHERE id = ?");
            $stmt->bind_param('sssisisisssi', $description, $filename, $relative_path, $is_service, $service_name, $has_timer, $timer_name, $version, $author, $timestamp, $timestamp, $previous_version);
        }

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    private function updateIPAddress($device_uuid, $ip_address)
    {
        $stmt = $this->conn->prepare("UPDATE devices SET last_ip_address = ? WHERE uuid = ? LIMIT 1");
        $stmt->bind_param('ss', $ip_address, $device_uuid);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function getModels()
    {
        $stmt = $this->conn->prepare("SELECT  name FROM device_network_models");
        $models = [];
        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($name);
            while ($stmt->fetch()) {
                $models[] = $name;
            }

            $stmt->close();

            if ($rows > 0) {
                return $models;
            } else {
                return $models;
            }
        } else {
            return $models;
        }
    }

    public function getModemInfo($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT mac_address,router_device_model,vendor,hrw_ver,soft_ver,serial,boot_rom,upst_power,upst_freq,dwnst_power,dwnst_freq,dwnst_snr FROM devices WHERE uuid = ? LIMIT 1");
        $stmt->bind_param('s', $device_uuid);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($mac_address, $router_device_model, $vendor, $hrw_ver, $soft_ver, $serial, $boot_room, $upst_power, $upst_freq, $dwnst_power, $dwnst_freq, $dwnst_snr);

            while ($stmt->fetch()) {
                $device["mac_address"] = $mac_address;
                $device["router_device_model"] = $router_device_model;
                $device["vendor"] = $vendor;
                $device["hrw_ver"] = $hrw_ver;
                $device["soft_ver"] = $soft_ver;
                $device["serial"] = $serial;
                $device["boot_rom"] = $boot_room;
                $device["upst_power"] = $upst_power;
                $device["upst_freq"] = $upst_freq;
                $device["dwnst_power"] = $dwnst_power;
                $device["dwnst_freq"] = $dwnst_freq;
                $device["dwnst_snr"] = $dwnst_snr;
            }

            $stmt->close();

            if ($rows > 0) {
                return $device;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectUpsPowData1($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT upst_power, timestamp FROM connection_monitor WHERE device = ? AND upst_power IS NOT NULL AND  (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);
        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($upst_power_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = explode(",", $upst_power_data);
                $data[$i]["DBMV"] = (float)$current_record[0];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectUpsPowData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT upst_power, timestamp FROM connection_monitor WHERE device = ? AND upst_power IS NOT NULL AND  (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);
        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($upst_power_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = explode(",", $upst_power_data);
                $data[$i]["DBMV"] = (float)$current_record[0];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectDownPowData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT dwnst_power, timestamp FROM connection_monitor WHERE device = ? AND dwnst_power IS NOT NULL AND  (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);
        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($dwnst_power_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = explode(",", $dwnst_power_data);
                $data[$i]["DBMV"] = (float)$current_record[0];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUpstreamData($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT upst_power,upst_freq FROM connection_monitor WHERE device = ? and upst_power is not NULL");
        $stmt->bind_param("s", $device_uuid);
        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($upst_power_data, $upst_freq_data);
            while ($stmt->fetch()) {
                $device["upst_power"] = $upst_power_data;
                $device["upst_freq"] = $upst_freq_data;
            }
            $stmt->close();
            if ($rows > 0) {
                return $device;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getDownstreamData($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT dwnst_power,dwnst_freq FROM connection_monitor WHERE device = ? and dwnst_power is not NULL");
        $stmt->bind_param("s", $device_uuid);
        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($dwnst_power_data, $dwnst_freq_data);
            while ($stmt->fetch()) {
                $device["dwnst_power"] = $dwnst_power_data;
                $device["dwnst_freq"] = $dwnst_freq_data;
            }
            $stmt->close();
            if ($rows > 0) {
                return $device;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectEachUpstPowerData($device_uuid, $start_date, $end_date, $index)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT upst_power, timestamp FROM connection_monitor WHERE device = ? AND  upst_power IS NOT NULL AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($upst_power_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = explode(",", $upst_power_data);
                $data[$i]["DBMV"] = (float)$current_record[$index];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectEachDownstPowerData($device_uuid, $start_date, $end_date, $index)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT dwnst_power, timestamp FROM connection_monitor WHERE device = ? AND  dwnst_power IS NOT NULL AND (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);

        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($dwnst_power_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $current_record = explode(",", $dwnst_power_data);
                $data[$i]["DBMV"] = (float)$current_record[$index];
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function collectDownSNRData($device_uuid, $start_date, $end_date)
    {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        $stmt = $this->conn->prepare("SELECT dwnst_snr, timestamp FROM connection_monitor WHERE device = ? AND dwnst_snr IS NOT NULL AND  (timestamp BETWEEN ? AND ?)");
        $stmt->bind_param("sss", $device_uuid, $start_date, $end_date);
        if ($stmt->execute()) {
            $stmt->store_result();
            $rows = $stmt->num_rows;
            $stmt->bind_result($dwnst_snr_data, $timestamp);

            $i = 0;

            while ($stmt->fetch()) {
                $data[$i]["DBMV"] = (float)$dwnst_snr_data;;
                $data[$i]["timestamp"] = $timestamp;
                $i++;
            }

            $stmt->close();

            if ($rows > 0) {
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setMode($device_uuid, $test_type)
    {
        $stmt = $this->conn->prepare("UPDATE devices SET mode = ? WHERE uuid = ?");
        $stmt->bind_param('ss', $test_type, $device_uuid);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function getMode($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT mode FROM devices WHERE uuid = ?");
        $stmt->bind_param('s', $device_uuid);
        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($test_mode);
            while ($stmt->fetch()) {
                $data = $test_mode;
            }
        }
        $stmt->close();
        return $data;
    }

    public function setLimits($ping_limit, $upload_speed_limit, $download_speed_limit, $upstream_frequencies_limit, $downstream_frequencies_limit, $upstream_power_limit, $downstream_power_limit, $downstream_snr_limit, $cycles)
    {
        $stmt = $this->conn->prepare("UPDATE Limits SET ping_limit = ?,upload_speed_limit = ?,download_speed_limit = ?,upstream_frequencies_limit = ?,downstream_frequencies_limit = ?,upstream_power_limit = ?,downstream_power_limit = ?,downstream_snr_limit = ?,cycles = ?");
        $stmt->bind_param('sssssssss', $ping_limit, $upload_speed_limit, $download_speed_limit, $upstream_frequencies_limit, $downstream_frequencies_limit, $upstream_power_limit, $downstream_power_limit, $downstream_snr_limit, $cycles);
        if ($stmt->execute()) {
            return true;
            $stmt->close();
        } else {
            return false;
            $stmt->close();
        }
    }

    public function getLimits()
    {
        $stmt = $this->conn->prepare("SELECT * FROM Limits");
        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($ping_limit, $upload_speed_limit, $download_speed_limit, $upstream_frequencies_limit, $downstream_frequencies_limit, $upstream_power_limit, $downstream_power_limit, $downstream_snr_limit, $cycles);
            while ($stmt->fetch()) {
                $ping_limit_arr = json_decode($ping_limit, true);
                $upload_limit_arr = json_decode($upload_speed_limit, true);
                $download_limit_arr = json_decode($download_speed_limit, true);
                $upstream_limit_arr = json_decode($upstream_frequencies_limit, true);
                $downstream_limit_arr = json_decode($downstream_frequencies_limit, true);
                $upstream_power_limit_arr = json_decode($upstream_power_limit, true);
                $downstream_power_limit_arr = json_decode($downstream_power_limit, true);
                $downstream_snr_limit_arr = json_decode($downstream_snr_limit, true);
                $limit['ping_min'] = $ping_limit_arr['min'];
                $limit['ping_max'] = $ping_limit_arr['max'];
                $limit['upload_speed_min'] = $upload_limit_arr['min'];
                $limit['upload_speed_max'] = $upload_limit_arr['max'];
                $limit['download_speed_min'] = $download_limit_arr['min'];
                $limit['download_speed_max'] = $download_limit_arr['max'];
                $limit['upstream_freq_min'] = $upstream_limit_arr['min'];
                $limit['upstream_freq_max'] = $upstream_limit_arr['max'];
                $limit['downstream_freq_min'] = $downstream_limit_arr['min'];
                $limit['downstream_freq_max'] = $downstream_limit_arr['max'];
                $limit['upstream_power_min'] = $upstream_power_limit_arr['min'];
                $limit['upstream_power_max'] = $upstream_power_limit_arr['max'];
                $limit['downstream_power_min'] = $downstream_power_limit_arr['min'];
                $limit['downstream_power_max'] = $downstream_power_limit_arr['max'];
                $limit['downstream_snr_min'] = $downstream_snr_limit_arr['min'];
                $limit['downstream_snr_max'] = $downstream_snr_limit_arr['max'];
                $limit['cycles'] = $cycles;
            }
            $stmt->close();
        }
        return $limit;
    }

    public function getTestInfo($device_uuid)
    {
        $stmt = $this->conn->prepare("SELECT test_info From devices WHERE uuid = ?");
        $stmt->bind_param('s', $device_uuid);
        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($test_info_str);
            while ($stmt->fetch()) {
                $test_info_arr = json_decode($test_info_str, true);
                $test_info['test_name'] = $test_info_arr['name'];
                $test_info['test_status'] = $test_info_arr['status'];
                $test_info['test_details'] = $test_info_arr['details'];
            }
            $stmt->close();
        }
        return $test_info;
    }

    public function getTestId($uuid)
    {
        /*
        $stmt = $this->conn->prepare("SELECT id FROM connection_monitor WHERE test_info='start' and device = ? LIMIT 1;");
        $stmt->bind_param('s', $uuid);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($test_id);
        while ($stmt->fetch()) {
            $id = $test_id;
        }

        return $id;*/

        if($uuid=="399e7dec-1a50-4e2b-a12b-61a81af8a49d"){
            $id = 1283231;
            if($this->debug==1)echo "\n"."------------COMIENZA LA PRUEBA------------"."\n"."ID DEL PRIMER START (ACUMULADO) ".$id."\n";
            return $id;
        }
        if($uuid=="b938d7f8-32a0-4703-afa5-f5a4d13ae2de"){
            $id =  1405306;
            if($this->debug==1)echo "\n"."------------COMIENZA LA PRUEBA------------"."\n"."ID DEL PRIMER START (ACUMULADO) ".$id."\n";
            return $id;
        }
    }


    //TOMAR EL ID DE LA ULTIMA PRUEBA
    public function getLastTestId($uuid)
    {
        $debug = 1;
        $stmt = $this->conn->prepare("SELECT id FROM connection_monitor WHERE test_info='start' and device = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param('s', $uuid);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($test_id);
        while ($stmt->fetch()) {
            $id = $test_id;
        }
        if($this->debug==1){
            echo "ID DEL 'START' DE LA ULTIMA PRUEBA REALIZADA: ".$id."\n";
        }
        return $id;
    }

    public function getCycleStartsId($id,$uuid)
    {

            $stmt = $this->conn->prepare("SELECT test_info,id FROM connection_monitor where id >= ? and device = ?");
            $stmt->bind_param('ss', $id,$uuid);
            if ($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($test_info, $test_id);
                $i = 0;
                while ($stmt->fetch()) {
                    if (strpos($test_info, "ETH;down_started") OR preg_match("/ETH started/",$test_info) OR preg_match("/5GHZ started/",$test_info)) {
                        $cycles_id[$i] = $test_id;
                        $i++;
                    }
                    if($test_info=="finished"){
                        $end_test = $test_id;
                    }
                }
            }
        $final = sizeof($cycles_id);
        $cycles_id[$final] = $end_test;
        $counter = 0;
        if($this->debug==1){
            foreach ($cycles_id as $item) {
                echo "CICLO".$counter." ID: ".$item."\n";
                $counter++;
            }
        }
        return $cycles_id;
    }


    /*public function getManualTestResults($id,$uuid){
        $stmt = $this->conn->prepare("SELECT id FROM connection_monitor WHERE test_info='finished' and device = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param('s',$uuid);
        if ($stmt->execute()){
            $stmt->store_result();
            $stmt->bind_result($test_id);
            while ($stmt->fetch()){
                $end_test = $test_id;
            }
        }
        $cycles_id[0] = $id;
        $final = sizeof($cycles_id);
        $cycles_id[$final] = $end_test;
        if($this->debug == 1){
            echo "INICIO: ".$cycles_id[0]."\n"." FIN: ".$cycles_id[1]."\n";
        }

        return $cycles_id;
    }*/


    public function getPromData($ciclo,$uuid){
        $debug = $this->debug;
        /*CONTADORES*/
        $count_upload = 0;
        $count_download = 0;
        $count_ping = 0;
        $count_downstream = 0;
        $count_upstream = 0;
        $count_snr = 0;
        /*--------*/
        /*SUMA TOTAL*/
        $test_data_upload = 0;
        $test_data_download = 0;
        $test_data_ping = 0;
        $test_data_upstream = 0;
        $test_data_downstream = 0;
        $test_data_snr = 0;
        /*--------*/
        $j = 1;
        for($i=0;$i<(sizeof($ciclo)-1);$i++){
            $primer_ciclo = $ciclo[$i];
            $segundo_ciclo = $ciclo[$j];
            $stmt = $this->conn->prepare("SELECT upload,download,ping,upst_power,dwnst_power,dwnst_snr FROM connection_monitor where id BETWEEN ? AND ? AND device = ?");
            $stmt->bind_param('iis',$primer_ciclo,$segundo_ciclo,$uuid);
            if ($stmt->execute()){
                $stmt->store_result();
                $stmt->bind_result($upload,$download,$ping,$upst_power,$dwnst_power,$dwnst_snr);
                while ($stmt->fetch()){
                    if ($upload != NULL){
                        $upload_value = json_decode($upload,true);
                        if($upload_value['kBps']>=1){
                            $test_data_upload += $upload_value['kBps'];
                            $count_upload++;
                        }
                    }
                    if ($download != NULL){
                        $download_value = json_decode($download,true);
                        if($download_value['kBps']>=1){
                            $test_data_download += $download_value['kBps'];
                            $count_download++;
                        }
                    }
                    if ($download != NULL){
                        $ping_value = json_decode($ping,true);
                        $test_data_ping += $ping_value['ping'];
                        $count_ping++;
                    }
                    if ($upst_power != NULL){
                        $upst_power_arr = explode(",",$upst_power);
                        $test_data_upstream += $upst_power_arr[0];
                        $count_upstream++;
                    }
                    if ($dwnst_power != NULL){
                        $dwnst_power_arr = explode(",",$dwnst_power);
                        $test_data_downstream += $dwnst_power_arr[0];
                        $count_downstream++;
                    }
                    if ($dwnst_snr != NULL){
                        $dwnst_snr_arr = explode(",",$dwnst_snr);
                        $test_data_snr += $dwnst_snr_arr[0];
                        $count_snr++;
                    }
                }

            }
            $promedio_ciclo[$i]['upload'] = number_format($test_data_upload / $count_upload,2 ,'.', '');
            $promedio_ciclo[$i]['download'] = number_format($test_data_download / $count_download,2,'.', '');
            $promedio_ciclo[$i]['ping'] = number_format($test_data_ping / $count_ping,2 ,'.', '');
            $promedio_ciclo[$i]['upstream_p'] = number_format($test_data_upstream / $count_upstream,2,'.', '');
            $promedio_ciclo[$i]['downstream_p'] = number_format($test_data_downstream / $count_downstream,2 ,'.', '');
            $promedio_ciclo[$i]['downstream_snr'] = number_format($test_data_snr / $count_snr,2 ,'.', '');
            if($j>sizeof($ciclo)){
                $j=sizeof($ciclo);
            }else{
                $j++;
            }
        }
        if($this->debug == 1){
            echo "\n"."------------PROMEDIO DE DATOS------------"."\n";
            foreach ($promedio_ciclo as $item) {
                echo "PROMEDIOS UPLOAD: ".$item['upload']."\n";
                echo "PROMEDIOS DOWNLOAD: ".$item['download']."\n";
                echo "PROMEDIOS PING: ".$item['ping']."\n";
                echo "PROMEDIOS UPSTREAM P: ".$item['upstream_p']."\n";
                echo "PROMEDIOS DOWNSTREAM P: ".$item['downstream_p']."\n";
                echo "PROMEDIOS DOWNSTREAM SNR: ".$item['downstream_snr']."\n";
            }
        }
        return $promedio_ciclo;
    }
    public function compareTestData($promData){
        $limits = $this->getLimits();

        $s_cycle = 0;
        $f_cycle = 0;
        for ($i=0;$i<sizeof($promData);$i++){
            $test_failed = 0;
            /*UPLOAD*/
            if($this->debug == 1) echo "COMPARANDO upload: ".$promData[$i]['upload']."CON : "."MIN: ".$limits['upload_speed_min']."MAX: ".$limits['upload_speed_max']."\n";
            if (floatval($promData[$i]['upload']) > floatval($limits['upload_speed_max']) || floatval($promData[$i]['upload']) < floatval($limits['upload_speed_min'])){
                $test_failed+=1;
              if($this->debug == 1) echo "failed upload\n";
            }
            /*echo "TEST FALLIDOS: ".$test_failed."\n";*/
            /*DOWNLOAD*/
            if($this->debug == 1) echo "COMPARANDO download: ".$promData[$i]['download']."CON : "."MIN: ".$limits['download_speed_min']."MAX: ".$limits['download_speed_max']."\n";
            if (floatval($promData[$i]['download']) > floatval($limits['download_speed_max']) || floatval($promData[$i]['download']) < floatval($limits['download_speed_min'])){
                $test_failed+=1;
               if($this->debug == 1) echo "failed download\n";
            }
            /*echo "TEST FALLIDOS: ".$test_failed."\n";*/
            /*PING*/

            if($this->debug == 1) echo "COMPARANDO ping: ".$promData[$i]['ping']."CON : "."MIN: ".$limits['ping_min']."MAX: ".$limits['ping_max']."\n";
            if (floatval($promData[$i]['ping']) > floatval($limits['ping_max']) || floatval($promData[$i]['ping']) < floatval($limits['ping_min'])){
                $test_failed+=1;
               if($this->debug == 1) echo "failed ping\n";
            }
            /*echo "TEST FALLIDOS: ".$test_failed."\n";*/
            /*UPSTREAM P*/
            if($this->debug == 1) echo "COMPARANDO upstream: ".$promData[$i]['upstream_p']."CON : "."MIN: ".$limits['upstream_power_min']."MAX: ".$limits['upstream_power_max']."\n";
            if (floatval($promData[$i]['upstream_p']) > floatval($limits['upstream_power_max']) || floatval($promData[$i]['upstream_p']) < floatval($limits['upstream_power_min'])){
                $test_failed+=1;
               if($this->debug == 1) echo "failed upstream\n";
            }
            /*echo "TEST FALLIDOS: ".$test_failed."\n";*/
            /*DOWNSTREAM p*/
           if($this->debug == 1) echo "COMPARANDO downstream: ".$promData[$i]['downstream_p']."CON : "."MIN: ".$limits['downstream_power_min']."MAX: ".$limits['downstream_power_max']."\n";
            if (floatval($promData[$i]['downstream_p']) > floatval($limits['downstream_power_max']) || floatval($promData[$i]['downstream_p']) < floatval($limits['downstream_power_min'])){
                $test_failed+=1;
               if($this->debug == 1) echo "failed downstream\n";
            }
            if($this->debug == 1)"COMPARANDO down snr: ".$promData[$i]['downstream_snr']."CON : "."MIN: ".$limits['downstream_snr_min']."MAX: ".$limits['downstream_power_max']."\n";
            if (floatval($promData[$i]['downstream_snr']) > floatval($limits['downstream_snr_max']) || floatval($promData[$i]['downstream_snr']) < floatval($limits['downstream_snr_min'])){
                $test_failed+=1;
               if($this->debug == 1) echo "failed down_snr\n";
            }
            //if($this->debug == 1) echo "TEST FALLIDOS: ".$test_failed."\n";
            if($test_failed>=1){
                $f_cycle++;
            }else{
                $s_cycle++;
            }
        }
        $compareResult['success'] = $s_cycle;
        $compareResult['failed'] = $f_cycle;
        if($this->debug == 1){
            echo "SUCCCESS C:".$compareResult['success']."\n";
            echo "FAILED C:".$compareResult['failed']."\n";
        }

        return $compareResult;
    }

}
?>

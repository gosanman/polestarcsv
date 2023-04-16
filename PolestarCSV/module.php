<?php

    declare(strict_types=1);
	include_once __DIR__ . '/../libs/WebHookModule.php';

    class PolestarCSV extends WebHookModule {
		
		public function __construct($InstanceID) {
		    parent::__construct($InstanceID, 'polestar/' . $InstanceID);
        }
				
        public function Create() {
            	
			parent::Create();
			
			//Erstelle Profile
			if (!IPS_VariableProfileExists('Polestar.driveState')) {
				IPS_CreateVariableProfile('Polestar.driveState', 1);
				IPS_SetVariableProfileIcon('Polestar.driveState', 'Car');
				IPS_SetVariableProfileValues('Polestar.driveState', 0, 1, 1);
				IPS_SetVariableProfileAssociation('Polestar.driveState', 0, 'Auto steht', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.driveState', 1, 'Auto fährt', '', 0xFFFFFF);
			}

			if (!IPS_VariableProfileExists('Polestar.currentGear')) {
				IPS_CreateVariableProfile('Polestar.currentGear', 1);
				IPS_SetVariableProfileIcon('Polestar.currentGear', 'Car');
				IPS_SetVariableProfileValues('Polestar.currentGear', 0, 10, 1);
				IPS_SetVariableProfileAssociation('Polestar.currentGear', 1, 'Leerlauf', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentGear', 2, 'Rückwärtsgang', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentGear', 4, 'Parken', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentGear', 8, 'Dauerbetrieb', '', 0xFFFFFF);
			}

			if (!IPS_VariableProfileExists('Polestar.currentIgnitionState')) {
				IPS_CreateVariableProfile('Polestar.currentIgnitionState', 1);
				IPS_SetVariableProfileIcon('Polestar.currentIgnitionState', 'Car');
				IPS_SetVariableProfileValues('Polestar.currentIgnitionState', 0, 10, 1);
				IPS_SetVariableProfileAssociation('Polestar.currentIgnitionState', 0, 'Lenkrad ist verriegelt', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentIgnitionState', 1, 'Das Lenkrad ist nicht verriegelt, der Motor und alle Zubehörteile sind ausgeschaltet', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentIgnitionState', 2, 'In diesem Zustand ist in der Regel das Zubehör verfügbar (z. B. das Radio). Kombiinstrument und Motor sind ausgeschaltet', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentIgnitionState', 3, 'Die Zündung befindet sich im Zustand AN. Zubehör und Kombiinstrument vorhanden, Motor könnte laufen oder startbereit sein', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentIgnitionState', 4, 'Normalerweise wird in diesem Zustand der Motor gestartet', '', 0xFFFFFF);
				IPS_SetVariableProfileAssociation('Polestar.currentIgnitionState', 5, 'Auto fährt', '', 0xFFFFFF);
			}

			//Erstelle Variablen
			if (!@$this->GetIDForIdent('timestamp')) {
				$this->RegisterVariableInteger('timestamp', 'timestamp', '~UnixTimestamp', 90);
			}			
			if (!@$this->GetIDForIdent('ambientTemperature')) {
				$this->RegisterVariableFloat('ambientTemperature', 'ambientTemperature', '~Temperature', 80);
			}			
			if (!@$this->GetIDForIdent('positionPic')) {
				$this->RegisterVariableString('positionPic', 'positionPic', '~HTMLBox', 63);
			}				
			if (!@$this->GetIDForIdent('positionLongitude')) {
				$this->RegisterVariableString('positionLongitude', 'positionLongitude', '', 62);
			}					
			if (!@$this->GetIDForIdent('positionLatitude')) {
				$this->RegisterVariableString('positionLatitude', 'positionLatitude', '', 61);
			}					
			if (!@$this->GetIDForIdent('positionAltitude')) {
				$this->RegisterVariableString('positionAltitude', 'positionAltitude', '', 60);
			}					
			if (!@$this->GetIDForIdent('isFastCharging')) {
				$this->RegisterVariableBoolean('isFastCharging', 'isFastCharging', '', 52);
			}			
			if (!@$this->GetIDForIdent('isCharging')) {
				$this->RegisterVariableBoolean('isCharging', 'isCharging', '', 51);
			}			
			if (!@$this->GetIDForIdent('chargePortConnected')) {
				$this->RegisterVariableBoolean('chargePortConnected', 'chargePortConnected', '', 50);
			}			
			if (!@$this->GetIDForIdent('maxBatteryLevel')) {
				$this->RegisterVariableFloat('maxBatteryLevel', 'maxBatteryLevel', '~Power', 42);
			}
			if (!@$this->GetIDForIdent('batteryLevel')) {
				$this->RegisterVariableFloat('batteryLevel', 'batteryLevel', '~Power', 41);
			}
			if (!@$this->GetIDForIdent('stateOfCharge')) {
				$this->RegisterVariableInteger('stateOfCharge', 'stateOfCharge', '~Intensity.100', 40);
			}
			if (!@$this->GetIDForIdent('isParked')) {
				$this->RegisterVariableBoolean('isParked', 'isParked', '~Lock', 13);
			}	
			if (!@$this->GetIDForIdent('currentIgnitionState')) {
				$this->RegisterVariableInteger('currentIgnitionState', 'currentIgnitionState', 'Polestar.currentIgnitionState', 12);
			}
			if (!@$this->GetIDForIdent('currentGear')) {
				$this->RegisterVariableInteger('currentGear', 'currentGear', 'Polestar.currentGear', 11);
			}
			if (!@$this->GetIDForIdent('driveState')) {
				$this->RegisterVariableInteger('driveState', 'driveState', 'Polestar.driveState', 10);
			}

			$this->RegisterPropertyString('GoogleApiKey', '');
			$this->RegisterPropertyInteger('Interval', 5);
			$this->RegisterPropertyString('Username', '');
			$this->RegisterPropertyString('Password', '');
        }
	    
		public function Destroy() {
			
			parent::Destroy();
		}

        public function ApplyChanges() {
            //Never delete this line!	
			parent::ApplyChanges();
			
			//Cleanup old hook script
			$id = @IPS_GetObjectIDByIdent('Hook', $this->InstanceID);
			if ($id > 0) {
					IPS_DeleteScript($id, true);
			}
        }

        /*** This function will be called by the hook control. Visibility should be protected! ***/
        protected function ProcessHookData() {
            //Never delete this line!
            parent::ProcessHookData();

            //$this->SendDebug('Data', file_get_contents('php://input'), 0);

            if ((IPS_GetProperty($this->InstanceID, 'Username') != '') || (IPS_GetProperty($this->InstanceID, 'Password') != '')) {
                if (!isset($_SERVER['PHP_AUTH_USER'])) {
                    $_SERVER['PHP_AUTH_USER'] = '';
                }
                if (!isset($_SERVER['PHP_AUTH_PW'])) {
                    $_SERVER['PHP_AUTH_PW'] = '';
                }
                if (($_SERVER['PHP_AUTH_USER'] != IPS_GetProperty($this->InstanceID, 'Username')) || ($_SERVER['PHP_AUTH_PW'] != IPS_GetProperty($this->InstanceID, 'Password'))) {
                    header('WWW-Authenticate: Basic Realm="Polestar CarStatsViewer WebHook"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo 'Authorization required';
                    $this->SendDebug('Unauthorized', file_get_contents('php://input'), 0);
                    return;
                }
            }
			
			$hook = file_get_contents('php://input');
			$arr = json_decode($hook, true);
			
			//$this->SendDebug('Data', print_r($arr, true), 0);
			
			SetValue($this->GetIDForIdent('timestamp'), $arr['timestamp'] / 1000);
			SetValue($this->GetIDForIdent('ambientTemperature'), $arr['ambientTemperature']);

			SetValue($this->GetIDForIdent('batteryLevel'), $arr['batteryLevel'] / 1000);
			SetValue($this->GetIDForIdent('maxBatteryLevel'), $arr['maxBatteryLevel'] / 1000);
			SetValue($this->GetIDForIdent('stateOfCharge'), $arr['stateOfCharge']);

			if($arr['chargePortConnected'] === true)
				SetValue($this->GetIDForIdent('chargePortConnected'), true); else SetValue($this->GetIDForIdent('chargePortConnected'), false);
			if($arr['isCharging'] === true)
				SetValue($this->GetIDForIdent('isCharging'), true); else SetValue($this->GetIDForIdent('isCharging'), false);
			if($arr['isFastCharging'] === true)
				SetValue($this->GetIDForIdent('isFastCharging'), true); else SetValue($this->GetIDForIdent('isFastCharging'), false);

			if($arr['isParked'] === true)
				SetValue($this->GetIDForIdent('isParked'), true); else SetValue($this->GetIDForIdent('isParked'), false);
			SetValue($this->GetIDForIdent('currentIgnitionState'), $arr['currentIgnitionState']);
			SetValue($this->GetIDForIdent('currentGear'), $arr['currentGear']);
			SetValue($this->GetIDForIdent('driveState'), $arr['driveState']);
			
			$apikey = IPS_GetProperty($this->InstanceID, 'GoogleApiKey');
			
			if (empty($apikey)) {
				$this->SendDebug('No Google API Key', 'Please enter a Google Maps API Key in the properties of the module', 0);
				return;
			}
			
			if (!isset($arr['alt']) || !isset($arr['lat']) || !isset($arr['lon'])) {
                $this->SendDebug('No position data', file_get_contents('php://input'), 0);
                return;
            }
				
				$altitude = str_replace(",",".",$arr["alt"]);
				SetValue($this->GetIDForIdent('positionAltitude'), substr($altitude, 0, -4));
				$latitude = str_replace(",",".",$arr["lat"]);
				SetValue($this->GetIDForIdent('positionLatitude'), $latitude);
				$longitude = str_replace(",",".",$arr["lon"]);
				SetValue($this->GetIDForIdent('positionLongitude'), $longitude);
				SetValue($this->GetIDForIdent('positionPic'), '<iframe frameborder="0" class="map-top" width="100%" height="600px" src="https://www.google.com/maps/embed/v1/place?key='.$apikey.'&q='.$latitude.','.$longitude.'&zoom=19&maptype=roadmap" allowfullscreen=""></iframe>');
        
		}			
    }
	
	/**
{
    "alt": 123.6581433287942,                       // Höhenlage
    "ambientTemperature": 4.0,                      // Umgebungstemperatur -> Outside temperature in celsius.
    "avgConsumption": 0.22899047,
    "avgSpeed": 12.445969,
    "batteryLevel": 43416.0,                        // Batterie Level in W -> / 1000 für kw
    "chargePortConnected": false,                   // Ladeanschluss angeschlossen
    "chargeStartDate": "Apr 1, 2023 17:29:48",
    "chargeTime": 3031779,
    "chargedEnergy": 3616.8496,
    "currentGear": 4,                               // aktueller Stand des Getriebe -> EV: 4 = GEAR_PARK
                                                                                           2 = GEAR_REVERSE
                                                                                           8 = GEAR_DRIVE
                                                                                           1 = GEAR_NEUTRAL
    "currentIgnitionState": 2,                      // aktueller Stand der Zündung -> EV: 0 = Steering wheel is locked
                                                                                              Lenkrad ist verriegelt
                                                                                          1 = Steering wheel is not locked, engine and all accessories are OFF
                                                                                              Das Lenkrad ist nicht verriegelt, der Motor und alle Zubehörteile sind ausgeschaltet.
                                                                                          2 = Typically in this state accessories become available (e.g. radio). Instrument cluster and engine are turned off
                                                                                              In diesem Zustand ist in der Regel das Zubehör verfügbar (z. B. das Radio). Kombiinstrument und Motor sind ausgeschaltet
                                                                                          3 = Ignition is in state ON. Accessories and instrument cluster available, engine might be running or ready to be started
                                                                                              Die Zündung befindet sich im Zustand AN. Zubehör und Kombiinstrument vorhanden, Motor könnte laufen oder startbereit sein
                                                                                          4 = Typically in this state engine is starting
                                                                                              Normalerweise wird in diesem Zustand der Motor gestartet
                                                                                          5 = Auto fährt
    "currentPower": 0.28175,                        // aktuelle Leistung
    "currentSpeed": 0.0,                            // aktuelle Geschwindigkeit
    "driveState": 0,                                // Antriebszustand -> 0 = im Stand
                                                                          1 = Auto fährt              
    "isCharging": false,                            // wird geladen
    "isFastCharging": false,                        // ist Schnellladen
    "isParked": true,                               // ist geparkt
    "lat": 45.3456416,                              // Breitengrad
    "lon": 8.4768977,                               // Längengrad
    "maxBatteryLevel": 80400.0,                     // max. Batterie Level in W -> / 1000 für kw
    "stateOfCharge": 54,                            // Status der Ladung -> SoC
    "timestamp": 1680722690134,                     // Zeitstempel des Datenpakets
    "travelTime": 81694269,
    "traveledDistance": 1016760.94,
    "tripStartDate": "Mar 8, 2023 10:26:11",
    "usedEnergy": 232828.56
}
*/
	
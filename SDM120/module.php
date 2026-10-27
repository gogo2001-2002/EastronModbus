<?php

declare(strict_types=1);
	class SDM120 extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();
			// Eigenschaften registrieren
		        $this->RegisterPropertyInteger("DeviceID", 1);
       			$this->RegisterPropertyInteger("Interval", 10);

        		// Verbindung zum Modbus Gateway (Parent)
        		$this->ConnectParent("{A5F663AB-C400-4FE3-A231-4456747A09D2}");

        		// Statusvariablen (Profile müssen in IPS existieren)
        		$this->RegisterVariableFloat("Voltage", "Spannung", "", 1);
        		$this->RegisterVariableFloat("Current", "Stromstärke", "", 2);
        		$this->RegisterVariableFloat("Power", "Wirkleistung", "", 3);
        		$this->RegisterVariableFloat("TotalEnergy", "Gesamtenergie", "", 4);

        		// Timer initialisieren
        		$this->RegisterTimer("UpdateTimer", 0, 'SDM120_Update($_IPS["TARGET"]);');

		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();

		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			// Timer-Intervall setzen
		        $interval = $this->ReadPropertyInteger("Interval");
		        $this->SetTimerInterval("UpdateTimer", $interval * 1000);
        
		        // Instanzstatus setzen
		        if ($this->HasActiveParent()) {
		            $this->SetStatus(102);
		        } else {
		            $this->SetStatus(201);
		        }
		}

		public function Update(): void {
        		if (!$this->HasActiveParent()) {
		            return;
		        }

		        // Adressen laut SDM120 Manual (Input Register, Function 04)
		        $this->SetValue("Voltage", $this->ReadModbusFloat(0x0000));
		        $this->SetValue("Current", $this->ReadModbusFloat(0x0006));
		        $this->SetValue("Power", $this->ReadModbusFloat(0x000C));
		        $this->SetValue("TotalEnergy", $this->ReadModbusFloat(0x0156));
		}

		protected function ReadModbusFloat(int $address): float {
	        // Modbus RTU/TCP Datenpaket senden
        		$result = $this->SendDataToParent(json_encode([
		        "DataID" => "{E310AD31-7FD5-47F3-8465-C0A99FC516CB}",
            			"Function" => 4,
            			"Address" => $address,
            			"Quantity" => 2,
            			"Data" => ""
		        ]));
	
        		if ($result === false || strlen($result) < 6) {
	    			$this->SendDebug("Modbus Error", "Fehler bei Adresse " . $address, 0);
		            	return 0.0;
		        }

		        // Symcon liefert Header + Daten. Wir brauchen nur die letzten 4 Bytes.
		        $data = substr($result, 2);

		        /* * Eastron liefert Big-Endian (ABCD). 
		         * Wir drehen den String um für unpack('f'), falls das System Little-Endian nutzt.
		         */
		        $values = unpack("f", strrev($data));
        
		        return (float)round($values[1], 2);
		}
		
	

	}
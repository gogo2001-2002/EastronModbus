<?php

declare(strict_types=1);

class SDM120 extends IPSModule
{
    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyInteger("Interval", 10);
        $this->ConnectParent("{A5F663AB-C400-4FE5-B207-4D67CC030564}");

        $this->RegisterVariableFloat("Voltage", "Spannung", "", 1);
        $this->RegisterVariableFloat("Current", "Stromstärke", "", 2);
        $this->RegisterVariableFloat("Power", "Wirkleistung", "", 3);
        $this->RegisterVariableFloat("TotalEnergy", "Gesamtenergie", "", 4);

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

        $interval = $this->ReadPropertyInteger("Interval");
        $this->SetTimerInterval("UpdateTimer", $interval * 1000);

        if ($this->HasActiveParent()) {
            $this->SetStatus(102);
        } else {
            $this->SetStatus(201);
        }
    }

    public function Update(): void
    {
        if (!$this->HasActiveParent()) {
            return;
        }

        $this->SetValue("Voltage", $this->ReadModbusFloat(0x0000));
        $this->SetValue("Current", $this->ReadModbusFloat(0x0006));
        $this->SetValue("Power", $this->ReadModbusFloat(0x000C));
        $this->SetValue("TotalEnergy", $this->ReadModbusFloat(0x0156));
    }

    protected function ReadModbusFloat(int $address): float
    {
        $result = $this->SendDataToParent(json_encode([
            "DataID" => "{E310B701-4AE7-458E-B618-EC13A1A6F6A8}",
            "Function" => 4,
            "Address" => $address,
            "Quantity" => 2,
            "Data" => ""
        ]));

        if ($result === false || strlen($result) < 6) {
            $this->SendDebug("Modbus Error", "Fehler bei Adresse " . $address, 0);
            return 0.0;
        }

        // Die Antwort enthält einen zwei Byte langen Header und danach den Float32-Wert.
        $data = substr($result, -4);
        $values = unpack("f", strrev($data));

        return (float) round($values[1], 2);
    }
}

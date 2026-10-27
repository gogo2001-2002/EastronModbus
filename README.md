# EastronModbus

IP-Symcon-Modul für den Eastron-Energiezähler **SDM120** über ein Modbus-Gateway.

## Enthaltene Module

- [SDM120](SDM120/README.md) – liest Spannung, Strom, Wirkleistung und Gesamtwirkenergie.

## Installation

1. Dieses Repository als Modul-Repository in IP-Symcon hinzufügen: https://github.com/gogo2001-2002/EastronModbus
2. Das Modul **SDM120** installieren.
3. Eine SDM120-Instanz anlegen und mit einem kompatiblen Modbus-Gateway verbinden.
4. Die Geräteadresse/Unit-ID für die Modbus-Verbindung am Gateway passend zum Zähler einstellen.

Voraussetzung: IP-Symcon 7.1 oder neuer.

## Hinweise zum aktuellen Funktionsumfang

Die Instanz fragt die vier Messwerte im konfigurierten Intervall einzeln mit Modbus-Funktion 04 ab. Das konfigurierte Feld **Modbus Unit ID** wird im aktuellen PHP-Code nicht in die Anfrage übernommen; die Unit-ID muss daher derzeit am Gateway eingestellt werden. Fehlerhafte oder zu kurze Antworten werden als Messwert 0 abgelegt und im Debug protokolliert.

Weitere Details stehen in der [Dokumentation des SDM120-Moduls](SDM120/README.md).

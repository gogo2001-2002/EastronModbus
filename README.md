# EastronModbus

IP-Symcon-Bibliothek für den Eastron-Energiezähler SDM120 über ein Modbus-Gateway.

## Enthaltenes Modul

- [SDM120](SDM120/README.md): liest Spannung, Stromstärke, Wirkleistung und Gesamtenergie.

## Installation

1. Dieses Repository als Modulquelle in IP-Symcon hinzufügen: https://github.com/gogo2001-2002/EastronModbus
2. Das Modul SDM120 installieren und eine Instanz anlegen.
3. Die Instanz mit einem kompatiblen Modbus-Gateway verbinden.
4. Die Modbus-Unit-ID am Gateway auf die Adresse des Zählers einstellen.

Voraussetzung: IP-Symcon 7.1 oder neuer.

## Hinweis

Das Modul führt pro Aktualisierung vier einzelne FC04-Anfragen aus. Ein fehlgeschlagener oder zu kurzer Request wird als 0,0 in die betreffende Variable geschrieben und zusätzlich im Instanz-Debug protokolliert. Eine echte Messung von 0 ist deshalb nicht von einem Kommunikationsfehler zu unterscheiden.

Die Registeradressen basieren auf dem [SDM120-Modbus-Protokoll von Eastron](https://www.eastroneurope.com/images/uploads/products/protocol/SDM120-MODBUS_Protocol.pdf).

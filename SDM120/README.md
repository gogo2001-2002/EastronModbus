# SDM120

IP-Symcon-Gerätemodul für den Eastron SDM120. Es liest vier Messwerte über ein verbundenes Modbus-Gateway.

## Voraussetzungen und Einrichtung

- IP-Symcon 7.1 oder neuer
- Eastron SDM120 und kompatibles Modbus-RTU/TCP-Gateway
- Eine SDM120-Instanz mit verbundenem Gateway-Parent

Die Modbus-Unit-ID wird am Gateway eingestellt. Das Modul selbst bietet keine Unit-ID-Einstellung.

## Konfiguration

| Feld | Bedeutung |
| --- | --- |
| Abfrageintervall | Zeitabstand zwischen automatischen Aktualisierungen in Sekunden. Standard: 10 s. Wert 0 deaktiviert den Timer. |
| Jetzt aktualisieren | Startet eine sofortige manuelle Abfrage. |

## Messwerte

Für jeden Messwert wird ein separater Read-Input-Registers-Aufruf (Funktion 04) mit zwei Registern ausgeführt.

| Variable | Startadresse (hex) | Typ | Einheit |
| --- | ---: | --- | --- |
| Spannung | 0x0000 | Float32 | V |
| Stromstärke | 0x0006 | Float32 | A |
| Wirkleistung | 0x000C | Float32 | W |
| Gesamtenergie | 0x0156 | Float32 | kWh |

Die Adressen und Einheiten entsprechen der [Eastron-Protokollbeschreibung](https://www.eastroneurope.com/images/uploads/products/protocol/SDM120-MODBUS_Protocol.pdf). Eastron beschreibt Messwerte als 32-Bit IEEE-754-Floats über je zwei aufeinanderfolgende Register.

## Verhalten des Moduls

- Bei einem aktiven Parent hat die Instanz Status 102; ohne aktiven Parent setzt sie Status 201.
- Beim Anwenden der Konfiguration wird der Timer auf Abfrageintervall × 1000 ms gestellt.
- Die Antwort wird anhand der letzten vier Bytes als Float32 interpretiert. Der Code kehrt die Byte-Reihenfolge um und nutzt PHPs native Float-Dekodierung.
- Bei einem fehlgeschlagenen oder kürzeren Antwortpaket als sechs Bytes wird 0,0 zurückgegeben; dazu erscheint eine Debugmeldung „Modbus Error“ mit der Registeradresse.
- Fehlerwerte werden nicht separat markiert. Deshalb kann ein Kommunikationsfehler wie ein gültiger Messwert von 0 aussehen.
- Variablenprofile oder Einheitenprofile werden vom Modul nicht angelegt.
- Das Modul liest keine weiteren Werte wie Frequenz, Leistungsfaktor, Import-/Exportenergie oder Blindleistung.

## Fehlerdiagnose

1. Status 201: Parent-Gateway verbinden und Gatewaystatus prüfen.
2. Alle Werte bleiben 0: Unit-ID und Verbindung am Gateway sowie die Registeranfragen prüfen.
3. Einzelne Werte bleiben 0: Instanz-Debug auf die Meldung „Modbus Error“ prüfen.
4. Prüfen, ob das Antwortformat des Gateways mit der Float-Auswertung des Moduls übereinstimmt.

Die PHP-Syntax wurde mit IP-Symcons Modul-Linter geprüft. Eine Prüfung an einem real angeschlossenen SDM120 war nicht möglich.

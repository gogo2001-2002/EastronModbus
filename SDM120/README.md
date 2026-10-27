# SDM120

Dieses IP-Symcon-Gerätemodul liest vier Messwerte eines Eastron SDM120 über ein verbundenes Modbus-Gateway.

## Voraussetzungen

- IP-Symcon 7.1 oder neuer
- Eastron SDM120 mit erreichbarem Modbus RTU/TCP-Gateway
- Ein Modbus-Gateway als übergeordnete Instanz

## Einrichtung

1. SDM120-Instanz erstellen.
2. Als Parent ein kompatibles Modbus-Gateway verbinden.
3. Die Unit-ID des Zählers am Gateway konfigurieren.
4. Ein Abfrageintervall in Sekunden einstellen. Standardwert ist 10 Sekunden.
5. Mit „Jetzt aktualisieren“ kann eine sofortige Abfrage ausgelöst werden.

## Konfiguration

| Feld | Bedeutung |
| --- | --- |
| Modbus Unit ID | Im aktuellen Modulcode gespeichert, aber nicht in die Modbus-Anfrage eingesetzt. Die tatsächlich verwendete Unit-ID ist daher am Gateway einzustellen. |
| Abfrageintervall | Zeit zwischen den automatischen Aktualisierungen. 0 deaktiviert den Timer. |

## Messwerte

Das Modul fragt Input Register mit Funktion 04 ab. Die Registeradresse und Einheit entsprechen der SDM120-Modbus-Registertabelle.

| Variable | Registeradresse | Typ | Einheit |
| --- | ---: | --- | --- |
| Spannung | 0x0000 | Float32 | V |
| Stromstärke | 0x0006 | Float32 | A |
| Wirkleistung | 0x000C | Float32 | W |
| Gesamtenergie | 0x0156 | Float32 | kWh |

Jeder Messwert wird mit einer eigenen Anfrage gelesen. Die Registeranzahl ist jeweils 2 (ein Float32-Wert).

## Verhalten und Grenzen des aktuellen Codes

- Beim Anwenden der Konfiguration wird der Timer auf Intervall × 1000 ms gesetzt.
- Die Instanz erhält Status 102, wenn ein aktiver Parent vorhanden ist; andernfalls Status 201.
- Bei fehlendem aktivem Parent wird die Aktualisierung abgebrochen.
- Modbus-Anfragen verwenden Funktion 04, die oben aufgeführte Registeradresse und eine Länge von zwei Registern.
- Der Parser entfernt die ersten zwei Bytes der Antwort, kehrt die folgenden Bytes um und interpretiert sie als PHP-Little-Endian-Float. Das setzt voraus, dass das Gateway genau dieses Antwortformat liefert und das Zählerformat dazu passt.
- Bei einer Antwort kürzer als sechs Bytes oder bei einem fehlgeschlagenen Request wird 0.0 zurückgegeben. Ein Modbus-Fehler kann dadurch wie ein echter Messwert von 0 aussehen; zusätzlich wird eine Debugmeldung geschrieben.
- Die Eigenschaft „Modbus Unit ID“ wird zwar angelegt, aber vom Anfragecode nicht verwendet.
- Die Variablenprofile sind nicht Bestandteil des Moduls und werden auch nicht angelegt. Es sind deshalb keine expliziten Anzeigeeinheiten in der Moduldatenbank definiert.

Die aktuelle Modulsyntax wurde mit PHP-Lint geprüft. Eine Funktion mit angeschlossenem und real antwortendem Zähler wurde hier nicht getestet.

## Fehlerdiagnose

- Status 201: Parent-Gateway verbinden und dessen Status prüfen.
- Messwerte bleiben bei 0: Parent-Verbindung, Unit-ID am Gateway, Funktion 04, Registeradresse und Antwortformat kontrollieren.
- Weitere Meldungen stehen im Debug der Instanz unter „Modbus Error“.

# PolestarCSV
Anbindung von (Volvo) Polestar Fahrzeugen an IP-Symcon. Empfängt und verarbeitet den WebHook von Car States Viewer.
Modul befindet sich in der Entwicklung, ich versuche weitere Daten und Funktionen laufend hinzuzufügen.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Anbindung von Volvo Fahrezugen an IP-Symcon. Auslesen von Fahrzeugdaten möglich.
* Das Modul ist noch Beta, für jedes Fahrzeug muss eine eigene Instance angelegt werden und die URL im Car Stats Viewer eingetragen werden.
* Car Stats Viewer sendet alle 5 Sekunden ein Datenpaket an die angegebene URL.

### 2. Voraussetzungen

- IP-Symcon ab Version 6.0

### 3. Software-Installation

* Über den Module Store das Modul PolestarCSV installieren.
* Alternativ über das Module Control folgende URL hinzufügen:
`https://github.com/gosanman/polestarcsv/`

### 4. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" ist das 'PolestarCSV'-Modul unter dem Hersteller '(Gerät)' aufgeführt.  

__Konfigurationsseite__:

Name      | Beschreibung
--------- | ---------------------------------
Google API Schlüssel  | Google API Schlüssel für Maps Integration
Benutzername          | Benutzername für den Zugriff auf den WebHook
Passwort              | Passwort für den Zugriff auf den WebHook
Intervall (Sekunden)  | Intervall in Sekunden zum aktuallisieren der Variablen, 0 = Standard 5 Sekunden (AKTUELL NOCH NICHT IMPLEMENTIERT)

### 5. Statusvariablen und Profile

Die Statusvariablen werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Name                	| Typ      	| Beschreibung
----------------------- | --------- | ----------------
timestamp     			| Integer   | Zeitstempel des letzten Datenpakets
ambientTemperature      | Float   	| Anzeige der Aussentemperatur am Auto
isFastCharging     		| Integer   | Anzeige ob DC geladen wird, true bei über 11kw
isCharging            	| Integer   | Anzeige ob geladen wird
chargePortConnected     | Boolean   | Anzeige ob ein Ladekabel angeschlossen ist
maxBatteryLevel         | Float   	| Anzeige des maximalen Ledezustands der Batterie in kw
batteryLevel           	| Float   	| Anzeige des Ladezustands in kw
stateOfCharge           | Integer   | Anzeige der Batterieladung (SoC)
isParked           		| Boolean   | Anzeige ob das Auto geparkt ist
currentIgnitionState    | Integer   | Anzeige aktueller Stand der "Zündung"
currentGear           	| Integer   | Anzeige aktueller Stand des Getriebes
driveState           	| Integer   | Anzeige des aktuellen Antriebszustand
positionAltitude    	| String    | Aktuelle Position des Autos (Höhenlage)
positionLatitude    	| String    | Aktuelle Position des Autos (Breitengrad)
positionLongitude   	| String    | Aktuelle Position des Autos (Längengrad)
positionPic         	| String    | Google Maps Integration für das Webfront (API Key wird zwingend benötigt)

##### Profile:

Name             				| Typ
------------------------------- | ------- 
Polestar.driveState   			| Integer
Polestar.currentGear 			| Integer
Polestar.currentIgnitionState  	| Integer

### 6. WebFront

Über das WebFront können die momentanen Werte der gelisteten Zielvariablen angezeigt werden.

### 7. PHP-Befehlsreferenz

Aktuell keine verfügbar

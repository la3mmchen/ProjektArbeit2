README

Dieses Repository enthält eine schlanke Version des riddl.me Web-Services, welche im Rahmen einer Projektarbeit für VaWI entwickelt wurde. Die einzige bereitgestellte Ressource ist /get/getContacts. 

Das Repository enthält eine unter protected/data/riddl.mysql eine Testdaten-Kollektion an Benutzer und Relationen zwischen Benutzern. Die Web-Service Adresse zur Einsicht der Doku ist "<relativer Pfad>/index.php/api/" oder zum Aufrufen der Ressource "<relativer Pfad>/index.php/api/get/getContacts". Ein mögliches Programm zum Aufrufen der REST-Schnittstelle ist die Chrome Webapp "Advanced REST Client".

Folgende Benutzerdaten sind vorhanden:
- Benutzername: user1; Password: password; besitzt Kontakt zu user2, user3, user4, user5
- Benutzername: user2; Password: password; besitzt Kontakt zu user1
- Benutzername: user3; Password: password; besitzt Kontakt zu user1
- Benutzername: user4; Password: password; besitzt Kontakt zu user1, user5
- Benutzername: user5; Password: password; besitzt Kontakt zu user1, user4


Die  Anpassungen, welche in dieser Installation gegenüber einer Standard-Yii-Installtion durchgeführt wurden, sind folgende:
- index.php: Setzen des Pfades zum Framework
- protected/config/main.php: Anpassung an Umgebung (DB-Zugang, URL-Manager); Anpassungen im URL-Handling zur Ansteuerung des Web-Services
- protected/components/UserIdentity.php: Implementierung eines rudimentären Passwort-Hashens über eine md5-Summe
- protected/models/User.php: Implementierung der Methoden :beforeSave und :validatePassword zur Passwort-Verwaltung sowie Anpassung in Methode :relations()
- protected/controllers/ApiController: Implementiert die REST-Schnittstelle
- protected/data/riddl.mysql: Bereitstellen von Testdaten zum Import. 


Folgende Aktionen sind zum Benutzen des Quellcodes durchzuführen:
- Download des Repistory
- Einbinden in eine Apache+PHP5 Installation
- Anlage einer MYSQL-Datenbank
- Konfiguration der Datenbank-Credentials unter protected/config/main.php unter dem Eintrag 'db'
- Import des enthaltenen MYSQL-Exports in die angelegte Datenbank

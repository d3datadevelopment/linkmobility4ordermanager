[![deutsche Version](https://logos.oxidmodule.com/de2_xs.svg)](README.md)
[![english version](https://logos.oxidmodule.com/en2_xs.svg)](README.en.md)

# D3 Auftragsmanager-Erweiterung: LINK Mobility Mobile Messaging

Anbindung der LINK Mobility API (Nachrichtenversand per SMS) an den D3 Auftragsmanager

## Inhaltsverzeichnis

- [Installation](#installation)
- [Verwendung](#verwendung)
- [Changelog](#changelog)
- [Beitragen](#beitragen)
- [Lizenz](#lizenz)

## Installation

Dieses Paket erfordert einen mit Composer installierten OXID eShop in einer in der [composer.json](composer.json) definierten Version.

Öffnen Sie eine Kommandozeile und navigieren Sie zum Stammverzeichnis des Shops (Elternverzeichnis von source und vendor). Führen Sie den folgenden Befehl aus. Passen Sie die Pfadangaben an Ihre Installationsumgebung an.

```bash
php composer require d3/linkmobility4ordermanager:^1.0
```

Aktivieren Sie das Modul im Shopadmin unter "Erweiterungen -> Module".

## Verwendung

Die Erweiterung bindet sich direkt im OXID Modul "Auftragsmanager" ein. Öffnen Sie den Eintrag der zu ergänzende Aufgabe und wählen in den Aktionen im Abschnitt "Informationen" die Aktion "SMS senden". Konfigurieren Sie diese nach Ihren Anforderungen. 

Die nötigen Zugangseinstellungen zu LINK Mobility setzen Sie bitte im Schnittstellenmodul für den OXID Shop.

## Changelog

Siehe [CHANGELOG](CHANGELOG.md) für weitere Informationen.

## Beitragen

Wenn Sie eine Verbesserungsvorschlag haben, legen Sie einen Fork des Repositories an und erstellen Sie einen Pull Request. Alternativ können Sie einfach ein Issue erstellen. Fügen Sie das Projekt zu Ihren Favoriten hinzu. Vielen Dank.

- Erstellen Sie einen Fork des Projekts
- Erstellen Sie einen Feature Branch (git checkout -b feature/AmazingFeature)
- Fügen Sie Ihre Änderungen hinzu (git commit -m 'Add some AmazingFeature')
- Übertragen Sie den Branch (git push origin feature/AmazingFeature)
- Öffnen Sie einen Pull Request

## Support

Bei Fragen zum *Messaging Service* und dessen *Verträgen* kontaktieren Sie bitte das [LINK Mobility Team](https://www.linkmobility.de/kontakt).

Zu *technischen Anfragen* finden Sie die Kontaktmöglichkeiten in der [composer.json](composer.json).

## Lizenz
(Stand: 01.09.2022)

Vertrieben unter der GPLv3 Lizenz.

```
Copyright (c) D3 Data Development (Inh. Thomas Dartsch)

Diese Software wird unter der GNU GENERAL PUBLIC LICENSE Version 3 vertrieben.
```

Die vollständigen Copyright- und Lizenzinformationen entnehmen Sie bitte der [LICENSE](LICENSE.md)-Datei, die mit diesem Quellcode verteilt wurde.
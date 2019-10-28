# Prosjekt 1 IMT2291 våren 2019 #
Velkommen til prosjekt 1 i IMT2291 WWW-teknologi våren 2019. For å begynne å jobbe med prosjektet må en fra hver gruppe lage en fork av dette repositoriet og invitere de andre på gruppen til å delta på dette repositoriet.

Husk å velge å beholde rettigheter fra det originale repositoriet når dere oppretter forken av dette repositoriet, da får jeg automatisk tilgang til repositoriet. Sett også repositoriet til et privat repository, dere vil ikke dele koden deres med alle andre men kun de andre på gruppa.

# Prosjektdeltakere #
André Gyrud Gunhildberget

Elisabeth Wåde-Bye

# Oppgaveteksten # 
Oppgaveteksten ligger i [Wikien til det originale repositoriet](https://bitbucket.org/okolloen/imt2291-prosjekt1-2019/wiki/Home).

# Rapporten #
# IMT2291 - Prosjekt 1 - NTNUTube

<center><img src="https://i.imgur.com/U932XJ7.png =200x200" height="200"></center>


### Deltagere:
* Elisabeth Wåde Bye, BPROG, studentnr: 493593.
* André Gyrud Gunhildberget, BPROG, studentnr: 493561.


---


 
### Oppsett av prosjekt:
For å sette opp prosjektet er det følgende ting som må endres:

#### Database:
- I mappen <strong>'dbinit'</strong> ligger det en fil <strong>'prosjekt1.sql'</strong>. Denne filen inneholder all informasjon om tables og relasjoner.
    - Det kan være hensiktsmessig å endre filen så det blir laget en database med ett annet navn enn 'prosjekt1'
- I '<strong>classes/db.php</strong>' må connection informasjonen til mysql/mariadb serveren endres. Følgende variabler må oppdateres:
    - <strong>private $dsn</strong>
    - <strong>private $user</strong>
    - <strong>private $password</strong>

#### Testing:
- I mappen 'tests/' ligger det to filer. 'functional.suite.yml' og 'unit.suite.yml'. Innholdet i begge disse må endres.
    - <strong>'functional.suite.yml'</strong> må følgende endres:
        - <strong>'url:'</strong> endres til URL'en websiden kan nåes på.

    - <strong>'unit.suite.yml'</strong> må følgende endres:
        - <strong>'dsn:'</strong> til den samme informasjonen som ble satt i 'classes/db.php'
        - <strong>'user:'</strong> til den samme informasjonen som ble satt i 'classe/db.php'
        - <strong>'pasword'</strong> til den samme informasjonen som ble satt i 'classes/db.php'

#### Webserver:
- For å ha muligheten til å laste opp thumbnails / videoer på størrelse større enn 2MB må følgende variabler endres i php.ini
    - <strong>'upload_max_filesize'</strong> til ønsket størrelse
    - <strong>'post_max_size'</strong> til ønsket størrelse

### Testing:
For å teste prosjektet er det viktig å kjøre testene i denne rekkefølgen:

- <strong>./vendor/bin/codecept run unit</strong>
- <strong>./vendor/bin/codecept run functional CreateUserCest</strong>
- <strong>./vendor/bin/codecept run functional VideoCest</strong>
- <strong>./vendor/bin/codecept run functional PlaylistCest</strong>


---
## Funksjonalitet og design
### Design
I dette prosjektet har vi valgt å bruke bootstrap for å skape et ryddig og oversiktlig design. Siden er veldig ren og minimalistisk med fokus på funksjonalitet. Tanken bak designet er å holde siden brukervennlig. Samtidig har bruken av bootstrap gjort at vi kunne fokusere mer på funksjonaliten, samtidig som siden ser fin ut.

### Funksjonalitet
Funksjonaliteten i NTNUTube mener vi fyller kravene i oppgavespesifikasjonen på en god og oversiktlig måte. Nedenfor er noen eksempler på organisering av funksjonalitet og/eller implementasjon som vi er fornøyd med. 

#### Bruk av error messages
Vi valgte å lage en egen HTML-template med en feilmelding som vi kan inkludere i andre templates med `{% include 'errormsg.html' %}`, som lar oss effektivt sette en feilmelding som kun vises hvis noe har gått galt. Vi kan også sende med en mer detaljert beskjed om det skulle være behov. Dette har vært en elegant løsning, fordi det betyr vi ikke trenger å ha unik kode per template og hvis vi vil forandre utseende på feilmeldingen kan vi gjøre det på et sted og oppdatere seg i alle andre HTML filene som bruker den. 

#### Forsiden
![Bilde av forsiden](https://i.imgur.com/f06CwL7.png)
Forsiden av NTNUTube, som blir rendret med twig via index.html, er et godt eksempel på hvordan vi har laget en oversiktlig side. Helt øverst har brukeren mulighet til å søke etter video basert på foreleser, tema, emne osv., og søket tar deg videre til en organisert liste av videoer.

Lengre ned på siden får man se nye opplastninger av videoer, der de siste 8 opplastede videoene blir vist, og en liste med spillelister som man abonnerer på. Dette gjør det enkelt å følge med om forelesningen man forventer å se har kommet ut eller ikke. Det blir brukt thumbnails til å gjøre det mer synlig for brukeren hvilke videoer som har blitt lastet opp. 

En annen viktig del av forsiden, som også blir inkludert i de andre sidene, er navbaren. Den gjør det enkelt å navigere rundt på siden, og gjør at brukeren alltid er et trykk unna den viktigste funksjonaliteten. (Det eneste som trenger to klikk er å oppdatere profil og logge ut). Navbaren oppdaterer seg også etter hvilken rolle man har. F.eks. en lærer får muligheten til å laste opp videoer og se sine videoer.

#### Spille av videoen

Når brukeren trykker seg inn på den videoen som han eller hun ønsker å se, blir videoen skalert til et fint 1280x720 format. Brukeren har også valget om å spille av videoen i fullskjerm.

![Bilde av rating](https://i.imgur.com/QZaWH6e.png)

Under videoen kan brukeren, hvis han/hun er en student, avgi en rating. Det vises 5 stjerner, men brukeren kan gi stemme fra 0-5. Det vises også en gjennomsnittlig rating for denne videoen og hvor mange stemmer/ratings som har blitt gitt. Dette gir en oversikt over hvor bra kvalitet videoen har, og hva brukerne syns om den. 

I tillegg kan brukere legge inn opp til flere kommentarer. 

#### Spilleliste
<center><img src="https://i.imgur.com/qlYs1Mg.png"></center>

Bildet ovenfor viser hvordan oversikten av tilgjengelige spillelister ser ut.
Ved å trykke 'Se spilleliste' kan brukern se en oversikt over alle videoene i spillelisten som brukeren samtidlig kan abonnere på den.

### Testing
Vi skrev tester for alle klasser og funksjoner hvor det ga mening. Resultatet av testene før innlevering så slik ut:
<strong>Unit:</strong>

<center><img src="https://i.imgur.com/0VfDeYi.png" width="200"></center>
<strong>Functional:</strong>
</br>
<center><img src="https://i.imgur.com/dgJYPwb.png" width="200"></center>
</br>
<center><img src="https://i.imgur.com/MF6pgad.png" width="200"></center>
</br>
<center><img src="https://i.imgur.com/RAEpNe8.png" width="200"></center>

### Vårt læringsutbytte

Gjennom dette prosjektet har vi lært framework som twig og codeception. Vi har hatt et bra samarbeid på gruppa og jobbet jevnt. Samarbeidet har også latt oss unngå store "merge conflicts". Vi har blitt tryggere på php, twig, testing og bruk av bitbucket.
 

### Forslag til forbedring
Arbeidet vi har gjort i dette prosjektet legger et grunnlag for mer funksjonalitet. Hadde vi skulle forbedret prosjektet ville vi ha lagt til følgende:

- <i>Begrense mengde av elementer på siden ved å legge til 'sider' og en side velger på bunnen av siden.</i>
- <i>Notifikasjonssystem som gir notifikasjoner når en ny kommentar eller rating blir lagt til. Når en ny video i en spilleliste du abonnere er lag til</i>
- <i>Forbedre og optimalisere sql statements</i>
- <i>Legge til en oversikt over de mest populære videoene (de med høyest gjennomsnittlig rating eller mest engasjement i form av videoer.)</i>
- <i>Bruke maskinlæring til å analysere brukerdata og foreslå mer relevante videoer og innhold.</i>
### Notater

* Vi skrev koden på engelsk fordi det føltes mer intuitivt.
* Hvis noe skulle være uklart er det bare å ta kontakt.

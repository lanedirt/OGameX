<?php

return [
    'metal_mine' => [
        'title'            => 'Metaalmijn',
        'description'      => 'Gebruikt bij de winning van metaalertsen, metaalmijnen zijn van primair belang voor alle opkomende en gevestigde rijken.',
        'description_long' => 'Metaal is de primaire grondstof die wordt gebruikt bij de opbouw van uw Imperium. Op grotere diepten kunnen de mijnen meer bruikbaar metaal produceren voor gebruik bij de bouw van gebouwen, schepen, verdedigingssystemen en onderzoek. Naarmate de mijnen dieper graven, is meer energie nodig voor maximale productie. Omdat metaal de meest voorkomende van alle beschikbare grondstoffen is, wordt de waarde ervan beschouwd als de laagste van alle grondstoffen voor handel.',
    ],

    'crystal_mine' => [
        'title'            => 'Kristalmijn',
        'description'      => 'Kristallen zijn de belangrijkste grondstof die wordt gebruikt om elektronische schakelingen te bouwen en bepaalde legeringen te vormen.',
        'description_long' => 'Kristalmijnen leveren de belangrijkste grondstof voor de productie van elektronische schakelingen en bepaalde legeringsverbindingen. Het delven van kristal verbruikt ongeveer anderhalf keer meer energie dan het delven van metaal, waardoor kristal waardevoller is. Bijna alle schepen en gebouwen hebben kristal nodig. De meeste kristallen die nodig zijn voor de bouw van ruimteschepen zijn echter zeer zeldzaam en kunnen, net als metaal, alleen op een bepaalde diepte worden gevonden. Daarom zal het bouwen van mijnen in diepere lagen de hoeveelheid geproduceerd kristal verhogen.',
    ],

    'deuterium_synthesizer' => [
        'title'            => 'Deuteriumsynthesizer',
        'description'      => 'Deuteriumsynthesizers onttrekken het spoor-deuteriumgehalte uit het water op een planeet.',
        'description_long' => 'Deuterium wordt ook zwaar waterstof genoemd. Het is een stabiel isotoop van waterstof met een natuurlijke abundantie in de oceanen van kolonies van ongeveer één atoom per 6500 waterstofatomen (~154 PPM). Deuterium maakt daarmee ongeveer 0,015% (op gewichtsbasis 0,030%) van alle waterstof uit. Deuterium wordt verwerkt door speciale synthesizers die het water van het deuterium kunnen scheiden met behulp van speciaal ontworpen centrifuges. Het uitbreiden van de synthesizer maakt het mogelijk meer deuteriumvoorraden te verwerken. Deuterium wordt gebruikt bij sensorfalanxscans, het verkennen van melkwegen, als brandstof voor schepen en bij gespecialiseerde onderzoeksupgrades.',
    ],

    'solar_plant' => [
        'title'            => 'Zonnecentrale',
        'description'      => 'Zonneenergiecentrales absorberen energie uit zonnestraling. Alle mijnen hebben energie nodig om te functioneren.',
        'description_long' => 'Gigantische zonnepanelenrijen worden gebruikt om stroom te genereren voor de mijnen en de deuteriumsynthesizer. Naarmate de zonnecentrale wordt uitgebreid, neemt het oppervlak van de fotovoltaïsche cellen die de planeet bedekken toe, wat resulteert in een hogere energieproductie over de elektriciteitsnetten van uw planeet.',
    ],

    'fusion_plant' => [
        'title'            => 'Fusiereactor',
        'description'      => 'De fusiereactor gebruikt deuterium om energie te produceren.',
        'description_long' => 'In fusiekrachtcentrales worden waterstofkernen samengesmolten tot heliumkernen onder enorme temperatuur en druk, waarbij enorme hoeveelheden energie vrijkomen. Voor elke gram verbruikt deuterium kan tot 41,32*10^-13 Joule energie worden geproduceerd; met 1 g kunt u 172 MWh energie opwekken.

Grotere reactorcomplexen verbruiken meer deuterium en kunnen meer energie per uur produceren. Het energie-effect kan worden vergroot door energietechnologie te onderzoeken.

De energieproductie van de fusiereactor wordt als volgt berekend:
30 * [Niveau Fusiereactor] * (1,05 + [Niveau Energietechnologie] * 0,01) ^ [Niveau Fusiereactor]',
    ],

    'metal_store' => [
        'title'            => 'Metaalopslag',
        'description'      => 'Biedt opslagcapaciteit voor overtollig metaal.',
        'description_long' => 'Deze reusachtige opslagfaciliteit wordt gebruikt voor de opslag van metaalerts. Elk upgradeniveau verhoogt de hoeveelheid metaalerts die kan worden opgeslagen. Als de opslag vol is, wordt er geen metaal meer gedolven.

De Metaalopslag beschermt een bepaald percentage van de dagelijkse productie van de mijn (max. 10 procent).',
    ],

    'crystal_store' => [
        'title'            => 'Kristalopslagplaats',
        'description'      => 'Biedt opslagcapaciteit voor overtollig kristal.',
        'description_long' => 'Het onverwerkte kristal wordt tijdelijk opgeslagen in deze reusachtige opslaghallen. Met elk upgradeniveau neemt de hoeveelheid kristal die kan worden opgeslagen toe. Als de kristalopslag vol is, wordt er geen kristal meer gedolven.

De Kristalopslagplaats beschermt een bepaald percentage van de dagelijkse productie van de mijn (max. 10 procent).',
    ],

    'deuterium_store' => [
        'title'            => 'Deuteriumtank',
        'description'      => 'Reusachtige tanks voor de opslag van nieuw gewonnen deuterium.',
        'description_long' => 'De deuteriumtank is bedoeld voor de opslag van nieuw gesynthetiseerd deuterium. Zodra het door de synthesizer is verwerkt, wordt het via pijpleidingen in deze tank gepompt voor later gebruik. Met elke upgrade van de tank neemt de totale opslagcapaciteit toe. Zodra de capaciteit bereikt is, wordt er geen deuterium meer gesynthetiseerd.

De Deuteriumtank beschermt een bepaald percentage van de dagelijkse productie van de synthesizer (max. 10 procent).',
    ],

    // -------------------------------------------------------------------------
    // Station / Facilities objects (from StationObjects.php)
    // -------------------------------------------------------------------------

    'robot_factory' => [
        'title'            => 'Robotfabriek',
        'description'      => 'Robotfabrieken leveren bouwrobots om te helpen bij de bouw van gebouwen. Elk niveau verhoogt de bouwsnelheid van gebouwen.',
        'description_long' => 'Het primaire doel van de robotfabriek is de productie van hypermoderne bouwrobots. Elke upgrade van de robotfabriek resulteert in de productie van snellere robots, die worden gebruikt om de tijd die nodig is voor de bouw van gebouwen te verkorten.',
    ],

    'shipyard' => [
        'title'            => 'Scheepswerf',
        'description'      => 'Alle soorten schepen en verdedigingsinstallaties worden gebouwd in de planetaire scheepswerf.',
        'description_long' => 'De planetaire scheepswerf is verantwoordelijk voor de bouw van ruimtevaartuigen en verdedigingsmechanismen. Naarmate de scheepswerf wordt uitgebreid, kan zij een grotere verscheidenheid aan voertuigen produceren met een veel hogere snelheid. Als er een nanietfabriek aanwezig is op de planeet, wordt de snelheid waarmee schepen worden gebouwd enorm verhoogd.',
    ],

    'research_lab' => [
        'title'            => 'Onderzoekslaboratorium',
        'description'      => 'Een onderzoekslaboratorium is vereist om onderzoek te doen naar nieuwe technologieën.',
        'description_long' => 'Een essentieel onderdeel van elk imperium; onderzoekslaboratoria zijn de plek waar nieuwe technologieën worden ontdekt en oudere technologieën worden verbeterd. Met elk geconstrueerd niveau van het onderzoekslaboratorium neemt de snelheid waarmee nieuwe technologieën worden onderzocht toe, terwijl ook nieuwere technologieën om te onderzoeken worden ontgrendeld. Om zo snel mogelijk onderzoek te kunnen doen, worden wetenschappers onmiddellijk naar de kolonie gestuurd om te beginnen met werken en ontwikkelen. Op deze manier kan kennis over nieuwe technologieën gemakkelijk worden verspreid door het imperium.',
    ],

    'alliance_depot' => [
        'title'            => 'Alliantiedepot',
        'description'      => 'Het alliantiedepot levert brandstof aan bevriende vloten in een baan om de planeet die helpen bij de verdediging.',
        'description_long' => 'Het alliantiedepot levert brandstof aan bevriende vloten in een baan om de planeet die helpen bij de verdediging. Voor elk upgradeniveau van het alliantiedepot kan een speciale hoeveelheid deuterium per uur naar een orbiterende vloot worden gestuurd.',
    ],

    'missile_silo' => [
        'title'            => 'Raketensilo',
        'description'      => 'Raketensilo\'s worden gebruikt voor de opslag van raketten.',
        'description_long' => 'Raketensilo\'s worden gebruikt voor de bouw, opslag en lancering van interplanetaire raketten en anti-ballistische raketten. Met elk niveau van de silo kunnen vijf interplanetaire raketten of tien anti-ballistische raketten worden opgeslagen. Eén interplanetaire raket neemt dezelfde ruimte in als twee anti-ballistische raketten. Opslag van zowel interplanetaire raketten als anti-ballistische raketten in dezelfde silo is toegestaan.',
    ],

    'nano_factory' => [
        'title'            => 'Nanietfabriek',
        'description'      => 'Dit is het toppunt van robotica-technologie. Elk niveau verkort de bouwtijd voor gebouwen, schepen en verdedigingen.',
        'description_long' => 'Een nanomachine, ook wel naniët genoemd, is een mechanisch of elektromechanisch apparaat waarvan de afmetingen worden gemeten in nanometers (miljoensten van een millimeter, of eenheden van 10^-9 meter). De microscopische omvang van nanomachines vertaalt zich in hogere operationele snelheid. Deze fabriek produceert nanomachines die de ultieme evolutie in robotica-technologie zijn. Eenmaal gebouwd verkort elke upgrade de productietijd voor gebouwen, schepen en verdedigingsstructuren aanzienlijk.',
    ],

    'terraformer' => [
        'title'            => 'Terraformer',
        'description'      => 'De terraformer vergroot het bruikbare oppervlak van planeten.',
        'description_long' => 'Met de toenemende bebouwing op planeten wordt zelfs de leefruimte voor de kolonie steeds beperkter. Traditionele methoden zoals hoge gebouwen en ondergrondse constructie worden steeds onvoldoende. Een kleine groep hoge-energiefysici en nano-ingenieurs vond uiteindelijk de oplossing: terraforming.
Door gebruik te maken van enorme hoeveelheden energie kan de terraformer hele landstreken of zelfs continenten bewerkbaar maken. Dit gebouw herbergt de productie van nanomachines die speciaal voor dit doel zijn gemaakt en die zorgen voor een consistente bodemkwaliteit.

Elk terraformerniveau maakt 5 velden bebouwbaar. Met elk niveau neemt de terraformer zelf één veld in beslag. Elke 2 terraformerniveaus ontvangt u 1 bonusveld.

Eenmaal gebouwd kan de terraformer niet worden gesloopt.',
    ],

    'space_dock' => [
        'title'            => 'Ruimtedok',
        'description'      => 'Wrakken kunnen worden gerepareerd in het Ruimtedok.',
        'description_long' => 'Het Ruimtedok biedt de mogelijkheid om schepen die zijn vernietigd in een gevecht en wrakken hebben achtergelaten, te repareren. De reparatietijd neemt maximaal 12 uur in beslag, maar het duurt minimaal 30 minuten voordat de schepen weer in gebruik kunnen worden gesteld.

Reparaties moeten beginnen binnen 3 dagen na het ontstaan van het wrak. De gerepareerde schepen moeten na voltooiing van de reparaties handmatig worden teruggestuurd naar actieve dienst. Als dit niet gedaan wordt, worden individuele schepen van elk type na 3 dagen automatisch teruggezet.

Een wrak verschijnt alleen als meer dan 150.000 eenheden zijn vernietigd, inclusief eigen schepen die deelnamen aan het gevecht met een waarde van minimaal 5% van de scheepspunten.

Omdat het Ruimtedok in een baan om de planeet zweeft, heeft het geen planetair veld nodig.',
    ],

    'lunar_base' => [
        'title'            => 'Maanbasis',
        'description'      => 'Omdat de maan geen atmosfeer heeft, is een maanbasis vereist om bewoonbare ruimte te creëren.',
        'description_long' => 'Een maan heeft geen atmosfeer, dus er moet eerst een maanbasis worden gebouwd voordat er een nederzetting kan worden ingericht. Deze levert vervolgens zuurstof, verwarming en zwaartekracht. Met elk geconstrueerd niveau wordt een grotere leef- en ontwikkelingsruimte geboden binnen de biosfeer. Elk geconstrueerd niveau biedt drie velden voor andere gebouwen. Met elk niveau neemt de maanbasis zelf één veld in beslag.
Eenmaal gebouwd kan de maanbasis niet worden gesloopt.',
    ],

    'sensor_phalanx' => [
        'title'            => 'Sensorfalanx',
        'description'      => 'Met de sensorfalanx kunnen vloten van andere rijken worden ontdekt en geobserveerd. Hoe groter de sensorfalanx, hoe groter het bereik dat gescand kan worden.',
        'description_long' => 'Met behulp van hoge-resolutiesensoren scant de sensorfalanx eerst het lichtspectrum, de samenstelling van gassen en de stralingsemissies van een verre wereld, en stuurt de gegevens door naar een supercomputer voor verwerking. Zodra de informatie is verkregen, vergelijkt de supercomputer wijzigingen in het spectrum, de gassamenstelling en de stralingsemissies met een basiskaart van bekende spectrumwijzigingen die worden veroorzaakt door verschillende scheepsbewegingen. De resulterende gegevens tonen vervolgens de activiteit van elke vloot binnen het bereik van de falanx. Om te voorkomen dat de supercomputer oververhit raakt tijdens het proces, wordt deze gekoeld met 5k verwerkt deuterium.
Klik op een planeet in de galaxyweergave binnen uw sensorbereik om de falanx te gebruiken.',
    ],

    'jump_gate' => [
        'title'            => 'Sprongpoort',
        'description'      => 'Sprongpoorten zijn reusachtige transceivers die zelfs de grootste vloot in een oogwenk naar een verre sprongpoort kunnen sturen.',
        'description_long' => 'Een sprongpoort is een systeem van reusachtige transceivers dat zelfs de grootste vloten naar een ontvangende poort ergens in het universum kan sturen zonder tijdverlies. Gebruik makend van technologie vergelijkbaar met die van een wormgat om de sprong te bereiken, is deuterium niet vereist. Tussen sprongen moet een oplaadperiode van enkele minuten verstrijken voor regeneratie. Het transporteren van grondstoffen via de poort is evenmin mogelijk. Met elk upgradeniveau kan de afkoeltijd van de sprongpoort worden verkort.',
    ],

    // -------------------------------------------------------------------------
    // Research objects (from ResearchObjects.php)
    // -------------------------------------------------------------------------

    'energy_technology' => [
        'title'            => 'Energietechnologie',
        'description'      => 'De beheersing van verschillende soorten energie is noodzakelijk voor veel nieuwe technologieën.',
        'description_long' => 'Naarmate verschillende onderzoeksgebieden vorderden, werd ontdekt dat de huidige technologie voor energiedistributie niet voldoende was om bepaald gespecialiseerd onderzoek te beginnen. Met elke upgrade van uw Energietechnologie kan nieuw onderzoek worden gedaan dat de ontwikkeling van geavanceerdere schepen en verdedigingen ontgrendelt.',
    ],

    'laser_technology' => [
        'title'            => 'Lasertechnologie',
        'description'      => 'Het focussen van licht produceert een bundel die schade veroorzaakt wanneer deze een object raakt.',
        'description_long' => 'Lasers (lichtversterking door gestimuleerde emissie van straling) produceren een intense, energierijke emissie van coherent licht. Deze apparaten kunnen worden gebruikt in allerlei gebieden, van optische computers tot zware lasergewapens die moeiteloos door pantserplaten heen snijden. De lasertechnologie vormt een belangrijke basis voor onderzoek naar andere wapenentechnologieën.',
    ],

    'ion_technology' => [
        'title'            => 'Ionentechnologie',
        'description'      => 'De concentratie van ionen maakt de bouw van kanonnen mogelijk die enorme schade kunnen aanrichten en de sloopkosten per niveau met 4% verlagen.',
        'description_long' => 'Ionen kunnen worden geconcentreerd en versneld tot een dodelijke bundel. Deze bundels kunnen vervolgens enorme schade aanrichten. Onze wetenschappers hebben ook een techniek ontwikkeld die de sloopkosten voor gebouwen en systemen aanzienlijk zal verlagen. Voor elk onderzoeksniveau dalen de sloopkosten met 4%.',
    ],

    'hyperspace_technology' => [
        'title'            => 'Hyperruimtetechnologie',
        'description'      => 'Door de integratie van de 4e en 5e dimensie is het nu mogelijk een nieuw type aandrijving te onderzoeken dat zuiniger en efficiënter is.',
        'description_long' => 'In theorie is het idee van hyperruimtereizen gebaseerd op het bestaan van een afzonderlijke en aangrenzende dimensie. Wanneer geactiveerd, verplaatst een hyperruimteaandrijving het ruimteschip naar deze andere dimensie, waar het in veel kortere tijd enorme afstanden kan overbruggen dan in de "normale" ruimte. Zodra het het punt in de hyperruimte bereikt dat overeenkomt met zijn bestemming in de echte ruimte, keert het terug.
Zodra voldoende hyperruimtetechnologie is onderzocht, is de hyperruimteaandrijving niet langer slechts een theorie. Elke verbetering van deze aandrijving vergroot de laadcapaciteit van uw schepen met 5% van de basiswaarde.',
    ],

    'plasma_technology' => [
        'title'            => 'Plasmatechnologie',
        'description'      => 'Een verdere ontwikkeling van de ionentechnologie die hoog-energetisch plasma versnelt, wat vervolgens verwoestende schade aanricht en bovendien de productie van metaal, kristal en deuterium optimaliseert (1%/0,66%/0,33% per niveau).',
        'description_long' => 'Een verdere ontwikkeling van de ionentechnologie die geen ionen maar hoog-energetisch plasma versnelt, dat bij inslag op een object verwoestende schade kan veroorzaken. Onze wetenschappers hebben ook een manier gevonden om de winning van metaal en kristal met behulp van deze technologie merkbaar te verbeteren.

De metaalproductie stijgt met 1%, de kristalproductie met 0,66% en de deuteriumproductie met 0,33% per bouwniveau van de plasmatechnologie.',
    ],

    'combustion_drive' => [
        'title'            => 'Verbrandingsaandrijving',
        'description'      => 'De ontwikkeling van deze aandrijving maakt sommige schepen sneller, hoewel elk niveau de snelheid slechts met 10% van de basiswaarde verhoogt.',
        'description_long' => 'De verbrandingsaandrijving is de oudste van alle technologieën, maar wordt nog steeds gebruikt. Bij de verbrandingsaandrijving wordt uitlaatgas gevormd uit drijfgassen die vóór gebruik aan boord van het schip worden meegevoerd. In een gesloten kamer zijn de drukken in elke richting gelijk en treedt er geen versnelling op. Als er onderaan de kamer een opening wordt gemaakt, wordt de druk aan die zijde niet meer tegengehouden. De resterende druk geeft een resulterende stuwkracht aan de zijde tegenover de opening, die het schip voortstuwt door het uitlaatgas met extreem hoge snelheid naar achteren uit te stoten.

Met elk niveau van de verbrandingsaandrijving neemt de snelheid van kleine en grote vrachtschepen, lichte jagers, recyclers en spionagesondes met 10% toe.',
    ],

    'impulse_drive' => [
        'title'            => 'Impulsaandrijving',
        'description'      => 'De impulsaandrijving is gebaseerd op het reactieprincipe. Verdere ontwikkeling van deze aandrijving maakt sommige schepen sneller, hoewel elk niveau de snelheid slechts met 20% van de basiswaarde verhoogt.',
        'description_long' => 'De impulsaandrijving is gebaseerd op het terugslagprincipe, waarbij de gestimuleerde emissie van straling voornamelijk wordt geproduceerd als bijproduct van de kernfusie om energie te winnen. Bovendien kunnen andere massa\'s worden ingespoten. Met elk niveau van de impulsaandrijving neemt de snelheid van bommenwerpers, kruisers, zware jagers en kolonisatieschepen met 20% van de basiswaarde toe. Bovendien worden de kleine transportschepen uitgerust met impulsaandrijvingen zodra hun onderzoeksniveau 5 bereikt. Zodra het onderzoek naar de impulsaandrijving niveau 17 heeft bereikt, worden recyclers uitgerust met impulsaandrijvingen.

Interplanetaire raketten reizen ook verder met elk niveau.',
    ],

    'hyperspace_drive' => [
        'title'            => 'Hyperruimteaandrijving',
        'description'      => 'De hyperruimteaandrijving krult de ruimte om een schip heen. De ontwikkeling van deze aandrijving maakt sommige schepen sneller, hoewel elk niveau de snelheid slechts met 30% van de basiswaarde verhoogt.',
        'description_long' => 'In de directe omgeving van het schip wordt de ruimte gekromd zodat grote afstanden zeer snel kunnen worden overbrugd. Hoe meer de hyperruimteaandrijving is ontwikkeld, hoe sterker de gekromde aard van de ruimte, waardoor de snelheid van de daarmee uitgeruste schepen (Slagkruisers, Slagschepen, Vernietigers, Sterren des Doods, Pioniers en Maaiers) met 30% per niveau toeneemt. Bovendien wordt de bommenwerper gebouwd met een hyperruimteaandrijving zodra het onderzoek niveau 8 bereikt. Zodra het hyperruimteaandrijvingonderzoek niveau 15 bereikt, wordt de recycler uitgerust met een hyperruimteaandrijving.',
    ],

    'espionage_technology' => [
        'title'            => 'Spionagetechnologie',
        'description'      => 'Informatie over andere planeten en manen kan worden verkregen met behulp van deze technologie.',
        'description_long' => 'Spionagetechnologie is in de eerste plaats een vooruitgang van sensortechnologie. Hoe geavanceerder deze technologie is, hoe meer informatie de gebruiker ontvangt over activiteiten in zijn omgeving.
De verschillen tussen uw eigen spionageniveau en de spionageniveaus van tegenstanders zijn cruciaal voor sondes. Hoe geavanceerder uw eigen spionagetechnologie is, hoe meer informatie het rapport kan verzamelen en hoe kleiner de kans is dat uw spionageactiviteiten worden ontdekt. Hoe meer sondes u op één missie stuurt, hoe meer details ze kunnen verzamelen van de doelplaneet. Maar tegelijkertijd vergroot dit ook de kans op ontdekking.
Spionagetechnologie verbetert ook de kans om buitenlandse vloten te lokaliseren. Het spionageniveau is essentieel bij het bepalen hiervan. Vanaf niveau 2 wordt het exacte totale aantal aanvallende schepen weergegeven naast de normale aanvalsmelding. Vanaf niveau 4 wordt het type aanvallende schepen plus het totale aantal getoond, en vanaf niveau 8 wordt het exacte aantal van de verschillende scheepstypes getoond.
Deze technologie is onmisbaar bij een aanstaande aanval, omdat het u informeert of de doelvloot verdediging beschikbaar heeft. Daarom moet deze technologie zo vroeg mogelijk worden onderzocht.',
    ],

    'computer_technology' => [
        'title'            => 'Computertechnologie',
        'description'      => 'Meer vloten kunnen worden aangestuurd door computercapaciteiten te vergroten. Elk niveau van computertechnologie verhoogt het maximale aantal vloten met één.',
        'description_long' => 'Eenmaal gelanceerd op een willekeurige missie worden vloten voornamelijk bestuurd door een reeks computers op de oorspronkelijke planeet. Deze gigantische computers berekenen de exacte aankomsttijd, voeren de nodige koerscorrecties uit, berekenen trajecten en reguleren vliegsnelheden.
Met elk onderzocht niveau wordt de vluchtcomputer geüpgraded om een extra slot te kunnen lanceren. Computertechnologie moet voortdurend worden ontwikkeld gedurende de opbouw van uw imperium.',
    ],

    'astrophysics' => [
        'title'            => 'Astrofysica',
        'description'      => 'Met een astrofysica-onderzoeksmodule kunnen schepen lange expedities ondernemen. Elk tweede niveau van deze technologie stelt u in staat een extra planeet te koloniseren.',
        'description_long' => 'Verdere bevindingen op het gebied van astrofysica maken de bouw mogelijk van laboratoria die op steeds meer schepen kunnen worden gemonteerd. Dit maakt lange expedities diep in onverkende gebieden van de ruimte mogelijk. Bovendien kunnen deze vorderingen worden gebruikt om het universum verder te koloniseren. Voor elke twee niveaus van deze technologie kan een extra planeet bruikbaar worden gemaakt.',
    ],

    'intergalactic_research_network' => [
        'title'            => 'Intergalactisch Onderzoeksnetwerk',
        'description'      => 'Onderzoekers op verschillende planeten communiceren via dit netwerk.',
        'description_long' => 'Dit is uw netwerk voor de diepe ruimte om onderzoeksresultaten naar uw kolonies te communiceren. Met het IGN kunnen snellere onderzoekstijden worden bereikt door de hoogste niveau-onderzoekslaboratoria te koppelen gelijk aan het niveau van het ontwikkelde IGN.
Om te functioneren moet elke kolonie het onderzoek zelfstandig kunnen uitvoeren.',
    ],

    'graviton_technology' => [
        'title'            => 'Gravitontechnologie',
        'description'      => 'Het afvuren van een geconcentreerde lading gravitondeeltjes kan een kunstmatig zwaartekrachtveld creëren dat schepen of zelfs manen kan vernietigen.',
        'description_long' => 'Een graviton is een elementair deeltje dat massaloos is en geen lading heeft. Het bepaalt de zwaartekrachtwerking. Door een geconcentreerde lading gravitons af te vuren, kan een kunstmatig gravitatieveld worden geconstrueerd. Net als een zwart gat trekt het massa naar zich toe. Het kan daarmee schepen en zelfs hele manen vernietigen. Om voldoende gravitons te produceren zijn enorme hoeveelheden energie vereist. Gravitononderzoek is vereist voor de bouw van een verwoestende Ster des Doods.',
    ],

    'weapon_technology' => [
        'title'            => 'Wapenentechnologie',
        'description'      => 'Wapenentechnologie maakt wapensystemen efficiënter. Elk niveau wapenentechnologie verhoogt de wapenkracht van eenheden met 10% van de basiswaarde.',
        'description_long' => 'Wapenentechnologie is een sleutelonderzoekstechnologie en is essentieel voor uw overleving tegen vijandige rijken. Met elk onderzocht niveau Wapenentechnologie worden de wapensystemen op schepen en uw verdedigingsmechanismen steeds efficiënter. Elk niveau verhoogt de basissterkte van uw wapens met 10% van de basiswaarde.',
    ],

    'shielding_technology' => [
        'title'            => 'Schildtechnologie',
        'description'      => 'Schildtechnologie maakt de schilden op schepen en verdedigingsinstallaties efficiënter. Elk niveau schildtechnologie verhoogt de sterkte van de schilden met 10% van de basiswaarde.',
        'description_long' => 'Met de uitvinding van de magnetosfeergenerator leerden wetenschappers dat een kunstmatig schild kon worden geproduceerd om de bemanning in ruimteschepen niet alleen te beschermen tegen de harde zonnestralingomgeving in de diepe ruimte, maar ook bescherming te bieden tegen vijandelijk vuur tijdens een aanval. Nadat wetenschappers de technologie uiteindelijk hadden geperfectioneerd, werd een magnetosfeergenerator op alle schepen en verdedigingssystemen geïnstalleerd.

Naarmate de technologie naar elk niveau wordt gevorderd, wordt de magnetosfeergenerator geüpgraded, wat een extra 10% sterkte aan de basiswaarde van de schilden geeft.',
    ],

    'armor_technology' => [
        'title'            => 'Pantsertechnologie',
        'description'      => 'Speciale legeringen verbeteren het pantser op schepen en verdedigingsstructuren. De effectiviteit van het pantser kan per niveau met 10% worden verhoogd.',
        'description_long' => 'De omgeving van de diepe ruimte is onherbergzaam. Piloten en bemanning op verschillende missies werden niet alleen geconfronteerd met intense zonnestraling, maar ook met de mogelijkheid geraakt te worden door ruimtepuin of vernietigd te worden door vijandelijk vuur tijdens een aanval. Met de ontdekking van een aluminium-lithium titaancarbide legering, die zowel lichtgewicht als duurzaam bleek te zijn, werd de bemanning een zekere mate van bescherming geboden. Met elk ontwikkeld niveau Pantsertechnologie wordt een hoogwaardigere legering geproduceerd, waardoor de sterkte van het pantser met 10% toeneemt.',
    ],

    // ---- Civil Ships ----

    'small_cargo' => [
        'title'            => 'Klein Vrachtschip',
        'description'      => 'Het kleine vrachtschip is een wendbaar schip dat snel grondstoffen naar andere planeten kan transporteren.',
        'description_long' => 'Transporters zijn ongeveer even groot als jagers, maar ze geven hoge-prestatieaandrijvingen en boordwapens op voor winst in hun vrachtvermogen. Als gevolg hiervan mag een transporter alleen in gevechten worden gestuurd wanneer hij vergezeld wordt door gevechtsklare schepen.

Zodra de impulsaandrijving onderzoeksniveau 5 bereikt, reist het kleine transportschip met verhoogde basissnelheid en is uitgerust met een impulsaandrijving.',
    ],

    'large_cargo' => [
        'title'            => 'Groot Vrachtschip',
        'description'      => 'Dit vrachtschip heeft een veel grotere laadcapaciteit dan het kleine vrachtschip, en is over het algemeen sneller dankzij een verbeterde aandrijving.',
        'description_long' => 'Naarmate de tijd verstreek, resulteerden de aanvallen op kolonies in steeds grotere hoeveelheden buit. Als gevolg hiervan werden kleine vrachtschepen in grote aantallen uitgezonden om de grotere buit te compenseren. Al snel bleek dat een nieuwe klasse schepen nodig was om de buit bij aanvallen te maximaliseren, maar ook kosteneffectief te zijn. Na veel ontwikkeling werd het grote vrachtschip geboren.

Om de grondstoffen die kunnen worden opgeslagen in de ruimen te maximaliseren, heeft dit schip weinig wapens of pantser. Dankzij de zeer ontwikkelde verbrandingsmotor die is geïnstalleerd, is het het meest economische grondstoffenleverancier tussen planeten en het meest effectief bij aanvallen op vijandige werelden.',
    ],

    'colony_ship' => [
        'title'            => 'Kolonisatieschip',
        'description'      => 'Lege planeten kunnen worden gekoloniseerd met dit schip.',
        'description_long' => 'In de 20e eeuw besloot de mens de sterren te bereiken. Eerst was er de landing op de Maan. Daarna werd er een ruimtestation gebouwd. Mars werd al snel gekoloniseerd. Al snel werd duidelijk dat onze groei afhing van het koloniseren van andere werelden. Wetenschappers en ingenieurs van over de hele wereld kwamen samen om \'s mensdoms grootste prestatie ooit te ontwikkelen. Het kolonisatieschip was geboren.

Dit schip wordt gebruikt om een nieuw ontdekte planeet voor te bereiden op kolonisatie. Zodra het de bestemming bereikt, wordt het schip onmiddellijk omgebouwd tot bewoonbare leefruimte om te helpen bij het bevolken en mijnbouwen van de nieuwe wereld. Het maximale aantal planeten wordt bepaald door de voortgang in het astrofysica-onderzoek. Twee nieuwe niveaus van astrotechnologie staan de kolonisatie van één extra planeet toe.',
    ],

    'recycler' => [
        'title'            => 'Recycler',
        'description'      => 'Recyclers zijn de enige schepen die puinvelden kunnen oogsten die na gevechten in een baan om een planeet drijven.',
        'description_long' => 'Gevechten in de ruimte namen steeds grotere proporties aan. Duizenden schepen werden vernietigd en de grondstoffen van hun resten leken voor altijd verloren in de puinvelden. Normale vrachtschepen konden niet dicht genoeg bij deze velden komen zonder aanzienlijke schade te riskeren.
Een recente ontwikkeling in schildtechnologieën omzeilde dit probleem efficiënt. Er werd een nieuwe klasse schepen gecreëerd die vergelijkbaar waren met de transporters: de recyclers. Hun inspanningen hielpen de ogenschijnlijk verloren grondstoffen te verzamelen en te bergen. Het puin vormde dankzij de nieuwe schilden geen echte bedreiging meer.

Zodra het impulsaandrijvingonderzoek niveau 17 heeft bereikt, worden recyclers uitgerust met impulsaandrijvingen. Zodra het hyperruimteaandrijvingonderzoek niveau 15 heeft bereikt, worden recyclers uitgerust met hyperruimteaandrijvingen.',
    ],

    'espionage_probe' => [
        'title'            => 'Spionagesonde',
        'description'      => 'Spionagesondes zijn kleine, wendbare drones die gegevens verstrekken over vloten en planeten over grote afstanden.',
        'description_long' => 'Spionagesondes zijn kleine, wendbare drones die gegevens verstrekken over vloten en planeten. Uitgerust met speciaal ontworpen motoren kunnen ze in slechts enkele minuten enorme afstanden overbruggen. Eenmaal in een baan om de doelplaneet verzamelen ze snel gegevens en sturen het rapport terug via uw Diepe Ruimtenetwerk voor evaluatie. Maar er is een risico aan het intelligentieverzamelingsaspect. Tijdens de tijd dat het rapport wordt teruggestuurd naar uw netwerk, kan het signaal worden gedetecteerd door het doelwit en kunnen de sondes worden vernietigd.',
    ],

    'solar_satellite' => [
        'title'            => 'Zonnesatelliet',
        'description'      => 'Zonnesatellieten zijn eenvoudige platforms van zonnecellen, gelegen in een hoge, stationaire baan. Ze vangen zonlicht op en sturen het via laser naar het grondstation.',
        'description_long' => 'Wetenschappers ontdekten een methode om elektrische energie via speciaal ontworpen satellieten in een geosynchrone baan naar de kolonie te sturen. Zonnesatellieten verzamelen zonne-energie en sturen dit via geavanceerde lasertechnologie naar een grondstation. De efficiëntie van een zonnesatelliet hangt af van de sterkte van de zonnestraling die hij ontvangt. In principe is de energieproductie in banen dichter bij de zon groter dan voor planeten in banen ver van de zon.
Vanwege hun goede prijs-prestatieverhouding kunnen zonnesatellieten veel energieproblemen oplossen. Maar let op: zonnesatellieten kunnen gemakkelijk worden vernietigd in gevechten.',
    ],

    'crawler' => [
        'title'            => 'Crawler',
        'description'      => 'Crawlers verhogen de productie van metaal, kristal en deuterium op hun toegewezen planeet met respectievelijk 0,02%, 0,02% en 0,02%. Als verzamelaar neemt de productie ook toe. Het maximale totale bonusbedrag hangt af van het algehele niveau van uw mijnen.',
        'description_long' => 'De crawler is een groot loopgraafvoertuig dat de productie van mijnen en synthesizers verhoogt. Het is wendbaarder dan het eruit ziet, maar het is niet bijzonder robuust. Elke crawler verhoogt de metaalproductie met 0,02%, de kristalproductie met 0,02% en de deuteriumproductie met 0,02%. Als verzamelaar neemt de productie ook toe. Het maximale totale bonusbedrag hangt af van het algehele niveau van uw mijnen.',
    ],

    'pathfinder' => [
        'title'            => 'Pionier',
        'description'      => 'De pionier is een snel en wendbaar schip, speciaal gebouwd voor expedities in onbekende ruimtesectoren.',
        'description_long' => 'De pionier is de nieuwste ontwikkeling in verkenningsstechnologie. Dit schip was speciaal ontworpen voor leden van de Ontdekker-klasse om hun potentieel te maximaliseren. Uitgerust met geavanceerde scansystemen en een groot vrachtruim voor het bergen van grondstoffen, blinkt de pionier uit bij expedities. Zijn geavanceerde sensoren kunnen waardevolle grondstoffen en anomalieën detecteren die onopgemerkt zouden blijven bij andere schepen. Het schip combineert een hoge snelheid met een goede laadcapaciteit, waardoor het perfect is voor snelle verkenningsopdrachten en het verzamelen van grondstoffen uit verre sectoren.',
    ],

    // ---- Military Ships ----

    'light_fighter' => [
        'title'            => 'Lichte Jager',
        'description'      => 'Dit is het eerste gevechtsschip dat alle keizers zullen bouwen. De lichte jager is een wendbaar schip, maar kwetsbaar op zichzelf. In grote aantallen kunnen ze een grote bedreiging vormen voor elk imperium. Ze zijn de eersten om kleine en grote vrachtschepen te vergezellen naar vijandige planeten met geringe verdediging.',
        'description_long' => 'Dit is het eerste gevechtsschip dat alle keizers zullen bouwen. De lichte jager is een wendbaar schip, maar kwetsbaar wanneer het op zichzelf is. In grote aantallen kunnen ze een grote bedreiging vormen voor elk imperium. Ze zijn de eersten om kleine en grote vrachtschepen te vergezellen naar vijandige planeten met geringe verdediging.',
    ],

    'heavy_fighter' => [
        'title'            => 'Zware Jager',
        'description'      => 'Deze jager is beter bepantserd en heeft een hogere aanvalskracht dan de lichte jager.',
        'description_long' => 'Bij de ontwikkeling van de zware jager bereikten onderzoekers een punt waarop conventionele aandrijvingen niet langer voldoende prestaties leverden. Om het schip optimaal te bewegen werd de impulsaandrijving voor het eerst gebruikt. Dit verhoogde de kosten, maar opende ook nieuwe mogelijkheden. Door gebruik te maken van deze aandrijving bleef er meer energie over voor wapens en schilden; bovendien werden er hoogwaardige materialen gebruikt voor deze nieuwe klasse jagers. Met deze wijzigingen vertegenwoordigt de zware jager een nieuw tijdperk in scheepstechnologie en vormt de basis voor kruisertechnologie.

Iets groter dan de lichte jager heeft de zware jager een dikkere romp, die meer bescherming biedt, en sterkere bewapening.',
    ],

    'cruiser' => [
        'title'            => 'Kruiser',
        'description'      => 'Kruisers zijn bijna driemaal zo zwaar bepantserd als zware jagers en hebben meer dan het dubbele vuurvermogen. Bovendien zijn ze zeer snel.',
        'description_long' => 'Met de ontwikkeling van de zware laser en het ionenkanon ondervonden lichte en zware jagers een alarmerend hoog aantal nederlagen dat toenam bij elke aanval. Ondanks vele modificaties, aanpassingen aan wapensterkte en pantser, kon dit niet snel genoeg worden verhoogd om deze nieuwe verdedigingsmaatregelen effectief te counteren. Daarom werd besloten een nieuwe klasse schepen te bouwen die meer pantser en meer vuurvermogen combineerde. Als resultaat van jaren onderzoek en ontwikkeling werd de kruiser geboren.

Kruisers zijn bijna driemaal zo zwaar bepantserd als de zware jagers en beschikken over meer dan het dubbele vuurvermogen van elk bestaand gevechtsschip. Ze beschikken ook over snelheden die alle ooit gemaakte ruimtevaartuigen verre overtreffen. Bijna een eeuw lang domineerden kruisers het universum. Met de ontwikkeling van gausskanonnen en plasmakanonnen eindigde hun dominantie echter. Ze worden vandaag de dag nog steeds gebruikt tegen groepen jagers, maar niet zo dominant als voorheen.',
    ],

    'battle_ship' => [
        'title'            => 'Slagschip',
        'description'      => 'Slagschepen vormen de ruggengraat van een vloot. Hun zware kanonnen, hoge snelheid en grote vrachtruimen maken hen tot tegenstanders die serieus genomen moeten worden.',
        'description_long' => 'Toen duidelijk werd dat de kruiser terrein verloor aan het toenemende aantal verdedigingsstructuren waarmee hij werd geconfronteerd, en met het verlies van schepen op missies op onaanvaardbare niveaus, werd besloten een schip te bouwen dat diezelfde soorten verdedigingsstructuren met zo weinig mogelijk verliezen kon aanpakken. Na uitgebreide ontwikkeling werd het slagschip geboren. Gebouwd om de grootste gevechten te doorstaan, beschikt het slagschip over grote vrachtruimen, zware kanonnen en een hoge hyperaandrijvingssnelheid. Eenmaal ontwikkeld bleek het uiteindelijk de ruggengraat te zijn van elke aanvallende keizers vloot.',
    ],

    'battlecruiser' => [
        'title'            => 'Slagkruiser',
        'description'      => 'De slagkruiser is sterk gespecialiseerd in het onderscheppen van vijandige vloten.',
        'description_long' => 'Dit schip is een van de meest geavanceerde gevechtsschepen die ooit zijn ontwikkeld, en is bijzonder dodelijk als het aankomt op het vernietigen van aanvallende vloten. Met zijn verbeterde laserkanonnen aan boord en geavanceerde hyperruimtemotor is de slagkruiser een serieuze kracht om mee rekening te houden bij elke aanval. Vanwege het ontwerp van het schip en zijn grote wapensysteem moesten de vrachtruimen worden verkleind, maar dit wordt gecompenseerd door het verlaagde brandstofverbruik.',
    ],

    'bomber' => [
        'title'            => 'Bommenwerper',
        'description'      => 'De bommenwerper werd speciaal ontwikkeld om de planetaire verdedigingen van een wereld te vernietigen.',
        'description_long' => 'Door de eeuwen heen, naarmate verdedigingen groter en geavanceerder werden, werden vloten in alarmerend tempo vernietigd. Er werd besloten dat een nieuw schip nodig was om verdedigingen te doorbreken voor maximale resultaten. Na jaren onderzoek en ontwikkeling werd de bommenwerper gecreëerd.

Met behulp van lasergeleide richtsystemen en plasmabommen zoekt de bommenwerper elk verdedigingsmechanisme dat hij kan vinden op en vernietigt het. Zodra de hyperruimteaandrijving is ontwikkeld tot niveau 8, wordt de bommenwerper uitgerust met de hyperruimtemotor en kan hij op hogere snelheden vliegen.',
    ],

    'destroyer' => [
        'title'            => 'Vernietiger',
        'description'      => 'De vernietiger is de koning van de oorlogsschepen.',
        'description_long' => 'De vernietiger is het resultaat van jaren werk en ontwikkeling. Met de ontwikkeling van Sterren des Doods werd besloten dat een klasse schepen nodig was om zich te verdedigen tegen zo\'n massief wapen. Dankzij zijn verbeterde zoeksensoren, multi-falanx ionenkanonnen, gausskanonnen en plasmakanonnen bleek de vernietiger een van de meest gevreesde schepen te zijn die ooit zijn gemaakt.

Omdat de vernietiger zeer groot is, is zijn wendbaarheid ernstig beperkt, waardoor het meer een gevechtsstation dan een gevechtsschip is. Het gebrek aan wendbaarheid wordt gecompenseerd door zijn enorme vuurvermogen, maar het kost ook aanzienlijke hoeveelheden deuterium om te bouwen en te gebruiken.',
    ],

    'deathstar' => [
        'title'            => 'Ster des Doods',
        'description'      => 'De destructieve kracht van de ster des doods is ongeëvenaard.',
        'description_long' => 'De ster des doods is het krachtigste schip dat ooit is gemaakt. Dit maangrote schip is het enige schip dat met het blote oog op de grond kan worden gezien. Tegen de tijd dat u het ziet, is het helaas te laat om nog iets te doen.

Bewapend met een gigantisch gravitonkanon, het meest geavanceerde wapensysteem dat ooit in het universum is gecreëerd, heeft dit massieve schip niet alleen de mogelijkheid om hele vloten en verdedigingen te vernietigen, maar ook de mogelijkheid om hele manen te vernietigen. Alleen de meest geavanceerde rijken hebben de mogelijkheid om een schip van deze enorme omvang te bouwen.',
    ],

    'reaper' => [
        'title'            => 'Maaier',
        'description'      => 'De maaier is een krachtig gevechtsschip gespecialiseerd in agressieve aanvallen en het oogsten van puinvelden.',
        'description_long' => 'De maaier vertegenwoordigt het toppunt van militaire ingenieursmeer in de Generaal-klasse. Dit zwaar bewapende vaartuig was ontworpen voor commandanten die zowel gevechtskracht als tactische flexibiliteit waarderen. Hoewel zijn primaire rol gevecht is, beschikt de maaier over versterkte vrachtruimen waarmee hij na een gevecht puinvelden kan oogsten. Zijn geavanceerde richtsystemen en zwaar pantser maken hem een geduchte tegenstander, terwijl zijn dubbeldoelontwerp betekent dat hij zowel de verwoesting kan veroorzaken als ervan kan profiteren. Het schip is uitgerust met geavanceerde wapenentechnologie en kan zijn mannetje staan tegen veel grotere vaartuigen.',
    ],

    // ---- Defense ----

    'rocket_launcher' => [
        'title'            => 'Raketlanceerder',
        'description'      => 'De raketlanceerder is een eenvoudige, kosteneffectieve verdedigingsoptie.',
        'description_long' => 'Uw eerste basislinie van verdediging. Dit zijn eenvoudige grondgebonden lanceerfaciliteiten die conventionele raketten afvuren op aanvallende vijandelijke doelen. Omdat ze goedkoop te bouwen zijn en geen onderzoek vereist is, zijn ze goed geschikt voor het verdedigen van aanvallen, maar verliezen ze effectiviteit bij de verdediging tegen grootschaligere aanvallen. Zodra u begint met de bouw van geavanceerdere defensiewapensystemen, worden raketlanceerders eenvoudig kanonnenvoer zodat uw schadelijkere wapens voor een langere periode grotere schade kunnen aanrichten.

Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'light_laser' => [
        'title'            => 'Lichte Laser',
        'description'      => 'Geconcentreerd vuren op een doel met fotonen kan aanzienlijk meer schade produceren dan standaard ballistieke wapens.',
        'description_long' => 'Naarmate de technologie zich ontwikkelde en er geavanceerdere schepen werden gemaakt, werd bepaald dat een sterkere verdedigingslinie nodig was om de aanvallen te counteren. Naarmate de lasertechnologie vorderde, werd een nieuw wapen ontworpen om het volgende verdedigingsniveau te bieden. Lichte lasers zijn eenvoudige grondgebonden wapens die speciale richtsystemen gebruiken om de vijand te volgen en een hoge intensiteit laser te vuren die is ontworpen om door de romp van het doel te snijden. Om kosteneffectief te blijven werden ze uitgerust met een verbeterd schildsysteem, maar de structurele integriteit is dezelfde als die van de raketlanceerder.

Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'heavy_laser' => [
        'title'            => 'Zware Laser',
        'description'      => 'De zware laser is de logische ontwikkeling van de lichte laser.',
        'description_long' => 'De zware laser is een praktische, verbeterde versie van de lichte laser. Meer gebalanceerd dan de lichte laser met een verbeterde legeringsamenstelling, maakt hij gebruik van sterkere, dichter verpakte bundels en nog betere boordrichtsystemen.

Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'gauss_cannon' => [
        'title'            => 'Gausskanon',
        'description'      => 'Het gausskanon vuurt projectielen van tonnen zwaar af met hoge snelheden.',
        'description_long' => 'Lange tijd werden projectielwapens beschouwd als verouderd in het licht van moderne thermonucleaire en energietechnologie en vanwege de ontwikkeling van de hyperaandrijving en verbeterd pantser. Dat was totdat de exacte energietechnologie die het eens had verouderd, het hielp zijn gevestigde positie te herwinnen.
Een gausskanon is een grote versie van de deeltjesversneller. Extreem zware projectielen worden versneld met een enorme elektromagnetische kracht en hebben mondingssnelheden die de grond rondom het projectiel in de lucht doen ontvlammen. Dit wapen is zo krachtig bij het afvuren dat het een supersonische knal veroorzaakt. Modern pantser en schilden kunnen de kracht nauwelijks weerstaan; het doel wordt vaak volledig doordrongen door de kracht van het projectiel. Verdedigingsstructuren schakelen uit zodra ze te zwaar beschadigd zijn.

Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'ion_cannon' => [
        'title'            => 'Ionenkanon',
        'description'      => 'Het ionenkanon vuurt een continue bundel versnellende ionen af die aanzienlijke schade veroorzaken aan objecten die het raakt.',
        'description_long' => 'Een ionenkanon is een wapen dat bundels ionen (positief of negatief geladen deeltjes) afvuurt. Het ionenkanon is eigenlijk een type deeltjeskanon; alleen de gebruikte deeltjes zijn geïoniseerd. Vanwege hun elektrische ladingen hebben ze ook het potentieel om elektronische apparaten uit te schakelen, en alles wat een elektrische of vergelijkbare energiebron heeft, met behulp van een fenomeen bekend als de Elektromagnetische Puls (EMP-effect). Vanwege het sterk verbeterde schildsysteem van het kanon biedt dit kanon verbeterde bescherming voor uw grotere, meer verwoestende defensiewapens.

Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'plasma_turret' => [
        'title'            => 'Plasmakanon',
        'description'      => 'Plasmakanonnen lossen de energie van een zonnevlam en overtreffen zelfs de vernietiger in destructief effect.',
        'description_long' => 'Een van de meest geavanceerde defensiewapensystemen die ooit zijn ontwikkeld, gebruikt het plasmakanon een grote nucleaire reactorbrandstofcel om een elektromagnetische versneller van stroom te voorzien die een puls, of toroïde, van plasma afvuurt. Tijdens de werking vergrendelt het plasmakanon zich eerst op een doel en begint het vuurproces. Een plasmabol wordt gecreëerd in de kern van het kanon door gassen te verhitten en te comprimeren, waarbij de ionen worden verwijderd. Zodra het gas oververhit en gecomprimeerd is en een plasmabol is gecreëerd, wordt deze geladen in de elektromagnetische versneller die wordt geladen. Eenmaal volledig geladen wordt de versneller geactiveerd, waardoor de plasmabol met een extreem hoge snelheid naar het beoogde doel wordt gelanceerd. Vanuit het perspectief van het doel is de naderende blauwachtige plasmabol indrukwekkend, maar eenmaal ingeslagen veroorzaakt het onmiddellijke vernietiging.

Verdedigingsinstallaties schakelen uit zodra ze te zwaar beschadigd zijn. Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'small_shield_dome' => [
        'title'            => 'Kleine Schildkoepel',
        'description'      => 'De kleine schildkoepel bedekt een hele planeet met een veld dat een enorme hoeveelheid energie kan absorberen.',
        'description_long' => 'Het koloniseren van nieuwe werelden bracht een nieuw gevaar met zich mee: ruimtepuin. Een grote asteroïde kon gemakkelijk de wereld en al zijn bewoners uitroeien. Vorderingen in schildtechnologie gaven wetenschappers een manier om een schild te ontwikkelen dat een hele planeet beschermt, niet alleen tegen ruimtepuin maar, zoals later bleek, ook tegen een vijandelijke aanval. Door een groot elektromagnetisch veld rondom de planeet te creëren, werd ruimtepuin dat de planeet normaal gesproken zou hebben vernietigd, afgeleid en werden aanvallen van vijandige rijken verijdeld. De eerste generatoren waren groot en het schild bood matige bescherming, maar later werd ontdekt dat kleine schilden niet voldoende bescherming boden tegen grootschaligere aanvallen. De kleine schildkoepel was het voorspel van een sterker, geavanceerder planetair schildsysteem dat nog komen zou.

Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'large_shield_dome' => [
        'title'            => 'Grote Schildkoepel',
        'description'      => 'De evolutie van de kleine schildkoepel kan aanzienlijk meer energie aanwenden om aanvallen te weerstaan.',
        'description_long' => 'De grote schildkoepel is de volgende stap in de verbetering van planetaire schilden; het is het resultaat van jaren werk om de kleine schildkoepel te verbeteren. Gebouwd om een grotere beschieting van vijandelijk vuur te weerstaan door een hoger energetisch elektromagnetisch veld te bieden, bieden grote koepels een langere beschermingsperiode voordat ze instorten.

Na een gevecht is er tot 70% kans dat mislukte verdedigingsinstallaties weer in gebruik kunnen worden genomen.',
    ],

    'anti_ballistic_missile' => [
        'title'            => 'Anti-Ballistische Raketten',
        'description'      => 'Anti-ballistische raketten vernietigen aanvallende interplanetaire raketten.',
        'description_long' => 'Anti-Ballistische Raketten (ABR) zijn uw enige verdedigingslinie wanneer u op uw planeet of maan wordt aangevallen door Interplanetaire Raketten (IPR). Wanneer een lancering van IPR\'s wordt gedetecteerd, bewapenen deze raketten zich automatisch, verwerken een lanceringscode in hun vluchtcomputers, mikken op de inkomende IPR en lanceren om te onderscheppen. Tijdens de vlucht wordt de doel-IPR voortdurend gevolgd en worden koerscorrecties toegepast totdat de ABR het doel bereikt en de aanvallende IPR vernietigt. Elke ABR vernietigt één inkomende IPR.',
    ],

    'interplanetary_missile' => [
        'title'            => 'Interplanetaire Raketten',
        'description'      => 'Interplanetaire raketten vernietigen vijandige verdedigingen.',
        'description_long' => 'Interplanetaire Raketten (IPR) zijn uw offensieve wapen om de verdedigingen van uw doelwit te vernietigen. Met behulp van geavanceerde volgingstechnologie richt elke raket zich op een bepaald aantal verdedigingen voor vernietiging. Voorzien van een antimateriebom leveren ze een zo ernstige destructieve kracht dat vernietigde schilden en verdedigingen niet kunnen worden gerepareerd. De enige manier om deze raketten te counteren is met ABR\'s.',
    ],

    // ---- Shop Booster Items ----

    'kraken' => [
        'title'       => 'KRAKEN',
        'description' => 'Verlaagt de bouwtijd van gebouwen die momenteel in aanbouw zijn met <b>:duration</b>.',
    ],

    'detroid' => [
        'title'       => 'DETROID',
        'description' => 'Verlaagt de constructietijd van huidige scheepswerf-contracten met <b>:duration</b>.',
    ],

    'newtron' => [
        'title'       => 'NEWTRON',
        'description' => 'Verlaagt de onderzoekstijd voor alle onderzoeken die momenteel bezig zijn met <b>:duration</b>.',
    ],
];

<?php

return [
    'metal_mine' => [
        'title'            => 'Miniera di Metallo',
        'description'      => 'La miniera di metallo fornisce le risorse base per un impero emergente e permette la realizzazione di costruzioni e navi.',
        'description_long' => 'Il metallo rappresenta la risorsa di base necessaria per la costruzione del tuo impero, esso consente infatti di realizzare costruzioni, navi, sistemi di difesa. Il metallo è la materia prima più economica, la sua estrazione richiede infatti un basso dispendio energetico e proprio per questo esso è molto più usato delle altre risorse. Il metallo si trova a profondità molto elevate, questo comporta la costruzione di miniere sempre più profonde e quindi un consumo maggiore di energia per farle funzionare.',
    ],

    'crystal_mine' => [
        'title'            => 'Miniera di Cristalli',
        'description'      => 'I cristalli sono la principale risorsa utilizzata per la costruzione di componenti elettronici e per la formazione di alcune leghe metalliche.',
        'description_long' => 'I cristalli sono la principale risorsa utilizzata per la costruzione di componenti elettronici e per la formazione di alcune leghe metalliche. La distruzione dei cristalli richiede una volta e mezza l`energia impiegata per la distruzione dei metalli; per questo motivo i cristalli hanno un prezzo molto più elevato. Quasi tutte le navi e le infrastrutture richiedono una certa quantità di cristalli, ma quelli adatti allo scopo sono piuttosto rari e giacciono a grandi profondità. Per questo motivo, l`ampliamento delle miniere in profondità rappresenta una fonte di accesso ad una maggiore quantità di cristalli.',
    ],

    'deuterium_synthesizer' => [
        'title'            => 'Sintetizzatore di deuterio',
        'description'      => 'Il sintetizzatore di deuterio estrae piccole quantità di deuterio dall`acqua di un pianeta.',
        'description_long' => 'Il deuterio si estrae dall`acqua pesante. Il nucleo di idrogeno contiene un neutrone in più ed è molto adatto come carburante data la grande produzione di energia che si ottiene dalla reazione deuterio-trizio (D-T). Il deuterio può essere trovato spesso negli abissi oceanici grazie al suo elevato peso molecolare e migliorare il sintetizzatore di deuterio rende possibile l`estrazione di queste risorse.',
    ],

    'solar_plant' => [
        'title'            => 'Centrale solare',
        'description'      => 'Le centrali solari convertono l`energia fotonica solare in energia elettrica da utilizzarsi per alimentare la maggior parte delle strutture.',
        'description_long' => 'Per fornire l`energia necessaria alle strutture sono richiesti impianti enormi. Una centrale solare è un modo per creare questa energia. Essa sfrutta i semiconduttori per i pannelli fotovoltaici che convertono i fotoni in corrente elettrica. Più sarà alto il livello della centrale solare più lo sarà l`area dove avviene la conversione e quindi sarà generata una maggiore quantità d`energia. Le centrali solari sono la spina dorsale delle infrastrutture planetarie.',
    ],

    'fusion_plant' => [
        'title'            => 'Centrale a Fusione',
        'description'      => 'Una centrale a fusione nucleare produce un atomo di elio partendo da due di deuterio utilizzando pressioni e temperature estremamente alte.',
        'description_long' => 'Nella centrale a fusione, i nuclei di idrogeno sono fusi in elio tramite un`elevatissima temperatura e pressione, rilasciando ingenti quantità di energia. Per ogni grammo di Deuterio consumato, possono essere prodotte fino a 41,32*10^-13 Joule di energia; con un grammo puoi produrre 172 MWh di energia.Reattori più grandi possono usare più deuterio e produrre più energia ogni ora. Miglioramenti si possono avere ricercando la tecnologia energetica.La produzione di energia della centrale a fusione è calcolata secondo questa formula:30 * [Livello Centrale a Fusione] * (1,05 + [Livello Tecnologia Energetica] * 0,01) ^  [Livello Centrale a Fusione]',
    ],

    'metal_store' => [
        'title'            => 'Deposito di metallo',
        'description'      => 'Deposito di stoccaggio per metallo grezzo.',
        'description_long' => 'Magazzini enormi per il deposito di metalli grezzi. Più grandi sono, più Metallo è possibile stoccarvi. Una volta raggiunto il limite di stoccaggio, non è più possibile estrarre altro Metallo.

Il Deposito di Metalli protegge una certa percentuale della produzione giornaliera della miniera (al massimo il 10%).',
    ],

    'crystal_store' => [
        'title'            => 'Deposito di cristalli',
        'description'      => 'Deposito di stoccaggio per cristalli non processati.',
        'description_long' => 'Il Cristallo non ancora elaborato viene temporaneamente depositato in questi enormi magazzini. Più grande è il deposito, più Cristallo è possibile stoccarvi. Una volta raggiunto il limite di stoccaggio, non è più possibile estrarre altro Cristallo.

Il Deposito di Cristalli protegge una certa percentuale della produzione giornaliera della miniera (al massimo il 10%).',
    ],

    'deuterium_store' => [
        'title'            => 'Cisterna di deuterio',
        'description'      => 'Sono cisterne che contengono il deuterio appena prodotto in attesa d`utilizzo.',
        'description_long' => 'Enormi cisterne che contengono il Deuterio appena estratto. Solitamente questi magazzini si trovano nei pressi del Cantiere Spaziale. Più grandi sono, più materiale è possibile stoccarvi. Una volta raggiunto il limite di stoccaggio, non è più possibile estrarre altro Deuterio.

La Cisterna di Deuterio protegge una certa percentuale della produzione giornaliera della miniera (al massimo il 10%).',
    ],

    // -------------------------------------------------------------------------
    // Stazioni / Strutture (da StationObjects.php)
    // -------------------------------------------------------------------------

    'robot_factory' => [
        'title'            => 'Fabbrica dei Robot',
        'description'      => 'Le fabbriche dei robots forniscono unità di costruzione economiche ed affidabili che possono essere usate per costruire o migliorare qualsiasi struttura planetaria. Ogni livello aggiuntivo aumenta l`efficienza e la quantità di unità robotiche che aiutano la costruzione.',
        'description_long' => 'Le fabbriche dei robots forniscono unità di costruzione economiche ed affidabili che possono essere usate per costruire o migliorare qualsiasi struttura planetaria. Ogni livello aggiuntivo aumenta l`efficienza e la quantità di unità robotiche che aiutano la costruzione.',
    ],

    'shipyard' => [
        'title'            => 'Cantiere Spaziale',
        'description'      => 'Il Cantiere Spaziale è il luogo dove vengono costruite le navi e le strutture difensive.',
        'description_long' => 'Il Cantiere Spaziale è il fondamento per la tua campagna intergalattica. È il luogo dove vengono costruite le navi con le quali potrai conquistare mondi lontani e le strutture difensive che proteggono i tuoi pianeti dagli attacchi nemici. Man mano che il cantiere si ingrandisce aumenteranno la sua velocità di produzione e la sua capacità permettendo di produrre sempre più navi e strutture difensive.',
    ],

    'research_lab' => [
        'title'            => 'Laboratorio di Ricerca',
        'description'      => 'Il laboratorio è indispensabile per compiere ricerche su nuove tecnologie.',
        'description_long' => 'Per poter condurre ricerche in nuove aree della tecnologia è necessario un laboratorio di ricerca. La crescita di livello del laboratorio porta non solo alla crescita della velocità delle ricerche, ma apre anche nuove frontiere di ricerca. Per condurre ricerche nel minor tempo possibile tutto il personale di ricerca dell`impero è inviato al pianeta dove la ricerca viene avviata. Non appena essa è completa, gli scienziati tornano al proprio pianeta portandosi dietro le conoscenze sulla nuova tecnologia. In questo modo la conoscenza delle nuove tecnologie viene divulgata in tutto l`impero.',
    ],

    'alliance_depot' => [
        'title'            => 'Base di appoggio',
        'description'      => 'La base d`appoggio offre la possibilità di rifornirsi alle flotte amiche in orbita che aiutano a difenderti.',
        'description_long' => 'La base d`appoggio offre la possibilità di rifornirsi alle flotte amiche in orbita che aiutano a difenderti. Ogni livello aggiuntivo di questa struttura, permette di inviare ogni ora, una certa quantità di deuterio alle flotte che sono in orbita.',
    ],

    'missile_silo' => [
        'title'            => 'Base missilistica',
        'description'      => 'La base missilistica è una struttura di lancio e di stoccaggio di missili planetari.',
        'description_long' => 'La base missilistica è una struttura di lancio e di stoccaggio di missili planetari. Hai spazio per 5 missili interplanetari o 10 antimissili per ogni livello della tua base . E` possibile utilizzare gli spazi con combinazioni diverse; 1 missile interplanetario occupa 2 spazi, gli antimissili 1.',
    ],

    'nano_factory' => [
        'title'            => 'Fabbrica dei Naniti',
        'description'      => 'La Fabbrica dei Naniti è l`ultima evoluzione della robotica. Ogni livello aggiuntivo dimezza il tempo di costruzione di edifici, navi spaziali e strutture difensive.',
        'description_long' => 'La Fabbrica dei Naniti è l`ultima evoluzione della robotica. Ogni livello aggiuntivo dimezza il tempo di costruzione di edifici, navi spaziali e strutture difensive.
I naniti sono delle unità robotiche di dimensioni medie pari a qualche nanometro, che formando un reticolo sono in grado, in pochi secondi, di trasformarsi in strumenti di lavoro precisi.
Ogni livello aggiuntivo della Fabbrica dei naniti dimezza il tempo di costruzione di edifici, navi spaziali e strutture difensive.',
    ],

    'terraformer' => [
        'title'            => 'Terraformer',
        'description'      => 'Il terraformer è richiesto per rendere edificabili aree del tuo pianeta altrimenti inaccessibili.',
        'description_long' => 'A causa del crescente sviluppo dei pianeti, lo spazio vitale delle colonie è sempre più limitato. I metodi tradizionali come la costruzione del soprassuolo e dell`edilizia sotterranea non si sono dimostrati sufficienti. Un piccolo gruppo di nanotecnologi ha trovato la soluzione: il terraformingUsando grandi quantità di energia è possibile effettuare il terraforming su vaste estensioni di territorio, arrivando a bonificare interi continenti. In questo edificio vengono costruiti dei naniti specificamente progettati, che garantiscono la qualità del suolo.
Per ogni livello di terraformer è possibile creare 5 spazi. Ogni livello del terraformer occupa uno spazio. Ogni 2 livelli di terraformer ricevi 1 spazio extra.Una volta costruito, il terraformer non può essere smantellato.',
    ],

    'space_dock' => [
        'title'            => 'Porto Spaziale',
        'description'      => 'Nel Porto Spaziale possono essere riparati i relitti delle navi.',
        'description_long' => 'Nel Porto Spaziale è possibile riparare le navi distrutte che, in seguito a un combattimento, sono diventate relitti. La riparazione richiede al massimo 12 ore, ma servono almeno 30 minuti prima di poter rimettere in servizio le navi.

Dal momento in cui si forma il relitto, si hanno 3 giorni di tempo per avviare la riparazione. Al termine della riparazione, le navi riparate devono essere rimesse in servizio. In caso contrario, le varie tipologie di nave si riattiveranno singolarmente dopo 3 giorni.
Il relitto si forma solo se sono state distrutte più di 150.000 unità.

Fluttuando nell`orbita, il Porto Spaziale non ha bisogno di Spazi pianeta.',
    ],

    'lunar_base' => [
        'title'            => 'Base Lunare',
        'description'      => 'Poiché la luna non ha atmosfera, è necessaria una base lunare per creare spazio abitabile.',
        'description_long' => 'La luna non ha atmosfera, quindi prima di poter stabilire un insediamento è necessario costruire una base lunare. Questa fornisce ossigeno, riscaldamento e gravità. Con ogni livello costruito, viene fornita una superficie abitabile e di sviluppo più ampia all\'interno della biosfera. Ogni livello costruito consente tre campi per altri edifici. Con ogni livello, la base lunare occupa da sé un campo.
Una volta costruita, la base lunare non può essere smantellata.',
    ],

    'sensor_phalanx' => [
        'title'            => 'Sensore Falanx',
        'description'      => 'Tramite il sensore falanx è possibile scoprire e osservare le flotte di altri imperi. Più grande è l\'array del sensore falanx, maggiore è il raggio di scansione.',
        'description_long' => 'Sfruttando sensori ad alta risoluzione, il Sensore Falanx analizza inizialmente lo spettro luminoso, la composizione gassosa e le emissioni di radiazioni di un mondo lontano, trasmettendo i dati a un supercomputer per l\'elaborazione. Una volta ottenute le informazioni, il supercomputer confronta le variazioni dello spettro, della composizione gassosa e delle emissioni di radiazioni con un grafico di riferimento dei cambiamenti noti dello spettro generati dai vari movimenti di navi. I dati risultanti mostrano l\'attività di qualsiasi flotta all\'interno del raggio del falanx. Per evitare il surriscaldamento del supercomputer durante il processo, viene raffreddato utilizzando 5k di deuterio raffinato.
Per utilizzare il Falanx, clicca su qualsiasi pianeta nella Vista Galattica all\'interno del raggio dei tuoi sensori.',
    ],

    'jump_gate' => [
        'title'            => 'Portale di Salto',
        'description'      => 'I portali di salto sono enormi ricetrasmettitori in grado di inviare anche la flotta più grande in un istante verso un portale di salto distante.',
        'description_long' => 'Un Portale di Salto è un sistema di giganteschi ricetrasmettitori in grado di inviare anche le flotte più grandi verso un portale ricevente in qualunque punto dell\'universo senza perdita di tempo. Utilizzando una tecnologia simile a quella di un Buco di Verme per effettuare il salto, il deuterio non è necessario. Tra un salto e l\'altro deve trascorrere un periodo di ricarica di pochi minuti per consentire la rigenerazione. Non è possibile trasportare risorse attraverso il portale. Con ogni livello di potenziamento, il tempo di ricarica del portale di salto può essere ridotto.',
    ],

    // -------------------------------------------------------------------------
    // Ricerche (da ResearchObjects.php)
    // -------------------------------------------------------------------------

    'energy_technology' => [
        'title'            => 'Tecnologia Energetica',
        'description'      => 'Comprendendo la tecnologia che sta dietro i vari tipi di energia, possono esserne adottate di nuove e più avanzate. La tecnologia energetica è di somma importanza per un laboratorio moderno.',
        'description_long' => 'La tecnologia energetica ha a che fare con la conoscenza e il raffinamento delle sorgenti di energia, dei problemi di stoccaggio e delle tecnologie che forniscono il componente fondamentale di oggi: l`energia. Più questa tecnologia è sviluppata, più saranno efficienti i tuoi sistemi. Certi livelli di avanzamento sono addirittura richiesti per poter raggiungere altre tecnologie specifiche che si basano sulla conoscenza dell`energia.',
    ],

    'laser_technology' => [
        'title'            => 'Tecnologia dei Laser',
        'description'      => 'Il fascio di luce genera un raggio in grado di danneggiare l`oggetto che ne viene colpito.',
        'description_long' => 'I laser (Light Amplification by Stimulated Emission of Radiation) producono un raggio intenso ed energico di luce coerente. Le unità laser hanno un`ampia gamma di utilizzo: dalla navigazione giroscopica ai computer ottici o sistemi d`armamento, la tecnologia laser rappresenta un tassello fondamentale per lo studio di ulteriori tecnologie delle armi.',
    ],

    'ion_technology' => [
        'title'            => 'Tecnologia ionica',
        'description'      => 'Attraverso la concentrazione di ioni si possono costruire cannoni in grado di arrecare danni seri e ridurre i costi di demolizione degli edifici per ogni livello di un 4%.',
        'description_long' => 'Gli ioni possono essere concentrati e accelerati in un fascio direttivo mortale. Questi raggi possono causare danni seri. I nostri scienziati hanno elaborato una tecnica in grado di abbassare notevolmente i costi di demolizione di edifici e complessi. Per ciascun livello di ricerca i costi di demolizione si riducono di un 4%.',
    ],

    'hyperspace_technology' => [
        'title'            => 'Tecnologia iperspaziale',
        'description'      => 'Incorporando la quarta e la quinta dimensione nelle tecnologie di propulsione si rende disponibile un nuovo sistema di propulsione, molto più efficiente e che usa meno carburante dei sistemi tradizionali. Ora, se si utilizzano la quarta e la quinta dimensione, è possibile manipolare lo spazio della stiva della propria nave.',
        'description_long' => 'Incorporando la quarta e la quinta dimensione nelle tecnologie di propulsione si rende disponibile un nuovo sistema di propulsione, molto più efficiente e che usa meno carburante dei sistemi tradizionali. Inoltre la tecnologia iperspaziale fornisce la base per i viaggi iperspaziali che sono usati dalle grandi navi da guerra (come i portali dimensionali). È una nuova e complicata tecnologia questa che richiede equipaggiamenti di laboratorio e strutture di test dispendiose. Ogni livello di questa tecnologia incrementa la capacità di carico della nave (+5% calcolato sul valore di base).',
    ],

    'plasma_technology' => [
        'title'            => 'Tecnologia del plasma',
        'description'      => 'Uno sviluppo ulteriore della tecnologia ionica che accelera il plasma ad alta energia, in grado di infliggere danni devastanti, e ottimizza la produzione di metallo, cristallo e deuterio (1%/0,66%/0,33% per ogni livello).',
        'description_long' => 'Uno sviluppo ulteriore della tecnologia ionica che non accelera gli ioni, bensì il plasma ad alta energia, in grado di infliggere danni devastanti quando colpisce un oggetto. I nostri scienziati, inoltre, hanno trovato il modo di sfruttare questa tecnologia per ottimizzare la produzione di metallo e cristallo.

Per ogni livello di sviluppo della tecnologia al plasma, la produzione di metallo aumenta di un 1%, quella di cristallo di un 0,66% e quella di deuterio di un 0,33%.',
    ],

    'combustion_drive' => [
        'title'            => 'Propulsore a combustione',
        'description'      => 'Effettuare ricerche in questo campo fornisce propulsori a combustione sempre più veloci nonostante ogni livello aggiunga solo il 10% di velocità rispetto al motore base.',
        'description_long' => 'I propulsori a combustione appartengono alla più vecchia categoria di motori esistenti e sono basati sulla repulsione. Le particelle sono accelerate e lasciano il motore generando una forza repulsiva che fa muovere la nave nella direzione opposta. L`efficienza di questi motori a combustione è bassa ma sono anche poco costosi da costruire e hanno dato prova di affidabilità. Le loro dimensioni sono piccole e non richiedono un processo troppo dispendioso per il controllo. Fare ricerca ad alti livelli garantisce motori sempre più veloci, dato che ogni livello aggiunge il 10% di velocità rispetto al livello base. Siccome questa tecnologia è tra le più importanti per un impero emergente, bisognerebbe condurre ricerche al più presto.',
    ],

    'impulse_drive' => [
        'title'            => 'Propulsore a impulso',
        'description'      => 'Il propulsore a impulso si basa sul principio di repulsione delle particelle. Lo sviluppo di questi propulsori aumenta la velocità delle proprie navi del 20% del valore di base.',
        'description_long' => 'Il propulsore a impulso si basa sul principio di repulsione delle particelle. La materia respinta è in realtà costituita dalle scorie generate dal reattore a fusione che fornisce l`energia per questo tipo di propulsione. È inoltre possibile iniettare della massa extra. La velocità di bombardieri, incrociatori, caccia pesanti e colonizzatrici aumenta del 20% del valore di base con ogni livello di sviluppo dei motori a impulso. Inoltre, non appena la ricerca raggiunge il livello 5 il cargo leggero viene munito di propulsore a impulso. Non appena il propulsore a impulso raggiunge il livello 17 tramite la ricerca, esso entra a far parte dell`equipaggiamento della riciclatrice.

All`aumentare del livello, i missili interplanetari possono volare ancora più lontano.',
    ],

    'hyperspace_drive' => [
        'title'            => 'Propulsore iperspaziale',
        'description'      => 'Il propulsore iperspaziale permette di entrare nell`iperspazio attraverso una finestra iperspaziale in modo da ridurre estremamente i tempi di viaggio. L`iperspazio è uno spazio alternativo con più di tre dimensioni.',
        'description_long' => 'La curvatura spazio-temporale nell`ambiente circostante l`astronave consente di percorrere lunghe distanze in un breve lasso di tempo. La curvatura dello spazio-tempo aumenta man mano che il propulsore iperspaziale viene sviluppato. In questo modo, la velocità delle navi dotate di questo motore (incrociatori da battaglia, navi da battaglia, corazzate e morti nere, Pathfinder e Reaper) aumenta del 30% con ogni livello. Inoltre, non appena la ricerca raggiunge il livello 8 il bombardiere viene munito di propulsore iperspaziale. Non appena il propulsore iperspaziale raggiunge il livello 15 tramite la ricerca, esso entra a far parte dell`equipaggiamento della riciclatrice.',
    ],

    'espionage_technology' => [
        'title'            => 'Tecnologia per lo spionaggio',
        'description'      => 'Studiando la tecnologia da applicare allo spionaggio potrete ottenere informazioni riguardanti gli altri pianeti.',
        'description_long' => 'La tecnologia per lo spionaggio è l`evoluzione della tecnologia dei sensori. Più la tecnica è avanzata, maggiori saranno le informazioni che l`utente avrà a disposizione relativamente ai processi che avvengono nei suoi immediati dintorni.
Nel caso delle sonde, la differenza tra il proprio livello di spionaggio e quello degli avversari è decisiva. Più la propria tecnica di spionaggio sarà avanzata, più il rapporto conterrà maggiori informazioni: di conseguenza diminuirà la possibilità che un`azione di spionaggio venga scoperta. Tanto più elevato è il numero di sonde che vengono spedite in una missione, quanto maggiori saranno i dettagli che verranno trasmessi dai pianeti obiettivo. Allo stesso tempo aumenterà però anche la possibilità di essere scoperti. 
Ad ogni modo, la tecnologia per lo spionaggio consente di migliorare la localizzazione delle flotte di terzi. A questo proposito il proprio livello di spionaggio è decisivo. A partire dal livello 2, oltre alla notifica di attacco viene mostrato anche il numero totale delle navi attaccanti. A partire dal livello 4 vengono mostrati il tipo di navi attaccanti ed il loro numero totale e dal livello 8 il numero preciso dei diversi tipi di nave.
Questa tecnica risulta irrinunciabile in caso di attacco imminente, essa consente infatti di scoprire se l`avversario dispone o meno di flotte e/o di difesa. Per questo motivo è fondamentale iniziare il prima possibile ad analizzare questa tecnica.',
    ],

    'computer_technology' => [
        'title'            => 'Tecnologia informatica',
        'description'      => 'Maggiore è il livello della tecnologia informatica, più si avranno posti nella flotta. Ogni livello aggiuntivo permette di avere un posto in più nella propria flotta.',
        'description_long' => 'La tecnologia informatica viene utilizzata per costruire unità processuali e di controllo dati  sempre più potenti. Ogni livello aumenta la potenza di calcolo e il livello di parallelismo. Più questa tecnologia è sviluppata, maggiori saranno i posti nella flotta. Più posti ha un impero, maggiore sarà l`attività che genera introiti. I posti nella flotta sono utilizzati per navi militari come per cargo o manovre di spionaggio, è una buona idea aumentare costantemente la ricerca in quest`area per fornire un`adeguata flessibilità alla flotta.',
    ],

    'astrophysics' => [
        'title'            => 'Astrofisica',
        'description'      => 'Con un modulo di ricerca astrofisica, le navi possono prendere parte a lunghe spedizioni. Ogni secondo livello di questa tecnologia permetterà la colonizzazione di un ulteriore pianeta.',
        'description_long' => 'Ulteriori scoperte nel campo dell`astrofisica permettono la costruzione di laboratori che possono essere ospitati su più navi. Questo rende possibile lunghe spedizioni nelle inesplorate aree dello spazio. Inoltre, questi avanzamenti, possono essere utilizzati per colonizzare le galassie. Per ogni due livelli di questa tecnologia, può essere colonizzato un pianeta.',
    ],

    'intergalactic_research_network' => [
        'title'            => 'Rete interplanetaria di ricerca',
        'description'      => 'Gli scienziati dei tuoi pianeti possono comunicare tra loro attraverso questa rete.',
        'description_long' => 'Gli scienziati dei tuoi pianeti possono comunicare tra loro attraverso questa rete. Per ogni livello di ricerca il tuo laboratorio con livello più alto che non sia già collegato al network, verrà aggiunto alla rete. Quando la rete sarà stabilita, i loro livelli si sommeranno.Ogni laboratorio collegato deve avere il livello necessario per la tecnologia che si ha in programma di ricercare. In tal modo si unirà alla rete.',
    ],

    'graviton_technology' => [
        'title'            => 'Tecnologia Gravitonica',
        'description'      => 'Sparando delle particelle gravitoniche concentrate si crea un campo gravitazionale artificiale la cui potenza e forza attrattiva possono non solo distruggere navi ma addirittura lune intere.',
        'description_long' => 'Un gravitone è una particella elementare che non ha massa e una carica. Esso determina la forza gravitazionale. Sparando delle particelle gravitoniche concentrate si crea un campo gravitazionale artificiale la cui potenza e forza attrattiva possono non solo distruggere navi ma addirittura lune intere. Per poter produrre la quantità necessaria di gravitoni, occorre un`immensa quantità di energia. Senza la Tecnologia gravitonica non è possibile costruire una Morte Nera distruttiva.',
    ],

    'weapon_technology' => [
        'title'            => 'Tecnologia delle armi',
        'description'      => 'Questo tipo di tecnologia aumenta l`efficacia dei tuoi armamenti dove ogni livello aggiuntivo aumenta la potenza di fuoco del 10% ad ogni tipo di arma.',
        'description_long' => 'Le tecnologie delle armi cercano di sviluppare ulteriormente le armi disponibili. Esse sono principalmente focalizzate nell`aumento della potenza e efficienza dei sistemi d`armamento. 
In questo modo aumentando il livello di questa tecnologia la stessa arma ha più potenza di fuoco e fa più danni - ogni livello aggiunge il 10% rispetto alla potenza base.
Siccome la tecnologia degli armamenti è importante per stare al passo con i nemici, è una buona idea aumentare continuamente le conoscenze in questo campo.',
    ],

    'shielding_technology' => [
        'title'            => 'Tecnologia degli scudi',
        'description'      => 'La tecnologia degli scudi è utilizzata per generare uno scudo protettivo a particelle attorno alle strutture difensive. Ogni livello migliora la schermatura del 10% rispetto al livello base di una data struttura.',
        'description_long' => 'La tecnologia degli scudi è utilizzata per generare uno scudo protettivo a particelle attorno alle strutture. Ogni livello migliora la schermatura del 10% rispetto al livello base di una data struttura. I livelli aggiuntivi aumentano la quantità di energia che uno scudo può assorbire prima di cedere. Gli scudi non sono solo usati sulle navi, ma anche per cupole protettive planetarie.',
    ],

    'armor_technology' => [
        'title'            => 'Tecnologia delle corazze',
        'description'      => 'Leghe altamente sofisticate aiutano ad aumentare la corazzatura di una nave aggiungendo il 10%, rispetto alla struttura base, di forza per livello.',
        'description_long' => 'Leghe altamente sofisticate aiutano ad aumentare la corazzatura di una nave aggiungendo il 10%, rispetto alla struttura base, di forza per livello. Per una data lega risultata efficace, può essere alterata la struttura molecolare per manipolarne il comportamento in situazioni di combattimento e per incorporare le ultime scoperte tecnologiche.',
    ],

    // ---- Navi Civili ----

    'small_cargo' => [
        'title'            => 'Cargo leggero',
        'description'      => 'I cargo leggeri sono mezzi molto agili usati per trasportare risorse da un pianeta ad un altro.',
        'description_long' => 'I cargo leggeri sono grandi, approssimativamente, come i caccia, ma hanno motori ed armamenti meno efficienti in modo da ricavare più spazio per il cargo. Pertanto, i cargo leggeri andrebbero impiegati in battaglia solo se supportati da navi forti in combattimento.Non appena la ricerca del motore a impulso raggiunge il livello 5, i cargo leggeri vengono equipaggiati con motori a impulso, pertanto la loro velocità di base aumenta.',
    ],

    'large_cargo' => [
        'title'            => 'Cargo Pesante',
        'description'      => 'I cargo pesanti sono una versione avanzata delle più piccole navi cargo, rendendo disponibile più spazio per il carico e maggiore velocità dato il sistema di propulsione migliorato.',
        'description_long' => 'Questo tipo di nave non dovrebbe mai fare missioni da sola perché non ha armamenti seri od altre tecnologie, in modo da fornire il massimo spazio per il trasporto. I cargo pesanti possono velocemente rifornire pianeti grazie anche ai suoi motori a combustione altamente sofisticati. Naturalmente esso accompagna la flotta sui pianeti attaccati per recuperare tante più risorse quanto possibile.',
    ],

    'colony_ship' => [
        'title'            => 'Colonizzatrice',
        'description'      => 'I pianeti vuoti possono essere colonizzati grazie a questa nave.',
        'description_long' => 'Nel ventesimo secolo, l`uomo ha deciso di puntare verso le stelle. Dapprima, è atterrato sulla Luna. In seguito, è stata creata una stazione spaziale. Poco dopo, Marte fu colonizzato. Ben presto si capì che la nostra crescita sarebbe dipesa dalla colonizzazione di altri mondi. Gli scienziati e gli ingegneri di tutto il mondo si riunirono per sviluppare quella che diventò il più grande traguardo mai raggiunto dall`uomo. Era nata la prima Colonizzatrice. Questa nave è utilizzata per preparare per la colonizzazione un pianeta appena scoperto. Appena arriva a destinazione, si trasforma in spazio abitativo, per assistere i coloni durante la popolazione del nuovo mondo. Il massimo numero di pianeti colonizzabili è determinato dai progressi nella ricerca dell`astrofisica. Due nuovi livelli di Astrofisica consentono la colonizzazione di un nuovo pianeta.',
    ],

    'recycler' => [
        'title'            => 'Riciclatrici',
        'description'      => 'Le navi riciclatrici sono usate per raccogliere detriti che fluttuano nello spazio e riciclare risorse utili.',
        'description_long' => 'I combattimenti nello spazio si stavano intensificando sempre più. Migliaia di navi erano già andate distrutte e i loro resti sembravano essersi dispersi per sempre nei campi di detriti. Le navi cargo standard non erano in grado di avvicinarsi abbastanza a questi campi senza correre il rischio di subire enormi danni.
Ulteriori progressi nell`ambito della tecnologia di protezione permisero di risolvere il problema in modo efficiente. Nacque, infatti, una nuova classe di navi molto simili alle navi cargo: le riciclatrici. Queste navi consentono di raccogliere e riutilizzare le risorse apparentemente perdute. Grazie ai nuovi rivestimenti protettivi, i detriti non rappresentano più alcuna minaccia per le navi.

Non appena il propulsore a impulso raggiunge il livello 17 tramite la ricerca, esso entra a far parte dell`equipaggiamento della riciclatrice. Non appena il propulsore iperspaziale raggiunge il livello 15 tramite la ricerca, esso entra a far parte dell`equipaggiamento della riciclatrice.',
    ],

    'espionage_probe' => [
        'title'            => 'Sonda spia',
        'description'      => 'Le sonde spia sono piccoli droni non pilotati dall`uomo con sistemi di propulsione eccezionalmente veloci usati per spiare mondi stranieri.',
        'description_long' => 'Le sonde spia sono piccoli droni che, con i loro sistemi di comunicazione altamente avanzati, posso inviare da grandi distanze informazioni di spionaggio in pochi secondi. Esse utilizzano le orbite dei pianeti per raccogliere informazioni e, allo stesso tempo, ridirigersi verso la terra madre. Durante la permanenza nell`orbita nemica, esse sono particolarmente facili da rilevare. Non disponendo di copertura, scudi o sistemi d`armamento, esse sono particolarmente vulnerabili alle strutture difensive.',
    ],

    'solar_satellite' => [
        'title'            => 'Satellite Solare',
        'description'      => 'I satelliti solari sono semplici satelliti in orbita equipaggiati di celle fotovoltaiche e servono a trasferire energia al pianeta. L`energia in questo modo è trasmessa a terra utilizzando un raggio laser speciale. Su questo pianeta, un satellite solare produce una quantità di energia pari a 27.',
        'description_long' => 'I satelliti solari vengono lanciati nell`orbita geostazionaria attorno ad un pianeta. Essi riuniscono in fasci l`energia solare e la indirizzano ad un sistema riflettente posizionato a terra. L`efficienza dei satelliti solari dipende dalla potenza dei raggi solari. Il rendimento energetico dei pianeti situati vicino al sole è normalmente più elevato rispetto a quello dei pianeti più lontani da esso. Grazie al vantaggioso rapporto qualità/prezzo, i satelliti solari sono utilizzati ad ampio raggio per risolvere i problemi a livello energetico. Va tuttavia tenuto in considerazione che i satelliti solari potrebbero venire distrutti durante i combattimenti.',
    ],

    'crawler' => [
        'title'            => 'Crawler',
        'description'      => 'I Crawler incrementano la produzione di Metallo, Cristallo e Deuterio sui pianeti in cui vengono utilizzati rispettivamente delle seguenti percentuali: 0,02%, 0,02% e 0,02% Inoltre, quando svolge le funzioni di Collezionista, incrementa la produzione. Il bonus totale massimo dipende dal livello totale delle miniere.',
        'description_long' => 'Il Crawler è un mezzo da lavoro di grandi dimensioni che ottimizza la produzione delle miniere e la sintetizzazione. È più agile di quanto sembri, ma non particolarmente robusto. Ogni Crawler incrementa la produzione di metallo (+0,02%), quella di cristallo (+0,02%) e quella di deuterio (+0,02%). Inoltre, quando svolge le funzioni di Collezionista, incrementa la produzione. Il bonus totale massimo dipende dal livello totale delle miniere.',
    ],

    'pathfinder' => [
        'title'            => 'Pathfinder',
        'description'      => 'I Pathfinder sono veloci, spaziosi e possono estrarre materie prime dai Campi detriti durante le spedizioni. Inoltre, la resa totale viene incrementata.',
        'description_long' => 'I Pathfinder sono veloci e spaziosi. Sono costruiti per avanzare in modo ottimale in aree sconosciute. Durante le spedizioni, individuano i Campi detriti e ne estraggono materie prime. In aggiunta, sono in grado di trovare oggetti. Inoltre, la resa totale viene incrementata.',
    ],

    // ---- Navi Militari ----

    'light_fighter' => [
        'title'            => 'Caccia Leggero',
        'description'      => 'Il caccia leggero è una nave manovrabile che si trova su quasi tutti i pianeti. I costi non sono particolarmente elevati ma allo stesso tempo la forza degli scudi e lo spazio per il trasporto sono molto limitati.',
        'description_long' => 'Data la loro corazza leggera e il semplice sistema d`armamento, i caccia leggeri appartengono alle navi di supporto in battaglia. La loro agilità e velocità insieme alla quantità di navi che attaccano, può farle apparire come un diversivo rispetto alle navi più grandi che non sono così manovrabili.',
    ],

    'heavy_fighter' => [
        'title'            => 'Caccia Pesante',
        'description'      => 'Il caccia pesante è la diretta evoluzione del caccia leggero ed offre una corazzatura maggiore ed una più grande potenza d`attacco.',
        'description_long' => 'Durante lo sviluppo del caccia leggero i ricercatori sono arrivati al punto in cui la guida convenzionale raggiunse i propri limiti. Per fornire l`agilità necessaria ai nuovi caccia, è stato usato per la prima volta un motore ad impulso. Nonostante i costi aggiuntivi e la complessità si sono rivelate nuove possibilità in parte grazie al maggior costo dei materiali generalmente utilizzati.
Tramite l`uso della tecnologia d`impulso, è stata resa disponibile più energia per le armi e gli scudi. La corazzatura migliorata e un maggior numero di armi rendono questo caccia una minaccia molto maggiore rispetto ai suoi predecessori.',
    ],

    'cruiser' => [
        'title'            => 'Incrociatore',
        'description'      => 'Gli incrociatori hanno una corazza almeno tre volte più potente dei caccia pesanti e hanno a disposizione più del doppio di potenza d fuoco. La loro velocità di crociera è allo stesso modo superiore ad ogni altra cosa vista prima.',
        'description_long' => 'Con la comparsa di laser pesanti e cannoni a ioni sui campi di battaglia, le navi da combattimento vennero sempre più sopraffatte. Nonostante le molte modifiche la potenza di fuoco e la corazzatura dello scafo non poterono essere migliorati a sufficienza per affrontare questi nuovi sistemi di difesa.
Questo è il motivo per il quale è stato scelto di sviluppare un tipo di nave completamente nuovo, fornendo più corazzatura e armi più potenti. Così nacque l`incrociatore. Gli incrociatori hanno una corazza almeno tre volte più potente dei caccia pesanti e hanno a disposizione più del doppio di potenza d fuoco. La loro velocità di crociera è allo stesso modo superiore ad ogni altra cosa vista prima. Non c`è praticamente nessuna nave migliore contro le difese planetarie leggere o medie e perciò gli incrociatori sono stati ampiamente adottati in tutto l`universo da almeno un centinaio di anni.
Sfortunatamente con la creazione dei nuovi e potenti sistemi difensivi come il cannone di Gauss o gli emettitori di plasma, il regno degli incrociatori finì presto. Al giorno d`oggi essi vengono ancora usati per combattere contro battaglioni di caccia dato l`efficace sistema d`armamento contro questi ultimi',
    ],

    'battle_ship' => [
        'title'            => 'Nave da battaglia',
        'description'      => 'Le navi da battaglia sono la spina dorsale di ogni flotta militare. Corazzatura rinforzata, armamenti pesanti e alte velocità di spostamento, fanno di questa nave, insieme al grande spazio per il cargo, un nemico difficile da battere.',
        'description_long' => 'Le navi da battaglia sono la spina dorsale di ogni flotta militare. La loro corazzatura rinforzata, abbinata a degli armamenti pesanti, e una velocità di crociera elevata, rendono questa nave indispensabile per ogni impero. Inoltre possiede un`ampia stiva che è ottima in situazioni ostili.',
    ],

    'battlecruiser' => [
        'title'            => 'Incrociatore da Battaglia',
        'description'      => 'L`Incrociatore da battaglia è altamente specializzato nell`intercettare flotte nemiche.',
        'description_long' => 'Questa nave, tecnologicamente avanzata, è mortale quando viene usata per distruggere le flotte d`attacco. Con i suoi cannoni laser migliorati e un avanzato motore Iperspaziale, detiene una posizione privilegiata tra le navi pesanti, che è deputata a distruggere. A causa del suo design piccolo e del suo enorme armamento, la capacità di carico è minima; ma questo è compensato dal basso consumo di carburante.',
    ],

    'bomber' => [
        'title'            => 'Bombardiere',
        'description'      => 'Il bombardiere è una nave stellare speciale sviluppata per sfondare pesanti difese planetarie.',
        'description_long' => 'Il bombardiere è una nave stellare speciale sviluppata per sfondare pesanti difese planetarie. Grazie ad un sistema di puntamento con guida laser, possono essere sganciate bombe al plasma con precisione sul bersaglio, causando enorme distruzione tra i sistemi di difesa planetari.La velocità base dei bombardieri è aumentata non appena viene ricercato il motore iperspaziale di livello 8 in quanto essi vengono equipaggiati con motori iperspaziali.',
    ],

    'destroyer' => [
        'title'            => 'Corazzata',
        'description'      => 'La corazzata è la nave stellare più pesante mai vista e ha una potenza di fuoco mai eguagliata in precedenza.',
        'description_long' => 'Con la corazzata, la madre di tutte le navi da guerra entra nell`arena. Il suo sistema multi-falange d`armamento consiste di cannoni ionici, al plasma e di Gauss montati su torrette veloci nella risposta che permettono loro di eliminare i caccia operativi con un margine di successo del 99%. La dimensione della nave è d`altro canto il suo svantaggio dal momento che la manovrabilità è limitata, facendo della corazzata più una stazione da combattimento che una nave da guerra. Il consumo di carburante di queste immense corazzate è però tanto alto quanto lo è il loro potere di fuoco...',
    ],

    'deathstar' => [
        'title'            => 'Morte Nera',
        'description'      => 'Non c`è nulla di così grande e pericoloso come una morte nera che si avvicina.',
        'description_long' => 'La morte nera è equipaggiata con un singolo gigantesco Cannone di Gauss in grado di distruggere praticamente qualsiasi cosa con un singolo colpo, sia che siano corazzate o lune. In modo da produrre l`energia necessaria per quest`arma, enormi parti all`interno della Morte Nera sono utilizzate come generatori di potenza. La dimensione della Morte Nera inoltre ne limita la velocità negli spostamenti, che è molto bassa. Si dice che spesso il capitano aiuti a spingerla per aumentarne la velocità. Solo imperi immensi e avanzati hanno la manodopera e le conoscenze estese richieste per poter costruire una tale nave stellare della dimensione di una luna.',
    ],

    'reaper' => [
        'title'            => 'Reaper',
        'description'      => 'Le navi della classe Reaper sono un potente strumento di distruzione in grado di saccheggiare i Campi detriti subito dopo le battaglie.',
        'description_long' => 'La Reaper è la regina delle navi da guerra: essa combina potenza, robusti scudi, velocità e capacità. Inoltre, è l`unica in grado di sfruttare istantaneamente parte del Campo detriti che si crea dopo la battaglia. Tale funzione non è attiva dopo le battaglie contro alieni e pirati.',
    ],

    // ---- Difese ----

    'rocket_launcher' => [
        'title'            => 'Lanciamissili',
        'description'      => 'Il lanciamissili è un semplice ma indispensabile sistema di difesa.',
        'description_long' => 'Il lanciamissili è un semplice ma indispensabile sistema di difesa. Diventa abbastanza funzionale se in grandi quantità e può essere costruito senza specifiche ricerche perché è una semplice arma balistica. Il basso costo di produzione lo rende adeguato contro piccole flotte, ma diventa sempre meno efficace con l`incremento dei sistemi di difesa. In successivi sviluppi risulta essere utilizzato solo come specchietto per le allodole nei combattimenti. In generale, i sistemi di difesa si disattivano da soli quando raggiungono parametri operazionali critici in modo da lasciare la possibilità di riparazione. In media, il 70% delle difese planetarie distrutte può essere riportato in funzione dopo un combattimento.',
    ],

    'light_laser' => [
        'title'            => 'Laser leggero',
        'description'      => 'Con l`utilizzo di un raggio laser concentrato si possono causare più danni che attraverso normali armi missilistiche.',
        'description_long' => 'Per tenere il passo con la velocità di sviluppo sempre in aumento in termini di tecnologie aerospaziali, gli scienziati dovettero sviluppare un nuovo tipo di sistema di difesa in grado di gestire navi e flotte sempre più forti e meglio equipaggiate. In breve nacque l`unità laser piccola, che era in grado di sparare sul bersaglio un raggio laser altamente concentrato e causare molto più danno dell`impatto di missili balistici. Dall`altro lato, fu migliorata la schermatura dei cannoni per gestire le maggiori potenze di fuoco delle moderne navi. Dal momento che il basso costo dell`unità era un obiettivo essenziale designato, la struttura di base non è migliorata rispetto al lancia-missili. Poiché il laser leggero offre un vantaggioso rapporto qualità-prezzo, è il più conosciuto sistema di difesa essendo usato contemporaneamente da piccoli imperi in crescita e da grandi imperi multigalattici.',
    ],

    'heavy_laser' => [
        'title'            => 'Laser pesante',
        'description'      => 'I laser pesanti hanno una potenza di emissione e un`integrità strutturale superiore ai laser leggeri.',
        'description_long' => 'Il laser pesante è la diretta evoluzione del sistema laser leggero, ha infatti una migliorata integrità strutturale e vi sono adottati nuovi materiali. In questo modo è stato possibile migliorare la struttura di copertura e grazie all`installazione di nuovi sistemi computerizzati ed energetici, molta più energia, rispetto a quando si usavano le unità laser leggere, è rilasciata contro il bersaglio.',
    ],

    'gauss_cannon' => [
        'title'            => 'Cannone Gauss',
        'description'      => 'Usando un`enorme accelerazione elettromagnetica, il cannone gauss accelera pesanti proiettili.',
        'description_long' => 'Le armi a proiettili erano considerate obsolete date la moderna tecnologia di fusione nucleare, le nuove fonti di energia, la scoperta della tecnologia iperspaziale e la ulteriormente migliorata tecnologia delle leghe. Però è la stessa tecnologia energetica che una volta ne prese il posto, che ora le richiama indietro in modo da potersi muovere verso il nuovo secolo: il principio di base è da lungo conosciuto e si data a cavallo del 20esimo e 21esimo secolo: l`acceleratore di particelle. Un cannone di Gauss in realtà non è altro che un acceleratore di particelle di enorme misura dove i proiettili di parecchie tonnellate di peso vengono accelerati utilizzando immense spirali elettromagnetiche. La velocità di uscita di questi grandi proiettili è così elevata da bruciare la polvere nell`aria circostante e il rinculo del colpo scuote il terreno. Anche le tecnologie di schermatura e di leghe protettive più innovate con difficoltà resistono all`impatto di tali proiettili - capita la maggior parte delle volte, che il proiettile semplicemente passi attraverso la struttura bersaglio.',
    ],

    'ion_cannon' => [
        'title'            => 'Cannone ionico',
        'description'      => 'I cannoni ionici sparano raggi ionici altamente energetici verso i propri obiettivi destabilizzando gli scudi e distruggendo l`elettronica.',
        'description_long' => 'Nel 21esimo secolo esisteva una tecnologia chiamata EMP, che sta per impulso elettromagnetico. Un tale impulso energetico si dimostra principalmente pericoloso per i sistemi che utilizzano elettricità o sono sensibili ad essa. In quei tempi, tali armi erano trasportate in bombe o razzi missilistici, ma con il continuo sviluppo nell`area degli EMP, è oggi possibile montare tali unità in semplici cannoni. Il cannone ionico è fino a questo punto il miglior equipaggiamento per queste armi. Il raggio ionico mirato distrugge sul bersaglio ogni sistema elettrico non schermato e destabilizza il sistema di scudi della nave stellare. In combinazione questo spesso significa la distruzione totale nonostante il fatto che gli esseri viventi non vengano colpiti direttamente. L`unica nave che si sappia usi i cannoni ionici è l`incrociatore da battaglia, a causa delle alte richieste di energia da parte di questi cannoni e il fatto che il combattimento spesso richieda la distruzione del bersaglio non il paralizzarlo.',
    ],

    'plasma_turret' => [
        'title'            => 'Cannone al Plasma',
        'description'      => 'I cannoni al plasma liberano la potenza di una piccola eruzione solare nella forma di un proiettile di plasma. L`energia distruttiva è addirittura superiore a quella della corazzata.',
        'description_long' => 'La tecnologia laser è stata portata quasi alla perfezione, la tecnologia ionica sembrava aver raggiunto il massimo e in generale non vi era visione di come sarebbe stato possibile ottenere un miglioramento degli esistenti sistemi d`armamento. Ma questo cambierà una volta che sarà nata l`idea di mettere assieme le due tecnologie. Mentre il laser è utilizzato per scaldare le particelle di deuterio a milioni di gradi, la tecnologia ionica carica elettricamente queste particelle e le conoscenze in elettromagnetismo permettono di contenere questo pericoloso plasma.  La scarica blu del plasma è molto bella quando è in movimento verso il suo obiettivo, ma dal punto di vista dell`equipaggio di una nave stellare, questa sfera di plasma apparentemente amichevole significa morte e distruzione. Gli armamenti al plasma sono identificati come le più pericolose minacce, ma hanno il loro costo.',
    ],

    'small_shield_dome' => [
        'title'            => 'Cupola scudo piccola',
        'description'      => 'La Cupola scudo piccola copre il pianeta con un sottile campo protettivo e di schermatura, che può assorbire enormi quantità di potenza di fuoco.',
        'description_long' => 'Molto prima che i generatori di scudi diventassero integrati e portatili, c`erano vecchi grandi generatori sulla superficie dei pianeti. Quelli erano in grado di disporre una enorme cupola difensiva attorno alla superficie dell`intero pianeta capace di assorbire grandi quantità di energia qualora subisse un attacco. Ogni tanto capita che un piccolo convoglio da combattimento venga abbattuto da queste cupole-scudo. Usando tecnologie di schermatura sempre più avanzate, queste cupole possono essere migliorate ulteriormente, così che la loro abilità di assorbire energia sia ancora più grande. Chiaramente solo una di ciascuna cupola-scudo può essere costruita su un pianeta.',
    ],

    'large_shield_dome' => [
        'title'            => 'Cupola scudo potenziata',
        'description'      => 'La cupola scudo potenziata è un` evoluta tecnologia di schermatura che assorbe ancor più energia prima di collassare.',
        'description_long' => 'Questa è una versione avanzata della cupola scudo e la sua principale caratteristica è la maggiore capacità di assorbimento d`energia. Si basa sulla stessa conoscenza tecnologica delle cupole più piccole. I generatori sono inoltre meno rumorosi quando in azione.',
    ],

    'anti_ballistic_missile' => [
        'title'            => 'Missili anti balistici',
        'description'      => 'I missili anti balistici distruggono i missili interplanetari.',
        'description_long' => 'I missili anti balistici distruggono i missili attaccanti. Ogni missile anti balistico distrugge un missile interplanetario.',
    ],

    'interplanetary_missile' => [
        'title'            => 'Missili Interplanetari',
        'description'      => 'I missili interplanetari distruggono le difese nemiche. I tuoi missili interplanetari hanno un raggio di azione pari a 0 sistemi.',
        'description_long' => 'I missili interplanetari distruggono le difese nemiche. Le difese distrutte dai missili interplanetari non verranno ricostruite.',
    ],

    // ---- Oggetti Potenziamento Shop ----

    'kraken' => [
        'title'       => 'KRAKEN',
        'description' => 'Riduce il tempo di costruzione degli edifici attualmente in costruzione di <b>:duration</b>.',
    ],

    'detroid' => [
        'title'       => 'DETROID',
        'description' => 'Riduce il tempo di costruzione dei contratti del Cantiere Spaziale attualmente in corso di <b>:duration</b>.',
    ],

    'newtron' => [
        'title'       => 'NEWTRON',
        'description' => 'Riduce il tempo di ricerca per tutte le ricerche attualmente in corso di <b>:duration</b>.',
    ],
];

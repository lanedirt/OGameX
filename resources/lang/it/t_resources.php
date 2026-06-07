<?php

return [
    'metal_mine' => [
        'title'            => 'Miniera di Metallo',
        'description'      => 'Utilizzate nell\'estrazione del minerale di metallo, le miniere di metallo sono di primaria importanza per tutti gli imperi emergenti e consolidati.',
        'description_long' => 'Il metallo è la risorsa primaria utilizzata nella fondazione del tuo Impero. A profondità maggiori, le miniere possono produrre una quantità maggiore di metallo utilizzabile per la costruzione di edifici, navi, sistemi difensivi e ricerche. Man mano che le miniere scavano più in profondità, è necessaria più energia per la massima produzione. Poiché il metallo è la risorsa più abbondante tra quelle disponibili, il suo valore è considerato il più basso di tutte le risorse nel commercio.',
    ],

    'crystal_mine' => [
        'title'            => 'Miniera di Cristallo',
        'description'      => 'I cristalli sono la principale risorsa utilizzata per costruire circuiti elettronici e formare determinati composti in lega.',
        'description_long' => 'Le miniere di cristallo forniscono la risorsa principale per produrre circuiti elettronici e determinati composti in lega. L\'estrazione del cristallo consuma circa una volta e mezza più energia dell\'estrazione del metallo, rendendolo più prezioso. Quasi tutte le navi e tutti gli edifici richiedono cristallo. La maggior parte dei cristalli necessari per costruire astronavi è però molto rara e, come il metallo, può essere trovata solo a una certa profondità. Pertanto, costruire miniere negli strati più profondi aumenterà la quantità di cristallo prodotta.',
    ],

    'deuterium_synthesizer' => [
        'title'            => 'Sintetizzatore di Deuterio',
        'description'      => 'I sintetizzatori di deuterio estraggono il contenuto residuo di deuterio dall\'acqua presente su un pianeta.',
        'description_long' => 'Il deuterio è detto anche idrogeno pesante. È un isotopo stabile dell\'idrogeno con un\'abbondanza naturale negli oceani delle colonie di circa un atomo su 6500 di idrogeno (~154 PPM). Il deuterio rappresenta quindi circa lo 0,015% (in peso, 0,030%) del totale. Viene elaborato da speciali sintetizzatori in grado di separare l\'acqua dal deuterio tramite centrifughe appositamente progettate. Il potenziamento del sintetizzatore consente di aumentare la quantità di depositi di deuterio elaborati. Il deuterio viene utilizzato per eseguire scansioni con il sensore falanx, visualizzare la galassia, come carburante per le navi e per eseguire speciali aggiornamenti di ricerca.',
    ],

    'solar_plant' => [
        'title'            => 'Centrale Solare',
        'description'      => 'Le centrali solari assorbono energia dalla radiazione solare. Tutte le miniere necessitano di energia per funzionare.',
        'description_long' => 'Enormi pannelli solari vengono utilizzati per generare energia per le miniere e il sintetizzatore di deuterio. Man mano che la centrale solare viene potenziata, la superficie delle celle fotovoltaiche che ricoprono il pianeta aumenta, determinando una maggiore produzione di energia nelle reti elettriche del tuo pianeta.',
    ],

    'fusion_plant' => [
        'title'            => 'Reattore a Fusione',
        'description'      => 'Il reattore a fusione utilizza deuterio per produrre energia.',
        'description_long' => 'Nelle centrali a fusione, i nuclei di idrogeno si fondono in nuclei di elio a temperature e pressioni enormi, rilasciando quantità straordinarie di energia. Per ogni grammo di deuterio consumato è possibile produrre fino a 41,32×10⁻¹³ Joule di energia; con 1 g è possibile produrre 172 MWh di energia.

Complessi di reattori più grandi utilizzano più deuterio e possono produrre più energia all\'ora. L\'effetto energetico può essere aumentato ricercando la tecnologia energetica.

La produzione di energia del reattore a fusione viene calcolata come segue:
30 × [Livello Reattore a Fusione] × (1,05 + [Livello Tecnologia Energetica] × 0,01) ^ [Livello Reattore a Fusione]',
    ],

    'metal_store' => [
        'title'            => 'Deposito di Metallo',
        'description'      => 'Fornisce spazio di stoccaggio per il metallo in eccesso.',
        'description_long' => 'Questa enorme struttura di stoccaggio viene utilizzata per immagazzinare il minerale di metallo. Ogni livello di potenziamento aumenta la quantità di minerale che può essere immagazzinata. Se i depositi sono pieni, non verrà estratto altro metallo.

Il Deposito di Metallo protegge una certa percentuale della produzione giornaliera della miniera (massimo 10 percento).',
    ],

    'crystal_store' => [
        'title'            => 'Deposito di Cristallo',
        'description'      => 'Fornisce spazio di stoccaggio per il cristallo in eccesso.',
        'description_long' => 'Il cristallo non elaborato viene conservato nel frattempo in questi enormi magazzini. Con ogni livello di potenziamento aumenta la quantità di cristallo che può essere immagazzinata. Se i depositi di cristallo sono pieni, non verrà estratto altro cristallo.

Il Deposito di Cristallo protegge una certa percentuale della produzione giornaliera della miniera (massimo 10 percento).',
    ],

    'deuterium_store' => [
        'title'            => 'Cisterna di Deuterio',
        'description'      => 'Enormi cisterne per immagazzinare il deuterio appena estratto.',
        'description_long' => 'La cisterna di deuterio serve per immagazzinare il deuterio appena sintetizzato. Una volta elaborato dal sintetizzatore, viene convogliato in questa cisterna per un uso successivo. Con ogni potenziamento della cisterna, la capacità di stoccaggio totale aumenta. Una volta raggiunta la capacità massima, non verrà sintetizzato altro deuterio.

La Cisterna di Deuterio protegge una certa percentuale della produzione giornaliera del sintetizzatore (massimo 10 percento).',
    ],

    // -------------------------------------------------------------------------
    // Stazioni / Strutture (da StationObjects.php)
    // -------------------------------------------------------------------------

    'robot_factory' => [
        'title'            => 'Fabbrica di Robot',
        'description'      => 'Le fabbriche di robot forniscono robot da costruzione per assistere nell\'edificazione degli edifici. Ogni livello aumenta la velocità di potenziamento degli edifici.',
        'description_long' => 'L\'obiettivo principale della fabbrica di robot è la produzione di robot da costruzione all\'avanguardia. Ogni potenziamento della fabbrica porta alla produzione di robot più veloci, che riducono i tempi necessari per costruire gli edifici.',
    ],

    'shipyard' => [
        'title'            => 'Cantiere Spaziale',
        'description'      => 'Tutti i tipi di navi e strutture difensive vengono costruiti nel Cantiere Spaziale planetario.',
        'description_long' => 'Il Cantiere Spaziale planetario è responsabile della costruzione di astronavi e meccanismi difensivi. Man mano che il Cantiere Spaziale viene potenziato, può produrre una varietà più ampia di veicoli a una velocità molto maggiore. Se sul pianeta è presente una fabbrica di naniti, la velocità di costruzione delle navi aumenta enormemente.',
    ],

    'research_lab' => [
        'title'            => 'Laboratorio di Ricerca',
        'description'      => 'Un laboratorio di ricerca è necessario per condurre ricerche su nuove tecnologie.',
        'description_long' => 'Parte essenziale di qualsiasi impero, i Laboratori di Ricerca sono il luogo in cui vengono scoperte nuove tecnologie e quelle esistenti vengono migliorate. Con ogni livello costruito, la velocità con cui vengono ricercate nuove tecnologie aumenta, sbloccando al contempo tecnologie sempre più avanzate. Per condurre le ricerche nel più breve tempo possibile, gli scienziati vengono immediatamente inviati nella colonia per iniziare i lavori. In questo modo la conoscenza delle nuove tecnologie può essere facilmente diffusa in tutto l\'impero.',
    ],

    'alliance_depot' => [
        'title'            => 'Deposito dell\'Alleanza',
        'description'      => 'Il deposito dell\'alleanza rifornisce di carburante le flotte amiche in orbita che contribuiscono alla difesa.',
        'description_long' => 'Il deposito dell\'alleanza rifornisce di carburante le flotte amiche in orbita che contribuiscono alla difesa. Per ogni livello di potenziamento del deposito, una specifica quantità di deuterio all\'ora può essere inviata a una flotta in orbita.',
    ],

    'missile_silo' => [
        'title'            => 'Silo per Missili',
        'description'      => 'I silos per missili vengono utilizzati per immagazzinare i missili.',
        'description_long' => 'I silos per missili vengono utilizzati per costruire, immagazzinare e lanciare missili interplanetari e missili anti-balistici. Con ogni livello del silo possono essere immagazzinati cinque missili interplanetari o dieci missili anti-balistici. Un missile interplanetario occupa lo stesso spazio di due missili anti-balistici. È consentito lo stoccaggio contemporaneo di missili interplanetari e anti-balistici nello stesso silo.',
    ],

    'nano_factory' => [
        'title'            => 'Fabbrica di Naniti',
        'description'      => 'Questa rappresenta il massimo della tecnologia robotica. Ogni livello riduce i tempi di costruzione di edifici, navi e difese.',
        'description_long' => 'Un nanorobot, detto anche nanite, è un dispositivo meccanico o elettromeccanico le cui dimensioni sono misurate in nanometri (milionesimi di millimetro, ovvero unità di 10^-9 metro). La ridotta dimensione dei nanomachinari si traduce in una maggiore velocità operativa. Questa fabbrica produce naniti che rappresentano la massima evoluzione della tecnologia robotica. Una volta costruita, ogni potenziamento riduce significativamente i tempi di produzione di edifici, navi e strutture difensive.',
    ],

    'terraformer' => [
        'title'            => 'Terraformer',
        'description'      => 'Il terraformer aumenta la superficie utilizzabile dei pianeti.',
        'description_long' => 'Con il crescente sviluppo sui pianeti, anche lo spazio abitabile per le colonie diventa sempre più limitato. I metodi tradizionali come costruzioni in altezza e sotterranee si rivelano progressivamente insufficienti. Un piccolo gruppo di fisici ad alta energia e nano-ingegneri è giunto alla soluzione: la terraformazione.
Sfruttando enormi quantità di energia, il terraformer è in grado di rendere coltivabili interi tratti di terra o persino interi continenti. Questo edificio ospita la produzione di naniti creati specificamente a questo scopo, che garantiscono una qualità del suolo uniforme ovunque.

Ogni livello del terraformer consente di coltivare 5 campi aggiuntivi. Con ogni livello, il terraformer occupa da sé un campo. Ogni 2 livelli di terraformer si riceve 1 campo bonus.

Una volta costruito, il terraformer non può essere smantellato.',
    ],

    'space_dock' => [
        'title'            => 'Bacino di Carenaggio',
        'description'      => 'I relitti possono essere riparati nel Bacino di Carenaggio.',
        'description_long' => 'Il Bacino di Carenaggio offre la possibilità di riparare le navi distrutte in battaglia che hanno lasciato dei relitti. Il tempo di riparazione è di massimo 12 ore, ma occorrono almeno 30 minuti prima che le navi possano essere rimesse in servizio.

Le riparazioni devono iniziare entro 3 giorni dalla creazione del relitto. Le navi riparate devono essere rimesse in servizio manualmente al termine delle riparazioni. In caso contrario, le singole navi vengono automaticamente rimesse in servizio dopo 3 giorni.

I relitti appaiono solo se sono stati distrutti più di 150.000 unità, incluse le proprie navi che hanno partecipato al combattimento con un valore di almeno il 5% dei punti nave.

Poiché il Bacino di Carenaggio orbita nello spazio, non richiede un campo planetario.',
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
        'description'      => 'Il controllo di diversi tipi di energia è necessario per molte nuove tecnologie.',
        'description_long' => 'Con il progredire di vari campi di ricerca, si scoprì che la tecnologia attuale di distribuzione dell\'energia non era sufficientemente avanzata per avviare alcune ricerche specializzate. Con ogni potenziamento della Tecnologia Energetica, nuove ricerche possono essere condotte, sbloccando lo sviluppo di navi e difese più sofisticate.',
    ],

    'laser_technology' => [
        'title'            => 'Tecnologia Laser',
        'description'      => 'Concentrare la luce produce un raggio che causa danni quando colpisce un oggetto.',
        'description_long' => 'I laser (amplificazione della luce tramite emissione stimolata di radiazioni) producono un\'emissione intensa e ricca di energia di luce coerente. Questi dispositivi possono essere utilizzati in molti settori, dai computer ottici alle pesanti armi laser che tagliano effortlessly le armature. La tecnologia laser fornisce una base importante per la ricerca di altre tecnologie belliche.',
    ],

    'ion_technology' => [
        'title'            => 'Tecnologia Ionica',
        'description'      => 'La concentrazione di ioni consente la costruzione di cannoni in grado di infliggere danni enormi e ridurre i costi di demolizione per livello del 4%.',
        'description_long' => 'Gli ioni possono essere concentrati e accelerati in un fascio letale. Questi fasci possono infliggere danni enormi. I nostri scienziati hanno anche sviluppato una tecnica che riduce significativamente i costi di demolizione per edifici e sistemi. Per ogni livello di ricerca, i costi di demolizione si riducono del 4%.',
    ],

    'hyperspace_technology' => [
        'title'            => 'Tecnologia Iperspaziale',
        'description'      => 'Integrando la 4ª e la 5ª dimensione è ora possibile ricercare un nuovo tipo di propulsore più economico ed efficiente.',
        'description_long' => 'In teoria, l\'idea del viaggio iperspaziale si basa sull\'esistenza di una dimensione separata e adiacente. Quando attivato, un motore iperspaziale sposta l\'astronave in questa altra dimensione, dove può coprire enormi distanze in un tempo notevolmente ridotto rispetto al normale spazio. Una volta raggiunto il punto nell\'iperspazio corrispondente alla destinazione nello spazio reale, l\'astronave riaffiora.
Una volta raggiunto un livello sufficiente di Tecnologia Iperspaziale, il Motore Iperspaziale non è più solo una teoria. Ogni miglioramento di questo motore aumenta la capacità di carico delle navi equipaggiate del 5% del valore base.',
    ],

    'plasma_technology' => [
        'title'            => 'Tecnologia al Plasma',
        'description'      => 'Un ulteriore sviluppo della tecnologia ionica che accelera plasma ad alta energia, il quale infligge danni devastanti e ottimizza la produzione di metallo, cristallo e deuterio (1%/0,66%/0,33% per livello).',
        'description_long' => 'Un ulteriore sviluppo della tecnologia ionica che non accelera ioni ma plasma ad alta energia, capace di infliggere danni devastanti all\'impatto. I nostri scienziati hanno anche trovato un modo per migliorare sensibilmente l\'estrazione di metallo e cristallo grazie a questa tecnologia.

La produzione di metallo aumenta dell\'1%, quella di cristallo dello 0,66% e quella di deuterio dello 0,33% per ogni livello della tecnologia al plasma.',
    ],

    'combustion_drive' => [
        'title'            => 'Motore a Combustione',
        'description'      => 'Lo sviluppo di questo motore rende alcune navi più veloci; ogni livello aumenta la velocità solo del 10% del valore base.',
        'description_long' => 'Il Motore a Combustione è la tecnologia più antica, ma è ancora in uso. Con il Motore a Combustione, lo scarico è formato da propellenti trasportati dalla nave prima dell\'uso. In una camera chiusa, le pressioni sono uguali in ogni direzione e non si verifica accelerazione. Se viene fornita un\'apertura nella parte inferiore della camera, la pressione non viene più contrastata da quel lato. La pressione residua fornisce una spinta nella direzione opposta all\'apertura, che spinge la nave in avanti espellendo lo scarico verso il basso a velocità estremamente elevata.

Con ogni livello del Motore a Combustione sviluppato, la velocità di piccoli e grandi trasportatori, caccia leggeri, riciclatori e sonde di spionaggio aumenta del 10%.',
    ],

    'impulse_drive' => [
        'title'            => 'Motore ad Impulso',
        'description'      => 'Il motore ad impulso è basato sul principio di reazione. Il suo ulteriore sviluppo rende alcune navi più veloci; ogni livello aumenta la velocità solo del 20% del valore base.',
        'description_long' => 'Il motore ad impulso è basato sul principio di rinculo, per cui l\'emissione stimolata di radiazioni è prodotta principalmente come sottoprodotto della fusione nucleare del nucleo per ottenere energia. Possono essere iniettate anche altre masse. Con ogni livello del Motore ad Impulso sviluppato, la velocità di bombardieri, incrociatori, caccia pesanti e navi coloniali aumenta del 20% del valore base. Inoltre, i piccoli trasportatori vengono equipaggiati con motori ad impulso non appena la ricerca raggiunge il livello 5. Non appena la ricerca del Motore ad Impulso raggiunge il livello 17, i Riciclatori vengono riadattati con Motori ad Impulso.

Anche i missili interplanetari raggiungono distanze maggiori con ogni livello.',
    ],

    'hyperspace_drive' => [
        'title'            => 'Motore Iperspaziale',
        'description'      => 'Il motore iperspaziale deforma lo spazio attorno a una nave. Il suo sviluppo rende alcune navi più veloci; ogni livello aumenta la velocità solo del 30% del valore base.',
        'description_long' => 'Nelle immediate vicinanze della nave, lo spazio viene deformato in modo che grandi distanze possano essere percorse molto rapidamente. Più il Motore Iperspaziale viene sviluppato, più forte è la deformazione dello spazio, aumentando così la velocità delle navi equipaggiate (Incrociatori da Battaglia, Navi da Battaglia, Distruttrici, Stelle della Morte, Cercatori e Mietitori) del 30% per livello. Inoltre, il bombardiere viene costruito con un Motore Iperspaziale non appena la ricerca raggiunge il livello 8. Non appena la ricerca del Motore Iperspaziale raggiunge il livello 15, il Riciclatore viene riadattato con un Motore Iperspaziale.',
    ],

    'espionage_technology' => [
        'title'            => 'Tecnologia di Spionaggio',
        'description'      => 'Tramite questa tecnologia è possibile ottenere informazioni su altri pianeti e lune.',
        'description_long' => 'La Tecnologia di Spionaggio è, in primo luogo, un avanzamento della tecnologia sensoriale. Più questa tecnologia è avanzata, più informazioni l\'utente riceve sulle attività nel suo ambiente.
Le differenze tra il proprio livello di spionaggio e quelli avversari sono cruciali per le sonde. Più avanzata è la propria tecnologia di spionaggio, più informazioni il rapporto può raccogliere e minore è la probabilità che le attività di spionaggio vengano scoperte. Più sonde si inviano in una singola missione, più dettagli possono raccogliere dal pianeta bersaglio, ma allo stesso tempo aumenta anche la probabilità di essere scoperti.
La tecnologia di spionaggio migliora anche le possibilità di localizzare flotte straniere. Il livello di spionaggio è vitale per determinarlo. Dal livello 2 in poi, il numero totale esatto delle navi d\'attacco viene mostrato insieme alla normale notifica di attacco. Dal livello 4 in poi vengono mostrati il tipo di navi d\'attacco e il numero totale, mentre dal livello 8 in poi viene mostrato il numero esatto dei diversi tipi di nave.
Questa tecnologia è indispensabile per un attacco imminente, poiché informa se la flotta della vittima dispone di difese. Pertanto dovrebbe essere ricercata molto presto.',
    ],

    'computer_technology' => [
        'title'            => 'Tecnologia Informatica',
        'description'      => 'È possibile comandare più flotte aumentando le capacità informatiche. Ogni livello di tecnologia informatica aumenta di uno il numero massimo di flotte.',
        'description_long' => 'Una volta lanciate in qualsiasi missione, le flotte sono controllate principalmente da una serie di computer situati sul pianeta di origine. Questi enormi computer calcolano il tempo esatto di arrivo, controllano le correzioni di rotta secondo necessità, calcolano le traiettorie e regolano le velocità di volo.
Con ogni livello ricercato, il computer di volo viene potenziato per consentire il lancio di uno slot aggiuntivo. La tecnologia informatica dovrebbe essere continuamente sviluppata durante la costruzione del tuo impero.',
    ],

    'astrophysics' => [
        'title'            => 'Astrofisica',
        'description'      => 'Con un modulo di ricerca astrofisica, le navi possono intraprendere lunghe spedizioni. Ogni secondo livello di questa tecnologia consentirà di colonizzare un pianeta aggiuntivo.',
        'description_long' => 'Ulteriori scoperte nel campo dell\'astrofisica consentono la costruzione di laboratori che possono essere installati su un numero sempre maggiore di navi. Ciò rende possibili lunghe spedizioni nelle aree inesplorate dello spazio. Inoltre questi progressi possono essere utilizzati per colonizzare ulteriormente l\'universo. Ogni due livelli di questa tecnologia è possibile rendere utilizzabile un pianeta aggiuntivo.',
    ],

    'intergalactic_research_network' => [
        'title'            => 'Rete di Ricerca Intergalattica',
        'description'      => 'I ricercatori su pianeti diversi comunicano tramite questa rete.',
        'description_long' => 'Questa è la tua rete di comunicazione nello spazio profondo per condividere i risultati delle ricerche con le tue colonie. Con la RRI, è possibile ottenere tempi di ricerca più rapidi collegando i laboratori di ricerca di livello più elevato pari al livello della RRI sviluppato.
Affinché funzioni, ogni colonia deve essere in grado di condurre la ricerca in modo indipendente.',
    ],

    'graviton_technology' => [
        'title'            => 'Tecnologia Gravitone',
        'description'      => 'Sparare una carica concentrata di particelle gravitone può creare un campo gravitazionale artificiale, capace di distruggere navi o persino lune.',
        'description_long' => 'Un gravitone è una particella elementare priva di massa e carica. Determina la forza gravitazionale. Sparando un carico concentrato di gravitoni, è possibile costruire un campo gravitazionale artificiale. Non diversamente da un buco nero, attira la massa verso di sé. Può quindi distruggere navi e persino interi pianeti. Per produrre una quantità sufficiente di gravitoni, sono necessarie enormi quantità di energia. La ricerca sui Gravitoni è necessaria per costruire la devastante Stella della Morte.',
    ],

    'weapon_technology' => [
        'title'            => 'Tecnologia delle Armi',
        'description'      => 'La tecnologia delle armi rende i sistemi d\'arma più efficienti. Ogni livello di tecnologia delle armi aumenta la potenza delle armi delle unità del 10% del valore base.',
        'description_long' => 'La Tecnologia delle Armi è una tecnologia chiave ed è fondamentale per la sopravvivenza contro gli Imperi nemici. Con ogni livello di Tecnologia delle Armi ricercato, i sistemi d\'arma sulle navi e i meccanismi difensivi diventano sempre più efficienti. Ogni livello aumenta la potenza base delle armi del 10% del valore base.',
    ],

    'shielding_technology' => [
        'title'            => 'Tecnologia degli Scudi',
        'description'      => 'La tecnologia degli scudi rende gli scudi su navi e strutture difensive più efficienti. Ogni livello di tecnologia degli scudi aumenta la potenza degli scudi del 10% del valore base.',
        'description_long' => 'Con l\'invenzione del generatore di magnetosfera, gli scienziati scoprirono che poteva essere prodotto uno scudo artificiale per proteggere l\'equipaggio delle astronavi non solo dalle rigide radiazioni solari nell\'ambiente dello spazio profondo, ma anche dai colpi nemici durante un attacco. Una volta che gli scienziati perfezionarono la tecnologia, un generatore di magnetosfera fu installato su tutte le navi e i sistemi difensivi.

Mano a mano che la tecnologia viene avanzata a ogni livello, il generatore di magnetosfera viene potenziato, fornendo un ulteriore 10% di resistenza al valore base degli scudi.',
    ],

    'armor_technology' => [
        'title'            => 'Tecnologia delle Armature',
        'description'      => 'Speciali leghe migliorano le armature su navi e strutture difensive. L\'efficacia dell\'armatura può essere aumentata del 10% per livello.',
        'description_long' => 'L\'ambiente dello spazio profondo è ostile. Piloti ed equipaggi in varie missioni affrontavano non solo intense radiazioni solari, ma anche la possibilità di essere colpiti da detriti spaziali o distrutti dal fuoco nemico in un attacco. Con la scoperta di una lega di alluminio-litio carburo di titanio, risultata sia leggera che resistente, l\'equipaggio ottenne un certo grado di protezione. Con ogni livello di Tecnologia delle Armature sviluppato, viene prodotta una lega di qualità superiore, che aumenta la resistenza dell\'armatura del 10%.',
    ],

    // ---- Navi Civili ----

    'small_cargo' => [
        'title'            => 'Cargo Leggero',
        'description'      => 'I cargo leggeri sono grandi, approssimativamente, come i caccia, ma hanno motori ed armamenti meno efficienti in modo da ricavare più spazio per il cargo. Pertanto, i cargo leggeri andrebbero impiegati in battaglia solo se supportati da navi forti in combattimento.

Non appena la ricerca del motore a impulso raggiunge il livello 5, i cargo leggeri vengono equipaggiati con motori a impulso, pertanto la loro velocità di base aumenta.',
    ],

    'large_cargo' => [
        'title'            => 'Cargo Pesante',
        'description'      => 'I cargo pesanti sono una versione avanzata delle più piccole navi cargo, rendendo disponibile più spazio per il carico e maggiore velocità dato il sistema di propulsione migliorato.',
        'description_long' => 'Questo tipo di nave non dovrebbe mai fare missioni da sola perché non ha armamenti seri od altre tecnologie, in modo da fornire il massimo spazio per il trasporto. I cargo pesanti possono velocemente rifornire pianeti grazie anche ai suoi motori a combustione altamente sofisticati. Naturalmente esso accompagna la flotta sui pianeti attaccati per recuperare tante più risorse quanto possibile.',
    ],

    'colony_ship' => [
        'title'            => 'Colonizzatrice',
        'description'      => 'I pianeti vuoti possono essere colonizzati grazie a questa nave.',
        'description_long' => 'Nel ventesimo secolo, l`uomo ha deciso di puntare verso le stelle. Dapprima, è atterrato sulla Luna. In seguito, è stata creata una stazione spaziale. Poco dopo, Marte fu colonizzato. Ben presto si capì che la nostra crescita sarebbe dipesa dalla colonizzazione di altri mondi. Gli scienziati e gli ingegneri di tutto il mondo si riunirono per sviluppare quella che diventò il più grande traguardo mai raggiunto dall`uomo. Era nata la prima Colonizzatrice.

Questa nave è utilizzata per preparare per la colonizzazione un pianeta appena scoperto. Appena arriva a destinazione, si trasforma in spazio abitativo, per assistere i coloni durante la popolazione del nuovo mondo. Il massimo numero di pianeti colonizzabili è determinato dai progressi nella ricerca dell`astrofisica. Due nuovi livelli di Astrofisica consentono la colonizzazione di un nuovo pianeta.',
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
        'description'      => 'I satelliti solari sono semplici satelliti in orbita equipaggiati di celle fotovoltaiche e servono a trasferire energia al pianeta. L`energia in questo modo è trasmessa a terra utilizzando un raggio laser speciale. Su questo pianeta, un satellite solare produce una quantità di energia pari a 32.',
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
        'title'            => 'Nave da Battaglia',
        'description'      => 'Le navi da battaglia sono la spina dorsale di ogni flotta militare. Corazzatura rinforzata, armamenti pesanti e alte velocità di spostamento, fanno di questa nave, insieme al grande spazio per il cargo, un nemico difficile da battere.',
        'description_long' => 'Le navi da battaglia sono la spina dorsale di ogni flotta militare. La loro corazzatura rinforzata, abbinata a degli armamenti pesanti, e una velocità di crociera elevata, rendono questa nave indispensabile per ogni impero. Inoltre possiede un`ampia stiva che è ottima in situazioni ostili.',
    ],

    'battlecruiser' => [
        'title'            => 'Incrociatore da Battaglia',
        'description'      => '\'Incrociatore da battaglia è altamente specializzato nell\'intercettare flotte nemiche.',
        'description_long' => 'Questa nave è una delle più avanzate mai sviluppate, ed è particolarmente letale quando si tratta di distruggere flotte in attacco. Con i suoi migliorati cannoni laser a bordo e il motore Iperspaziale avanzato, l\'Incrociatore da Battaglia è una forza seria da affrontare in qualsiasi attacco. A causa del design della nave e del suo grande sistema d\'armi, le stive hanno dovuto essere ridotte, ma questo è compensato dal minor consumo di carburante.',
    ],

    'bomber' => [
        'title'            => 'Bombardiere',
        'description'      => 'Il bombardiere è una nave stellare speciale sviluppata per sfondare pesanti difese planetarie.',
        'description_long' => 'Il bombardiere è una nave stellare speciale sviluppata per sfondare pesanti difese planetarie. Grazie ad un sistema di puntamento con guida laser, possono essere sganciate bombe al plasma con precisione sul bersaglio, causando enorme distruzione tra i sistemi di difesa planetari.

La velocità base dei bombardieri è aumentata non appena viene ricercato il motore iperspaziale di livello 8 in quanto essi vengono equipaggiati con motori iperspaziali.',
    ],

    'destroyer' => [
        'title'            => 'Corazzata',
        'La corazzata è la nave stellare più pesante mai vista e ha una potenza di fuoco mai eguagliata in precedenza.',
        'description_long' => 'Con la corazzata, la madre di tutte le navi da guerra entra nell`arena. Il suo sistema multi-falange d`armamento consiste di cannoni ionici, al plasma e di Gauss montati su torrette veloci nella risposta che permettono loro di eliminare i caccia operativi con un margine di successo del 99%. La dimensione della nave è d`altro canto il suo svantaggio dal momento che la manovrabilità è limitata, facendo della corazzata più una stazione da combattimento che una nave da guerra. Il consumo di carburante di queste immense corazzate è però tanto alto quanto lo è il loro potere di fuoco...

Poiché il Distruttore è molto grande, la sua manovrabilità è gravemente limitata, il che lo rende più una stazione di battaglia che una nave da combattimento. La mancanza di manovrabilità è compensata dalla sua pura potenza di fuoco, ma richiede anche quantità significative di Deuterio per essere costruita e operata.',
    ],

    'deathstar' => [
        'title'            => 'Morte Nera',
        'description'      => 'Non c`è nulla di così grande e pericoloso come una morte nera che si avvicina.',
        'description_long' => 'La morte nera è equipaggiata con un singolo gigantesco Cannone di Gauss in grado di distruggere praticamente qualsiasi cosa con un singolo colpo, sia che siano corazzate o lune. In modo da produrre l`energia necessaria per quest`arma, enormi parti all`interno della Morte Nera sono utilizzate come generatori di potenza. La dimensione della Morte Nera inoltre ne limita la velocità negli spostamenti, che è molto bassa. Si dice che spesso il capitano aiuti a spingerla per aumentarne la velocità.
Solo imperi immensi e avanzati hanno la manodopera e le conoscenze estese richieste per poter costruire una tale nave stellare della dimensione di una luna.',
    ],

    'reaper' => [
        'title'            => 'Reaper',
        'description'      => 'Le navi della classe Reaper sono un potente strumento di distruzione in grado di saccheggiare i Campi detriti subito dopo le battaglie.',
        'description_long' => 'La Reaper è la regina delle navi da guerra: essa combina potenza, robusti scudi, velocità e capacità. Inoltre, è l`unica in grado di sfruttare istantaneamente parte del Campo detriti che si crea dopo la battaglia. Tale funzione non è attiva dopo le battaglie contro alieni e pirati.',
    ],

    // ---- Difese ----

    'rocket_launcher' => [
        'title'            => 'Lanciamissili',
        'description'      => 'Il Lanciamissili è una semplice e conveniente opzione difensiva.',
        'description_long' => 'La tua prima, basilare linea di difesa. Si tratta di semplici strutture di lancio al suolo che sparano missili a testata convenzionale contro gli obiettivi nemici in attacco. Poiché sono economici da costruire e non richiedono ricerca, sono adatti per difendersi dalle incursioni, ma perdono efficacia contro attacchi su larga scala. Una volta che inizi la costruzione di sistemi d\'arma difensivi più avanzati, i Lanciamissili diventano semplice carne da cannone per consentire alle tue armi più potenti di infliggere danni maggiori per un periodo di tempo più lungo.

Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'light_laser' => [
        'title'            => 'Laser Leggero',
        'description'      => 'Il fuoco concentrato su un bersaglio con fotoni può produrre danni significativamente maggiori rispetto alle armi balistiche standard.',
        'description_long' => 'Con lo sviluppo della tecnologia e la creazione di navi sempre più sofisticate, si determinò che era necessaria una linea di difesa più forte per contrastare gli attacchi. Man mano che la Tecnologia Laser avanzava, fu progettata una nuova arma per fornire il livello successivo di difesa. I Laser Leggeri sono semplici armi a terra che utilizzano speciali sistemi di puntamento per tracciare il nemico e sparare un laser ad alta intensità progettato per penetrare lo scafo del bersaglio. Per mantenere la convenienza economica, sono stati dotati di un sistema di schermatura migliorato, tuttavia l\'integrità strutturale è la stessa del Lanciamissili.

Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'heavy_laser' => [
        'title'            => 'Laser Pesante',
        'description'      => 'Il Laser Pesante è il logico sviluppo del Laser Leggero.',
        'description_long' => 'Il Laser Pesante è una versione pratica e migliorata del Laser Leggero. Essendo più equilibrato del Laser Leggero con una composizione in lega migliorata, utilizza raggi più potenti e densi, e sistemi di puntamento integrati ancora migliori.

Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'gauss_cannon' => [
        'title'            => 'Cannone Gauss',
        'description'      => 'Il Cannone Gauss spara proiettili del peso di tonnellate ad alta velocità.',
        'description_long' => 'Per molto tempo le armi a proiettile erano considerate antiquate alla luce della moderna tecnologia termonucleare ed energetica e grazie allo sviluppo dell\'iperspazio e delle armature migliorate. Fino a quando la stessa tecnologia energetica che le aveva rese obsolete aiutò a riconquistare la loro posizione consolidata.
Un cannone Gauss è una versione di grandi dimensioni dell\'acceleratore di particelle. Missili estremamente pesanti vengono accelerati con un\'enorme forza elettromagnetica e raggiungono velocità di uscita che fanno bruciare il terreno circostante nella atmosfera. Quest\'arma è così potente al momento dello sparo che crea un boom sonico. Le armature e gli scudi moderni riescono a malapena a resistere alla forza; spesso il bersaglio viene completamente penetrato dalla potenza del missile. Le strutture difensive si disattivano non appena sono state danneggiate troppo gravemente.

Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'ion_cannon' => [
        'title'            => 'Cannone Ionico',
        'description'      => 'Il Cannone Ionico spara un raggio continuo di ioni acceleranti, causando danni considerevoli agli oggetti che colpisce.',
        'description_long' => 'Un cannone ionico è un\'arma che spara fasci di ioni (particelle caricate positivamente o negativamente). Il Cannone Ionico è in realtà un tipo di Cannone a Particelle; solo che le particelle utilizzate sono ionizzate. A causa delle loro cariche elettriche, hanno anche il potenziale di disabilitare dispositivi elettronici e qualsiasi altra cosa che abbia una fonte di alimentazione elettrica o simile, utilizzando un fenomeno noto come l\'Impulso Elettromagnetico (effetto EMP). Grazie all\'ottimo sistema di schermatura del cannone, questo fornisce una protezione migliorata per le tue armi difensive più grandi e distruttive.

Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'plasma_turret' => [
        'title'            => 'Cannone al Plasma',
        'description'      => 'Il Cannone al Plasma rilascia l\'energia di un brillamento solare e supera persino il Distruttore in termini di effetto distruttivo.',
        'description_long' => 'Uno dei sistemi d\'arma difensivi più avanzati mai sviluppati, il Cannone al Plasma utilizza una grande cella a combustibile di reattore nucleare per alimentare un acceleratore elettromagnetico che spara un impulso, o toroide, di plasma. Durante il funzionamento, il Cannone al Plasma blocca prima un bersaglio e avvia il processo di sparo. Una sfera di plasma viene creata nel nucleo del cannone surriscaldando e comprimendo i gas, privandoli dei loro ioni. Una volta che il gas è stato surriscaldato, compresso e creata una sfera di plasma, viene caricata nell\'acceleratore elettromagnetico che viene energizzato. Una volta completamente energizzato, l\'acceleratore viene attivato, con il risultato che la sfera di plasma viene lanciata a una velocità estremamente elevata verso il bersaglio previsto. Dal punto di vista del bersaglio, la palla bluastra di plasma in avvicinamento è impressionante, ma una volta che colpisce causa una distruzione istantanea.

Le strutture difensive si disattivano non appena sono troppo gravemente danneggiate. Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'small_shield_dome' => [
        'title'            => 'Piccola Cupola-Scudo',
        'description'      => 'La Piccola Cupola-Scudo copre un intero pianeta con un campo capace di assorbire un\'enorme quantità di energia.',
        'description_long' => 'La colonizzazione di nuovi mondi portò con sé un nuovo pericolo: i detriti spaziali. Un grande asteroide potrebbe facilmente distruggere il mondo e tutti i suoi abitanti. I progressi nella tecnologia di schermatura fornirono agli scienziati un modo per sviluppare uno scudo per proteggere un intero pianeta non solo dai detriti spaziali ma, come si apprese, anche da un attacco nemico. Creando un grande campo elettromagnetico attorno al pianeta, i detriti spaziali che normalmente avrebbero distrutto il pianeta venivano deflessi, e gli attacchi da imperi nemici venivano sventati. I primi generatori erano grandi e lo scudo forniva una protezione moderata, ma fu scoperto in seguito che le cupole piccole non offrivano la protezione dagli attacchi su larga scala. La Piccola Cupola-Scudo era il preludio a un sistema di schermatura planetaria più robusto e avanzato.

Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'large_shield_dome' => [
        'title'            => 'Grande Cupola-Scudo',
        'description'      => 'L\'evoluzione della Piccola Cupola-Scudo può impiegare molto più energia per resistere agli attacchi.',
        'description_long' => 'La Grande Cupola-Scudo è il passo successivo nell\'avanzamento degli scudi planetari; è il risultato di anni di lavoro per migliorare la Piccola Cupola-Scudo. Costruita per resistere a un bombardamento più intenso del fuoco nemico fornendo un campo elettromagnetico maggiormente energizzato, le Grandi Cupole offrono un periodo di protezione più lungo prima di collassare.

Dopo una battaglia, c\'è fino al 70% di probabilità che le strutture difensive danneggiate possano essere riportate in uso.',
    ],

    'anti_ballistic_missile' => [
        'title'            => 'Missile Anti-Balistico',
        'description'      => 'I Missili Anti-Balistici distruggono i Missili Interplanetari in arrivo.',
        'description_long' => 'I Missili Anti-Balistici (ABM) sono la tua unica linea di difesa quando vieni attaccato da Missili Interplanetari (IPM) sul tuo pianeta o luna. Quando viene rilevato un lancio di IPM, questi missili si armano automaticamente, elaborano un codice di lancio nei loro calcolatori di volo, prendono di mira l\'IPM in arrivo e si lanciano per intercettarlo. Durante il volo, l\'IPM bersaglio viene costantemente tracciato e le correzioni di rotta vengono applicate fino a quando l\'ABM raggiunge il bersaglio e distrugge l\'IPM attaccante. Ogni ABM distrugge un IPM in arrivo.',
    ],

    'interplanetary_missile' => [
        'title'            => 'Missile Interplanetario',
        'description'      => 'I Missili Interplanetari distruggono le difese nemiche.',
        'description_long' => 'I Missili Interplanetari (IPM) sono la tua arma offensiva per distruggere le difese del tuo bersaglio. Usando tecnologia di tracciamento all\'avanguardia, ogni missile prende di mira un certo numero di difese per la distruzione. Dotati di una bomba all\'antimateria, consegnano una forza distruttiva così grave che gli scudi e le difese distrutti non possono essere riparati. L\'unico modo per contrastare questi missili è con gli ABM.',
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

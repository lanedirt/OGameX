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
        'description'      => 'Il Cargo Leggero è un\'agile nave da trasporto che può rapidamente portare risorse su altri pianeti.',
        'description_long' => 'I Trasportatori hanno più o meno le dimensioni dei caccia, ma rinunciano ai motori ad alte prestazioni e all\'armamento di bordo a favore di una maggiore capacità di carico. Di conseguenza, un Trasportatore dovrebbe essere inviato in battaglia solo se accompagnato da navi da combattimento.

Non appena il Motore ad Impulso raggiunge il livello 5 di ricerca, il Cargo Leggero viaggia con una velocità base maggiore ed è equipaggiato con un Motore ad Impulso.',
    ],

    'large_cargo' => [
        'title'            => 'Cargo Pesante',
        'description'      => 'Questa nave da carico ha una capacità molto maggiore rispetto al Cargo Leggero, ed è generalmente più veloce grazie ad un motore migliorato.',
        'description_long' => 'Con il tempo, le incursioni alle colonie portarono a catturare quantità sempre maggiori di risorse. Di conseguenza, venivano inviati in massa Cargo Leggeri per compensare i carichi sempre più grandi. Si comprese rapidamente che era necessaria una nuova classe di navi per massimizzare le risorse catturate nelle incursioni, pur essendo economicamente conveniente. Dopo un lungo sviluppo, nacque il Cargo Pesante.

Per massimizzare le risorse che possono essere stivate nelle stive, questa nave ha pochissime armi o armature. Grazie al motore a combustione altamente sviluppato installato a bordo, rappresenta il fornitore di risorse più economico tra i pianeti, ed il più efficace nelle incursioni su mondi ostili.',
    ],

    'colony_ship' => [
        'title'            => 'Nave Colonizzatrice',
        'description'      => 'I pianeti vacanti possono essere colonizzati con questa nave.',
        'description_long' => 'Nel XX Secolo, l\'umanità decise di puntare alle stelle. Prima fu lo sbarco sulla Luna. Poi fu costruita una stazione spaziale. Marte fu colonizzato poco dopo. Si determinò presto che la nostra crescita dipendeva dalla colonizzazione di altri mondi. Scienziati e ingegneri da tutto il mondo si riunirono per sviluppare il più grande risultato mai raggiunto dall\'uomo: la Nave Colonizzatrice.

Questa nave viene usata per preparare un pianeta di nuova scoperta alla colonizzazione. Una volta giunta a destinazione, la nave viene istantaneamente trasformata in spazio abitativo per assistere nel popolare e sfruttare il nuovo mondo. Il numero massimo di pianeti è determinato dai progressi nella ricerca di Astrofisica. Due nuovi livelli di Astrotecnologia permettono la colonizzazione di un pianeta aggiuntivo.',
    ],

    'recycler' => [
        'title'            => 'Riciclatrice',
        'description'      => 'Le Riciclatrici sono le uniche navi in grado di raccogliere i campi di detriti che fluttuano nell\'orbita di un pianeta dopo i combattimenti.',
        'description_long' => 'I combattimenti nello spazio assunsero proporzioni sempre maggiori. Migliaia di navi furono distrutte e le risorse dei loro resti sembravano perse per sempre nei campi di detriti. Le normali navi da carico non potevano avvicinarsi abbastanza a questi campi senza rischiare danni ingenti.
Un recente sviluppo nelle tecnologie degli scudi aggirò efficacemente questo problema. Fu creata una nuova classe di navi simile ai Trasportatori: le Riciclatrici. I loro sforzi aiutarono a recuperare le risorse date per perse. I detriti non rappresentavano più alcun vero pericolo grazie ai nuovi scudi.

Non appena la ricerca del Motore ad Impulso raggiunge il livello 17, le Riciclatrici vengono riattrezzate con Motori ad Impulso. Non appena la ricerca del Motore Iperspaziale raggiunge il livello 15, le Riciclatrici vengono riattrezzate con Motori Iperspaziali.',
    ],

    'espionage_probe' => [
        'title'            => 'Sonda Spia',
        'description'      => 'Le Sonde Spia sono piccoli droni agili che forniscono dati su flotte e pianeti su grandi distanze.',
        'description_long' => 'Le Sonde Spia sono piccoli droni agili che forniscono dati su flotte e pianeti. Dotate di motori appositamente progettati, permettono loro di coprire vaste distanze in pochi minuti. Una volta in orbita attorno al pianeta bersaglio, raccolgono rapidamente dati e trasmettono il rapporto alla tua Rete di Comunicazione Profonda per la valutazione. Ma c\'è un rischio nell\'attività di raccolta informazioni. Durante la trasmissione del rapporto alla rete, il segnale può essere rilevato dal bersaglio e le sonde possono essere distrutte.',
    ],

    'solar_satellite' => [
        'title'            => 'Satellite Solare',
        'description'      => 'I Satelliti Solari sono semplici piattaforme di celle solari, collocate in un\'orbita alta e stazionaria. Raccolgono la luce solare e la trasmettono alla stazione a terra tramite laser.',
        'description_long' => 'Gli scienziati scoprirono un metodo per trasmettere energia elettrica alla colonia usando satelliti appositamente progettati in un\'orbita geosincronizzata. I Satelliti Solari raccolgono energia solare e la trasmettono a una stazione a terra utilizzando avanzate tecnologie laser. L\'efficienza di un satellite solare dipende dall\'intensità della radiazione solare che riceve. In linea di principio, la produzione di energia nelle orbite più vicine al sole è maggiore rispetto ai pianeti in orbite più lontane.
Grazie al loro buon rapporto costo/prestazioni, i Satelliti Solari possono risolvere molti problemi energetici. Attenzione però: i Satelliti Solari possono essere facilmente distrutti in battaglia.',
    ],

    'crawler' => [
        'title'            => 'Crawler',
        'description'      => 'I Crawler aumentano la produzione di metallo, cristallo e Deuterio sul pianeta assegnato rispettivamente dello 0,02%, 0,02% e 0,02%. Come Collezionista, la produzione aumenta ulteriormente. Il bonus totale massimo dipende dal livello complessivo delle miniere.',
        'description_long' => 'Il Crawler è un grande veicolo da trincea che aumenta la produzione di miniere e sintetizzatori. È più agile di quanto sembri ma non è particolarmente robusto. Ogni Crawler aumenta la produzione di metallo dello 0,02%, la produzione di cristallo dello 0,02% e la produzione di Deuterio dello 0,02%. Come Collezionista, la produzione aumenta ulteriormente. Il bonus totale massimo dipende dal livello complessivo delle miniere.',
    ],

    'pathfinder' => [
        'title'            => 'Esploratore',
        'description'      => 'L\'Esploratore è una nave veloce ed agile, progettata specificamente per spedizioni in settori inesplorati dello spazio.',
        'description_long' => 'L\'Esploratore è l\'ultimo sviluppo nella tecnologia di esplorazione. Questa nave è stata progettata appositamente per i membri della classe Scopritore per massimizzarne il potenziale. Dotato di sistemi di scansione avanzati e di una grande stiva per il recupero di risorse, l\'Esploratore eccelle nelle spedizioni. I suoi sofisticati sensori possono rilevare risorse preziose e anomalie che passerebbero inosservate ad altre navi. La nave combina un\'alta velocità con una buona capacità di carico, rendendola perfetta per rapide missioni di esplorazione e raccolta di risorse da settori distanti.',
    ],

    // ---- Navi Militari ----

    'light_fighter' => [
        'title'            => 'Cacciatore Leggero',
        'description'      => 'Questa è la prima nave da combattimento che tutti gli imperatori costruiranno. Il Cacciatore Leggero è un\'agile nave, ma vulnerabile da sola. In grande numero, possono diventare una seria minaccia per qualsiasi impero. Sono i primi ad accompagnare i Cargo Leggeri e Pesanti su pianeti ostili con difese minori.',
        'description_long' => 'Questa è la prima nave da combattimento che tutti gli imperatori costruiranno. Il Cacciatore Leggero è un\'agile nave, ma vulnerabile quando affronta il nemico da sola. In grande numero, possono diventare una seria minaccia per qualsiasi impero. Sono i primi ad accompagnare i Cargo Leggeri e Pesanti su pianeti ostili con difese minori.',
    ],

    'heavy_fighter' => [
        'title'            => 'Cacciatore Pesante',
        'description'      => 'Questo cacciatore è meglio corazzato e ha una maggiore forza d\'attacco rispetto al Cacciatore Leggero.',
        'description_long' => 'Nello sviluppo del Cacciatore Pesante, i ricercatori raggiunsero un punto in cui i motori convenzionali non erano più in grado di fornire prestazioni sufficienti. Per muovere la nave in modo ottimale, il Motore ad Impulso fu utilizzato per la prima volta. Questo aumentò i costi, ma aprì anche nuove possibilità. Utilizzando questo motore, rimase più energia per armi e scudi; inoltre, vennero utilizzati materiali di alta qualità per questa nuova famiglia di caccia. Con questi cambiamenti, il Cacciatore Pesante rappresenta una nuova era nella tecnologia delle navi ed è la base per la tecnologia degli Incrociatori.

Leggermente più grande del Cacciatore Leggero, il Cacciatore Pesante ha scafi più spessi, che forniscono maggiore protezione, e armamento più potente.',
    ],

    'cruiser' => [
        'title'            => 'Incrociatore',
        'description'      => 'Gli Incrociatori sono corazzati quasi tre volte tanto i Cacciatori Pesanti e hanno più del doppio della potenza di fuoco. Inoltre, sono molto veloci.',
        'description_long' => 'Con lo sviluppo del laser pesante e del cannone ionico, i Cacciatori Leggeri e Pesanti incontrarono un numero allarmante di sconfitte che aumentavano con ogni incursione. Nonostante molte modifiche, la potenza delle armi e i cambiamenti all\'armatura non potevano essere aumentati abbastanza velocemente per contrastare efficacemente queste nuove misure difensive. Pertanto, si decise di costruire una nuova classe di nave che combinasse più corazza e più potenza di fuoco. Come risultato di anni di ricerca e sviluppo, nacque l\'Incrociatore.

Gli Incrociatori sono corazzati quasi tre volte rispetto ai Cacciatori Pesanti e possiedono più del doppio della potenza di fuoco di qualsiasi nave da combattimento esistente. Possiedono anche velocità di gran lunga superiori a qualsiasi astronave mai costruita. Per quasi un secolo, gli Incrociatori dominarono l\'universo. Tuttavia, con lo sviluppo dei cannoni Gauss e delle torrette al plasma, la loro supremazia terminò. Sono ancora usati oggi contro gruppi di caccia, ma non in modo così predominante come prima.',
    ],

    'battle_ship' => [
        'title'            => 'Nave da Battaglia',
        'description'      => 'Le Navi da Battaglia formano la spina dorsale di una flotta. I loro pesanti cannoni, l\'alta velocità e le grandi stive le rendono avversari da prendere sul serio.',
        'description_long' => 'Quando divenne evidente che l\'Incrociatore stava cedendo terreno al crescente numero di strutture difensive che si trovava ad affrontare, e con la perdita di navi nelle missioni a livelli inaccettabili, si decise di costruire una nave che potesse affrontare gli stessi tipi di strutture difensive con quante meno perdite possibili. Dopo un intenso sviluppo, nacque la Nave da Battaglia. Costruita per resistere alle battaglie più grandi, la Nave da Battaglia dispone di grandi stive, pesanti cannoni e un\'alta velocità con il motore iperspaziale. Una volta sviluppata, divenne il pilastro della flotta di ogni Imperatore predatore.',
    ],

    'battlecruiser' => [
        'title'            => 'Corazzata',
        'description'      => 'La Corazzata è altamente specializzata nell\'intercettazione di flotte ostili.',
        'description_long' => 'Questa nave è una delle più avanzate mai sviluppate, ed è particolarmente letale quando si tratta di distruggere flotte in attacco. Con i suoi migliorati cannoni laser a bordo e il motore Iperspaziale avanzato, la Corazzata è una forza seria da affrontare in qualsiasi attacco. A causa del design della nave e del suo grande sistema d\'armi, le stive hanno dovuto essere ridotte, ma questo è compensato dal minor consumo di carburante.',
    ],

    'bomber' => [
        'title'            => 'Bombardiere',
        'description'      => 'Il Bombardiere è stato sviluppato appositamente per distruggere le difese planetarie di un mondo.',
        'description_long' => 'Nel corso dei secoli, man mano che le difese diventavano sempre più grandi e sofisticate, le flotte cominciarono ad essere distrutte a un ritmo allarmante. Si decise che era necessaria una nuova nave per sfondare le difese e garantire il massimo dei risultati. Dopo anni di ricerca e sviluppo, nacque il Bombardiere.

Usando attrezzature di mira laser-guidate e Bombe al Plasma, il Bombardiere cerca e distrugge qualsiasi meccanismo difensivo che riesce a trovare. Non appena il Motore Iperspaziale viene sviluppato al Livello 8, il Bombardiere viene riequipaggiato con il motore iperspaziale e può volare a velocità maggiori.',
    ],

    'destroyer' => [
        'title'            => 'Distruttore',
        'description'      => 'Il Distruttore è il re delle navi da guerra.',
        'description_long' => 'Il Distruttore è il risultato di anni di lavoro e sviluppo. Con lo sviluppo delle Morti Nere, si decise che era necessaria una classe di navi per difendersi da una tale arma massiccia. Grazie ai suoi migliorati sensori di localizzazione, ai cannoni ionici multi-falange, ai Cannoni Gauss e alle Torrette al Plasma, il Distruttore si rivelò una delle navi più temibili mai costruite.

Poiché il Distruttore è molto grande, la sua manovrabilità è gravemente limitata, il che lo rende più una stazione di battaglia che una nave da combattimento. La mancanza di manovrabilità è compensata dalla sua pura potenza di fuoco, ma richiede anche quantità significative di Deuterio per essere costruita e operata.',
    ],

    'deathstar' => [
        'title'            => 'Morte Nera',
        'description'      => 'La potenza distruttiva della Morte Nera è insuperabile.',
        'description_long' => 'La Morte Nera è la nave più potente mai creata. Questa nave delle dimensioni di una luna è l\'unica nave che può essere vista ad occhio nudo dal suolo. Nel momento in cui la si scorge, purtroppo, è già troppo tardi per fare qualsiasi cosa.

Armata di un gigantesco cannone gravitonico, il sistema d\'arma più avanzato mai creato nell\'Universo, questa enorme nave non solo ha la capacità di distruggere intere flotte e difese, ma ha anche la capacità di distruggere intere lune. Solo gli imperi più avanzati hanno la capacità di costruire una nave di queste proporzioni titaniche.',
    ],

    'reaper' => [
        'title'            => 'Mietitore',
        'description'      => 'Il Mietitore è una potente nave da combattimento specializzata nel saccheggio aggressivo e nella raccolta di campi di detriti.',
        'description_long' => 'Il Mietitore rappresenta il culmine dell\'ingegneria militare della classe Generale. Questa nave pesantemente armata è stata progettata per i comandanti che valorizzano sia la capacità di combattimento che la flessibilità tattica. Sebbene il suo ruolo primario sia il combattimento, il Mietitore dispone di stive rinforzate che gli consentono di raccogliere campi di detriti dopo la battaglia. I suoi sistemi di mira avanzati e l\'armatura pesante lo rendono un avversario formidabile, mentre il suo design a doppia funzione significa che può sia creare che trarre profitto dalla carneficina del campo di battaglia. La nave è equipaggiata con tecnologia d\'arma all\'avanguardia e può tenere testa a navi molto più grandi.',
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

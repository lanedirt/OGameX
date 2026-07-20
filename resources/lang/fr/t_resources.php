<?php

return [
    'metal_mine' => [
        'title' => 'Mine de métal',
        'description' => 'Utilisées pour l’extraction de minerais métalliques, les mines de métaux sont d’une importance primordiale pour tous les empires émergents et établis.',
        'description_long' => 'Le métal est la principale ressource utilisée dans la fondation de votre empire. À de plus grandes profondeurs, les mines peuvent produire davantage de métal viable destiné à la construction de bâtiments, de navires, de systèmes de défense et de recherche. À mesure que les mines creusent plus profondément, plus d’énergie est nécessaire pour une production maximale. Le métal étant la plus abondante de toutes les ressources disponibles, sa valeur est considérée comme la plus faible de toutes les ressources commerciales.',
    ],
    'crystal_mine' => [
        'title' => 'Mine de cristal',
        'description' => 'Les cristaux sont la principale ressource utilisée pour construire des circuits électroniques et former certains composés d’alliage.',
        'description_long' => 'Les mines de cristaux fournissent la principale ressource utilisée pour produire des circuits électroniques et de certains composés d\'alliages. L’extraction du cristal consomme environ une fois et demie plus d’énergie qu’un métal extrait, ce qui rend le cristal plus précieux. Presque tous les navires et tous les bâtiments nécessitent du cristal. Cependant, la plupart des cristaux nécessaires à la construction des vaisseaux spatiaux sont très rares et, comme le métal, ne peuvent être trouvés qu’à une certaine profondeur. Par conséquent, la construction de mines dans des couches plus profondes augmentera la quantité de cristaux produits.',
    ],
    'deuterium_synthesizer' => [
        'title' => 'Synthétiseur de deutérium',
        'description' => 'Les synthétiseurs de deutérium extraient les traces de deutérium contenues dans l\'eau d\'une planète.',
        'description_long' => 'Le deutérium est également appelé hydrogène lourd. C\'est un isotope stable de l\'hydrogène avec une abondance naturelle dans les océans des colonies d\'environ un atome sur 6 500 d\'hydrogène (~ 154 PPM). Le deutérium représente donc environ 0,015 % (en poids, 0,030 %) du total. Le deutérium est traité par des synthétiseurs spéciaux qui peuvent séparer l\'eau du deutérium à l\'aide de centrifugeuses spécialement conçues. La mise à niveau du synthétiseur permet d\'augmenter la quantité de dépôts de deutérium traités. Le deutérium est utilisé pour effectuer des analyses de phalanges de capteurs, pour observer des galaxies, comme carburant pour les navires et pour effectuer des mises à niveau de recherche spécialisées.',
    ],
    'solar_plant' => [
        'title' => 'Centrale solaire',
        'description' => 'Les centrales solaires absorbent l’énergie du rayonnement solaire. Toutes les mines ont besoin d’énergie pour fonctionner.',
        'description_long' => 'De gigantesques panneaux solaires sont utilisés pour produire de l’électricité pour les mines et le synthétiseur de deutérium. À mesure que la centrale solaire est améliorée, la surface des cellules photovoltaïques couvrant la planète augmente, ce qui entraîne une production d\'énergie plus élevée sur les réseaux électriques de votre planète.',
    ],
    'fusion_plant' => [
        'title' => 'Réacteur à fusion',
        'description' => 'Le réacteur à fusion utilise du deutérium pour produire de l\'énergie.',
        'description_long' => 'Dans les centrales à fusion, les noyaux d’hydrogène sont fusionnés en noyaux d’hélium sous d’énormes températures et pressions, libérant d’énormes quantités d’énergie. Pour chaque gramme de deutérium consommé, jusqu\'à 41,32*10^-13 Joule d\'énergie peuvent être produits ; avec 1 g, vous pouvez produire 172 MWh d’énergie.

Les grands complexes de réacteurs utilisent plus de deutérium et peuvent produire plus d’énergie par heure. L\'effet énergétique pourrait être accru par la recherche sur les technologies énergétiques.

La production d\'énergie de l\'usine de fusion est calculée ainsi :
30 * [Plante de fusion de niveau] * (1,05 + [Technologie énergétique de niveau] * 0,01) ^ [Plante de fusion de niveau]',
    ],
    'metal_store' => [
        'title' => 'Stockage en métal',
        'description' => 'Fournit un espace de stockage pour l\'excédent de métal.',
        'description_long' => 'Cette installation de stockage géante est utilisée pour stocker du minerai métallique. Chaque niveau de mise à niveau augmente la quantité de minerai métallique pouvant être stockée. Si les magasins sont pleins, aucun autre métal ne sera extrait.

Le stockage de métaux protège un certain pourcentage de la production quotidienne de la mine (max. 10 pour cent).',
    ],
    'crystal_store' => [
        'title' => 'Stockage de cristaux',
        'description' => 'Fournit un stockage pour l’excès de cristal.',
        'description_long' => 'Entre-temps, le cristal non traité sera stocké dans ces halls de stockage géants. À chaque niveau de mise à niveau, la quantité de cristaux pouvant être stockée augmente. Si les réserves de cristaux sont pleines, aucun autre cristal ne sera extrait.

Le Crystal Storage protège un certain pourcentage de la production quotidienne de la mine (max. 10 pour cent).',
    ],
    'deuterium_store' => [
        'title' => 'Réservoir de deutérium',
        'description' => 'Des réservoirs géants pour stocker le deutérium nouvellement extrait.',
        'description_long' => 'Le réservoir de deutérium est destiné au stockage du deutérium nouvellement synthétisé. Une fois traité par le synthétiseur, il est acheminé vers ce réservoir pour une utilisation ultérieure. À chaque mise à niveau du réservoir, la capacité totale de stockage augmente. Une fois la capacité atteinte, plus aucun deutérium ne sera synthétisé.

Le réservoir de deutérium protège un certain pourcentage de la production quotidienne du synthétiseur (max. 10 pour cent).',
    ],
    'robot_factory' => [
        'title' => 'Usine de robotique',
        'description' => 'Les usines robotiques fournissent des robots de construction pour aider à la construction de bâtiments. Chaque niveau augmente la vitesse de mise à niveau des bâtiments.',
        'description_long' => 'L\'objectif principal de Robotics Factory est la production de robots de construction de pointe. Chaque mise à niveau de l\'usine robotique entraîne la production de robots plus rapides, utilisés pour réduire le temps nécessaire à la construction des bâtiments.',
    ],
    'shipyard' => [
        'title' => 'Chantier spatial',
        'description' => 'Tous les types de navires et d\'installations défensives sont construits dans le chantier naval planétaire.',
        'description_long' => 'Le chantier naval planétaire est responsable de la construction d\'engins spatiaux et de mécanismes défensifs. Au fur et à mesure que le chantier naval se modernise, il peut produire une plus grande variété de véhicules à une vitesse beaucoup plus élevée. Si une usine de nanites est présente sur la planète, la vitesse à laquelle les navires sont construits est considérablement augmentée.',
    ],
    'research_lab' => [
        'title' => 'Laboratoire de recherche',
        'description' => 'Un laboratoire de recherche est nécessaire pour mener des recherches sur les nouvelles technologies.',
        'description_long' => 'Élément essentiel de tout empire, les laboratoires de recherche sont l\'endroit où les nouvelles technologies sont découvertes et les anciennes technologies sont améliorées. À mesure que chaque niveau du laboratoire de recherche est construit, la vitesse à laquelle les nouvelles technologies sont recherchées augmente, tout en ouvrant également la voie à de nouvelles technologies pour la recherche. Afin de mener les recherches le plus rapidement possible, des chercheurs sont immédiatement dépêchés sur la colonie pour commencer les travaux et le développement. De cette manière, les connaissances sur les nouvelles technologies peuvent facilement être diffusées dans tout l’empire.',
    ],
    'alliance_depot' => [
        'title' => 'Dépôt de l\'Alliance',
        'description' => 'Le dépôt de l\'alliance fournit du carburant aux flottes amies en orbite pour contribuer à leur défense.',
        'description_long' => 'Le dépôt de l\'alliance fournit du carburant aux flottes amies en orbite pour contribuer à leur défense. Pour chaque niveau de mise à niveau du dépôt de l\'alliance, une demande spéciale de deutérium par heure peut être envoyée à une flotte en orbite.',
    ],
    'missile_silo' => [
        'title' => 'Silo à missiles',
        'description' => 'Les silos à missiles sont utilisés pour stocker les missiles.',
        'description_long' => 'Les silos de missiles sont utilisés pour construire, stocker et lancer des missiles interplanétaires et antibalistiques. A chaque niveau du silo, cinq missiles interplanétaires ou dix missiles anti-balistiques peuvent être stockés. Un missile interplanétaire utilise le même espace que deux missiles antibalistiques. Le stockage des missiles interplanétaires et des missiles antibalistiques dans le même silo est autorisé.',
    ],
    'nano_factory' => [
        'title' => 'Usine de nanites',
        'description' => 'C\'est le nec plus ultra de la technologie robotique. Chaque niveau réduit le temps de construction des bâtiments, des navires et des défenses.',
        'description_long' => 'Une nanomachine, également appelée nanite, est un dispositif mécanique ou électromécanique dont les dimensions sont mesurées en nanomètres (millionièmes de millimètre, ou unités de 10^-9 mètres). La taille microscopique des nanomachines se traduit par une vitesse opérationnelle plus élevée. Cette usine produit des nanomachines qui constituent l’évolution ultime de la technologie robotique. Une fois construite, chaque mise à niveau réduit considérablement le temps de production des bâtiments, des navires et des structures défensives.',
    ],
    'terraformer' => [
        'title' => 'Terraformeur',
        'description' => 'Le terraformeur augmente la surface utilisable des planètes.',
        'description_long' => 'Avec la construction croissante sur les planètes, même l’espace vital de la colonie devient de plus en plus limité. Les méthodes traditionnelles telles que la construction en hauteur et souterraine deviennent de plus en plus insuffisantes. Un petit groupe de physiciens des hautes énergies et d’ingénieurs en nano est finalement parvenu à la solution : la terraformation.
Utilisant d’énormes quantités d’énergie, le terraformer peut rendre arables des étendues entières de terre, voire des continents. Ce bâtiment abrite la production de nanites créées spécifiquement à cet effet, qui garantissent une qualité de sol constante partout.

Chaque niveau de terraformer permet de cultiver 5 champs. A chaque niveau, le terraformateur occupe lui-même un champ. Tous les 2 niveaux de terraformer, vous recevrez 1 champ bonus.

Une fois construit, le terraformateur ne peut être démonté.',
    ],
    'space_dock' => [
        'title' => 'Quai spatial',
        'description' => 'Les épaves peuvent être réparées dans le Space Dock.',
        'description_long' => 'Le Space Dock offre la possibilité de réparer les navires détruits au combat et qui ont laissé des épaves. Le temps de réparation dure au maximum 12 heures, mais il faut au moins 30 minutes jusqu\'à ce que les navires puissent être remis en service.

Les réparations doivent commencer dans les 3 jours suivant la création de l\'épave. Les navires réparés doivent être remis en service manuellement une fois les réparations terminées. Si cela n’est pas fait, les navires individuels de tout type seront remis en service après 3 jours.

L\'épave n\'apparaît que si plus de 150 000 unités ont été détruites, y compris ses propres navires ayant pris part au combat pour une valeur d\'au moins 5 % des points du navire.

Puisque le Space Dock flotte en orbite, il ne nécessite pas de champ planétaire.',
    ],
    'lunar_base' => [
        'title' => 'Base lunaire',
        'description' => 'Puisque la Lune n’a pas d’atmosphère, une base lunaire est nécessaire pour générer un espace habitable.',
        'description_long' => 'Une lune n\'a pas d\'atmosphère, donc une base lunaire doit d\'abord être construite avant qu\'une colonie puisse être établie. Cela fournit alors de l’oxygène, du chauffage et de la gravité. À chaque niveau construit, une plus grande zone de vie et de développement est créée au sein de la biosphère. Chaque niveau construit permet trois champs pour d\'autres bâtiments. A chaque niveau, la base lunaire occupe elle-même un champ.
Une fois construite, la base lunaire ne peut être démolie.',
    ],
    'sensor_phalanx' => [
        'title' => 'Phalange du capteur',
        'description' => 'Grâce à la phalange des capteurs, les flottes d\'autres empires peuvent être découvertes et observées. Plus le réseau de phalanges du capteur est grand, plus la portée qu\'il peut balayer est grande.',
        'description_long' => 'Utilisant des capteurs haute résolution, le Sensor Phalanx analyse d’abord le spectre de la lumière, la composition des gaz et les émissions de rayonnement d’un monde lointain et transmet les données à un superordinateur pour traitement. Une fois les informations obtenues, le supercalculateur compare les changements dans le spectre, la composition des gaz et les émissions de rayonnements à un graphique de base des changements connus du spectre créés par divers mouvements du navire. Les données résultantes affichent ensuite l\'activité de toute flotte à portée de la phalange. Pour éviter que le supercalculateur ne surchauffe pendant le processus, il est refroidi en utilisant 5k de deutérium traité.
Pour utiliser le Phalanx, cliquez sur n’importe quelle planète dans la vue Galaxy à portée de vos capteurs.',
    ],
    'jump_gate' => [
        'title' => 'Porte de saut',
        'description' => 'Les portes de saut sont d\'énormes émetteurs-récepteurs capables d\'envoyer même la plus grande flotte en un rien de temps vers une porte de saut distante.',
        'description_long' => 'Un Jump Gate est un système d\'émetteurs-récepteurs géants capables d\'envoyer même les plus grandes flottes vers une porte de réception n\'importe où dans l\'univers sans perte de temps. Utilisant une technologie similaire à celle d’un Worm Hole pour réaliser le saut, le deutérium n’est pas nécessaire. Une période de recharge de quelques minutes doit s\'écouler entre les sauts pour permettre la régénération. Le transport de ressources à travers la Porte n’est pas non plus possible. À chaque niveau de mise à niveau, le temps de recharge de la porte de saut peut être réduit.',
    ],
    'energy_technology' => [
        'title' => 'Technologie énergétique',
        'description' => 'La maîtrise de différents types d’énergie est nécessaire à de nombreuses nouvelles technologies.',
        'description_long' => 'Au fur et à mesure que divers domaines de recherche avançaient, on a découvert que la technologie actuelle de distribution d’énergie n’était pas suffisante pour entreprendre certaines recherches spécialisées. Avec chaque mise à niveau de votre technologie énergétique, de nouvelles recherches peuvent être menées qui débloquent le développement de navires et de défenses plus sophistiqués.',
    ],
    'laser_technology' => [
        'title' => 'Technologie laser',
        'description' => 'La focalisation de la lumière produit un faisceau qui provoque des dégâts lorsqu\'il frappe un objet.',
        'description_long' => 'Les lasers (amplification de la lumière par émission stimulée de rayonnement) produisent une émission intense et riche en énergie de lumière cohérente. Ces appareils peuvent être utilisés dans toutes sortes de domaines, des ordinateurs optiques aux armes laser lourdes, qui coupent sans effort la technologie des blindages. La technologie laser constitue une base importante pour la recherche d’autres technologies d’armes.',
    ],
    'ion_technology' => [
        'title' => 'Technologie ionique',
        'description' => 'La concentration d\'ions permet la construction de canons, qui peuvent infliger d\'énormes dégâts et réduire les coûts de déconstruction par niveau de 4 %.',
        'description_long' => 'Les ions peuvent être concentrés et accélérés pour former un faisceau mortel. Ces faisceaux peuvent alors infliger d\'énormes dégâts. Nos scientifiques ont également développé une technique qui réduira nettement les coûts de déconstruction des bâtiments et des systèmes. Pour chaque niveau de recherche, les coûts de déconstruction diminueront de 4 %.',
    ],
    'hyperspace_technology' => [
        'title' => 'Technologie hyperspatiale',
        'description' => 'En intégrant les 4ème et 5ème dimensions, il est désormais possible de rechercher un nouveau type de motorisation, plus économique et plus efficace.',
        'description_long' => 'En théorie, l’idée du voyage dans l’hyperespace repose sur l’existence d’une dimension distincte et adjacente. Lorsqu\'il est activé, un moteur hyperspatial propulse le vaisseau spatial dans cette autre dimension, où il peut parcourir de vastes distances en un temps considérablement réduit par rapport au temps qu\'il prendrait dans l\'espace « normal ». Une fois qu’il atteint le point de l’hyperespace qui correspond à sa destination dans l’espace réel, il réapparaît.
Une fois qu’un niveau suffisant de technologie hyperspatiale est recherché, l’Hyperspace Drive n’est plus seulement une théorie. Chaque amélioration de ce moteur augmente la capacité de chargement de vos navires de 5 % de la valeur de base.',
    ],
    'plasma_technology' => [
        'title' => 'Technologie plasma',
        'description' => 'Un développement ultérieur de la technologie ionique qui accélère le plasma à haute énergie, qui inflige ensuite des dégâts dévastateurs et optimise en outre la production de métal, de cristal et de deutérium (1 %/0,66 %/0,33 % par niveau).',
        'description_long' => 'Un développement ultérieur de la technologie ionique qui n\'accélère pas les ions mais plutôt un plasma à haute énergie, qui peut ensuite infliger des dégâts dévastateurs lors de l\'impact avec un objet. Nos scientifiques ont également trouvé un moyen d’améliorer sensiblement l’extraction des métaux et des cristaux grâce à cette technologie.

La production de métaux augmente de 1 %, la production de cristaux de 0,66 % et la production de deutérium de 0,33 % par niveau de construction de la technologie plasma.',
    ],
    'combustion_drive' => [
        'title' => 'Entraînement de combustion',
        'description' => 'Le développement de ce moteur rend certains vaisseaux plus rapides, même si chaque niveau n\'augmente la vitesse que de 10 % par rapport à la valeur de base.',
        'description_long' => 'Le Combustion Drive est la plus ancienne des technologies, mais il est toujours utilisé. Avec le Combustion Drive, les gaz d\'échappement sont formés à partir des propulseurs transportés à l\'intérieur du navire avant utilisation. Dans une enceinte fermée, les pressions sont égales dans chaque direction et aucune accélération ne se produit. Si une ouverture est prévue au fond de la chambre alors la pression ne s\'oppose plus de ce côté. La pression restante donne une poussée résultante dans le côté opposé à l\'ouverture, qui propulse le navire vers l\'avant en expulsant les gaz d\'échappement vers l\'arrière à une vitesse extrêmement élevée.

À chaque niveau de Combustion Drive développé, la vitesse des petits et grands cargos, des chasseurs légers, des recycleurs et des sondes d\'espionnage est augmentée de 10 %.',
    ],
    'impulse_drive' => [
        'title' => 'Entraînement par impulsion',
        'description' => 'L\'impulsion est basée sur le principe de réaction. Le développement ultérieur de ce moteur rend certains vaisseaux plus rapides, bien que chaque niveau n\'augmente la vitesse que de 20 % par rapport à la valeur de base.',
        'description_long' => 'L\'entraînement à impulsion est basé sur le principe du recul, par lequel l\'émission stimulée de rayonnement est principalement produite comme déchet de la fusion du noyau pour gagner de l\'énergie. De plus, d’autres masses peuvent être injectées. À chaque niveau d\'Impulse Drive développé, la vitesse des bombardiers, des croiseurs, des chasseurs lourds et des navires de colonie est augmentée de 20 % par rapport à la valeur de base. De plus, les petits transporteurs sont équipés de moteurs à impulsion dès que leur niveau de recherche atteint 5. Dès que la recherche sur les moteurs à impulsion a atteint le niveau 17, les recycleurs sont rééquipés de moteurs à impulsion.

Les missiles interplanétaires voyagent également plus loin à chaque niveau.',
    ],
    'hyperspace_drive' => [
        'title' => 'Lecteur hyperespace',
        'description' => 'Le lecteur hyperspatial déforme l\'espace autour d\'un vaisseau. Le développement de ce moteur rend certains vaisseaux plus rapides, même si chaque niveau n\'augmente la vitesse que de 30 % par rapport à la valeur de base.',
        'description_long' => 'A proximité immédiate du navire, l\'espace est déformé de sorte que de longues distances peuvent être parcourues très rapidement. Plus l\'Hyperspace Drive est développé, plus la nature déformée de l\'espace est forte, ce qui fait que la vitesse des vaisseaux qui en sont équipés (Battlecruisers, Battleships, Destroyers, Deathstars, Pathfinders et Reapers) augmente de 30 % par niveau. De plus, le bombardier est construit avec un Hyperspace Drive dès que la recherche atteint le niveau 8. Dès que la recherche Hyperspace Drive atteint le niveau 15, le Recycler est rééquipé d\'un Hyperspace Drive.',
    ],
    'espionage_technology' => [
        'title' => 'Technologie d\'espionnage',
        'description' => 'Des informations sur d’autres planètes et lunes peuvent être obtenues grâce à cette technologie.',
        'description_long' => 'La technologie d’espionnage est avant tout une avancée dans la technologie des capteurs. Plus cette technologie est avancée, plus l\'utilisateur reçoit d\'informations sur les activités de son environnement.
Les différences entre votre propre niveau d\'espionnage et les niveaux d\'espionnage de vos adversaires sont cruciales pour les sondes. Plus votre propre technologie d’espionnage est avancée, plus le rapport peut recueillir d’informations et moins il y a de chances que vos activités d’espionnage soient découvertes. Plus vous envoyez de sondes dans une mission, plus elles peuvent recueillir de détails sur la planète cible. Mais en même temps, cela augmente également les chances de découverte.
La technologie d’espionnage améliore également les chances de localiser les flottes étrangères. Le niveau d’espionnage est essentiel pour déterminer cela. À partir du niveau 2, le nombre total exact de navires attaquants est affiché ainsi que la notification d\'attaque normale. Et à partir du niveau 4, le type de navires attaquants ainsi que leur nombre total sont affichés et à partir du niveau 8, le nombre exact de types de navires différents est affiché.
Cette technologie est indispensable pour une attaque à venir, car elle vous informe si la flotte victime dispose ou non d\'une défense. C’est pourquoi cette technologie doit être étudiée très tôt.',
    ],
    'computer_technology' => [
        'title' => 'Technologie informatique',
        'description' => 'Davantage de flottes peuvent être commandées en augmentant les capacités informatiques. Chaque niveau de technologie informatique augmente le nombre maximum de flottes d\'un.',
        'description_long' => 'Une fois lancées dans une mission, les flottes sont principalement contrôlées par une série d\'ordinateurs situés sur la planète d\'origine. Ces ordinateurs massifs calculent l’heure exacte d’arrivée, contrôlent les corrections de trajectoire si nécessaire, calculent les trajectoires et régulent les vitesses de vol.
À chaque niveau recherché, l\'ordinateur de vol est mis à niveau pour permettre le lancement d\'un emplacement supplémentaire. La technologie informatique doit être continuellement développée tout au long de la construction de votre empire.',
    ],
    'astrophysics' => [
        'title' => 'Astrophysique',
        'description' => 'Dotés d\'un module de recherche en astrophysique, les navires peuvent entreprendre de longues expéditions. Un niveau sur deux de cette technologie vous permettra de coloniser une planète supplémentaire.',
        'description_long' => 'D\'autres découvertes dans le domaine de l\'astrophysique permettent de construire des laboratoires qui peuvent être installés sur de plus en plus de navires. Cela rend possibles de longues expéditions dans des zones inexplorées de l’espace. De plus, ces progrès peuvent être utilisés pour coloniser davantage l’univers. Tous les deux niveaux de cette technologie, une planète supplémentaire peut être rendue utilisable.',
    ],
    'intergalactic_research_network' => [
        'title' => 'Réseau de recherche intergalactique',
        'description' => 'Les chercheurs de différentes planètes communiquent via ce réseau.',
        'description_long' => 'Il s\'agit de votre réseau spatial lointain pour communiquer les résultats de la recherche à vos colonies. Avec l\'IRN, des délais de recherche plus rapides peuvent être obtenus en reliant les laboratoires de recherche du plus haut niveau égal au niveau de l\'IRN développé.
Pour fonctionner, chaque colonie doit pouvoir mener les recherches de manière indépendante.',
    ],
    'graviton_technology' => [
        'title' => 'Technologie Graviton',
        'description' => 'Le tir d’une charge concentrée de particules de gravitons peut créer un champ de gravité artificiel pouvant détruire des navires ou même des lunes.',
        'description_long' => 'Un graviton est une particule élémentaire sans masse et sans charge. Il détermine la puissance gravitationnelle. En tirant une charge concentrée de gravitons, un champ gravitationnel artificiel peut être construit. Un peu comme un trou noir, il attire la masse en lui. Ainsi, il peut détruire des navires et même des lunes entières. Pour produire une quantité suffisante de gravitons, il faut d’énormes quantités d’énergie. Graviton Research est nécessaire pour construire une Deathstar destructrice.',
    ],
    'weapon_technology' => [
        'title' => 'Technologie des armes',
        'description' => 'La technologie des armes rend les systèmes d’armes plus efficaces. Chaque niveau de technologie d\'armement augmente la puissance des armes des unités de 10 % de la valeur de base.',
        'description_long' => 'La technologie des armes est une technologie de recherche clé et est essentielle à votre survie contre les empires ennemis. À chaque niveau de technologie d\'armes étudié, les systèmes d\'armes des navires et vos mécanismes de défense deviennent de plus en plus efficaces. Chaque niveau augmente la force de base de vos armes de 10 % de la valeur de base.',
    ],
    'shielding_technology' => [
        'title' => 'Technologie de bouclier',
        'description' => 'La technologie des boucliers rend les boucliers des navires et des installations défensives plus efficaces. Chaque niveau de technologie de bouclier augmente la force des boucliers de 10 % de la valeur de base.',
        'description_long' => 'Avec l\'invention du générateur de magnétosphère, les scientifiques ont appris qu\'un bouclier artificiel pouvait être produit pour protéger l\'équipage des vaisseaux spatiaux non seulement contre le rude environnement de rayonnement solaire dans l\'espace lointain, mais également pour assurer une protection contre les tirs ennemis lors d\'une attaque. Une fois que les scientifiques ont finalement perfectionné la technologie, un générateur de magnétosphère a été installé sur tous les navires et systèmes de défense.

As the technology is advanced to each level, the magnetosphere generator is upgraded which provides an additional 10% strength to the shields base value.',
    ],
    'armor_technology' => [
        'title' => 'Technologie d\'armure',
        'description' => 'Des alliages spéciaux améliorent le blindage des navires et des structures défensives. L\'efficacité de l\'armure peut être augmentée de 10 % par niveau.',
        'description_long' => 'L’environnement de l’espace lointain est rude. Les pilotes et les équipages de diverses missions ont non seulement été confrontés à un rayonnement solaire intense, mais ils ont également été confrontés à la possibilité d\'être touchés par des débris spatiaux ou détruits par les tirs ennemis lors d\'une attaque. Avec la découverte d\'un alliage aluminium-lithium-carbure de titane, qui s\'est avéré à la fois léger et durable, cela a offert à l\'équipage un certain degré de protection. À chaque niveau de technologie de blindage développé, un alliage de meilleure qualité est produit, ce qui augmente la résistance du blindage de 10 %.',
    ],
    'small_cargo' => [
        'title' => 'Petite cargaison',
        'description' => 'La petite cargaison est un vaisseau agile qui peut rapidement transporter des ressources vers d\'autres planètes.',
        'description_long' => 'Les transporteurs sont à peu près aussi gros que les chasseurs, mais ils renoncent aux systèmes de propulsion hautes performances et aux armements embarqués pour gagner en capacité de transport. En conséquence, un transporteur ne devrait être envoyé au combat que s\'il est accompagné de navires prêts au combat.

Dès que l\'Impulse Drive atteint le niveau de recherche 5, le petit transporteur se déplace avec une vitesse de base accrue et est équipé d\'un Impulse Drive.',
    ],
    'large_cargo' => [
        'title' => 'Grande cargaison',
        'description' => 'Ce cargo a une capacité de chargement beaucoup plus grande que la petite cargaison et est généralement plus rapide grâce à une propulsion améliorée.',
        'description_long' => 'Au fil du temps, les raids sur les colonies ont entraîné la capture de quantités de ressources de plus en plus importantes. En conséquence, de petites cargaisons étaient envoyées en masse pour compenser les captures plus importantes. On s\'est vite rendu compte qu\'une nouvelle classe de navires était nécessaire pour maximiser les ressources capturées lors des raids, tout en étant également rentable. Après de nombreux développements, le Large Cargo était né.

Pour maximiser les ressources pouvant être stockées dans les cales, ce navire dispose de peu d\'armes ou d\'armures. Grâce au moteur à combustion hautement développé installé, il constitue le fournisseur de ressources le plus économique entre les planètes et le plus efficace lors des raids sur des mondes hostiles.',
    ],
    'colony_ship' => [
        'title' => 'Navire de colonie',
        'description' => 'Les planètes vacantes peuvent être colonisées avec ce vaisseau.',
        'description_long' => 'Au XXe siècle, l’Homme décide de viser les étoiles. Premièrement, il atterrissait sur la Lune. Après cela, une station spatiale a été construite. Mars a été colonisée peu de temps après. Il fut vite établi que notre croissance dépendait de la colonisation d’autres mondes. Des scientifiques et des ingénieurs du monde entier se sont réunis pour développer la plus grande réussite de l’homme. Le navire de colonie est né.

Ce vaisseau est utilisé pour préparer une planète nouvellement découverte à la colonisation. Une fois arrivé à destination, le navire est instantanément transformé en espace de vie habituel pour aider à peupler et à exploiter le nouveau monde. Le nombre maximum de planètes est ainsi déterminé par les progrès de la recherche en astrophysique. Deux nouveaux niveaux d\'astrotechnologie permettent la colonisation d\'une planète supplémentaire.',
    ],
    'recycler' => [
        'title' => 'Recycleur',
        'description' => 'Les recycleurs sont les seuls navires capables de récolter les champs de débris flottant sur l\'orbite d\'une planète après un combat.',
        'description_long' => 'Les combats dans l’espace prirent une ampleur toujours plus grande. Des milliers de navires ont été détruits et les ressources de leurs restes semblaient perdues à jamais dans les champs de débris. Les cargos normaux ne pouvaient pas s\'approcher suffisamment de ces champs sans risquer des dommages importants.
Un développement récent des technologies de bouclier a contourné efficacement ce problème. Une nouvelle classe de navires similaires aux Transporteurs fut créée : les Recycleurs. Leurs efforts ont permis de rassembler les ressources que l’on pensait perdues, puis de les récupérer. Les débris ne représentaient plus de réel danger grâce aux nouveaux boucliers.

Dès que la recherche Impulse Drive a atteint le niveau 17, les recycleurs sont rééquipés avec Impulse Drives. Dès que la recherche Hyperspace Drive a atteint le niveau 15, les recycleurs sont rééquipés avec des Hyperspace Drives.',
    ],
    'espionage_probe' => [
        'title' => 'Enquête d\'espionnage',
        'description' => 'Les sondes d\'espionnage sont de petits drones agiles qui fournissent des données sur les flottes et les planètes sur de grandes distances.',
        'description_long' => 'Les sondes d\'espionnage sont de petits drones agiles qui fournissent des données sur les flottes et les planètes. Equipé de moteurs spécialement conçus, il leur permet de parcourir de grandes distances en quelques minutes seulement. Une fois en orbite autour de la planète cible, ils collectent rapidement des données et transmettent le rapport via votre réseau Deep Space pour évaluation. Mais il existe un risque pour l’aspect rassemblement intelligent. Pendant que le rapport est renvoyé à votre réseau, le signal peut être détecté par la cible et les sondes peuvent être détruites.',
    ],
    'solar_satellite' => [
        'title' => 'Satellite solaire',
        'description' => 'Les satellites solaires sont de simples plates-formes de cellules solaires situées sur une orbite haute et stationnaire. Ils captent la lumière du soleil et la transmettent à la station au sol via laser.',
        'description_long' => 'Les scientifiques ont découvert une méthode de transmission de l\'énergie électrique à la colonie à l\'aide de satellites spécialement conçus sur une orbite géosynchrone. Les satellites solaires collectent l\'énergie solaire et la transmettent à une station au sol à l\'aide d\'une technologie laser avancée. L\'efficacité d\'un satellite solaire dépend de la force du rayonnement solaire qu\'il reçoit. En principe, la production d’énergie sur les orbites plus proches du soleil est plus importante que pour les planètes sur des orbites éloignées du soleil.
Grâce à leur bon rapport coût/performance, les satellites solaires peuvent résoudre de nombreux problèmes énergétiques. Mais attention : les satellites solaires peuvent être facilement détruits au combat.',
    ],
    'crawler' => [
        'title' => 'Chenille',
        'description' => 'Les robots augmentent la production de métal, de cristal et de deutérium sur la planète qui leur est assignée respectivement de 0,02 %, 0,02 % et 0,02 %. En tant que collectionneur, la production augmente également. Le bonus total maximum dépend du niveau global de vos mines.',
        'description_long' => 'Le Crawler est un grand véhicule de tranchée qui augmente la production de mines et de synthétiseurs. Il est plus agile qu’il n’y paraît mais il n’est pas particulièrement robuste. Chaque chenille augmente la production de métal de 0,02 %, la production de cristaux de 0,02 % et la production de deutérium de 0,02 %. En tant que collectionneur, la production augmente également. Le bonus total maximum dépend du niveau global de vos mines.',
    ],
    'pathfinder' => [
        'title' => 'Éclaireur',
        'description' => 'Le Pathfinder est un vaisseau rapide et agile, spécialement conçu pour les expéditions dans des secteurs inconnus de l\'espace.',
        'description_long' => 'Le Pathfinder est le dernier développement en matière de technologie d\'exploration. Ce navire a été spécialement conçu pour les membres de la classe Discoverer afin de maximiser leur potentiel. Équipé de systèmes de numérisation avancés et d\'une grande soute pour récupérer les ressources, le Pathfinder excelle lors des expéditions. Ses capteurs sophistiqués peuvent détecter des ressources précieuses et des anomalies qui passeraient inaperçues aux yeux des autres navires. Le navire combine une vitesse élevée avec une bonne capacité de chargement, ce qui le rend parfait pour les missions d\'exploration rapides et la collecte de ressources dans des secteurs éloignés.',
    ],
    'light_fighter' => [
        'title' => 'Combattant léger',
        'description' => 'C\'est le premier navire de combat que tous les empereurs construiront. Le chasseur léger est un navire agile, mais vulnérable en soi. En grand nombre, ils peuvent devenir une grande menace pour n’importe quel empire. Ils sont les premiers à accompagner des petites et grandes cargaisons vers des planètes hostiles dotées de défenses mineures.',
        'description_long' => 'C\'est le premier navire de combat que tous les empereurs construiront. Le chasseur léger est un vaisseau agile, mais vulnérable lorsqu\'il est seul. En grand nombre, ils peuvent devenir une grande menace pour n’importe quel empire. Ils sont les premiers à accompagner des petites et grandes cargaisons vers des planètes hostiles dotées de défenses mineures.',
    ],
    'heavy_fighter' => [
        'title' => 'Chasseur lourd',
        'description' => 'Ce chasseur est mieux blindé et possède une force d\'attaque plus élevée que le chasseur léger.',
        'description_long' => 'En développant ce chasseur lourd, les chercheurs ont atteint un point où les propulsions conventionnelles n\'offraient plus des performances suffisantes. Afin de déplacer le navire de manière optimale, l\'entraînement à impulsion a été utilisé pour la première fois. Cela a augmenté les coûts, mais a également ouvert de nouvelles possibilités. En utilisant ce moteur, il restait plus d’énergie pour les armes et les boucliers ; de plus, des matériaux de haute qualité ont été utilisés pour cette nouvelle famille de combattants. Avec ces changements, le chasseur lourd représente une nouvelle ère dans la technologie des navires et constitue la base de la technologie des croiseurs.

Légèrement plus grand que le chasseur léger, le chasseur lourd a une coque plus épaisse, offrant plus de protection et un armement plus puissant.',
    ],
    'cruiser' => [
        'title' => 'Croiseur',
        'description' => 'Les croiseurs sont dotés d\'un blindage presque trois fois plus lourd que les chasseurs lourds et disposent d\'une puissance de feu plus de deux fois supérieure. De plus, ils sont très rapides.',
        'description_long' => 'Avec le développement du laser lourd et du canon à ions, les chasseurs légers et lourds ont subi un nombre alarmant de défaites, qui augmentait à chaque raid. Malgré de nombreuses modifications, changements de puissance des armes et de blindage, il n\'a pas pu être augmenté assez rapidement pour contrer efficacement ces nouvelles mesures défensives. Par conséquent, il a été décidé de construire une nouvelle classe de navires combinant plus de blindage et plus de puissance de feu. Après des années de recherche et de développement, le Cruiser est né.

Les croiseurs sont dotés d\'un blindage près de trois fois supérieur à celui des chasseurs lourds et possèdent une puissance de feu plus de deux fois supérieure à celle de n\'importe quel navire de combat existant. Ils possèdent également des vitesses qui dépassent de loin n’importe quel vaisseau spatial jamais construit. Pendant près d’un siècle, les croiseurs ont dominé l’univers. Cependant, avec le développement des canons Gauss et des tourelles à plasma, leur prédominance a pris fin. Ils sont encore utilisés aujourd’hui contre des groupes de combattants, mais de manière moins prédominante qu’auparavant.',
    ],
    'battle_ship' => [
        'title' => 'Navire de guerre',
        'description' => 'Les cuirassés constituent l\'épine dorsale d\'une flotte. Leurs canons lourds, leur vitesse élevée et leurs grandes soutes en font des adversaires à prendre au sérieux.',
        'description_long' => 'Une fois qu\'il est devenu évident que le croiseur perdait du terrain face au nombre croissant de structures de défense auxquelles il était confronté, et avec la perte de navires en mission à des niveaux inacceptables, il a été décidé de construire un navire capable d\'affronter ce même type de structures de défense avec le moins de pertes possible. Après un développement approfondi, le Battleship était né. Construit pour résister aux plus grandes batailles, le cuirassé dispose de grands espaces de chargement, de canons lourds et d\'une vitesse d\'hyperpropulsion élevée. Une fois développé, il s’est finalement avéré être l’épine dorsale de chaque flotte de raid de l’Empereur.',
    ],
    'battlecruiser' => [
        'title' => 'Croiseur de bataille',
        'description' => 'Le Battlecruiser est hautement spécialisé dans l\'interception des flottes hostiles.',
        'description_long' => 'Ce navire est l\'un des navires de combat les plus avancés jamais développés, et il est particulièrement mortel lorsqu\'il s\'agit de détruire les flottes attaquantes. Avec ses canons laser améliorés à bord et son moteur Hyperspace avancé, le Battlecruiser est une force sérieuse à affronter lors de toute attaque. En raison de la conception du navire et de son système d\'armes de grande taille, les soutes ont dû être réduites, mais cela a été compensé par une consommation de carburant réduite.',
    ],
    'bomber' => [
        'title' => 'Bombardier',
        'description' => 'Le bombardier a été développé spécialement pour détruire les défenses planétaires d\'un monde.',
        'description_long' => 'Au fil des siècles, alors que les défenses commençaient à devenir plus grandes et plus sophistiquées, les flottes commençaient à être détruites à un rythme alarmant. Il a été décidé qu\'un nouveau navire était nécessaire pour briser les défenses et garantir des résultats optimaux. Après des années de recherche et développement, le Bomber a été créé.

À l\'aide d\'un équipement de ciblage à guidage laser et de bombes à plasma, le bombardier recherche et détruit tout mécanisme de défense qu\'il peut trouver. Dès que la propulsion hyperspatiale atteint le niveau 8, le bombardier est équipé du moteur hyperspatial et peut voler à des vitesses plus élevées.',
    ],
    'destroyer' => [
        'title' => 'Destructeur',
        'description' => 'Le destroyer est le roi des navires de guerre.',
        'description_long' => 'Le Destroyer est le résultat d’années de travail et de développement. Avec le développement des Deathstars, il a été décidé qu\'une classe de navire était nécessaire pour se défendre contre une arme aussi massive. Grâce à ses capteurs de guidage améliorés, ses canons à ions multi-phalanges, ses canons Gauss et ses tourelles à plasma, le Destroyer s\'est avéré être l\'un des vaisseaux les plus redoutables jamais créés.

Parce que le destroyer est très grand, sa manœuvrabilité est sévèrement limitée, ce qui en fait davantage une station de combat qu\'un navire de combat. Le manque de maniabilité est compensé par sa puissance de feu, mais sa construction et son fonctionnement coûtent également des quantités importantes de deutérium.',
    ],
    'deathstar' => [
        'title' => 'Étoile de la mort',
        'description' => 'Le pouvoir destructeur de l’Étoile de la Mort est inégalé.',
        'description_long' => 'Le Deathstar est le vaisseau le plus puissant jamais créé. Ce navire de la taille d’une lune est le seul navire visible à l’œil nu au sol. Au moment où vous le repérez, il est malheureusement trop tard pour faire quoi que ce soit.

Armé d\'un gigantesque canon à gravitons, le système d\'armes le plus avancé jamais créé dans l\'Univers, cet énorme vaisseau a non seulement la capacité de détruire des flottes et des défenses entières, mais également la capacité de détruire des lunes entières. Seuls les empires les plus avancés ont la capacité de construire un navire de cette taille gigantesque.',
    ],
    'reaper' => [
        'title' => 'Moissonneuse',
        'description' => 'Le Reaper est un puissant navire de combat spécialisé dans les raids agressifs et la récolte de débris sur le terrain.',
        'description_long' => 'Le Reaper représente le summum de l’ingénierie militaire de classe générale. Ce navire lourdement armé a été conçu pour les commandants qui apprécient à la fois les prouesses au combat et la flexibilité tactique. Bien que son rôle principal soit le combat, le Reaper est doté de soutes renforcées qui lui permettent de récolter les champs de débris après la bataille. Ses systèmes de ciblage avancés et son blindage lourd en font un adversaire redoutable, tandis que sa conception à double usage lui permet de créer et de profiter du carnage sur le champ de bataille. Le navire est équipé d’une technologie d’armement de pointe et peut tenir tête à des navires beaucoup plus gros.',
    ],
    'rocket_launcher' => [
        'title' => 'Lance-roquettes',
        'description' => 'Le lance-roquettes constitue une option défensive simple et économique.',
        'description_long' => 'Votre première ligne de défense de base. Il s’agit de simples installations de lancement au sol qui tirent des missiles conventionnels à tête nucléaire sur des cibles ennemies attaquantes. Comme ils sont peu coûteux à construire et qu\'aucune recherche n\'est requise, ils sont bien adaptés à la défense contre les raids, mais perdent en efficacité contre des attaques à plus grande échelle. Une fois que vous commencez à construire des systèmes d\'armes de défense plus avancés, les lance-roquettes deviennent de simples éléments permettant à vos armes les plus dommageables d\'infliger des dégâts plus importants pendant une période de temps plus longue.

Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'light_laser' => [
        'title' => 'Laser léger',
        'description' => 'Un tir concentré sur une cible avec des photons peut produire des dégâts bien plus importants que les armes balistiques standard.',
        'description_long' => 'À mesure que la technologie se développait et que des navires plus sophistiqués étaient créés, il fut déterminé qu\'une ligne de défense plus solide était nécessaire pour contrer les attaques. À mesure que la technologie laser progressait, une nouvelle arme a été conçue pour fournir un niveau de défense supérieur. Les lasers légers sont de simples armes au sol qui utilisent des systèmes de ciblage spéciaux pour suivre l\'ennemi et tirer un laser de haute intensité conçu pour traverser la coque de la cible. Afin de rester rentables, ils ont été équipés d\'un système de blindage amélioré, mais l\'intégrité structurelle est la même que celle du lance-roquettes.

Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'heavy_laser' => [
        'title' => 'Laser lourd',
        'description' => 'Le laser lourd est l’évolution logique du laser léger.',
        'description_long' => 'Le Heavy Laser est une version pratique et améliorée du Light Laser. Étant plus équilibré que le Light Laser avec une composition d’alliage améliorée, il utilise des faisceaux plus puissants et plus denses et des systèmes de ciblage embarqués encore meilleurs.

Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'gauss_cannon' => [
        'title' => 'Canon Gauss',
        'description' => 'Le canon Gauss tire des projectiles pesant des tonnes à grande vitesse.',
        'description_long' => 'Pendant longtemps, les armes à projectiles ont été considérées comme obsolètes dans le sillage de la technologie thermonucléaire et énergétique moderne et en raison du développement de l\'hyperpropulsion et de l\'amélioration du blindage. C\'était jusqu\'à ce que la technologie énergétique exacte qui l\'avait autrefois vieilli l\'aide à retrouver sa position établie.
Un canon Gauss est une version agrandie de l\'accélérateur de particules. Les missiles extrêmement lourds sont accélérés avec une énorme force électromagnétique et ont des vitesses initiales qui font brûler dans le ciel la saleté entourant le missile. Cette arme est si puissante lorsqu\'elle est tirée qu\'elle crée un bang supersonique. Les armures et boucliers modernes peuvent à peine résister à la force, la cible étant souvent complètement pénétrée par la puissance du missile. Les structures de défense se désactivent dès qu\'elles sont trop endommagées.

Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'ion_cannon' => [
        'title' => 'Canon à ions',
        'description' => 'Le canon à ions tire un faisceau continu d\'ions accélérés, causant des dégâts considérables aux objets qu\'il frappe.',
        'description_long' => 'Un canon à ions est une arme qui tire des faisceaux d\'ions (particules chargées positivement ou négativement). Le canon à ions est en fait un type de canon à particules ; seules les particules utilisées sont ionisées. En raison de leurs charges électriques, ils ont également le potentiel de désactiver les appareils électroniques et tout ce qui possède une source d\'énergie électrique ou similaire, en utilisant un phénomène connu sous le nom d\'impulsion électromagetique (effet EMP). Grâce au système de blindage hautement amélioré du canon, ce canon offre une protection améliorée pour vos armes de défense plus grandes et plus destructrices.

Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'plasma_turret' => [
        'title' => 'Tourelle à plasma',
        'description' => 'Les tourelles à plasma libèrent l\'énergie d\'une éruption solaire et surpassent même le destroyer en termes d\'effet destructeur.',
        'description_long' => 'L\'un des systèmes d\'armes de défense les plus avancés jamais développés, la tourelle à plasma utilise une grande pile à combustible de réacteur nucléaire pour alimenter un accélérateur électromagnétique qui déclenche une impulsion, ou tore, de plasma. Pendant le fonctionnement, la tourelle à plasma se verrouille d\'abord sur une cible et commence le processus de tir. Une sphère de plasma est créée dans le noyau de la tourelle en surchauffant et en comprimant les gaz, les dépouillant de leurs ions. Une fois que le gaz est surchauffé, comprimé et qu’une sphère de plasma est créée, il est ensuite chargé dans l’accélérateur électromagnétique qui est mis sous tension. Une fois pleinement alimenté, l\'accélérateur est activé, ce qui entraîne le lancement de la sphère de plasma à une vitesse extrêmement élevée vers la cible prévue. Du point de vue de la cible, la boule de plasma bleuâtre qui approche est impressionnante, mais une fois qu\'elle frappe, elle provoque une destruction instantanée.

Les installations défensives se désactivent dès qu\'elles sont trop fortement endommagées. Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'small_shield_dome' => [
        'title' => 'Petit dôme de bouclier',
        'description' => 'Le petit dôme de bouclier recouvre une planète entière d’un champ capable d’absorber une énorme quantité d’énergie.',
        'description_long' => 'La colonisation de nouveaux mondes a engendré un nouveau danger : les débris spatiaux. Un gros astéroïde pourrait facilement anéantir le monde et tous ses habitants. Les progrès de la technologie de protection ont fourni aux scientifiques un moyen de développer un bouclier pour protéger une planète entière non seulement des débris spatiaux mais, comme on l’a appris, d’une attaque ennemie. En créant un vaste champ électromagnétique autour de la planète, les débris spatiaux qui auraient normalement détruit la planète ont été déviés et les attaques des empires ennemis ont été contrecarrées. Les premiers générateurs étaient de grande taille et le bouclier offrait une protection modérée, mais on découvrit plus tard que les petits boucliers n\'offraient pas de protection contre les attaques à plus grande échelle. Le petit dôme de bouclier était le prélude à un système de protection planétaire plus puissant et plus avancé à venir.

Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'large_shield_dome' => [
        'title' => 'Grand Dôme Bouclier',
        'description' => 'L\'évolution du petit dôme de bouclier peut utiliser beaucoup plus d\'énergie pour résister aux attaques.',
        'description_long' => 'Le Grand Dôme Bouclier est la prochaine étape dans l’avancement des boucliers planétaires, c’est le résultat d’années de travail pour améliorer le Petit Dôme Bouclier. Construits pour résister à un plus grand barrage de tirs ennemis en fournissant un champ électromagnétique plus énergique, les grands dômes offrent une période de protection plus longue avant de s\'effondrer.

Après une bataille, il y a jusqu\'à 70 % de chances que les installations défensives défaillantes puissent être réutilisées.',
    ],
    'anti_ballistic_missile' => [
        'title' => 'Missiles anti-balistiques',
        'description' => 'Les missiles antibalistiques détruisent les missiles interplanétaires attaquants.',
        'description_long' => 'Les missiles antibalistiques (ABM) sont votre seule ligne de défense lorsque vous êtes attaqué par des missiles interplanétaires (IPM) sur votre planète ou votre lune. Lorsqu\'un lancement d\'IPM est détecté, ces missiles s\'arment automatiquement, traitent un code de lancement dans leurs ordinateurs de vol, ciblent l\'IPM entrant et se lancent pour l\'intercepter. Pendant le vol, l\'IPM cible est constamment suivi et des corrections de trajectoire sont appliquées jusqu\'à ce que l\'ABM atteigne la cible et détruise l\'IPM attaquant. Chaque ABM détruit un IPM entrant.',
    ],
    'interplanetary_missile' => [
        'title' => 'Missiles interplanétaires',
        'description' => 'Les missiles interplanétaires détruisent les défenses ennemies.',
        'description_long' => 'Les missiles interplanétaires (IPM) sont votre arme offensive pour détruire les défenses de votre cible. Utilisant une technologie de suivi de pointe, chaque missile cible un certain nombre de défenses à détruire. Equipés d\'une bombe antimatière, ils délivrent une force destructrice si puissante que les boucliers et les défenses détruits ne peuvent pas être réparés. La seule façon de contrer ces missiles est d’utiliser des ABM.',
    ],
    'kraken' => [
        'title' => 'KRAKEN',
        'description' => 'Réduit le temps de construction des bâtiments actuellement en construction de <b>:duration</b>.',
    ],
    'detroid' => [
        'title' => 'DÉTROÏDE',
        'description' => 'Réduit le temps de construction des contrats de chantier naval actuels de <b>:duration</b>.',
    ],
    'newtron' => [
        'title' => 'NEWTRON',
        'description' => 'Réduit le temps de recherche pour toutes les recherches actuellement en cours de <b>:duration</b>.',
    ],
];

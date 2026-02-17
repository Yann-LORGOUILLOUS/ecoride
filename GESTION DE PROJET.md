GESTION DE PROJET
Lien Trello : https://trello.com/b/VpR9vtCH/ecoride-ecf

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\

1\. Choix de la méthode

Pour la réalisation du projet EcoRide, j’ai opté pour une gestion de projet de type Kanban via Trello. Ce choix s’explique pour plusieurs raisons :

* Projet individuel
* Développement progressif et évolutif
* Nécessité de visualiser l’ensemble des fonctionnalités
* Besoin de flexibilité dans l’ordre de réalisation

La méthode Kanban s’est révélée plus adaptée au cadre d’un projet individuel, tout en me permettant une gestion structurée des priorités.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\

2\. Organisation du Kanban

Le tableau Trello est structuré en cinq colonnes :


TÂCHES : recensement exhaustif des fonctionnalités prévues, classées par importance.
À FAIRE : fonctionnalités sélectionnées pour le cycle de travail en cours.
EN COURS : tâches en cours d'exécution, avec checklist pour suivre les étapes.
TERMINÉES (branche dev) : fonctionnalités validées fonctionnellement et intégrées sur la branche de développement.
MERGES (branche main) : fonctionnalités stabilisées et fusionnées sur la branche principale.

Cette organisation permet :

* Une vision globale du projet
* Une limitation du travail en cours (brique par brique)
* Un suivi clair entre gestion de projet et gestion des branches Git

Chaque fonctionnalité a été développée sur une branche dédiée avant intégration progressive vers dev puis vers main.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\

3\. Gestion des priorités

La priorisation a été effectuée selon trois niveaux :

Importance haute : tâches initiales et fonctionnalités cœur métier indispensables (traduction de la commande après lecture des US).
Importance moyenne : tâches complémentaires non cruciales (j'ai notamment créé plus de maquettes que demandées).
Importance faible : améliorations ergonomiques ou bonus non strictement exigés par l’énoncé.

Force est de constater que les tâches à importance faible n'ont pu être menées par faute de temps, ce qui justifie leur classement et leur traitement en fin de projet (si possible).

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\

4\. Gestion des itérations

Le projet a nécessité plusieurs ajustements. Certaines fonctionnalités ont été réouvertes afin de :

* Corriger des interprétations initiales des exigences
* Adapter la structure de la base de données
* Améliorer la cohérence (notamment sur la gestion des crédits et des statuts de trajets)

Ces itérations ont permis :

* Une meilleure compréhension des besoins fonctionnels
* Un renforcement de la cohérence globale de l’application

Ce travail m’a également permis d’identifier un axe d’amélioration important : approfondir l’analyse fonctionnelle (et la traduction concrète de la commande) en amont du développement afin de limiter les réajustements ultérieurs. Cela est d'autant plus important dans le cadre d'un travail collectif.
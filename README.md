# klaxurit-JuncaHugo_6_16092022
SnowTricks Website @Symfony
[![Maintainability](https://api.codeclimate.com/v1/badges/b9ffbac3a93d9252c7cc/maintainability)](https://codeclimate.com/github/klaxurit/klaxurit-JuncaHugo_6_16092022/maintainability)
<h1 align="center">Welcome to SnowTricks 👋</h1>
<p>
  <img alt="Version" src="https://img.shields.io/badge/version-Symfony 5.4-blue.svg?cacheSeconds=2592000" />
  <a href="#" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
  <a href="https://twitter.com/axurit19" target="_blank">
    <img alt="Twitter: axurit19" src="https://img.shields.io/twitter/follow/axurit19.svg?style=social" />
  </a>
</p>

> A community site with all the snowboard tricks you are looking for.

## Prérequis

- PHP >= 7.2.5
- Composer

## Paquet installé via composer

- symfony/mailer v5.4.12
- twig/twig v3.4.2
- twig/string-extra v3.4.0


## Installation

1. Clonez le dépôt git :
git clone https://github.com/klaxurit/klaxurit-JuncaHugo_6_16092022.git
2. Installez les dépendances en utilisant Composer :
composer install
3. Copiez le .env en .env.local et modifier les paramètres sql, email.
Ne pas oubliez d'installé MailHog pour recevoir les emails.(inscriptions, changement de mot de passe et verification d'email)
Suivez les instructions sur: https://github.com/mailhog/MailHog
4. Créez la base de données et effectuez les migrations en utilisant les commandes Doctrine :
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

## Exécution

Exécutez le serveur local Symfony pour lancer l'application :
php bin/console server:run
Vous devriez maintenant pouvoir accéder à l'application en accédant à l'adresse `http://127.0.0.1:8000` dans votre navigateur.

Pour chargez un jeux de donnée veuillez saisir cette commande dans votre terminal:

php bin/console doctrine:fixtures:load

Les différents comptes disponible sont:

### Administrateur
identifiant: Admin
Mot de passe: Admin_1234

### Utilisateur
identifiant: UserOne
Mot de passe: Userone_1234

identifiant: UserTwo
Mot de passe: UserTwo_1234


## Autheur

👤 **JUNCA Hugo**

* Website: JUNCA Hugo
* Twitter: [@axurit19](https://twitter.com/axurit19)
* Github: [@klaxurit](https://github.com/klaxurit)
* LinkedIn: [@juncahugo](https://linkedin.com/in/juncahugo)

## Contribuer

Si vous souhaitez contribuer à ce projet, veuillez suivre les étapes suivantes :

1. Forkez ce dépôt
2. Créez une nouvelle branche (`git checkout -b nom_de_la_nouvelle_branche`)
3. Faites vos modifications
4. Commit et push sur votre branche (`git push origin nom_de_la_nouvelle_branche`)
5. Créez une pull request

***


# 🛒 Projet E-commerce-PHP


## 🚀 Fonctionnalités

### 🔐 1. Enregistrement et Connexion

- Un système de register avec username / email / password pour ce connecter et garder une authenticité unique grâce à l’email.

  ![image](img/1.png)
  
- Un système de login / password est aussi mise en place avec un affichage différent en fonction de si on est user / user non enregistré / admin.

  ![image](img/2.png)

### 👤 2. Comptes Utilisateurs

- Possibilité de consulter les comptes des autres utilisateurs pour voir leurs ventes ou bien encore leur email pour les contacter en cas de problème.

  ![image](img/3.png)
  
- Les utilisateurs peuvent modifier leurs profils (changer le mot de passe ou l'adresse e-mail) de sorte à vite régler des problèmes de sécurité de leurs point de vue.

  ![image](img/4.png)

### ✏️ 3. Édition des Publications

- Les utilisateurs peuvent éditer leurs publications pour corriger des erreurs de frappe ou des problèmes d'URL d'image ce qui rend l’erreur de poste plus acceptable.

  ![image](img/5.png)
  
- L'administrateur peut aussi modifier les posts de tous les utilisateurs pour facilité la tâche à ceux qui auraient du mal avec l’informatique.

  ![image](img/6.png)

### 🔧 4. Rôle de l'Administrateur

- L'administrateur peut créer et modifier des posts utilisateurs pour éviter les débordement d’annonce ou bien d’insulte dans les postes.

  ![image](img/7.png)
  
- L'administrateur ne voit pas les informations sensibles des utilisateurs (ex : montant d'argent) ce qui permet une certain sécurité et climat de confiance.

  ![image](img/8.png)

### 🛍️ 5. Système d'Achat

- Les utilisateurs peuvent acheter des articles en quantité avec un système pour éviter d’acheter des objets qui sont au dessus de son montant total d’argent sans dépasser le stock prévue.

  ![image](img/9.png)
  
- Un panier est disponible pour sauvegarder les achats potentiels pour éviter des oublies ou bien de revenir en arrière si besoin.

  ![image](img/10.png)

## 🏗️ Technologies utilisées

- **Backend** : (php)
- **Base de données** : (php mysql avec phpmyadmin)
- **Frontend** : (html(dans le php) / css)

## 📜 Installation

1. Clonez ce dépôt :
   ```sh
   git clone https://github.com/Altrevis/E-commerce-PHP.git
   ```
2. Ligne à modifier dans /includes/db.php :
   ```sh
   $host = 'localhost';
    $dbname = 'php_exam';
    $username = 'xxxx';
    $password = 'xxxx';
   ```
3. installer la db :

    - Créer la db avec le nom ci-dessous

    ![image](img/db_1.png)

    - Ajouter la db dans l'emplacement importer

    ![image](img/db_2.png)

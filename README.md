## bfmania.com

Site bfmania.com

#### Spécifications

Ce projet nécessite :
* PHP: >= 7.1
* PHP extensions: mbstring, mysqli, intl, fileinfo
* MariaDB ou MySQL: >=5.x
* Server modules : composer
* Apache modules: rewrite, expires, header

#### CSS utile

`.btn-rounded` ajoutera un border-radius de 100px

`.btn-color-1` fera un bouton de la couleur principale 
`.btn-outline-color-1` fera un bouton transparent avec une bordure de la couleur principale

`.btn-color-2` et `.btn-outline-color-2` existe pour des boutons de la couleur secondaires

Les class commençant par `.btn-*` doivent être utilisé avec la class `.btn` initiale de bootstrap. Exemple :
```
<button class="btn btn-outline-color-1">Hello World</button>
```


`text-color-1` et `tesxt-color-2` pour mettre du texte de la couleur principale ou secondaire, ainsi que `bg-color-1` et `bg-color-2` pour les backgrounds


Pour les titres, utilisation de la class `.title-page` associé a la class `.with-underline` pour avoir le soulignement.

Les polices `.font-montserrat` où `.font-vibes`.
Il existe également des classes allant de `.font8` à `.font30` pour définir la taille. `.font8` correspondra a 8px et ainsi de suite.

Puis les nombres pairs à partir de `.font30` jusqu'à `.font50`.

Pour le gras, il existe les class `.fw-100` (font-weight: 100), `.fw-200`, `.fw-300` et jusqu'à `.fw-900`.

Le police doit néanmoins supporter cette "graisseur".
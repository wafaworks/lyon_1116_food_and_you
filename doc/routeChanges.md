#Wild Code School

##Route modifications

###ApiController
+ event/list => evenement/liste

###DefaultController
+ regelement_du_jeu => reglement-du-jeu

###EventController
+ event/list => evenement/liste
+ event/details/{id} => evenement/details/{slug}
+ event/reserve/{id} => evenement/reservation/{slug}
+ event/vote => evenement/vote
+ event/reserve-process/{id} => evenement/processus-de-reservation/{slug}
+ event/reserve-cancel/{id} => evenement/reservation-annulée/{slug}
+ event/apply-to/{id}
+ evenement/apply-to/{slug}
+ event/photos/{id}
+ evenement/photos/{slug}

###MemberController
+ utilisateur/search => utilisateur/recherche
+ utilisateur/search/{id} => utilisateur/recherche-id

###RecipeController
+ my-recipes => mes-recettes

###RestaurantController
+ restaurants/details/{id}
+ restaurants/details/{slug}
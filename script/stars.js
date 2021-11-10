// GESTION DES ETOILES POUR LA NOTATION :

window.onload = () => {
    // On va chercher toutes les étoiles
    const stars = document.querySelectorAll(".changestar");
    // On va chercher l'input
    const note = document.querySelector("#note");

    // On boucle sur les étoiles pour le ajouter des écouteurs d'évènements
    for(star of stars){
        // On écoute le survol
		star.style.cursor = "pointer";
        star.addEventListener("mouseover", function(){
            resetStars();
            this.style.color = "#701818";
            this.classList.add("fas");
            this.classList.remove("far");
            // L'élément précédent dans le DOM (de même niveau, balise soeur)
            let previousStar = this.previousElementSibling;

            while(previousStar){
                // On passe l'étoile qui précède en rouge
                previousStar.style.color = "#701818";
                previousStar.classList.add("fas");
                previousStar.classList.remove("far");
                // On récupère l'étoile qui la précède
                previousStar = previousStar.previousElementSibling;
            }
        });

        // On écoute le clic
        star.addEventListener("click", function(){
            note.value = this.dataset.value;
        });

        star.addEventListener("mouseout", function(){
            resetStars(note.value);
        });
    }

    /**
     * Reset des étoiles en vérifiant la note dans l'input caché
     * @param {number} note 
     */
    function resetStars(note = 0){
        for(star of stars){
            if(star.dataset.value > note){
                star.style.color = "black";
                star.classList.add("far");
                star.classList.remove("fas");
            }else{
                star.style.color = "#701818";
                star.classList.add("fas");
                star.classList.remove("far");
            }
        }
    }
}
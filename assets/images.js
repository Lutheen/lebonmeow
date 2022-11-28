window.onload = () => {
    // Gestion des liens "delete"
    let links = document.querySelectorAll("[data-delete]")
    // On boucle sur links
    for (link of links){
        // On écoute le clic
        link.addEventListener("click", function(e){
            // On empêche la navigation
            e.preventDefault()
            // On demande confirmation
            if (confirm("Voulez-vous vraiment supprimer cette image ?")){
                // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    // On récupère la réponse en JSON
                    response => response.json()
                ).then(data => {
                    if (data.success) {
                        this.parentElement.remove()
                    } else {
                        alert(data.error)
                    }
                }).catch(e => alert(e))
            }
        })
    }
}
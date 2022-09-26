window.onload = () => {
    // manage delete buttons
    let links = document.querySelectorAll("[data-delete]")

    // loop on buttons
    for(link of links){
        // listen clic
        link.addEventListener("click", function(e){
            //block navigation
            e.preventDefault()

            // ask for confirmation
            if(confirm("Did you really want to delete this image ?")){
                // send ajax request to the href button
                fetch(this.getAttribute("href"), {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': "XMLHttpRequest",
                        'Content-Type': "applicadtion/json"
                    },
                    body: JSON.stringify({'_token': this.dataset.token})
                }).then(
                    // get json response
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}
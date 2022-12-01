window.onload = () => {
    const initMediaTypeSelector = (item) => {
        const selectors = item.querySelectorAll('.btn-type')
        const imageUpload = item.querySelector('#image-upload')
        const imageAlt = item.querySelector('#image-alt')
        const videoUrl = item.querySelector('#video-url')
        selectors.forEach(selector => {
            selector.addEventListener('change', (e) => {
                if (e.currentTarget.value === 'Image') {
                    imageUpload.classList.remove('d-none')
                    imageAlt.classList.remove('d-none')
                    videoUrl.classList.add('d-none') 
                } else if (e.currentTarget.value === 'Video') {
                    videoUrl.classList.remove('d-none')
                    imageUpload.classList.add('d-none')
                    imageAlt.classList.add('d-none')
                }
            })
        })
    }

    const newItem = (e) => {
        const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
        const item = document.createElement("div");

        item.innerHTML += collectionHolder
            .dataset
            .prototype
            .replace(
                /__name__/g, 
                collectionHolder.dataset.index
            );
            item.querySelector(".btn-remove").addEventListener("click", () => item.remove());
            collectionHolder.appendChild(item);
            collectionHolder.dataset.index++;

            initMediaTypeSelector(item)
    };

    document
        .querySelectorAll('.btn-new')
        .forEach(btn => btn.addEventListener("click", newItem));

    document
        .querySelectorAll('.btn-remove')
        .forEach(btn => btn.addEventListener("click", (e) => e.currentTarget.closest(".item").remove()));

    document
        .querySelectorAll('.form-media')
        .forEach(formCtrl => {
            initMediaTypeSelector(formCtrl)
        })

    // manage delete buttons
    let links = document.querySelectorAll("[data-delete]")
    

    // EDIT FORM REMOVE MEDIA PART
    // loop on buttons
    for(link of links){
        // listen clic
        link.addEventListener("click", function(e){
            //block navigation
            e.preventDefault()
            const elParent = e.currentTarget.closest('.mediasPreview')

            // ask for confirmation
            if(confirm("Did you really want to delete this image ?")){
                // send ajax request to the href button
                fetch(this.getAttribute("href"), {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': "XMLHttpRequest",
                        'Content-Type': "application/json"
                    },
                    body: JSON.stringify({'_token': this.dataset.token})
                }).then(
                    // get json response
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        elParent.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}
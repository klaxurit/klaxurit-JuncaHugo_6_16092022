window.onload = () => {
    const initMediaTypeSelector = (item) => {
        const selectors = item.querySelectorAll('.btn-type')
        selectors.forEach(selector => {
            selector.addEventListener('change', (e) => {
                if (e.currentTarget.value === 'Image') {
                    item.querySelector('#image-upload').classList.remove('d-none')
                    item.querySelector('#image-alt').classList.remove('d-none')
                    item.querySelector('#video-url').classList.add('d-none') 
                } else if (e.currentTarget.value === 'Video') {
                    item.querySelector('#video-url').classList.remove('d-none')
                    item.querySelector('#image-upload').classList.add('d-none')
                    item.querySelector('#image-alt').classList.add('d-none')
                }
            })

            if (selector.value === 'Image' && selector.checked) {
                item.querySelector('#image-upload').classList.remove('d-none')
                item.querySelector('#image-alt').classList.remove('d-none')
                item.querySelector('#video-url').classList.add('d-none') 
            } else if (selector.value === 'Video' && selector.checked) {
                item.querySelector('#video-url').classList.remove('d-none')
                item.querySelector('#image-upload').classList.add('d-none')
                item.querySelector('#image-alt').classList.add('d-none')
            }
            console.log(selector.checked, selector.value)
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
    console.log(links)

    // EDIT FORM REMOVE MEDIA PART
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
                        document.getElementById('mediasPreview').remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}
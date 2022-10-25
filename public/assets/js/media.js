window.onload = () => {
    const initMediaTypeSelector = (item) => {
        const selectors = item.querySelectorAll('.btn-type')
        selectors.forEach(selector => {
            selector.addEventListener('change', (e) => {
                if (e.currentTarget.value === 'Image') {
                    item.querySelector('#image-upload').classList.remove('d-none')
                    item.querySelector('#image-upload input').setAttribute('required', true)
                    item.querySelector('#image-alt').classList.remove('d-none')
                    item.querySelector('#video-url').classList.add('d-none') 
                } else if (e.currentTarget.value === 'Video') {
                    item.querySelector('#video-url').classList.remove('d-none')
                    item.querySelector('#image-upload').classList.add('d-none')
                    item.querySelector('#image-alt').classList.add('d-none')
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

    
}
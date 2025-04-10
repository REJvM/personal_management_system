document.querySelectorAll(".add_item_link").forEach((btn) => {
    btn.addEventListener("click", addFormToCollection);
});

document
    .querySelectorAll("button.remove_item_link")
    .forEach((removeFormButton) => {
        removeFormButton.addEventListener("click", (e) => {
            e.preventDefault();
            const fieldId = "blog_post_links";
            const itemId = removeFormButton.id.substring(0, fieldId.length + 2);
            const item = document.querySelector("li:has(> #" + itemId + ")");
            item.remove();
        });
    });

function addFormToCollection(e) {
    const collectionHolder = document.querySelector(
        "." + e.currentTarget.dataset.collectionHolderClass
    );
    const item = document.createElement("li");

    item.innerHTML = collectionHolder.dataset.prototype.replace(
        /__name__/g,
        collectionHolder.dataset.index
    );
    item.querySelector("div");

    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;

    const removeFormButton = item.querySelector(".remove_item_link");
    removeFormButton.addEventListener("click", (e) => {
        e.preventDefault();
        item.remove();
    });
}

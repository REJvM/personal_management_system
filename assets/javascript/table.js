const tbody = document.querySelector('form#tableForm table tbody');
if(tbody) {
    tbody.addEventListener('click', element => {
        const tr = element.target.parentNode;
        const editButton = document.querySelector('button[value="edit"]');
        const deleteButton = document.querySelector('button[value="delete"]');
        if(tr.querySelector('input.object_id')){
            if(tr.querySelector('input.object_id').checked) {
                tr.querySelector('input.object_id').checked = false;
                if(editButton !== null) {
                    editButton.disabled = true;
                }
                if(deleteButton !== null) {
                    deleteButton.disabled = true;
                } 
            } else {
                tr.querySelector('input.object_id').checked = true;
                if(editButton !== null) {
                    editButton.disabled = false;
                }
                if(deleteButton !== null) {
                    deleteButton.disabled = false;
                }
            }
        }
    });
}

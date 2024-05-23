document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('image-preview');
                img.src = e.target.result;
                img.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('add-answer').addEventListener('click', function() {
        const container = document.getElementById('answers-container');
        if (container.children.length < 26) {
            const original = container.firstElementChild.cloneNode(true);
            original.querySelector('input').value = '';
            original.querySelector('select').value = 'false';
            container.appendChild(original);
        }
        
    });

    document.getElementById('remove-answer').addEventListener('click', function() {
        const container = document.getElementById('answers-container');
        if (container.children.length > 1) {
            container.removeChild(container.lastElementChild);
        }
    });
})